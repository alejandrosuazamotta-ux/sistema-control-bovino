<?php

namespace App\Exports;

use App\Models\Medicamento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MedicamentoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
        $query = Medicamento::query();

        // Aplicar filtros
        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $query->where('nombre', 'LIKE', "%{$this->filters['search']}%");
        }

        if (isset($this->filters['tipo']) && !empty($this->filters['tipo'])) {
            $query->where('tipo', $this->filters['tipo']);
        }

        if (isset($this->filters['activo'])) {
            $query->where('activo', $this->filters['activo']);
        }

        return $query->orderBy('nombre')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Tipo',
            'Principio Activo',
            'Vía Administración',
            'Dosis',
            'Período Retiro (días)',
            'Activo',
            'Observaciones',
        ];
    }

    /**
     * @param Medicamento $medicamento
     * @return array
     */
    public function map($medicamento): array
    {
        return [
            $medicamento->id_medicamento,
            $medicamento->nombre,
            $medicamento->tipo,
            $medicamento->principio_activo ?? 'N/A',
            $medicamento->via_administracion ?? 'N/A',
            $medicamento->dosis ?? 'N/A',
            $medicamento->periodo_retiro_dias,
            $medicamento->activo ? 'Sí' : 'No',
            $medicamento->observaciones ?? '',
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
        return 'Medicamentos';
    }
}

