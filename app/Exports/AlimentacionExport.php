<?php

namespace App\Exports;

use App\Models\Alimentacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AlimentacionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
        $query = Alimentacion::with(['vaca', 'personal']);

        // Aplicar filtros
        if (isset($this->filters['id_vaca']) && !empty($this->filters['id_vaca'])) {
            $query->where('id_vaca', $this->filters['id_vaca']);
        }

        if (isset($this->filters['tipo_alimento']) && !empty($this->filters['tipo_alimento'])) {
            $query->where('tipo_alimento', $this->filters['tipo_alimento']);
        }

        if (isset($this->filters['fecha_inicio']) && !empty($this->filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin']) && !empty($this->filters['fecha_fin'])) {
            $query->where('fecha', '<=', $this->filters['fecha_fin']);
        }

        return $query->orderBy('fecha', 'desc')->get();
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
            'Tipo Alimento',
            'Cantidad (kg)',
            'Personal',
            'Observaciones',
        ];
    }

    /**
     * @param Alimentacion $alimentacion
     * @return array
     */
    public function map($alimentacion): array
    {
        return [
            $alimentacion->id_alimentacion,
            $alimentacion->vaca->codigo ?? 'N/A',
            $alimentacion->fecha->format('d/m/Y'),
            $alimentacion->tipo_alimento,
            $alimentacion->cantidad ? number_format($alimentacion->cantidad, 2, '.', '') : 'N/A',
            $alimentacion->personal->nombre ?? 'N/A',
            $alimentacion->observaciones ?? '',
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
        return 'Alimentación';
    }
}

