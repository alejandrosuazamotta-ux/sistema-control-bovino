<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProduccionLecheraStoreRequest;
use App\Http\Requests\ProduccionLecheraUpdateRequest;
use App\Models\Vaca;
use App\Models\Personal;
use App\Models\ProduccionLechera;
use App\Services\ProduccionLecheraService;
use App\Repositories\ProduccionLecheraRepository;
use App\Imports\ProduccionLecheraImport;
use App\Exports\ProduccionLecheraExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ProduccionLecheraController extends Controller
{
    protected ProduccionLecheraService $produccionLecheraService;
    protected ProduccionLecheraRepository $produccionRepository;

    public function __construct(
        ProduccionLecheraService $produccionLecheraService,
        ProduccionLecheraRepository $produccionRepository
    ) {
        $this->produccionLecheraService = $produccionLecheraService;
        $this->produccionRepository = $produccionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Autorización: Admin, Supervisor y Pasante pueden ver
        Gate::authorize('viewAny', ProduccionLechera::class);

        $filters = [
            'search' => $request->get('search'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
            'id_personal' => $request->get('id_personal'),
            'turno' => $request->get('turno'),
            'destino' => $request->get('destino'),
        ];

        $registros = $this->produccionLecheraService->getPaginated($filters, 15);
        
        // Obtener estadísticas
        $estadisticas = $this->produccionLecheraService->getEstadisticas();

        // Datos para gráficas
        $produccionDiaria = $this->produccionRepository->getProduccionDiaria(30);
        $produccionPorTurno = $this->produccionRepository->getProduccionPorTurno(30);
        $produccionPorDestino = $this->produccionRepository->getProduccionPorDestino(30);

        // Datos para los filtros (optimizado - solo campos necesarios)
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();
        $personal = Personal::select('id_personal', 'nombre')->orderBy('nombre')->get();

        // Determinar la vista según el rol
        $viewName = auth()->user()->hasRole('Pasante') 
            ? 'pasante.produccion_lechera.index' 
            : 'admin.produccion_lechera.index';

        return view($viewName, compact(
            'registros', 
            'estadisticas',
            'vacas',
            'produccionDiaria',
            'produccionPorTurno',
            'produccionPorDestino',
            'personal'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Autorización: Solo Admin y Supervisor pueden crear
        Gate::authorize('create', ProduccionLechera::class);

        // Optimizado - solo campos necesarios
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();
        $personal = Personal::select('id_personal', 'nombre')->orderBy('nombre')->get();
        
        return view('admin.produccion_lechera.create', compact('vacas', 'personal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProduccionLecheraStoreRequest $request)
    {
        // Autorización: Solo Admin y Supervisor pueden crear
        Gate::authorize('create', ProduccionLechera::class);

        try {
            $produccion = $this->produccionLecheraService->create($request->validated());
            
            $routeName = auth()->user()->hasRole('Pasante') 
                ? 'pasante.produccion-lechera.index' 
                : 'admin.produccion-lechera.index';
            
            return redirect()->route($routeName)
                ->with('success', 'Registro de producción lechera creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $registro = $this->produccionLecheraService->findWithRelations($id);
        
        if (!$registro) {
            $routeName = auth()->user()->hasRole('Pasante') 
                ? 'pasante.produccion-lechera.index' 
                : 'admin.produccion-lechera.index';
            
            return redirect()->route($routeName)
                ->with('error', 'Registro de producción no encontrado.');
        }
        
        // Autorización: Admin, Supervisor y Pasante pueden ver
        Gate::authorize('view', $registro);
        
        // Calcular estadísticas de la vaca
        $estadisticasVaca = [
            'total_produccion' => $this->produccionLecheraService->getPromedioVaca($registro->id_vaca) * 30, // Estimado mensual
            'promedio_mensual' => $this->produccionLecheraService->getPromedioVaca(
                $registro->id_vaca,
                $registro->fecha->month,
                $registro->fecha->year
            ),
            'pico_produccion' => $this->produccionLecheraService->getPicoProduccion($registro->id_vaca),
        ];
        
        // Determinar la vista según el rol
        $viewName = auth()->user()->hasRole('Pasante') 
            ? 'pasante.produccion_lechera.show' 
            : 'admin.produccion_lechera.show';
        
        return view($viewName, compact('registro', 'estadisticasVaca'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Autorización: Solo Admin y Supervisor pueden editar
        Gate::authorize('update', ProduccionLechera::class);

        $registro = $this->produccionLecheraService->findById($id);
        
        if (!$registro) {
            $routeName = auth()->user()->hasRole('Pasante') 
                ? 'pasante.produccion-lechera.index' 
                : 'admin.produccion-lechera.index';
            
            return redirect()->route($routeName)
                ->with('error', 'Registro de producción no encontrado.');
        }

        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();
        
        return view('admin.produccion_lechera.edit', compact('registro', 'vacas', 'personal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProduccionLecheraUpdateRequest $request, string $id)
    {
        // Autorización: Solo Admin y Supervisor pueden actualizar
        $registro = $this->produccionLecheraService->findById($id);
        
        if (!$registro) {
            $routeName = auth()->user()->hasRole('Pasante') 
                ? 'pasante.produccion-lechera.index' 
                : 'admin.produccion-lechera.index';
            
            return redirect()->route($routeName)
                ->with('error', 'Registro de producción no encontrado.');
        }

        Gate::authorize('update', $registro);

        try {
            $registro = $this->produccionLecheraService->update($registro, $request->validated());
            
            $routeName = auth()->user()->hasRole('Pasante') 
                ? 'pasante.produccion-lechera.show' 
                : 'admin.produccion-lechera.show';
            
            return redirect()->route($routeName, $registro->id_produccion)
                ->with('success', 'Registro de producción lechera actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Autorización: Solo Admin puede eliminar
        $registro = $this->produccionLecheraService->findById($id);
        
        if (!$registro) {
            $routeName = auth()->user()->hasRole('Pasante') 
                ? 'pasante.produccion-lechera.index' 
                : 'admin.produccion-lechera.index';
            
            return redirect()->route($routeName)
                ->with('error', 'Registro de producción no encontrado.');
        }

        Gate::authorize('delete', $registro);

        try {
            $this->produccionLecheraService->delete($registro);
            
            $routeName = auth()->user()->hasRole('Pasante') 
                ? 'pasante.produccion-lechera.index' 
                : 'admin.produccion-lechera.index';
            
            return redirect()->route($routeName)
                ->with('success', 'Registro de producción lechera eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     */
    public function importForm()
    {
        // Autorización: Solo Admin y Supervisor pueden importar
        Gate::authorize('create', ProduccionLechera::class);
        
        return view('admin.produccion_lechera.import');
    }

    /**
     * Previsualizar archivo Excel antes de importar
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $archivo = $request->file('archivo');
            
            // Guardar archivo temporalmente para usar en processImport
            $nombreArchivo = 'import_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');
            
            // Leer archivo para previsualización
            $import = new ProduccionLecheraImport($this->produccionLecheraService);
            $rows = Excel::toArray($import, $archivo);
            
            // Limitar a las primeras 10 filas para previsualización
            $preview = array_slice($rows[0], 0, 10);
            
            return view('admin.produccion_lechera.import-preview', [
                'preview' => $preview,
                'totalRows' => count($rows[0]),
                'archivoTemp' => $rutaTemporal
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al leer el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Procesar importación del archivo Excel
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'archivo_temp' => 'required|string',
            'procesar_async' => 'nullable|boolean'
        ]);

        try {
            $rutaTemporal = $request->input('archivo_temp');
            $rutaArchivo = storage_path('app/' . $rutaTemporal);
            
            if (!file_exists($rutaArchivo)) {
                return redirect()->route('admin.produccion-lechera.import')
                    ->with('error', 'El archivo temporal no existe. Por favor, vuelva a subir el archivo.');
            }

            // Determinar si usar procesamiento asíncrono
            $usarAsync = $this->debeUsarProcesamientoAsync($rutaArchivo, $request->boolean('procesar_async'));

            if ($usarAsync) {
                // Procesar de forma asíncrona usando Job
                ProcessExcelImportJob::dispatch(
                    ProduccionLecheraImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Producción Lechera'
                );

                return redirect()->route('admin.produccion-lechera.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                // Procesar de forma síncrona (inmediata)
                $import = new ProduccionLecheraImport($this->produccionLecheraService);
                
                Excel::import($import, $rutaArchivo);
                
                // Eliminar archivo temporal
                @unlink($rutaArchivo);
                
                $errors = $import->getErrors();
                $failures = $import->failures();
                
                if (count($errors) > 0 || count($failures) > 0) {
                    return redirect()->route('admin.produccion-lechera.import')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('errors', $errors)
                        ->with('failures', $failures);
                }
                
                return redirect()->route('admin.produccion-lechera.index')
                    ->with('success', 'Archivo importado exitosamente.');
            }
        } catch (\Exception $e) {
            // Eliminar archivo temporal en caso de error
            if (isset($rutaArchivo) && file_exists($rutaArchivo)) {
                @unlink($rutaArchivo);
            }
            
            return redirect()->back()
                ->with('error', 'Error al importar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Determinar si debe usar procesamiento asíncrono
     * 
     * @param string $rutaArchivo
     * @param bool|null $forzarAsync
     * @return bool
     */
    protected function debeUsarProcesamientoAsync(string $rutaArchivo, ?bool $forzarAsync = null): bool
    {
        // Si el usuario forzó async, usar async
        if ($forzarAsync === true) {
            return true;
        }

        // Si el usuario forzó sync, usar sync
        if ($forzarAsync === false) {
            return false;
        }

        // Decisión automática basada en tamaño y número de filas
        $tamañoArchivo = filesize($rutaArchivo); // bytes
        $tamañoMB = $tamañoArchivo / (1024 * 1024);

        // Si el archivo es mayor a 1MB, usar async
        if ($tamañoMB > 1) {
            return true;
        }

        // Intentar contar filas (aproximado)
        try {
            $import = new ProduccionLecheraImport($this->produccionLecheraService);
            $rows = Excel::toArray($import, $rutaArchivo);
            $numeroFilas = count($rows[0] ?? []);

            // Si tiene más de 1000 filas, usar async
            if ($numeroFilas > 1000) {
                return true;
            }
        } catch (\Exception $e) {
            // Si no se puede leer, usar sync por defecto
            Log::warning('No se pudo determinar número de filas para importación', [
                'error' => $e->getMessage()
            ]);
        }

        // Por defecto, usar sync para archivos pequeños
        return false;
    }

    /**
     * Exportar producción lechera a Excel
     */
    public function exportExcel(Request $request)
    {
        // Autorización: Solo Admin y Supervisor pueden exportar
        Gate::authorize('viewAny', ProduccionLechera::class);

        $filters = [
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
            'turno' => $request->get('turno'),
            'destino' => $request->get('destino'),
        ];

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        
        $nombreArchivo = 'produccion_lechera_' . $fechaInicio . '_' . $fechaFin . '.xlsx';

        return Excel::download(new ProduccionLecheraExport($filters), $nombreArchivo);
    }

    /**
     * Exportar producción lechera a PDF
     */
    public function exportPdf(Request $request)
    {
        // Autorización: Solo Admin y Supervisor pueden exportar
        Gate::authorize('viewAny', ProduccionLechera::class);

        $filters = [
            'search' => $request->get('search'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
            'id_personal' => $request->get('id_personal'),
            'turno' => $request->get('turno'),
            'destino' => $request->get('destino'),
        ];

        $registros = $this->produccionLecheraService->getPaginated($filters, 1000);
        $estadisticas = $this->produccionLecheraService->getEstadisticas();

        $pdf = PDF::loadView('admin.produccion_lechera.pdf', [
            'registros' => $registros,
            'estadisticas' => $estadisticas,
            'filters' => $filters
        ]);

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        $nombreArchivo = 'produccion_lechera_' . $fechaInicio . '_' . $fechaFin . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    /**
     * Descargar plantilla Excel para importación
     */
    public function downloadTemplate()
    {
        try {
            // Headers de la plantilla
            $headers = [
                'codigo_vaca',
                'fecha',
                'turno',
                'cantidad_leche',
                'destino',
                'valor_unidad',
                'encargado',
                'observaciones'
            ];

            // Fila de ejemplo
            $ejemplo = [
                'VACA001',
                date('Y-m-d'),
                'AM',
                '15.5',
                'Agroindustria',
                '2500',
                'Juan Pérez',
                'Ejemplo de observación'
            ];

            // Crear array con headers y ejemplo
            $data = [
                $headers,
                $ejemplo
            ];

            // Generar Excel usando Excel::download con array simple
            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    
                    public function __construct($data) {
                        $this->data = $data;
                    }
                    
                    public function array(): array {
                        return [$this->data[1]]; // Solo la fila de ejemplo
                    }
                    
                    public function headings(): array {
                        return $this->data[0] ?? [];
                    }
                },
                'plantilla_produccion_lechera.xlsx'
            );

            return $export;
        } catch (\Exception $e) {
            Log::error('Error al generar plantilla de importación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

}
