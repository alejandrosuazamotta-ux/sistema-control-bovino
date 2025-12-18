<?php

namespace App\Exports;

use App\Models\Mortalidad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MortalidadExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
        $query = Mortalidad::with('animal');

        // Aplicar filtros
        if (isset($this->filters['animal_type']) && $this->filters['animal_type']) {
            $query->porTipoAnimal($this->filters['animal_type']);
        }

        if (isset($this->filters['clasificacion']) && $this->filters['clasificacion']) {
            $query->porClasificacion($this->filters['clasificacion']);
        }

        if (isset($this->filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin'])) {
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
            'Tipo Animal',
            'Código/Nombre',
            'Fecha',
            'Hora',
            'Clasificación',
            'Peso (kg)',
            'Causa',
            'Acta',
            'Observaciones',
        ];
    }

    /**
     * @param Mortalidad $mortalidad
     * @return array
     */
    public function map($mortalidad): array
    {
        $codigo = 'N/A';
        if ($mortalidad->animal) {
            if ($mortalidad->esVaca()) {
                $codigo = $mortalidad->animal->codigo ?? 'N/A';
            } elseif ($mortalidad->esCria()) {
                $codigo = $mortalidad->animal->nombre_cria ?? $mortalidad->animal->sinigan ?? 'N/A';
            }
        }

        return [
            $mortalidad->id_mortalidad,
            $mortalidad->esVaca() ? 'Vaca' : 'Cría',
            $codigo,
            $mortalidad->fecha->format('d/m/Y'),
            $mortalidad->hora ? $mortalidad->hora->format('H:i') : 'N/A',
            $mortalidad->clasificacion,
            $mortalidad->peso ? number_format($mortalidad->peso, 2, '.', '') : 'N/A',
            $mortalidad->causa,
            $mortalidad->acta ?? 'N/A',
            $mortalidad->observaciones ?? '',
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
                    'startColor' => ['rgb' => 'DC3545']
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
        return 'Mortalidad';
    }
}

