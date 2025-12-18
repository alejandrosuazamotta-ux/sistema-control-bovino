<?php

namespace App\Exports;

use App\Models\ProduccionLechera;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProduccionLecheraExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
        $query = ProduccionLechera::with(['vaca', 'personal'])->sinRetiro();

        // Aplicar filtros
        if (isset($this->filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin'])) {
            $query->where('fecha', '<=', $this->filters['fecha_fin']);
        }

        if (isset($this->filters['id_vaca']) && $this->filters['id_vaca']) {
            $query->where('id_vaca', $this->filters['id_vaca']);
        }

        if (isset($this->filters['turno']) && $this->filters['turno']) {
            $query->where('turno', $this->filters['turno']);
        }

        if (isset($this->filters['destino']) && $this->filters['destino']) {
            $query->where('destino', $this->filters['destino']);
        }

        return $query->orderBy('fecha', 'desc')->orderBy('turno', 'asc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Código Vaca',
            'Fecha',
            'Turno',
            'Cantidad Leche (L)',
            'Destino',
            'Valor Unidad',
            'Valor Total',
            'Personal',
            'Observaciones',
        ];
    }

    /**
     * @param ProduccionLechera $produccion
     * @return array
     */
    public function map($produccion): array
    {
        return [
            $produccion->id_produccion,
            $produccion->vaca->codigo ?? 'N/A',
            $produccion->fecha->format('d/m/Y'),
            $produccion->turno,
            number_format($produccion->cantidad_leche, 2, '.', ''),
            $produccion->destino,
            $produccion->valor_unidad ? number_format($produccion->valor_unidad, 2, '.', '') : 'N/A',
            $produccion->valor_total ? number_format($produccion->valor_total, 2, '.', '') : 'N/A',
            $produccion->personal->nombre ?? 'N/A',
            $produccion->observaciones ?? '',
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
                    'startColor' => ['rgb' => '28A745']
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
        return 'Producción Lechera';
    }
}

