<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Potrero;
use App\Imports\PotreroImport;
use App\Exports\PotreroExport;
use App\Jobs\ProcessExcelImportJob;
use App\Services\PotreroService;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PotreroController extends Controller
{
    protected PotreroService $potreroService;

    public function __construct(PotreroService $potreroService)
    {
        $this->potreroService = $potreroService;
    }
    public function index(Request $request)
    {
        $query = Potrero::with('vacas');

        // Búsqueda por nombre o ubicación
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por capacidad
        if ($request->filled('capacidad')) {
            $capacidad = $request->capacidad;
            switch ($capacidad) {
                case '1-10':
                    $query->whereBetween('capacidad', [1, 10]);
                    break;
                case '11-20':
                    $query->whereBetween('capacidad', [11, 20]);
                    break;
                case '21-50':
                    $query->whereBetween('capacidad', [21, 50]);
                    break;
                case '50+':
                    $query->where('capacidad', '>', 50);
                    break;
            }
        }

        $potreros = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Datos para gráficas
        $datosGraficas = $this->potreroService->getDatosGraficas();
        
        return view('admin.potreros.index', compact('potreros', 'datosGraficas'));
    }

    public function create()
    {
        return view('admin.potreros.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:potreros,nombre',
            'ubicacion' => 'nullable|string|max:100',
            'capacidad' => 'required|integer|min:1|max:1000',
            'area_hectareas' => 'nullable|numeric|min:0|max:10000',
            'dias_descanso_recomendado' => 'nullable|integer|min:0|max:365',
            'aforo_maximo' => 'nullable|numeric|min:0|max:100',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre del potrero es obligatorio.',
            'nombre.unique' => 'Ya existe un potrero con ese nombre.',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',
            'ubicacion.max' => 'La ubicación no puede tener más de 100 caracteres.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad debe ser al menos 1.',
            'capacidad.max' => 'La capacidad no puede ser mayor a 1000.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $potrero = Potrero::create($request->all());
            
            // Calcular aforo inicial si hay área
            if ($potrero->area_hectareas && $potrero->area_hectareas > 0) {
                $potrero->aforo_actual = $potrero->calcularAforoActual();
                $potrero->save();
            }
            
            return redirect()->route('admin.potreros.index')
                ->with('success', 'Potrero registrado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el potrero: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $potrero = Potrero::findOrFail($id);
        $potrero->load('vacas');
        return view('admin.potreros.show', compact('potrero'));
    }

    public function edit(string $id)
    {
        $potrero = Potrero::findOrFail($id);
        return view('admin.potreros.edit', compact('potrero'));
    }

    public function update(Request $request, string $id)
    {
        $potrero = Potrero::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:potreros,nombre,' . $potrero->id_potrero . ',id_potrero',
            'ubicacion' => 'nullable|string|max:100',
            'capacidad' => 'required|integer|min:1|max:1000',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre del potrero es obligatorio.',
            'nombre.unique' => 'Ya existe un potrero con ese nombre.',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',
            'ubicacion.max' => 'La ubicación no puede tener más de 100 caracteres.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad debe ser al menos 1.',
            'capacidad.max' => 'La capacidad no puede ser mayor a 1000.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
        ]);

        // Validar que la capacidad no sea menor que las vacas asignadas
        $vacasAsignadas = $potrero->vacas->count();
        if ($request->capacidad < $vacasAsignadas) {
            return redirect()->back()
                ->withErrors(['capacidad' => "La capacidad no puede ser menor a {$vacasAsignadas} ya que hay {$vacasAsignadas} vacas asignadas actualmente."])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $potrero->update($request->all());
            
            return redirect()->route('admin.potreros.show', $potrero->id_potrero)
                ->with('success', 'Potrero actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el potrero: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $potrero = Potrero::findOrFail($id);
        
        // Verificar si hay vacas asignadas
        if ($potrero->vacas->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el potrero porque tiene vacas asignadas. Desasigna todas las vacas primero.');
        }

        try {
            $potrero->delete();
            
            return redirect()->route('admin.potreros.index')
                ->with('success', 'Potrero eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el potrero: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        return view('admin.potreros.import');
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
            $nombreArchivo = 'import_potrero_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');

            $import = new PotreroImport();
            $rows = Excel::toArray($import, Storage::path($rutaTemporal));

            $preview = array_slice($rows[0], 0, 10);
            $totalRows = count($rows[0]);

            return view('admin.potreros.import-preview', compact('preview', 'totalRows', 'rutaTemporal'));
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
                    PotreroImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Potreros'
                );

                return redirect()->route('admin.potreros.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new PotreroImport();
                Excel::import($import, $rutaArchivo);

                Storage::delete($rutaTemporal);

                $errors = $import->getErrors();
                if (!empty($errors)) {
                    return redirect()->route('admin.potreros.index')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('import_errors', $errors);
                }

                return redirect()->route('admin.potreros.index')
                    ->with('success', 'Importación de potreros completada exitosamente.');
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
            $import = new PotreroImport();
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
            $headers = ['nombre', 'ubicacion', 'area_hectareas', 'capacidad_maxima', 'tipo_pasto', 'observaciones'];
            $ejemplo = ['Potrero 1', 'Norte', '5.5', '50', 'Pasto Natural', 'Ejemplo'];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_potreros.xlsx'
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
            $filters = $request->only(['search', 'capacidad']);
            return Excel::download(new PotreroExport($filters), 'potreros.xlsx');
        } catch (\Exception $e) {
            Log::error('Error al exportar potreros a Excel: ' . $e->getMessage(), ['exception' => $e]);
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
            $filters = $request->only(['search', 'capacidad']);
            $query = Potrero::withCount('vacas');
            
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'LIKE', "%{$search}%")
                      ->orWhere('ubicacion', 'LIKE', "%{$search}%");
                });
            }
            
            $potreros = $query->orderBy('nombre')->get();
            $pdf = PDF::loadView('admin.potreros.pdf', compact('potreros', 'filters'));
            return $pdf->download('potreros.pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar potreros a PDF: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a PDF: ' . $e->getMessage());
        }
    }
} 