<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PruebaSanitariaStoreRequest;
use App\Http\Requests\PruebaSanitariaUpdateRequest;
use App\Models\Vaca;
use App\Models\Personal;
use App\Models\PruebaSanitaria;
use App\Services\PruebaSanitariaService;
use App\Imports\PruebaSanitariaImport;
use App\Exports\PruebaSanitariaExport;
use App\Jobs\ProcessExcelImportJob;
use App\Traits\HandlesExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PruebaSanitariaController extends Controller
{
    use HandlesExcelImport;

    protected PruebaSanitariaService $pruebaSanitariaService;

    public function __construct(PruebaSanitariaService $pruebaSanitariaService)
    {
        $this->pruebaSanitariaService = $pruebaSanitariaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', PruebaSanitaria::class);

        $filters = [
            'search' => $request->get('search'),
            'tipo_prueba' => $request->get('tipo_prueba'),
            'resultado' => $request->get('resultado'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
            'cerrada' => $request->get('cerrada'),
        ];

        $pruebas = $this->pruebaSanitariaService->getPaginated($filters, 15);
        $estadisticas = $this->pruebaSanitariaService->getEstadisticas();
        $datosGraficas = $this->pruebaSanitariaService->getDatosGraficas();

        // Datos para los filtros
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();

        return view('admin.pruebas_sanitarias.index', compact(
            'pruebas',
            'estadisticas',
            'datosGraficas',
            'vacas'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', PruebaSanitaria::class);

        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();

        return view('admin.pruebas_sanitarias.create', compact('vacas', 'personal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PruebaSanitariaStoreRequest $request)
    {
        Gate::authorize('create', PruebaSanitaria::class);

        try {
            $prueba = $this->pruebaSanitariaService->create($request->validated());

            return redirect()->route('admin.pruebas-sanitarias.index')
                ->with('success', 'Prueba sanitaria registrada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear prueba sanitaria: ' . $e->getMessage(), [
                'data' => $request->except(['evidencia_archivo']),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al crear la prueba: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prueba = $this->pruebaSanitariaService->findWithRelations($id);

        if (!$prueba) {
            return redirect()->route('admin.pruebas-sanitarias.index')
                ->with('error', 'Prueba sanitaria no encontrada.');
        }

        Gate::authorize('view', $prueba);

        return view('admin.pruebas_sanitarias.show', compact('prueba'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $prueba = $this->pruebaSanitariaService->findById($id);

        if (!$prueba) {
            return redirect()->route('admin.pruebas-sanitarias.index')
                ->with('error', 'Prueba sanitaria no encontrada.');
        }

        Gate::authorize('update', $prueba);

        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();

        return view('admin.pruebas_sanitarias.edit', compact('prueba', 'vacas', 'personal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PruebaSanitariaUpdateRequest $request, string $id)
    {
        $prueba = $this->pruebaSanitariaService->findById($id);

        if (!$prueba) {
            return redirect()->route('admin.pruebas-sanitarias.index')
                ->with('error', 'Prueba sanitaria no encontrada.');
        }

        Gate::authorize('update', $prueba);

        try {
            $prueba = $this->pruebaSanitariaService->update($prueba, $request->validated());

            return redirect()->route('admin.pruebas-sanitarias.show', $prueba->id_prueba)
                ->with('success', 'Prueba sanitaria actualizada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar prueba sanitaria: ' . $e->getMessage(), [
                'prueba_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al actualizar la prueba: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prueba = $this->pruebaSanitariaService->findById($id);

        if (!$prueba) {
            return redirect()->route('admin.pruebas-sanitarias.index')
                ->with('error', 'Prueba sanitaria no encontrada.');
        }

        Gate::authorize('delete', $prueba);

        try {
            $this->pruebaSanitariaService->delete($prueba);

            return redirect()->route('admin.pruebas-sanitarias.index')
                ->with('success', 'Prueba sanitaria eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar prueba sanitaria: ' . $e->getMessage(), [
                'prueba_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar la prueba: ' . $e->getMessage());
        }
    }

    /**
     * Cerrar una prueba sanitaria
     */
    public function cerrar(string $id)
    {
        $prueba = $this->pruebaSanitariaService->findById($id);

        if (!$prueba) {
            return redirect()->route('admin.pruebas-sanitarias.index')
                ->with('error', 'Prueba sanitaria no encontrada.');
        }

        Gate::authorize('cerrar', $prueba);

        try {
            $this->pruebaSanitariaService->cerrar($prueba);

            return redirect()->route('admin.pruebas-sanitarias.show', $prueba->id_prueba)
                ->with('success', 'Prueba sanitaria cerrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cerrar la prueba: ' . $e->getMessage());
        }
    }

    /**
     * Descargar evidencia de prueba sanitaria
     */
    public function descargarEvidencia(string $id)
    {
        $prueba = $this->pruebaSanitariaService->findById($id);

        if (!$prueba || !$prueba->evidencia_archivo) {
            return redirect()->back()
                ->with('error', 'No se encontró evidencia para esta prueba.');
        }

        Gate::authorize('view', $prueba);

        $rutaArchivo = 'public/' . $prueba->evidencia_archivo;

        if (!Storage::exists($rutaArchivo)) {
            return redirect()->back()
                ->with('error', 'El archivo de evidencia no existe.');
        }

        return Storage::download($rutaArchivo);
    }

    /**
     * Mostrar formulario de importación
     */
    public function importForm()
    {
        Gate::authorize('create', PruebaSanitaria::class);

        return view('admin.pruebas_sanitarias.import');
    }

    /**
     * Previsualizar archivo Excel antes de importar
     */
    public function previewImport(Request $request)
    {
        return $this->previewExcelImport($request, PruebaSanitariaImport::class);
    }

    /**
     * Procesar importación de Excel
     */
    public function processImport(Request $request)
    {
        Gate::authorize('create', PruebaSanitaria::class);

        return $this->processExcelImport(
            $request,
            PruebaSanitariaImport::class,
            'admin.pruebas-sanitarias.index',
            'Pruebas Sanitarias'
        );
    }

    /**
     * Descargar plantilla Excel
     */
    public function downloadTemplate()
    {
        Gate::authorize('create', PruebaSanitaria::class);

        return $this->downloadExcelTemplate([
            'codigo_vaca',
            'tipo_prueba',
            'fecha_prueba',
            'resultado',
            'fecha_resultado',
            'severidad',
            'acta',
            'responsable_prueba',
            'observaciones'
        ], 'pruebas_sanitarias');
    }

    /**
     * Exportar a Excel
     */
    public function exportExcel(Request $request)
    {
        Gate::authorize('viewAny', PruebaSanitaria::class);

        $filters = [
            'tipo_prueba' => $request->get('tipo_prueba'),
            'resultado' => $request->get('resultado'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        
        $nombreArchivo = 'pruebas_sanitarias_' . $fechaInicio . '_' . $fechaFin . '.xlsx';

        return Excel::download(new PruebaSanitariaExport($filters), $nombreArchivo);
    }

    /**
     * Exportar a PDF
     */
    public function exportPdf(Request $request)
    {
        Gate::authorize('viewAny', PruebaSanitaria::class);

        $filters = [
            'tipo_prueba' => $request->get('tipo_prueba'),
            'resultado' => $request->get('resultado'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $pruebas = $this->pruebaSanitariaService->getPaginated($filters, 1000);
        $estadisticas = $this->pruebaSanitariaService->getEstadisticas();

        $pdf = PDF::loadView('admin.pruebas_sanitarias.pdf', [
            'pruebas' => $pruebas,
            'estadisticas' => $estadisticas,
            'filters' => $filters
        ]);

        $fechaInicio = $filters['fecha_inicio'] ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');
        $nombreArchivo = 'pruebas_sanitarias_' . $fechaInicio . '_' . $fechaFin . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}

