<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsoMedicamentoStoreRequest;
use App\Http\Requests\UsoMedicamentoUpdateRequest;
use App\Models\Vaca;
use App\Models\Personal;
use App\Models\Medicamento;
use App\Services\UsoMedicamentoService;
use App\Imports\UsoMedicamentoImport;
use App\Exports\UsoMedicamentoExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Models\UsoMedicamento;

class UsoMedicamentoController extends Controller
{
    protected UsoMedicamentoService $usoMedicamentoService;

    public function __construct(UsoMedicamentoService $usoMedicamentoService)
    {
        $this->usoMedicamentoService = $usoMedicamentoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', UsoMedicamento::class);

        $filters = [
            'search' => $request->get('search'),
            'id_vaca' => $request->get('id_vaca'),
            'id_medicamento' => $request->get('id_medicamento'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $usos = $this->usoMedicamentoService->getPaginated($filters, 15);

        // Datos para filtros (optimizado - solo campos necesarios)
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();
        $medicamentos = Medicamento::select('id_medicamento', 'nombre')->activos()->orderBy('nombre')->get();

        // Datos para gráficas
        $datosGraficas = $this->usoMedicamentoService->getDatosGraficas();

        return view('admin.uso_medicamentos.index', compact('usos', 'vacas', 'medicamentos', 'datosGraficas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', UsoMedicamento::class);
        // Optimizado - solo campos necesarios
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();
        $medicamentos = Medicamento::select('id_medicamento', 'nombre')->activos()->orderBy('nombre')->get();
        $personal = Personal::select('id_personal', 'nombre')->orderBy('nombre')->get();
        
        return view('admin.uso_medicamentos.create', compact('vacas', 'medicamentos', 'personal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsoMedicamentoStoreRequest $request)
    {
        Gate::authorize('create', UsoMedicamento::class);
        try {
            $uso = $this->usoMedicamentoService->create($request->validated());
            
            $mensaje = 'Uso de medicamento registrado exitosamente.';
            
            // Si se creó retiro automático, informar
            if ($uso->retiro) {
                $mensaje .= ' Se ha generado un retiro automático hasta el ' . $uso->retiro->fecha_fin->format('d/m/Y') . '.';
            }
            
            return redirect()->route('admin.uso-medicamentos.index')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el uso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $uso = $this->usoMedicamentoService->findWithRelations($id);
        
        if (!$uso) {
            return redirect()->route('admin.uso-medicamentos.index')
                ->with('error', 'Uso de medicamento no encontrado.');
        }

        Gate::authorize('view', $uso);
        return view('admin.uso_medicamentos.show', compact('uso'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $uso = $this->usoMedicamentoService->findById($id);
        
        if (!$uso) {
            return redirect()->route('admin.uso-medicamentos.index')
                ->with('error', 'Uso de medicamento no encontrado.');
        }

        Gate::authorize('update', $uso);
        $vacas = Vaca::orderBy('codigo')->get();
        $medicamentos = Medicamento::activos()->orderBy('nombre')->get();
        $personal = Personal::orderBy('nombre')->get();
        
        return view('admin.uso_medicamentos.edit', compact('uso', 'vacas', 'medicamentos', 'personal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsoMedicamentoUpdateRequest $request, string $id)
    {
        try {
            $uso = $this->usoMedicamentoService->findById($id);
            
            if (!$uso) {
                return redirect()->route('admin.uso-medicamentos.index')
                    ->with('error', 'Uso de medicamento no encontrado.');
            }

            Gate::authorize('update', $uso);
            $uso = $this->usoMedicamentoService->update($uso, $request->validated());
            
            return redirect()->route('admin.uso-medicamentos.show', $uso->id_uso)
                ->with('success', 'Uso de medicamento actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el uso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $uso = $this->usoMedicamentoService->findById($id);
            
            if (!$uso) {
                return redirect()->route('admin.uso-medicamentos.index')
                    ->with('error', 'Uso de medicamento no encontrado.');
            }

            Gate::authorize('delete', $uso);
            $this->usoMedicamentoService->delete($uso);
            
            return redirect()->route('admin.uso-medicamentos.index')
                ->with('success', 'Uso de medicamento eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el uso: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        Gate::authorize('create', UsoMedicamento::class);
        return view('admin.uso_medicamentos.import');
    }

    /**
     * Previsualizar archivo Excel antes de importar
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $archivo = $request->file('archivo');
            $nombreArchivo = 'import_uso_medicamento_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');

            $import = new UsoMedicamentoImport($this->usoMedicamentoService);
            $rows = Excel::toArray($import, Storage::path($rutaTemporal));

            $preview = array_slice($rows[0], 0, 10);
            $totalRows = count($rows[0]);

            return view('admin.uso_medicamentos.import-preview', compact('preview', 'totalRows', 'rutaTemporal'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al leer el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Procesar importación del archivo Excel
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'archivo_temp' => 'required|string',
            'procesar_async' => 'nullable|boolean'
        ]);

        try {
            $rutaTemporal = $request->input('archivo_temp');
            $rutaArchivo = Storage::path($rutaTemporal);
            
            if (!Storage::exists($rutaTemporal)) {
                throw new \Exception('Archivo temporal no encontrado.');
            }

            $usarAsync = $this->debeUsarProcesamientoAsync($rutaArchivo, $request->boolean('procesar_async'));

            if ($usarAsync) {
                ProcessExcelImportJob::dispatch(
                    UsoMedicamentoImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Uso de Medicamentos'
                );

                return redirect()->route('admin.uso-medicamentos.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new UsoMedicamentoImport($this->usoMedicamentoService);
                Excel::import($import, $rutaArchivo);

                Storage::delete($rutaTemporal);

                $errors = $import->getErrors();
                if (!empty($errors)) {
                    return redirect()->route('admin.uso-medicamentos.index')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('import_errors', $errors);
                }

                return redirect()->route('admin.uso-medicamentos.index')
                    ->with('success', 'Importación de usos de medicamentos completada exitosamente.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Determinar si debe usar procesamiento asíncrono
     */
    protected function debeUsarProcesamientoAsync(string $rutaArchivo, ?bool $forzarAsync = null): bool
    {
        if ($forzarAsync === true) return true;
        if ($forzarAsync === false) return false;

        $tamañoMB = filesize($rutaArchivo) / (1024 * 1024);
        if ($tamañoMB > 1) return true;

        try {
            $import = new UsoMedicamentoImport($this->usoMedicamentoService);
            $rows = Excel::toArray($import, $rutaArchivo);
            if (count($rows[0] ?? []) > 1000) return true;
        } catch (\Exception $e) {
            Log::warning('No se pudo determinar número de filas', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Descargar plantilla Excel para importación
     */
    public function downloadTemplate()
    {
        try {
            $headers = ['codigo_vaca', 'id_medicamento', 'fecha_aplicacion', 'dosis', 'via_administracion', 'observaciones', 'id_personal'];
            $ejemplo = ['VACA001', 1, date('Y-m-d'), '10', 'Intramuscular', 'Ejemplo', 1];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_uso_medicamentos.xlsx'
            );
            return $export;
        } catch (\Exception $e) {
            Log::error('Error al generar plantilla', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    /**
     * Exportar a Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $filters = $request->only(['search', 'id_vaca', 'id_medicamento', 'fecha_inicio', 'fecha_fin']);
            return Excel::download(new UsoMedicamentoExport($filters), 'usos_medicamentos.xlsx');
        } catch (\Exception $e) {
            Log::error('Error al exportar usos de medicamentos a Excel: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a Excel: ' . $e->getMessage());
        }
    }

    /**
     * Exportar a PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $filters = $request->only(['search', 'id_vaca', 'id_medicamento', 'fecha_inicio', 'fecha_fin']);
            $usos = $this->usoMedicamentoService->getFiltered($filters);
            $pdf = PDF::loadView('admin.uso_medicamentos.pdf', compact('usos', 'filters'));
            return $pdf->download('usos_medicamentos.pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar usos de medicamentos a PDF: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a PDF: ' . $e->getMessage());
        }
    }
}
