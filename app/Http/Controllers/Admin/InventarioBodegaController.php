<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventarioBodegaStoreRequest;
use App\Http\Requests\InventarioBodegaUpdateRequest;
use App\Services\InventarioBodegaService;
use App\Services\MedicamentoService;
use App\Imports\InventarioBodegaImport;
use App\Exports\InventarioBodegaExport;
use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InventarioBodegaController extends Controller
{
    protected InventarioBodegaService $inventarioService;
    protected MedicamentoService $medicamentoService;

    public function __construct(
        InventarioBodegaService $inventarioService,
        MedicamentoService $medicamentoService
    ) {
        $this->inventarioService = $inventarioService;
        $this->medicamentoService = $medicamentoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'tipo_producto' => $request->get('tipo_producto'),
            'activo' => $request->get('activo', true),
            'stock_bajo' => $request->get('stock_bajo'),
            'proximos_vencer' => $request->get('proximos_vencer'),
            'vencidos' => $request->get('vencidos'),
        ];

        $productos = $this->inventarioService->getPaginated($filters, 15);
        $estadisticas = $this->inventarioService->getEstadisticas();
        $datosGraficas = $this->inventarioService->getDatosGraficas();

        return view('admin.inventario_bodega.index', compact('productos', 'estadisticas', 'datosGraficas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicamentos = $this->medicamentoService->getActivos();
        return view('admin.inventario_bodega.create', compact('medicamentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventarioBodegaStoreRequest $request)
    {
        try {
            $producto = $this->inventarioService->create($request->validated());
            
            return redirect()->route('admin.inventario-bodega.index')
                ->with('success', 'Producto registrado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = $this->inventarioService->findById($id);
        
        if (!$producto) {
            return redirect()->route('admin.inventario-bodega.index')
                ->with('error', 'Producto no encontrado.');
        }

        $movimientos = $this->inventarioService->getMovimientosProducto($id, [
            'fecha_inicio' => request()->get('fecha_inicio'),
            'fecha_fin' => request()->get('fecha_fin'),
            'tipo_movimiento' => request()->get('tipo_movimiento'),
        ]);

        return view('admin.inventario_bodega.show', compact('producto', 'movimientos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $producto = $this->inventarioService->findById($id);
        
        if (!$producto) {
            return redirect()->route('admin.inventario-bodega.index')
                ->with('error', 'Producto no encontrado.');
        }

        $medicamentos = $this->medicamentoService->getActivos();
        return view('admin.inventario_bodega.edit', compact('producto', 'medicamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventarioBodegaUpdateRequest $request, string $id)
    {
        try {
            $producto = $this->inventarioService->findById($id);
            
            if (!$producto) {
                return redirect()->route('admin.inventario-bodega.index')
                    ->with('error', 'Producto no encontrado.');
            }

            $producto = $this->inventarioService->update($producto, $request->validated());
            
            return redirect()->route('admin.inventario-bodega.show', $producto->id_inventario)
                ->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $producto = $this->inventarioService->findById($id);
            
            if (!$producto) {
                return redirect()->route('admin.inventario-bodega.index')
                    ->with('error', 'Producto no encontrado.');
            }

            $this->inventarioService->delete($producto);
            
            return redirect()->route('admin.inventario-bodega.index')
                ->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Registrar entrada de inventario
     */
    public function registrarEntrada(Request $request, string $id)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'nullable|numeric|min:0',
            'fecha_movimiento' => 'nullable|date',
            'motivo' => 'nullable|string|max:200',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        try {
            $this->inventarioService->registrarEntrada($id, $request->all());
            
            return redirect()->route('admin.inventario-bodega.show', $id)
                ->with('success', 'Entrada registrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar la entrada: ' . $e->getMessage());
        }
    }

    /**
     * Registrar salida de inventario
     */
    public function registrarSalida(Request $request, string $id)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0.01',
            'fecha_movimiento' => 'nullable|date',
            'motivo' => 'nullable|string|max:200',
            'id_vaca' => 'nullable|exists:vacas,id_vaca',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        try {
            $this->inventarioService->registrarSalida($id, $request->all());
            
            return redirect()->route('admin.inventario-bodega.show', $id)
                ->with('success', 'Salida registrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar la salida: ' . $e->getMessage());
        }
    }

    /**
     * Registrar ajuste de inventario
     */
    public function registrarAjuste(Request $request, string $id)
    {
        $request->validate([
            'stock_nuevo' => 'required|numeric|min:0',
            'fecha_movimiento' => 'nullable|date',
            'motivo' => 'nullable|string|max:200',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        try {
            $this->inventarioService->registrarAjuste($id, $request->all());
            
            return redirect()->route('admin.inventario-bodega.show', $id)
                ->with('success', 'Ajuste registrado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el ajuste: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de importación
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        return view('admin.inventario_bodega.import');
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
            $nombreArchivo = 'import_inventario_' . time() . '_' . $archivo->getClientOriginalName();
            $rutaTemporal = $archivo->storeAs('temp', $nombreArchivo, 'local');

            $import = new InventarioBodegaImport($this->inventarioService);
            $rows = Excel::toArray($import, Storage::path($rutaTemporal));

            $preview = array_slice($rows[0], 0, 10);
            $totalRows = count($rows[0]);

            return view('admin.inventario_bodega.import-preview', compact('preview', 'totalRows', 'rutaTemporal'));
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
                    InventarioBodegaImport::class,
                    $rutaTemporal,
                    auth()->id(),
                    'Inventario Bodega'
                );

                return redirect()->route('admin.inventario-bodega.index')
                    ->with('info', 'La importación se está procesando en segundo plano. Recibirá una notificación cuando se complete.');
            } else {
                $import = new InventarioBodegaImport($this->inventarioService);
                Excel::import($import, $rutaArchivo);

                Storage::delete($rutaTemporal);

                $errors = $import->getErrors();
                if (!empty($errors)) {
                    return redirect()->route('admin.inventario-bodega.index')
                        ->with('warning', 'Importación completada con algunos errores.')
                        ->with('import_errors', $errors);
                }

                return redirect()->route('admin.inventario-bodega.index')
                    ->with('success', 'Importación de productos completada exitosamente.');
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
            $import = new InventarioBodegaImport($this->inventarioService);
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
            $headers = ['nombre', 'tipo_producto', 'unidad_medida', 'stock_actual', 'stock_minimo', 'fecha_vencimiento', 'proveedor', 'activo'];
            $ejemplo = ['Producto Ejemplo', 'Insumo', 'kg', '100', '20', date('Y-m-d', strtotime('+1 year')), 'Proveedor Ejemplo', '1'];
            $data = [$headers, $ejemplo];

            $export = Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    protected $data;
                    public function __construct($data) { $this->data = $data; }
                    public function array(): array { return [$this->data[1]]; }
                    public function headings(): array { return $this->data[0] ?? []; }
                },
                'plantilla_inventario_bodega.xlsx'
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
        try {
            $filters = $request->only(['search', 'tipo_producto', 'activo', 'stock_bajo', 'proximos_vencer', 'vencidos']);
            return Excel::download(new InventarioBodegaExport($filters), 'inventario_bodega.xlsx');
        } catch (\Exception $e) {
            Log::error('Error al exportar inventario a Excel: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a Excel: ' . $e->getMessage());
        }
    }

    /**
     * Exportar a PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        try {
            $filters = $request->only(['search', 'tipo_producto', 'activo', 'stock_bajo', 'proximos_vencer', 'vencidos']);
            $productos = $this->inventarioService->getFiltered($filters);
            $estadisticas = $this->inventarioService->getEstadisticas();
            $pdf = PDF::loadView('admin.inventario_bodega.pdf', compact('productos', 'filters', 'estadisticas'));
            return $pdf->download('inventario_bodega.pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar inventario a PDF: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Error al exportar a PDF: ' . $e->getMessage());
        }
    }
}

