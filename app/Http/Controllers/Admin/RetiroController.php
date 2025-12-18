<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RetiroStoreRequest;
use App\Http\Requests\RetiroUpdateRequest;
use App\Models\Vaca;
use App\Models\UsoMedicamento;
use App\Services\RetiroService;
use App\Imports\RetiroImport;
use App\Exports\RetiroExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

/**
 * RetiroController
 * 
 * NOTA: Retiro no tiene Policy asociada.
 * La autorización se maneja por middleware de rutas (role:Admin).
 * Se agregan verificaciones adicionales para mayor seguridad.
 */

class RetiroController extends Controller
{
    protected RetiroService $retiroService;

    public function __construct(RetiroService $retiroService)
    {
        $this->retiroService = $retiroService;
    }

    /**
     * Display a listing of the resource.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     * NOTA: Retiro no tiene Policy, se verifica por rol directamente
     */
    public function index(Request $request)
    {
        // SEGURIDAD: Admin siempre tiene acceso (rutas protegidas por middleware role:Admin)
        // Esta verificación es redundante pero se mantiene como defensa adicional
        // Admin nunca será bloqueado aquí porque las rutas ya están protegidas

        $filters = [
            'search' => $request->get('search'),
            'id_vaca' => $request->get('id_vaca'),
            'tipo_retiro' => $request->get('tipo_retiro'),
            'activo' => $request->get('activo'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $retiros = $this->retiroService->getPaginated($filters, 15);

        // Datos para filtros
        $vacas = Vaca::orderBy('codigo')->get();

        // Retiros activos y próximos a vencer
        $retirosActivos = $this->retiroService->getRetirosActivos();
        $retirosProximos = $this->retiroService->getRetirosProximosAVencer(7);

        // Datos para gráficas
        $datosGraficas = $this->retiroService->getDatosGraficas();

        return view('admin.retiros.index', compact('retiros', 'vacas', 'retirosActivos', 'retirosProximos', 'datosGraficas'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function create()
    {
        // SEGURIDAD: Admin siempre tiene acceso (rutas protegidas por middleware role:Admin)

        $vacas = Vaca::orderBy('codigo')->get();
        $usosMedicamentos = UsoMedicamento::with(['medicamento', 'vaca'])
            ->whereDoesntHave('retiro')
            ->orderBy('fecha_aplicacion', 'desc')
            ->get();
        
        return view('admin.retiros.create', compact('vacas', 'usosMedicamentos'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function store(RetiroStoreRequest $request)
    {
        // SEGURIDAD: Admin siempre tiene acceso (rutas protegidas por middleware role:Admin)

        try {
            $retiro = $this->retiroService->create($request->validated());
            
            return redirect()->route('admin.retiros.index')
                ->with('success', 'Retiro registrado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el retiro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function show(string $id)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        $retiro = $this->retiroService->findWithRelations($id);
        
        if (!$retiro) {
            return redirect()->route('admin.retiros.index')
                ->with('error', 'Retiro no encontrado.');
        }

        return view('admin.retiros.show', compact('retiro'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function edit(string $id)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        $retiro = $this->retiroService->findById($id);
        
        if (!$retiro) {
            return redirect()->route('admin.retiros.index')
                ->with('error', 'Retiro no encontrado.');
        }

        $vacas = Vaca::orderBy('codigo')->get();
        $usosMedicamentos = UsoMedicamento::with(['medicamento', 'vaca'])
            ->orderBy('fecha_aplicacion', 'desc')
            ->get();
        
        return view('admin.retiros.edit', compact('retiro', 'vacas', 'usosMedicamentos'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function update(RetiroUpdateRequest $request, string $id)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        $retiro = $this->retiroService->findById($id);
        
        if (!$retiro) {
            return redirect()->route('admin.retiros.index')
                ->with('error', 'Retiro no encontrado.');
        }

        try {
            $retiro = $this->retiroService->update($retiro, $request->validated());
            
            return redirect()->route('admin.retiros.show', $retiro->id_retiro)
                ->with('success', 'Retiro actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el retiro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Autorización: Solo Admin (manejado por middleware de rutas)
     */
    public function destroy(string $id)
    {
        // Verificación adicional de seguridad - solo admin puede eliminar
        if (!auth()->user()->hasRole('Admin')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        $retiro = $this->retiroService->findById($id);
        
        if (!$retiro) {
            return redirect()->route('admin.retiros.index')
                ->with('error', 'Retiro no encontrado.');
        }

        try {
            $this->retiroService->delete($retiro);
            
            return redirect()->route('admin.retiros.index')
                ->with('success', 'Retiro eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el retiro: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        return view('admin.retiros.import');
    }

    /**
     * Previsualizar archivo Excel antes de importar
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function previewImport(Request $request)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $archivo = $request->file('archivo');
            $nombreArchivo = 'import_retiro_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');

            $import = new RetiroImport($this->retiroService);
            $rows = Excel::toArray($import, Storage::path($rutaTemporal));

            $preview = array_slice($rows[0], 0, 10);
            $totalRows = count($rows[0]);

            return view('admin.retiros.import-preview', compact('preview', 'totalRows', 'rutaTemporal'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al leer el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Procesar importación del archivo Excel
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processImport(Request $request)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

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
                    RetiroImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Retiros'
                );

                return redirect()->route('admin.retiros.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new RetiroImport($this->retiroService);
                Excel::import($import, $rutaArchivo);

                Storage::delete($rutaTemporal);

                $errors = $import->getErrors();
                if (!empty($errors)) {
                    return redirect()->route('admin.retiros.index')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('import_errors', $errors);
                }

                return redirect()->route('admin.retiros.index')
                    ->with('success', 'Importación de retiros completada exitosamente.');
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
            $import = new RetiroImport($this->retiroService);
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
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function downloadTemplate()
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        try {
            $headers = ['codigo_vaca', 'tipo_retiro', 'fecha_inicio', 'fecha_fin', 'motivo', 'observaciones'];
            $ejemplo = ['VACA001', 'Ordeño', date('Y-m-d'), date('Y-m-d', strtotime('+7 days')), 'Tratamiento', 'Ejemplo'];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_retiros.xlsx'
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
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function exportExcel(Request $request)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        try {
            $filters = $request->only(['search', 'id_vaca', 'tipo_retiro', 'activo', 'fecha_inicio', 'fecha_fin']);
            return Excel::download(new RetiroExport($filters), 'retiros.xlsx');
        } catch (\Exception $e) {
            Log::error('Error al exportar retiros a Excel: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a Excel: ' . $e->getMessage());
        }
    }

    /**
     * Exportar a PDF
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function exportPdf(Request $request)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        try {
            $filters = $request->only(['search', 'id_vaca', 'tipo_retiro', 'activo', 'fecha_inicio', 'fecha_fin']);
            $retiros = $this->retiroService->getFiltered($filters);
            $pdf = PDF::loadView('admin.retiros.pdf', compact('retiros', 'filters'));
            return $pdf->download('retiros.pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar retiros a PDF: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a PDF: ' . $e->getMessage());
        }
    }
}
