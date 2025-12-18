<?php

namespace App\Exports;

use App\Models\Retiro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RetiroExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
        $query = Retiro::with(['vaca', 'usoMedicamento']);

        // Aplicar filtros
        if (isset($this->filters['id_vaca']) && !empty($this->filters['id_vaca'])) {
            $query->where('id_vaca', $this->filters['id_vaca']);
        }

        if (isset($this->filters['tipo_retiro']) && !empty($this->filters['tipo_retiro'])) {
            $query->where('tipo_retiro', $this->filters['tipo_retiro']);
        }

        if (isset($this->filters['activo']) !== null) {
            $query->where('activo', $this->filters['activo']);
        }

        if (isset($this->filters['fecha_inicio']) && !empty($this->filters['fecha_inicio'])) {
            $query->where('fecha_inicio', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin']) && !empty($this->filters['fecha_fin'])) {
            $query->where('fecha_fin', '<=', $this->filters['fecha_fin']);
        }

        return $query->orderBy('fecha_inicio', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Código Vaca',
            'Tipo Retiro',
            'Fecha Inicio',
            'Fecha Fin',
            'Días de Retiro',
            'Activo',
            'Medicamento Asociado',
            'Observaciones',
        ];
    }

    /**
     * @param Retiro $retiro
     * @return array
     */
    public function map($retiro): array
    {
        $diasRetiro = $retiro->fecha_inicio && $retiro->fecha_fin 
            ? $retiro->fecha_inicio->diffInDays($retiro->fecha_fin) 
            : 'N/A';

        return [
            $retiro->id_retiro,
            $retiro->vaca->codigo ?? 'N/A',
            $retiro->tipo_retiro,
            $retiro->fecha_inicio->format('d/m/Y'),
            $retiro->fecha_fin->format('d/m/Y'),
            $diasRetiro,
            $retiro->activo ? 'Sí' : 'No',
            $retiro->usoMedicamento->medicamento->nombre ?? 'N/A',
            $retiro->observaciones ?? '',
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
        return 'Retiros';
    }
}

