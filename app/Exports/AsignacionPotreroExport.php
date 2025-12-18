<?php

namespace App\Exports;

use App\Models\AsignacionPotrero;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AsignacionPotreroExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
        $query = AsignacionPotrero::with(['vaca', 'potrero']);

        // Aplicar filtros
        if (isset($this->filters['id_vaca']) && !empty($this->filters['id_vaca'])) {
            $query->where('id_vaca', $this->filters['id_vaca']);
        }

        if (isset($this->filters['id_potrero']) && !empty($this->filters['id_potrero'])) {
            $query->where('id_potrero', $this->filters['id_potrero']);
        }

        if (isset($this->filters['fecha_inicio']) && !empty($this->filters['fecha_inicio'])) {
            $query->where('fecha_asignacion', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin']) && !empty($this->filters['fecha_fin'])) {
            $query->where('fecha_asignacion', '<=', $this->filters['fecha_fin']);
        }

        return $query->orderBy('fecha_asignacion', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Código Vaca',
            'Potrero',
            'Fecha Asignación',
            'Días Asignado',
        ];
    }

    /**
     * @param AsignacionPotrero $asignacion
     * @return array
     */
    public function map($asignacion): array
    {
        $diasAsignado = $asignacion->fecha_asignacion 
            ? $asignacion->fecha_asignacion->diffInDays(now()) 
            : 'N/A';

        return [
            $asignacion->id_asignacion,
            $asignacion->vaca->codigo ?? 'N/A',
            $asignacion->potrero->nombre ?? 'N/A',
            $asignacion->fecha_asignacion->format('d/m/Y'),
            $diasAsignado,
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
        return 'Asignación Potreros';
    }
}

