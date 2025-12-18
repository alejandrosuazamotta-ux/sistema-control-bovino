<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MortalidadStoreRequest;
use App\Http\Requests\MortalidadUpdateRequest;
use App\Models\Vaca;
use App\Models\Cria;
use App\Services\MortalidadService;
use App\Imports\MortalidadImport;
use App\Exports\MortalidadExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Models\Mortalidad;

class MortalidadController extends Controller
{
    protected MortalidadService $mortalidadService;

    public function __construct(MortalidadService $mortalidadService)
    {
        $this->mortalidadService = $mortalidadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Mortalidad::class);

        $filters = [
            'search' => $request->get('search'),
            'animal_type' => $request->get('animal_type'),
            'clasificacion' => $request->get('clasificacion'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $mortalidades = $this->mortalidadService->getPaginated($filters, 15);
        $estadisticas = $this->mortalidadService->getEstadisticas();
        $datosGraficas = $this->mortalidadService->getDatosGraficas();
        
        return view('admin.mortalidad.index', compact('mortalidades', 'estadisticas', 'datosGraficas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Mortalidad::class);

        // Obtener vacas que no tienen registro de mortalidad (optimizado)
        $vacas = Vaca::select('id_vaca', 'codigo')
            ->whereDoesntHave('mortalidad')
            ->get();
        
        // Obtener crías que no tienen registro de mortalidad (optimizado)
        $crias = Cria::select('id_cria', 'nombre_cria', 'sinigan')
            ->whereDoesntHave('mortalidad')
            ->get();
        
        return view('admin.mortalidad.create', compact('vacas', 'crias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MortalidadStoreRequest $request)
    {
        Gate::authorize('create', Mortalidad::class);

        try {
            $mortalidad = $this->mortalidadService->create($request->validated());
            
            return redirect()
                ->route('admin.mortalidad.index')
                ->with('success', 'Registro de mortalidad creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el registro: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mortalidad = $this->mortalidadService->findWithRelations($id);
        
        if (!$mortalidad) {
            return redirect()
                ->route('admin.mortalidad.index')
                ->with('error', 'Registro de mortalidad no encontrado.');
        }
        
        Gate::authorize('view', $mortalidad);
        
        return view('admin.mortalidad.show', compact('mortalidad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mortalidad = $this->mortalidadService->findWithRelations($id);
        
        if (!$mortalidad) {
            return redirect()
                ->route('admin.mortalidad.index')
                ->with('error', 'Registro de mortalidad no encontrado.');
        }
        
        Gate::authorize('update', $mortalidad);
        
        return view('admin.mortalidad.edit', compact('mortalidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MortalidadUpdateRequest $request, string $id)
    {
        try {
            $mortalidad = $this->mortalidadService->findById($id);
            
            if (!$mortalidad) {
                return redirect()
                    ->route('admin.mortalidad.index')
                    ->with('error', 'Registro de mortalidad no encontrado.');
            }
            
            Gate::authorize('update', $mortalidad);
            
            $this->mortalidadService->update($mortalidad, $request->validated());
            
            return redirect()
                ->route('admin.mortalidad.index')
                ->with('success', 'Registro de mortalidad actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $mortalidad = $this->mortalidadService->findById($id);
            
            if (!$mortalidad) {
                return redirect()
                    ->route('admin.mortalidad.index')
                    ->with('error', 'Registro de mortalidad no encontrado.');
            }
            
            Gate::authorize('delete', $mortalidad);
            
            $this->mortalidadService->delete($mortalidad);
            
            return redirect()
                ->route('admin.mortalidad.index')
                ->with('success', 'Registro de mortalidad eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     */
    public function importForm()
    {
        return view('admin.mortalidad.import');
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
            
            // Guardar archivo temporalmente
            $nombreArchivo = 'import_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');
            
            $import = new MortalidadImport($this->mortalidadService);
            $rows = Excel::toArray($import, $archivo);
            
            $preview = array_slice($rows[0], 0, 10);
            
            return view('admin.mortalidad.import-preview', [
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
                return redirect()->route('admin.mortalidad.import')
                    ->with('error', 'El archivo temporal no existe. Por favor, vuelva a subir el archivo.');
            }

            $usarAsync = $this->debeUsarProcesamientoAsync($rutaArchivo, $request->boolean('procesar_async'));

            if ($usarAsync) {
                ProcessExcelImportJob::dispatch(
                    MortalidadImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Mortalidad'
                );

                return redirect()->route('admin.mortalidad.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new MortalidadImport($this->mortalidadService);
                Excel::import($import, $rutaArchivo);
                
                @unlink($rutaArchivo);
                
                $errors = $import->getErrors();
                $failures = $import->failures();
                
                if (count($errors) > 0 || count($failures) > 0) {
                    return redirect()->route('admin.mortalidad.import')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('errors', $errors)
                        ->with('failures', $failures);
                }
                
                return redirect()->route('admin.mortalidad.index')
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
            $import = new MortalidadImport($this->mortalidadService);
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
            $headers = ['animal_type', 'animal_id', 'fecha_muerte', 'clasificacion', 'causa', 'observaciones'];
            $ejemplo = ['Vaca', 1, date('Y-m-d'), 'Natural', 'Enfermedad', 'Ejemplo'];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_mortalidad.xlsx'
            );
            return $export;
        } catch (\Exception $e) {
            Log::error('Error al generar plantilla', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    /**
     * Exportar mortalidad a Excel
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'animal_type' => $request->get('animal_type'),
            'clasificacion' => $request->get('clasificacion'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        
        $nombreArchivo = 'mortalidad_' . $fechaInicio . '_' . $fechaFin . '.xlsx';

        return Excel::download(new MortalidadExport($filters), $nombreArchivo);
    }

    /**
     * Exportar mortalidad a PDF
     */
    public function exportPdf(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'animal_type' => $request->get('animal_type'),
            'clasificacion' => $request->get('clasificacion'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $registros = $this->mortalidadService->getPaginated($filters, 1000);
        $estadisticas = $this->mortalidadService->getEstadisticas();

        $pdf = PDF::loadView('admin.mortalidad.pdf', [
            'registros' => $registros,
            'estadisticas' => $estadisticas,
            'filters' => $filters
        ]);

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        $nombreArchivo = 'mortalidad_' . $fechaInicio . '_' . $fechaFin . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}

