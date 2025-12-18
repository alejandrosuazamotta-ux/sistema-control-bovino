<?php

namespace App\Exports;

use App\Models\InventarioBodega;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InventarioBodegaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = InventarioBodega::with(['medicamento']);

        // Aplicar filtros
        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('proveedor', 'LIKE', "%{$search}%");
            });
        }

        if (isset($this->filters['tipo_producto']) && !empty($this->filters['tipo_producto'])) {
            $query->where('tipo_producto', $this->filters['tipo_producto']);
        }

        if (isset($this->filters['activo']) && $this->filters['activo'] !== null) {
            $query->where('activo', $this->filters['activo']);
        }

        if (isset($this->filters['stock_bajo']) && $this->filters['stock_bajo']) {
            $query->stockBajo();
        }

        if (isset($this->filters['proximos_vencer']) && $this->filters['proximos_vencer']) {
            $query->proximosAVencer();
        }

        if (isset($this->filters['vencidos']) && $this->filters['vencidos']) {
            $query->vencidos();
        }

        return $query->orderBy('nombre', 'asc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Código',
            'Nombre',
            'Tipo Producto',
            'Medicamento',
            'Unidad Medida',
            'Stock Actual',
            'Stock Mínimo',
            'Stock Máximo',
            'Precio Unitario',
            'Valor Total Stock',
            'Proveedor',
            'Fecha Vencimiento',
            'Lote',
            'Ubicación Bodega',
            'Activo',
            'Observaciones',
        ];
    }

    /**
     * @param InventarioBodega $producto
     * @return array
     */
    public function map($producto): array
    {
        return [
            $producto->id_inventario,
            $producto->codigo,
            $producto->nombre,
            $producto->tipo_producto,
            $producto->medicamento->nombre ?? 'N/A',
            $producto->unidad_medida,
            number_format($producto->stock_actual, 2, '.', ''),
            number_format($producto->stock_minimo, 2, '.', ''),
            $producto->stock_maximo ? number_format($producto->stock_maximo, 2, '.', '') : 'N/A',
            number_format($producto->precio_unitario, 2, '.', ''),
            number_format($producto->valor_total_stock, 2, '.', ''),
            $producto->proveedor ?? 'N/A',
            $producto->fecha_vencimiento ? $producto->fecha_vencimiento->format('d/m/Y') : 'N/A',
            $producto->lote ?? 'N/A',
            $producto->ubicacion_bodega ?? 'N/A',
            $producto->activo ? 'Sí' : 'No',
            $producto->observaciones ?? '',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F713E']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Inventario Bodega';
    }
}

