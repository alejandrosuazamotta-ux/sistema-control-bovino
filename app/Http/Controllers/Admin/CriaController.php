<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CriaStoreRequest;
use App\Http\Requests\CriaUpdateRequest;
use App\Models\Vaca;
use App\Models\Cria;
use App\Services\CriaService;
use App\Imports\CriaImport;
use App\Exports\CriaExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class CriaController extends Controller
{
    protected CriaService $criaService;

    public function __construct(CriaService $criaService)
    {
        $this->criaService = $criaService;
    }

    /**
     * Display a listing of the resource.
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver el listado
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Cria::class);

        $filters = [
            'search' => $request->get('search'),
            'sexo' => $request->get('sexo'),
            'concepcion' => $request->get('concepcion'),
            'estado_destete' => $request->get('estado_destete'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca_madre' => $request->get('id_vaca_madre'),
        ];

        $crias = $this->criaService->getPaginated($filters, 15);

        // Obtener estadísticas
        $estadisticas = $this->criaService->getEstadisticas();

        // Obtener datos para gráficas
        $datosGraficas = $this->criaService->getDatosGraficas();

        // Obtener alertas
        $criasProximasDestete = $this->criaService->getProximasAlDestete();

        // Datos para los filtros (optimizado - solo campos necesarios)
        $vacas = Vaca::select('id_vaca', 'codigo', 'estado_reproductivo')
            ->whereIn('estado_reproductivo', ['Preñada', 'Lactancia'])
            ->orderBy('codigo')
            ->get();

        return view('admin.crias.index', compact(
            'crias',
            'estadisticas',
            'datosGraficas',
            'criasProximasDestete',
            'vacas'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * Autorización: Solo Admin y Supervisor pueden crear crías
     */
    public function create()
    {
        Gate::authorize('create', Cria::class);

        $vacas = Vaca::whereIn('estado_reproductivo', ['Preñada', 'Lactancia'])
            ->orderBy('codigo')
            ->get();

        return view('admin.crias.create', compact('vacas'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor pueden crear crías
     */
    public function store(CriaStoreRequest $request)
    {
        Gate::authorize('create', Cria::class);

        try {
            $cria = $this->criaService->create($request->validated());

            return redirect()->route('admin.crias.index')
                ->with('success', 'Cría registrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar la cría: ' . $e->getMessage())
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
        $cria = $this->criaService->findWithRelations($id);

        if (!$cria) {
            return redirect()->route('admin.crias.index')
                ->with('error', 'Cría no encontrada.');
        }

        Gate::authorize('view', $cria);

        // Obtener hermanos (otras crías de la misma madre)
        $hermanos = $this->criaService->getPorVacaMadre($cria->id_vaca_madre)
            ->where('id_cria', '!=', $cria->id_cria);

        // Estadísticas de la cría
        $estadisticasCria = [
            'edad_dias' => $cria->edadDias,
            'edad_meses' => $cria->edadMeses,
            'hermanos' => $hermanos->count(),
            'proxima_destete' => $cria->estaProximaAlDestete(),
        ];

        return view('admin.crias.show', compact('cria', 'estadisticasCria', 'hermanos'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * Autorización: Solo Admin y Supervisor pueden editar crías
     */
    public function edit(string $id)
    {
        $cria = $this->criaService->findById($id);

        if (!$cria) {
            return redirect()->route('admin.crias.index')
                ->with('error', 'Cría no encontrada.');
        }

        Gate::authorize('update', $cria);

        $vacas = Vaca::whereIn('estado_reproductivo', ['Preñada', 'Lactancia'])
            ->orderBy('codigo')
            ->get();

        return view('admin.crias.edit', compact('cria', 'vacas'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor pueden actualizar crías
     */
    public function update(CriaUpdateRequest $request, string $id)
    {
        $cria = $this->criaService->findById($id);

        if (!$cria) {
            return redirect()->route('admin.crias.index')
                ->with('error', 'Cría no encontrada.');
        }

        Gate::authorize('update', $cria);

        try {
            $cria = $this->criaService->update($cria, $request->validated());

            return redirect()->route('admin.crias.show', $cria->id_cria)
                ->with('success', 'Cría actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la cría: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Autorización: Solo Admin puede eliminar crías
     */
    public function destroy(string $id)
    {
        $cria = $this->criaService->findById($id);

        if (!$cria) {
            return redirect()->route('admin.crias.index')
                ->with('error', 'Cría no encontrada.');
        }

        Gate::authorize('delete', $cria);

        try {
            $this->criaService->delete($cria);

            return redirect()->route('admin.crias.index')
                ->with('success', 'Cría eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la cría: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     * 
     * Autorización: Solo Admin y Supervisor pueden importar
     */
    public function importForm()
    {
        Gate::authorize('create', Cria::class);

        return view('admin.crias.import');
    }

    /**
     * Previsualizar archivo Excel antes de importar
     * 
     * Autorización: Solo Admin y Supervisor pueden importar
     */
    public function previewImport(Request $request)
    {
        Gate::authorize('create', Cria::class);

        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $archivo = $request->file('archivo');
            
            // Guardar archivo temporalmente
            $nombreArchivo = 'import_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');
            
            $import = new CriaImport($this->criaService);
            $rows = Excel::toArray($import, $archivo);
            
            $preview = array_slice($rows[0], 0, 10);
            
            return view('admin.crias.import-preview', [
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
        Gate::authorize('create', Cria::class);

        $request->validate([
            'archivo_temp' => 'required|string'
        ]);

        try {
            $rutaArchivo = storage_path('app/' . $request->input('archivo_temp'));
            
            if (!file_exists($rutaArchivo)) {
                return redirect()->route('admin.crias.import')
                    ->with('error', 'El archivo temporal no existe. Por favor, vuelva a subir el archivo.');
            }

            $import = new CriaImport($this->criaService);
            
            Excel::import($import, $rutaArchivo);
            
            // Eliminar archivo temporal
            @unlink($rutaArchivo);
            
            $errors = $import->getErrors();
            $failures = $import->failures();
            
            if (count($errors) > 0 || count($failures) > 0) {
                return redirect()->route('admin.crias.import')
                    ->with('warning', 'Importación completada con algunos errores.')
                    ->with('errors', $errors)
                    ->with('failures', $failures);
            }
            
            return redirect()->route('admin.crias.index')
                ->with('success', 'Archivo importado exitosamente.');
        } catch (\Exception $e) {
            if (isset($rutaArchivo) && file_exists($rutaArchivo)) {
                @unlink($rutaArchivo);
            }
            
            return redirect()->back()
                ->with('error', 'Error al importar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Exportar crías a Excel
     * 
     * Autorización: Admin, Supervisor y Pasante pueden exportar (solo lectura)
     */
    public function exportExcel(Request $request)
    {
        Gate::authorize('viewAny', Cria::class);

        $filters = [
            'sexo' => $request->get('sexo'),
            'estado_destete' => $request->get('estado_destete'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        
        $nombreArchivo = 'crias_' . $fechaInicio . '_' . $fechaFin . '.xlsx';

        return Excel::download(new CriaExport($filters), $nombreArchivo);
    }

    /**
     * Exportar crías a PDF
     * 
     * Autorización: Admin, Supervisor y Pasante pueden exportar (solo lectura)
     */
    public function exportPdf(Request $request)
    {
        Gate::authorize('viewAny', Cria::class);

        $filters = [
            'search' => $request->get('search'),
            'sexo' => $request->get('sexo'),
            'estado_destete' => $request->get('estado_destete'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $registros = $this->criaService->getPaginated($filters, 1000);
        $estadisticas = $this->criaService->getEstadisticas();

        $pdf = PDF::loadView('admin.crias.pdf', [
            'registros' => $registros,
            'estadisticas' => $estadisticas,
            'filters' => $filters
        ]);

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        $nombreArchivo = 'crias_' . $fechaInicio . '_' . $fechaFin . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
