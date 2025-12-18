<?php

namespace App\Exports;

use App\Models\UsoMedicamento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UsoMedicamentoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
        $query = UsoMedicamento::with(['medicamento', 'vaca', 'personal']);

        // Aplicar filtros
        if (isset($this->filters['id_vaca']) && !empty($this->filters['id_vaca'])) {
            $query->where('id_vaca', $this->filters['id_vaca']);
        }

        if (isset($this->filters['id_medicamento']) && !empty($this->filters['id_medicamento'])) {
            $query->where('id_medicamento', $this->filters['id_medicamento']);
        }

        if (isset($this->filters['fecha_inicio']) && !empty($this->filters['fecha_inicio'])) {
            $query->where('fecha_aplicacion', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin']) && !empty($this->filters['fecha_fin'])) {
            $query->where('fecha_aplicacion', '<=', $this->filters['fecha_fin']);
        }

        return $query->orderBy('fecha_aplicacion', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Código Vaca',
            'Medicamento',
            'Fecha Aplicación',
            'Dosis Aplicada',
            'Unidad Dosis',
            'Personal',
            'Observaciones',
        ];
    }

    /**
     * @param UsoMedicamento $uso
     * @return array
     */
    public function map($uso): array
    {
        return [
            $uso->id_uso,
            $uso->vaca->codigo ?? 'N/A',
            $uso->medicamento->nombre ?? 'N/A',
            $uso->fecha_aplicacion->format('d/m/Y'),
            $uso->dosis_aplicada ? number_format($uso->dosis_aplicada, 2, '.', '') : 'N/A',
            $uso->unidad_dosis ?? 'N/A',
            $uso->personal->nombre ?? 'N/A',
            $uso->observaciones ?? '',
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
        return 'Uso de Medicamentos';
    }
}

