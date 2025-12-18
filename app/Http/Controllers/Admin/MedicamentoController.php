<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicamentoStoreRequest;
use App\Http\Requests\MedicamentoUpdateRequest;
use App\Services\MedicamentoService;
use App\Imports\MedicamentoImport;
use App\Exports\MedicamentoExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Models\Medicamento;

class MedicamentoController extends Controller
{
    protected MedicamentoService $medicamentoService;

    public function __construct(MedicamentoService $medicamentoService)
    {
        $this->medicamentoService = $medicamentoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Medicamento::class);

        $filters = [
            'search' => $request->get('search'),
            'tipo' => $request->get('tipo'),
            'activo' => $request->get('activo', true),
        ];

        $medicamentos = $this->medicamentoService->getPaginated($filters, 15);

        // Datos para gráficas
        $datosGraficas = $this->medicamentoService->getDatosGraficas();

        return view('admin.medicamentos.index', compact('medicamentos', 'datosGraficas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Medicamento::class);
        return view('admin.medicamentos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MedicamentoStoreRequest $request)
    {
        Gate::authorize('create', Medicamento::class);
        try {
            $medicamento = $this->medicamentoService->create($request->validated());
            
            return redirect()->route('admin.medicamentos.index')
                ->with('success', 'Medicamento registrado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el medicamento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $medicamento = $this->medicamentoService->findById($id);
        
        if (!$medicamento) {
            return redirect()->route('admin.medicamentos.index')
                ->with('error', 'Medicamento no encontrado.');
        }

        Gate::authorize('view', $medicamento);
        return view('admin.medicamentos.show', compact('medicamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $medicamento = $this->medicamentoService->findById($id);
        
        if (!$medicamento) {
            return redirect()->route('admin.medicamentos.index')
                ->with('error', 'Medicamento no encontrado.');
        }

        Gate::authorize('update', $medicamento);
        return view('admin.medicamentos.edit', compact('medicamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MedicamentoUpdateRequest $request, string $id)
    {
        try {
            $medicamento = $this->medicamentoService->findById($id);
            
            if (!$medicamento) {
                return redirect()->route('admin.medicamentos.index')
                    ->with('error', 'Medicamento no encontrado.');
            }

            Gate::authorize('update', $medicamento);
            $medicamento = $this->medicamentoService->update($medicamento, $request->validated());
            
            return redirect()->route('admin.medicamentos.show', $medicamento->id_medicamento)
                ->with('success', 'Medicamento actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el medicamento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $medicamento = $this->medicamentoService->findById($id);
            
            if (!$medicamento) {
                return redirect()->route('admin.medicamentos.index')
                    ->with('error', 'Medicamento no encontrado.');
            }

            Gate::authorize('delete', $medicamento);
            $this->medicamentoService->delete($medicamento);
            
            return redirect()->route('admin.medicamentos.index')
                ->with('success', 'Medicamento eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el medicamento: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        Gate::authorize('create', Medicamento::class);
        return view('admin.medicamentos.import');
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
            $nombreArchivo = 'import_medicamento_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');

            $import = new MedicamentoImport($this->medicamentoService);
            $rows = Excel::toArray($import, Storage::path($rutaTemporal));

            $preview = array_slice($rows[0], 0, 10);
            $totalRows = count($rows[0]);

            return view('admin.medicamentos.import-preview', compact('preview', 'totalRows', 'rutaTemporal'));
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
                    MedicamentoImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Medicamentos'
                );

                return redirect()->route('admin.medicamentos.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new MedicamentoImport($this->medicamentoService);
                Excel::import($import, $rutaArchivo);

                Storage::delete($rutaTemporal);

                $errors = $import->getErrors();
                if (!empty($errors)) {
                    return redirect()->route('admin.medicamentos.index')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('import_errors', $errors);
                }

                return redirect()->route('admin.medicamentos.index')
                    ->with('success', 'Importación de medicamentos completada exitosamente.');
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
            $import = new MedicamentoImport($this->medicamentoService);
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
            $headers = ['nombre', 'tipo', 'principio_activo', 'concentracion', 'unidad_medida', 'presentacion', 'fecha_vencimiento', 'stock_inicial', 'stock_minimo', 'activo'];
            $ejemplo = ['Medicamento Ejemplo', 'Antibiótico', 'Principio Activo', '10', 'ml', 'Ampolla', date('Y-m-d', strtotime('+1 year')), '100', '20', '1'];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_medicamentos.xlsx'
            );
            return $export;
        } catch (\Exception $e) {
            Log::error('Error al generar plantilla', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    /**
     * Exportar a Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        try {
            $filters = $request->only(['search', 'tipo', 'activo']);
            return Excel::download(new MedicamentoExport($filters), 'medicamentos.xlsx');
        } catch (\Exception $e) {
            Log::error('Error al exportar medicamentos a Excel: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a Excel: ' . $e->getMessage());
        }
    }

    /**
     * Exportar a PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        try {
            $filters = $request->only(['search', 'tipo', 'activo']);
            $medicamentos = $this->medicamentoService->getFiltered($filters);
            $pdf = PDF::loadView('admin.medicamentos.pdf', compact('medicamentos', 'filters'));
            return $pdf->download('medicamentos.pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar medicamentos a PDF: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a PDF: ' . $e->getMessage());
        }
    }
}
