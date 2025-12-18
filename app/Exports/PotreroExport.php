<?php

namespace App\Exports;

use App\Models\Potrero;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PotreroExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
        $query = Potrero::withCount('vacas');

        // Aplicar filtros
        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $query->where('nombre', 'LIKE', "%{$this->filters['search']}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$this->filters['search']}%");
        }

        if (isset($this->filters['capacidad']) && !empty($this->filters['capacidad'])) {
            $query->where('capacidad', '>=', $this->filters['capacidad']);
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
            'Ubicación',
            'Capacidad',
            'Ocupación',
            'Disponibilidad',
            'Descripción',
        ];
    }

    /**
     * @param Potrero $potrero
     * @return array
     */
    public function map($potrero): array
    {
        $ocupacion = $potrero->vacas_count ?? 0;
        $disponibilidad = $potrero->capacidad - $ocupacion;

        return [
            $potrero->id_potrero,
            $potrero->nombre,
            $potrero->ubicacion ?? 'N/A',
            $potrero->capacidad,
            $ocupacion,
            $disponibilidad,
            $potrero->descripcion ?? '',
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
        return 'Potreros';
    }
}

