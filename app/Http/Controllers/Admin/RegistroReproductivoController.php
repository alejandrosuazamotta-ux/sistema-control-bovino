<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistroReproductivoStoreRequest;
use App\Http\Requests\RegistroReproductivoUpdateRequest;
use App\Models\Vaca;
use App\Models\Personal;
use App\Models\RegistroReproductivo;
use App\Services\RegistroReproductivoService;
use App\Imports\RegistroReproductivoImport;
use App\Exports\RegistroReproductivoExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class RegistroReproductivoController extends Controller
{
    protected RegistroReproductivoService $registroReproductivoService;

    public function __construct(RegistroReproductivoService $registroReproductivoService)
    {
        $this->registroReproductivoService = $registroReproductivoService;
    }

    /**
     * Display a listing of the resource.
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver el listado
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', RegistroReproductivo::class);

        $filters = [
            'search' => $request->get('search'),
            'tipo_evento' => $request->get('tipo_evento'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
            'resultado_palpacion' => $request->get('resultado_palpacion'),
        ];

        $registros = $this->registroReproductivoService->getPaginated($filters, 15);

        // Obtener estadísticas
        $estadisticas = $this->registroReproductivoService->getEstadisticas();

        // Obtener datos para gráficas
        $datosGraficas = $this->registroReproductivoService->getDatosGraficas();

        // Obtener alertas
        $vacasProximasParto = $this->registroReproductivoService->getVacasProximasAlParto(21);
        $vacasNecesitanCelo = $this->registroReproductivoService->getVacasNecesitanCelo(21);

        // Datos para los filtros
        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();

        return view('admin.registros_reproductivos.index', compact(
            'registros',
            'estadisticas',
            'datosGraficas',
            'vacasProximasParto',
            'vacasNecesitanCelo',
            'vacas',
            'personal'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * Autorización: Solo Admin y Supervisor pueden crear registros
     */
    public function create()
    {
        Gate::authorize('create', RegistroReproductivo::class);

        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();

        return view('admin.registros_reproductivos.create', compact('vacas', 'personal'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor pueden crear registros
     */
    public function store(RegistroReproductivoStoreRequest $request)
    {
        Gate::authorize('create', RegistroReproductivo::class);

        try {
            $registro = $this->registroReproductivoService->create($request->validated());

            $mensaje = 'Registro reproductivo creado exitosamente.';
            
            // Si se calculó fecha probable parto, informar
            if ($registro->fecha_probable_parto) {
                $mensaje .= ' Fecha probable de parto: ' . $registro->fecha_probable_parto->format('d/m/Y') . '.';
            }

            return redirect()->route('admin.registros-reproductivos.index')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver detalles
     */
    public function show(string $id)
    {
        $registro = $this->registroReproductivoService->findWithRelations($id);

        if (!$registro) {
            return redirect()->route('admin.registros-reproductivos.index')
                ->with('error', 'Registro reproductivo no encontrado.');
        }

        Gate::authorize('view', $registro);

        // Obtener historial reproductivo de la vaca
        $historial = $this->registroReproductivoService->getPaginated(['id_vaca' => $registro->id_vaca], 10);

        return view('admin.registros_reproductivos.show', compact('registro', 'historial'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * Autorización: Solo Admin y Supervisor pueden editar registros
     */
    public function edit(string $id)
    {
        $registro = $this->registroReproductivoService->findById($id);

        if (!$registro) {
            return redirect()->route('admin.registros-reproductivos.index')
                ->with('error', 'Registro reproductivo no encontrado.');
        }

        Gate::authorize('update', $registro);

        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();

        return view('admin.registros_reproductivos.edit', compact('registro', 'vacas', 'personal'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor pueden actualizar registros
     */
    public function update(RegistroReproductivoUpdateRequest $request, string $id)
    {
        $registro = $this->registroReproductivoService->findById($id);

        if (!$registro) {
            return redirect()->route('admin.registros-reproductivos.index')
                ->with('error', 'Registro reproductivo no encontrado.');
        }

        Gate::authorize('update', $registro);

        try {
            $registro = $this->registroReproductivoService->update($registro, $request->validated());

            return redirect()->route('admin.registros-reproductivos.show', $registro->id_registro)
                ->with('success', 'Registro reproductivo actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Autorización: Solo Admin puede eliminar registros
     */
    public function destroy(string $id)
    {
        $registro = $this->registroReproductivoService->findById($id);

        if (!$registro) {
            return redirect()->route('admin.registros-reproductivos.index')
                ->with('error', 'Registro reproductivo no encontrado.');
        }

        Gate::authorize('delete', $registro);

        try {
            $this->registroReproductivoService->delete($registro);

            return redirect()->route('admin.registros-reproductivos.index')
                ->with('success', 'Registro reproductivo eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     * 
     * Autorización: Solo Admin y Supervisor pueden importar
     */
    public function importForm()
    {
        Gate::authorize('create', RegistroReproductivo::class);

        return view('admin.registros_reproductivos.import');
    }

    /**
     * Previsualizar archivo Excel antes de importar
     * 
     * Autorización: Solo Admin y Supervisor pueden importar
     */
    public function previewImport(Request $request)
    {
        Gate::authorize('create', RegistroReproductivo::class);

        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $archivo = $request->file('archivo');
            
            // Guardar archivo temporalmente
            $nombreArchivo = 'import_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');
            
            $import = new RegistroReproductivoImport($this->registroReproductivoService);
            $rows = Excel::toArray($import, $archivo);
            
            $preview = array_slice($rows[0], 0, 10);
            
            return view('admin.registros_reproductivos.import-preview', [
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
     * 
     * Autorización: Solo Admin y Supervisor pueden importar
     */
    public function processImport(Request $request)
    {
        Gate::authorize('create', RegistroReproductivo::class);

        $request->validate([
            'archivo_temp' => 'required|string',
            'procesar_async' => 'nullable|boolean'
        ]);

        try {
            $rutaTemporal = $request->input('archivo_temp');
            $rutaArchivo = storage_path('app/' . $rutaTemporal);
            
            if (!file_exists($rutaArchivo)) {
                return redirect()->route('admin.registros-reproductivos.import')
                    ->with('error', 'El archivo temporal no existe. Por favor, vuelva a subir el archivo.');
            }

            $usarAsync = $this->debeUsarProcesamientoAsync($rutaArchivo, $request->boolean('procesar_async'));

            if ($usarAsync) {
                ProcessExcelImportJob::dispatch(
                    RegistroReproductivoImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Registros Reproductivos'
                );

                return redirect()->route('admin.registros-reproductivos.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new RegistroReproductivoImport($this->registroReproductivoService);
                Excel::import($import, $rutaArchivo);
                
                @unlink($rutaArchivo);
                
                $errors = $import->getErrors();
                $failures = $import->failures();
                
                if (count($errors) > 0 || count($failures) > 0) {
                    return redirect()->route('admin.registros-reproductivos.import')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('errors', $errors)
                        ->with('failures', $failures);
                }
                
                return redirect()->route('admin.registros-reproductivos.index')
                    ->with('success', 'Archivo importado exitosamente.');
            }
        } catch (\Exception $e) {
            if (isset($rutaArchivo) && file_exists($rutaArchivo)) {
                @unlink($rutaArchivo);
            }
            
            return redirect()->back()
                ->with('error', 'Error al importar el archivo: ' . $e->getMessage());
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
            $import = new RegistroReproductivoImport($this->registroReproductivoService);
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
            $headers = ['codigo_vaca', 'tipo_evento', 'fecha', 'observaciones', 'id_personal'];
            $ejemplo = ['VACA001', 'Celo', date('Y-m-d'), 'Ejemplo', 1];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_registros_reproductivos.xlsx'
            );
            return $export;
        } catch (\Exception $e) {
            Log::error('Error al generar plantilla', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    /**
     * Exportar registros reproductivos a Excel
     * 
     * Autorización: Admin, Supervisor y Pasante pueden exportar (solo lectura)
     */
    public function exportExcel(Request $request)
    {
        Gate::authorize('viewAny', RegistroReproductivo::class);

        $filters = [
            'tipo_evento' => $request->get('tipo_evento'),
            'id_vaca' => $request->get('id_vaca'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        
        $nombreArchivo = 'registros_reproductivos_' . $fechaInicio . '_' . $fechaFin . '.xlsx';

        return Excel::download(new RegistroReproductivoExport($filters), $nombreArchivo);
    }

    /**
     * Exportar registros reproductivos a PDF
     * 
     * Autorización: Admin, Supervisor y Pasante pueden exportar (solo lectura)
     */
    public function exportPdf(Request $request)
    {
        Gate::authorize('viewAny', RegistroReproductivo::class);

        $filters = [
            'search' => $request->get('search'),
            'tipo_evento' => $request->get('tipo_evento'),
            'id_vaca' => $request->get('id_vaca'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $registros = $this->registroReproductivoService->getPaginated($filters, 1000);
        $estadisticas = $this->registroReproductivoService->getEstadisticas();

        $pdf = PDF::loadView('admin.registros_reproductivos.pdf', [
            'registros' => $registros,
            'estadisticas' => $estadisticas,
            'filters' => $filters
        ]);

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        $nombreArchivo = 'registros_reproductivos_' . $fechaInicio . '_' . $fechaFin . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
