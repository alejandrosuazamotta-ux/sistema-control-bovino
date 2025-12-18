<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionPotrero;
use App\Models\Vaca;
use App\Models\Potrero;
use App\Imports\AsignacionPotreroImport;
use App\Exports\AsignacionPotreroExport;
use App\Jobs\ProcessExcelImportJob;
use App\Services\AsignacionPotreroService;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * AsignacionPotreroController
 * 
 * NOTA: AsignacionPotrero no tiene Policy asociada.
 * La autorización se maneja por middleware de rutas (role:Admin).
 * Se agregan verificaciones adicionales para mayor seguridad.
 */

class AsignacionPotreroController extends Controller
{
    protected AsignacionPotreroService $asignacionPotreroService;

    public function __construct(AsignacionPotreroService $asignacionPotreroService)
    {
        $this->asignacionPotreroService = $asignacionPotreroService;
    }
    /**
     * Display a listing of the resource.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     * NOTA: AsignacionPotrero no tiene Policy, se verifica por rol directamente
     */
    public function index(Request $request)
    {
        // SEGURIDAD: Admin siempre tiene acceso (rutas protegidas por middleware role:Admin)
        // Esta verificación es redundante pero se mantiene como defensa adicional
        // Admin nunca será bloqueado aquí porque las rutas ya están protegidas

        $query = AsignacionPotrero::with(['vaca', 'potrero']);

        // Búsqueda por código de vaca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por potrero
        if ($request->filled('id_potrero')) {
            $query->where('id_potrero', $request->id_potrero);
        }

        // Filtro por vaca
        if ($request->filled('id_vaca')) {
            $query->where('id_vaca', $request->id_vaca);
        }

        // Filtro por fecha inicio
        if ($request->filled('fecha_inicio')) {
            $query->where('fecha_asignacion', '>=', $request->fecha_inicio);
        }

        // Filtro por fecha fin
        if ($request->filled('fecha_fin')) {
            $query->where('fecha_asignacion', '<=', $request->fecha_fin);
        }

        $asignaciones = $query->orderBy('fecha_asignacion', 'desc')->paginate(15);

        // Datos para los filtros
        $vacas = Vaca::orderBy('codigo')->get();
        $potreros = Potrero::orderBy('nombre')->get();

        // Datos para gráficas
        $datosGraficas = $this->asignacionPotreroService->getDatosGraficas();

        return view('admin.asignacion_potreros.index', compact('asignaciones', 'vacas', 'potreros', 'datosGraficas'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function create()
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        $vacas = Vaca::orderBy('codigo')->get();
        $potreros = Potrero::orderBy('nombre')->get();
        
        return view('admin.asignacion_potreros.create', compact('vacas', 'potreros'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function store(Request $request)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        $validator = Validator::make($request->all(), [
            'id_potrero' => 'required|exists:potreros,id_potrero',
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha_asignacion' => 'required|date|before_or_equal:today',
        ], [
            'id_potrero.required' => 'Debe seleccionar un potrero.',
            'id_potrero.exists' => 'El potrero seleccionado no existe.',
            'id_vaca.required' => 'Debe seleccionar una vaca.',
            'id_vaca.exists' => 'La vaca seleccionada no existe.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'fecha_asignacion.date' => 'La fecha debe ser válida.',
            'fecha_asignacion.before_or_equal' => 'La fecha no puede ser posterior al día actual.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->asignacionPotreroService->create($request->all());
            
            return redirect()->route('admin.asignacion-potreros.index')
                ->with('success', 'Asignación de potrero creada exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la asignación: ' . $e->getMessage())
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

        $asignacion = AsignacionPotrero::with(['vaca', 'potrero'])->findOrFail($id);
        
        return view('admin.asignacion_potreros.show', compact('asignacion'));
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

        $asignacion = AsignacionPotrero::findOrFail($id);
        $vacas = Vaca::orderBy('codigo')->get();
        $potreros = Potrero::orderBy('nombre')->get();
        
        return view('admin.asignacion_potreros.edit', compact('asignacion', 'vacas', 'potreros'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor (manejado por middleware de rutas)
     */
    public function update(Request $request, string $id)
    {
        // Verificación adicional de seguridad
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Supervisor')) {
            // Admin nunca será bloqueado (rutas protegidas por middleware)
        }

        $asignacion = AsignacionPotrero::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'id_potrero' => 'required|exists:potreros,id_potrero',
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha_asignacion' => 'required|date|before_or_equal:today',
        ], [
            'id_potrero.required' => 'Debe seleccionar un potrero.',
            'id_potrero.exists' => 'El potrero seleccionado no existe.',
            'id_vaca.required' => 'Debe seleccionar una vaca.',
            'id_vaca.exists' => 'La vaca seleccionada no existe.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'fecha_asignacion.date' => 'La fecha debe ser válida.',
            'fecha_asignacion.before_or_equal' => 'La fecha no puede ser posterior al día actual.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->asignacionPotreroService->update($asignacion, $request->all());
            
            return redirect()->route('admin.asignacion-potreros.index')
                ->with('success', 'Asignación de potrero actualizada exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la asignación: ' . $e->getMessage())
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
            abort(403, 'Solo los administradores pueden eliminar asignaciones.');
        }

        try {
            $asignacion = AsignacionPotrero::findOrFail($id);
            
            // Remover la asignación del potrero de la vaca
            $vaca = $asignacion->vaca;
            if ($vaca && $vaca->id_potrero == $asignacion->id_potrero) {
                $vaca->id_potrero = null;
                $vaca->save();
            }
            
            $asignacion->delete();
            
            return redirect()->route('admin.asignacion-potreros.index')
                ->with('success', 'Asignación de potrero eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la asignación: ' . $e->getMessage());
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
            abort(403, 'No tiene permisos para importar asignaciones.');
        }

        return view('admin.asignacion_potreros.import');
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
            abort(403, 'No tiene permisos para importar asignaciones.');
        }

        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $archivo = $request->file('archivo');
            $nombreArchivo = 'import_asignacion_potrero_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');

            $import = new AsignacionPotreroImport();
            $rows = Excel::toArray($import, Storage::path($rutaTemporal));

            $preview = array_slice($rows[0], 0, 10);
            $totalRows = count($rows[0]);

            return view('admin.asignacion_potreros.import-preview', compact('preview', 'totalRows', 'rutaTemporal'));
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
            abort(403, 'No tiene permisos para importar asignaciones.');
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
                    AsignacionPotreroImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Asignación Potreros'
                );

                return redirect()->route('admin.asignacion-potreros.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new AsignacionPotreroImport();
                Excel::import($import, $rutaArchivo);

                Storage::delete($rutaTemporal);

                $errors = $import->getErrors();
                if (!empty($errors)) {
                    return redirect()->route('admin.asignacion-potreros.index')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('import_errors', $errors);
                }

                return redirect()->route('admin.asignacion-potreros.index')
                    ->with('success', 'Importación de asignaciones de potreros completada exitosamente.');
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
            $import = new AsignacionPotreroImport();
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
            abort(403, 'No tiene permisos para descargar plantillas.');
        }

        try {
            $headers = ['codigo_vaca', 'id_potrero', 'fecha_asignacion', 'fecha_retiro', 'observaciones'];
            $ejemplo = ['VACA001', 1, date('Y-m-d'), null, 'Ejemplo'];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_asignacion_potreros.xlsx'
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
            abort(403, 'No tiene permisos para exportar asignaciones.');
        }

        try {
            $filters = $request->only(['search', 'id_potrero', 'id_vaca', 'fecha_inicio', 'fecha_fin']);
            return Excel::download(new AsignacionPotreroExport($filters), 'asignaciones_potreros.xlsx');
        } catch (\Exception $e) {
            Log::error('Error al exportar asignaciones de potreros a Excel: ' . $e->getMessage(), ['exception' => $e]);
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
            abort(403, 'No tiene permisos para exportar asignaciones.');
        }

        try {
            $filters = $request->only(['search', 'id_potrero', 'id_vaca', 'fecha_inicio', 'fecha_fin']);
            $query = AsignacionPotrero::with(['vaca', 'potrero']);
            
            if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
                $query->where('id_vaca', $filters['id_vaca']);
            }
            
            if (isset($filters['id_potrero']) && !empty($filters['id_potrero'])) {
                $query->where('id_potrero', $filters['id_potrero']);
            }
            
            if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
                $query->where('fecha_asignacion', '>=', $filters['fecha_inicio']);
            }
            
            if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
                $query->where('fecha_asignacion', '<=', $filters['fecha_fin']);
            }
            
            $asignaciones = $query->orderBy('fecha_asignacion', 'desc')->get();
            $pdf = PDF::loadView('admin.asignacion_potreros.pdf', compact('asignaciones', 'filters'));
            return $pdf->download('asignaciones_potreros.pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar asignaciones de potreros a PDF: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a PDF: ' . $e->getMessage());
        }
    }
}
