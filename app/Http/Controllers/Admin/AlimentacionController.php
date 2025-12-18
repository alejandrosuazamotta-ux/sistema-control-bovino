<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlimentacionStoreRequest;
use App\Http\Requests\AlimentacionUpdateRequest;
use App\Models\Alimentacion;
use App\Models\Vaca;
use App\Models\Personal;
use App\Services\AlimentacionService;
use App\Imports\AlimentacionImport;
use App\Exports\AlimentacionExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AlimentacionController extends Controller
{
    protected AlimentacionService $alimentacionService;

    public function __construct(AlimentacionService $alimentacionService)
    {
        $this->alimentacionService = $alimentacionService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'tipo_alimento' => $request->get('tipo_alimento'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
            'id_personal' => $request->get('id_personal'),
        ];

        $registros = $this->alimentacionService->getPaginated($filters, 15);
        
        // Obtener estadísticas
        $estadisticas = $this->alimentacionService->getEstadisticas($filters);

        // Datos para los filtros (optimizado - solo campos necesarios)
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();
        $personal = Personal::select('id_personal', 'nombre')->orderBy('nombre')->get();

        // Datos para gráficas
        $datosGraficas = $this->alimentacionService->getDatosGraficas();

        return view('admin.alimentacion.index', compact('registros', 'vacas', 'personal', 'estadisticas', 'datosGraficas'));
    }

    public function create()
    {
        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();
        
        return view('admin.alimentacion.create', compact('vacas', 'personal'));
    }

    public function store(AlimentacionStoreRequest $request)
    {
        try {
            $this->alimentacionService->create($request->validated());
            
            return redirect()->route('admin.alimentacion.index')
                ->with('success', 'Registro de alimentación creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $registro = $this->alimentacionService->findWithRelations($id);
        
        if (!$registro) {
            abort(404);
        }
        
        return view('admin.alimentacion.show', compact('registro'));
    }

    public function edit(string $id)
    {
        $registro = $this->alimentacionService->findById($id);
        
        if (!$registro) {
            abort(404);
        }
        
        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();
        
        return view('admin.alimentacion.edit', compact('registro', 'vacas', 'personal'));
    }

    public function update(AlimentacionUpdateRequest $request, string $id)
    {
        try {
            $registro = $this->alimentacionService->findById($id);
            
            if (!$registro) {
                abort(404);
            }
            
            $this->alimentacionService->update($registro, $request->validated());
            
            return redirect()->route('admin.alimentacion.index')
                ->with('success', 'Registro de alimentación actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $registro = $this->alimentacionService->findById($id);
            
            if (!$registro) {
                abort(404);
            }
            
            $this->alimentacionService->delete($registro);
            
            return redirect()->route('admin.alimentacion.index')
                ->with('success', 'Registro de alimentación eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        return view('admin.alimentacion.import');
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
            $nombreArchivo = 'import_alimentacion_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');

            $import = new AlimentacionImport();
            $rows = Excel::toArray($import, Storage::path($rutaTemporal));

            $preview = array_slice($rows[0], 0, 10);
            $totalRows = count($rows[0]);

            return view('admin.alimentacion.import-preview', compact('preview', 'totalRows', 'rutaTemporal'));
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

            // Determinar si usar procesamiento asíncrono
            $usarAsync = $this->debeUsarProcesamientoAsync($rutaArchivo, $request->boolean('procesar_async'));

            if ($usarAsync) {
                // Procesar de forma asíncrona usando Job
                ProcessExcelImportJob::dispatch(
                    AlimentacionImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Alimentación'
                );

                return redirect()->route('admin.alimentacion.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                // Procesar de forma síncrona (inmediata)
                $import = new AlimentacionImport();
                Excel::import($import, $rutaArchivo);

                Storage::delete($rutaTemporal);

                $errors = $import->getErrors();
                if (!empty($errors)) {
                    return redirect()->route('admin.alimentacion.index')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('import_errors', $errors);
                }

                return redirect()->route('admin.alimentacion.index')
                    ->with('success', 'Importación de registros de alimentación completada exitosamente.');
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
            $import = new AlimentacionImport();
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
            $headers = ['id_vaca', 'fecha', 'tipo_alimento', 'cantidad', 'observaciones', 'id_personal'];
            $ejemplo = [1, date('Y-m-d'), 'Ensilaje', '25.5', 'Ejemplo', 1];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_alimentacion.xlsx'
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
            $filters = $request->only(['search', 'tipo_alimento', 'fecha_inicio', 'fecha_fin', 'id_vaca']);
            return Excel::download(new AlimentacionExport($filters), 'registros_alimentacion.xlsx');
        } catch (\Exception $e) {
            Log::error('Error al exportar registros de alimentación a Excel: ' . $e->getMessage(), ['exception' => $e]);
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
            $filters = $request->only(['search', 'tipo_alimento', 'fecha_inicio', 'fecha_fin', 'id_vaca']);
            $registros = $this->alimentacionService->getRegistrosParaExportacion($filters);
            $pdf = PDF::loadView('admin.alimentacion.pdf', compact('registros', 'filters'));
            return $pdf->download('registros_alimentacion.pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar registros de alimentación a PDF: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a PDF: ' . $e->getMessage());
        }
    }
}
