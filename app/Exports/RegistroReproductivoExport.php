<?php

namespace App\Exports;

use App\Models\RegistroReproductivo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RegistroReproductivoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
        $query = RegistroReproductivo::with(['vaca', 'personal']);

        // Aplicar filtros
        if (isset($this->filters['tipo_evento']) && $this->filters['tipo_evento']) {
            $query->where('tipo_evento', $this->filters['tipo_evento']);
        }

        if (isset($this->filters['id_vaca']) && $this->filters['id_vaca']) {
            $query->where('id_vaca', $this->filters['id_vaca']);
        }

        if (isset($this->filters['fecha_inicio'])) {
            $query->where('fecha_evento', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin'])) {
            $query->where('fecha_evento', '<=', $this->filters['fecha_fin']);
        }

        return $query->orderBy('fecha_evento', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Código Vaca',
            'Tipo Evento',
            'Fecha Evento',
            'Resultado Palpación',
            'Tiempo Gestación (días)',
            'Fecha Probable Parto',
            'Días Abiertos',
            'Personal',
            'Observaciones',
        ];
    }

    /**
     * @param RegistroReproductivo $registro
     * @return array
     */
    public function map($registro): array
    {
        return [
            $registro->id_registro,
            $registro->vaca->codigo ?? 'N/A',
            $registro->tipo_evento,
            $registro->fecha_evento->format('d/m/Y'),
            $registro->resultado_palpacion ?? 'N/A',
            $registro->tiempo_gestacion_dias ?? 'N/A',
            $registro->fecha_probable_parto ? $registro->fecha_probable_parto->format('d/m/Y') : 'N/A',
            $registro->dias_abiertos ?? 'N/A',
            $registro->personal->nombre ?? 'N/A',
            $registro->observaciones ?? '',
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
                    'startColor' => ['rgb' => 'FFC107']
                ],
                'font' => ['color' => ['rgb' => '000000']],
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
        return 'Registros Reproductivos';
    }
}

