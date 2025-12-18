<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaludStoreRequest;
use App\Http\Requests\SaludUpdateRequest;
use App\Services\SaludService;
use App\Imports\SaludImport;
use App\Exports\SaludExport;
use App\Jobs\ProcessExcelImportJob;
use App\Models\Vaca;
use App\Models\Personal;
use App\Models\Salud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class SaludController extends Controller
{
    protected SaludService $saludService;

    public function __construct(SaludService $saludService)
    {
        $this->saludService = $saludService;
    }

    /**
     * Mostrar lista de registros de salud
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver el listado
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Salud::class);

        $filters = [
            'search' => $request->get('search'),
            'tipo_registro' => $request->get('tipo_registro'),
            'tipo_prueba' => $request->get('tipo_prueba'),
            'resultado' => $request->get('resultado'),
            'restriccion_ordeño' => $request->get('restriccion_ordeño'),
            'inhabilitada' => $request->get('inhabilitada'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
        ];

        $registros = $this->saludService->getPaginated($filters, 15);
        $estadisticas = $this->saludService->getEstadisticas();
        $datosGraficas = $this->saludService->getDatosGraficas();

        // Datos para los filtros (optimizado - solo campos necesarios)
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();
        $personal = Personal::select('id_personal', 'nombre')->orderBy('nombre')->get();

        return view('admin.salud.index', compact('registros', 'vacas', 'personal', 'estadisticas', 'datosGraficas', 'filters'));
    }

    /**
     * Mostrar formulario de creación
     * 
     * Autorización: Solo Admin y Supervisor pueden crear registros
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        Gate::authorize('create', Salud::class);

        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();
        
        return view('admin.salud.create', compact('vacas', 'personal'));
    }

    /**
     * Guardar nuevo registro
     * 
     * Autorización: Solo Admin y Supervisor pueden crear registros
     *
     * @param SaludStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaludStoreRequest $request)
    {
        Gate::authorize('create', Salud::class);

        try {
            $this->saludService->create($request->validated());
            
            return redirect()->route('admin.salud.index')
                ->with('success', 'Registro de salud creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear registro de salud: ' . $e->getMessage(), [
                'data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al crear el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar registro específico
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver detalles
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $registro = $this->saludService->findWithRelations($id);
        
        if (!$registro) {
            return redirect()->route('admin.salud.index')
                ->with('error', 'Registro de salud no encontrado.');
        }
        
        Gate::authorize('view', $registro);
        
        return view('admin.salud.show', compact('registro'));
    }

    /**
     * Mostrar formulario de edición
     * 
     * Autorización: Solo Admin y Supervisor pueden editar registros
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $registro = $this->saludService->findById($id);
        
        if (!$registro) {
            return redirect()->route('admin.salud.index')
                ->with('error', 'Registro de salud no encontrado.');
        }

        Gate::authorize('update', $registro);

        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();
        
        return view('admin.salud.edit', compact('registro', 'vacas', 'personal'));
    }

    /**
     * Actualizar registro
     * 
     * Autorización: Solo Admin y Supervisor pueden actualizar registros
     *
     * @param SaludUpdateRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaludUpdateRequest $request, string $id)
    {
        $registro = $this->saludService->findById($id);
        
        if (!$registro) {
            return redirect()->route('admin.salud.index')
                ->with('error', 'Registro de salud no encontrado.');
        }

        Gate::authorize('update', $registro);

        try {
            $this->saludService->update($registro, $request->validated());
            
            return redirect()->route('admin.salud.index')
                ->with('success', 'Registro de salud actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar registro de salud: ' . $e->getMessage(), [
                'salud_id' => $id,
                'data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al actualizar el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar registro
     * 
     * Autorización: Solo Admin puede eliminar registros
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $registro = $this->saludService->findById($id);
        
        if (!$registro) {
            return redirect()->route('admin.salud.index')
                ->with('error', 'Registro de salud no encontrado.');
        }

        Gate::authorize('delete', $registro);

        try {

            $this->saludService->delete($registro);
            
            return redirect()->route('admin.salud.index')
                ->with('success', 'Registro de salud eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar registro de salud: ' . $e->getMessage(), [
                'salud_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     * 
     * Autorización: Solo Admin y Supervisor pueden importar
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        Gate::authorize('create', Salud::class);

        return view('admin.salud.import');
    }

    /**
     * Previsualizar archivo Excel antes de importar
     * 
     * Autorización: Solo Admin y Supervisor pueden importar
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function previewImport(Request $request)
    {
        Gate::authorize('create', Salud::class);

        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $archivo = $request->file('archivo');
            
            // Guardar archivo temporalmente
            $nombreArchivo = 'import_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');
            
            $import = new SaludImport($this->saludService);
            $rows = Excel::toArray($import, $archivo);
            
            $preview = array_slice($rows[0], 0, 10);
            
            return view('admin.salud.import-preview', [
                'preview' => $preview,
                'totalRows' => count($rows[0]),
                'archivoTemp' => $rutaTemporal
            ]);
        } catch (\Exception $e) {
            Log::error('Error al previsualizar importación de salud: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al leer el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Procesar importación del archivo Excel
     * 
     * Autorización: Solo Admin y Supervisor pueden importar
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processImport(Request $request)
    {
        Gate::authorize('create', Salud::class);

        $request->validate([
            'archivo_temp' => 'required|string',
            'procesar_async' => 'nullable|boolean'
        ]);

        try {
            $rutaTemporal = $request->input('archivo_temp');
            $rutaArchivo = storage_path('app/' . $rutaTemporal);
            
            if (!file_exists($rutaArchivo)) {
                return redirect()->back()
                    ->with('error', 'El archivo temporal no existe.');
            }

            $usarAsync = $this->debeUsarProcesamientoAsync($rutaArchivo, $request->boolean('procesar_async'));

            if ($usarAsync) {
                ProcessExcelImportJob::dispatch(
                    SaludImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Salud'
                );

                return redirect()->route('admin.salud.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new SaludImport($this->saludService);
                Excel::import($import, $rutaArchivo);

                @unlink($rutaArchivo);

                $errores = $import->getErrors();
                $mensaje = 'Importación completada exitosamente.';
                
                if (count($errores) > 0) {
                    $mensaje .= ' Se encontraron ' . count($errores) . ' error(es).';
                    Log::warning('Errores en importación de salud', ['errores' => $errores]);
                }

                return redirect()->route('admin.salud.index')
                    ->with('success', $mensaje);
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar importación de salud: ' . $e->getMessage());
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
            $import = new SaludImport($this->saludService);
            $rows = Excel::toArray($import, $rutaArchivo);
            if (count($rows[0] ?? []) > 1000) return true;
        } catch (\Exception $e) {
            Log::warning('No se pudo determinar número de filas', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Descargar plantilla Excel para importación
     * 
     * Autorización: Solo Admin y Supervisor pueden descargar plantillas
     */
    public function downloadTemplate()
    {
        Gate::authorize('create', Salud::class);

        try {
            $headers = ['codigo_vaca', 'tipo_registro', 'tipo_prueba', 'resultado', 'fecha', 'observaciones', 'id_personal'];
            $ejemplo = ['VACA001', 'Prueba mastitis', 'Mastitis', 'Negativo', date('Y-m-d'), 'Ejemplo', 1];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_salud.xlsx'
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
        $filters = [
            'search' => $request->get('search'),
            'tipo_registro' => $request->get('tipo_registro'),
            'tipo_prueba' => $request->get('tipo_prueba'),
            'resultado' => $request->get('resultado'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
        ];

        return Excel::download(new SaludExport($filters), 'salud_' . date('Y-m-d_His') . '.xlsx');
    }

    /**
     * Exportar a PDF
     * 
     * Autorización: Admin, Supervisor y Pasante pueden exportar (solo lectura)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        Gate::authorize('viewAny', Salud::class);

        $filters = [
            'search' => $request->get('search'),
            'tipo_registro' => $request->get('tipo_registro'),
            'tipo_prueba' => $request->get('tipo_prueba'),
            'resultado' => $request->get('resultado'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
        ];

        $registros = $this->saludService->getPaginated($filters, 1000); // Sin límite para PDF
        $estadisticas = $this->saludService->getEstadisticas();

        $pdf = PDF::loadView('admin.salud.pdf', [
            'registros' => $registros,
            'estadisticas' => $estadisticas,
            'filters' => $filters
        ]);

        return $pdf->download('salud_' . date('Y-m-d_His') . '.pdf');
    }
}
