<?php

namespace App\Exports;

use App\Models\Cria;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CriaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
        $query = Cria::with('vacaMadre');

        // Aplicar filtros
        if (isset($this->filters['sexo']) && $this->filters['sexo']) {
            $query->porSexo($this->filters['sexo']);
        }

        if (isset($this->filters['estado_destete']) && $this->filters['estado_destete']) {
            $query->where('estado_destete', $this->filters['estado_destete']);
        }

        if (isset($this->filters['fecha_inicio'])) {
            $query->where('fecha_nacimiento', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin'])) {
            $query->where('fecha_nacimiento', '<=', $this->filters['fecha_fin']);
        }

        return $query->orderBy('fecha_nacimiento', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Código Vaca Madre',
            'Nombre Cría',
            'Sexo',
            'Fecha Nacimiento',
            'Peso (kg)',
            'Fecha Tatuado',
            'Concepción',
            'SINIGAN',
            'Estado Destete',
            'Fecha Destete',
            'Observaciones',
        ];
    }

    /**
     * @param Cria $cria
     * @return array
     */
    public function map($cria): array
    {
        return [
            $cria->id_cria,
            $cria->vacaMadre->codigo ?? 'N/A',
            $cria->nombre_cria ?? 'N/A',
            $cria->sexo,
            $cria->fecha_nacimiento->format('d/m/Y'),
            $cria->peso ? number_format($cria->peso, 2, '.', '') : 'N/A',
            $cria->fecha_tatuado ? $cria->fecha_tatuado->format('d/m/Y') : 'N/A',
            $cria->concepcion ?? 'N/A',
            $cria->sinigan ?? 'N/A',
            $cria->estado_destete,
            $cria->fecha_destete ? $cria->fecha_destete->format('d/m/Y') : 'N/A',
            $cria->observaciones ?? '',
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
                    'startColor' => ['rgb' => '17A2B8']
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
        return 'Crías';
    }
}

