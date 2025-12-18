<?php

namespace App\Exports;

use App\Models\Salud;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SaludExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = Salud::with(['vaca', 'personal']);

        // Aplicar filtros
        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        if (isset($this->filters['tipo_registro']) && !empty($this->filters['tipo_registro'])) {
            $query->where('tipo_registro', $this->filters['tipo_registro']);
        }

        if (isset($this->filters['tipo_prueba']) && !empty($this->filters['tipo_prueba'])) {
            $query->where('tipo_prueba', $this->filters['tipo_prueba']);
        }

        if (isset($this->filters['resultado']) && !empty($this->filters['resultado'])) {
            $query->where('resultado', $this->filters['resultado']);
        }

        if (isset($this->filters['fecha_inicio']) && !empty($this->filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin']) && !empty($this->filters['fecha_fin'])) {
            $query->where('fecha', '<=', $this->filters['fecha_fin']);
        }

        if (isset($this->filters['id_vaca']) && !empty($this->filters['id_vaca'])) {
            $query->where('id_vaca', $this->filters['id_vaca']);
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
            'Tipo Registro',
            'Tipo Prueba',
            'Fecha',
            'Resultado',
            'Fecha Resultado',
            'Severidad',
            'Restricción Ordeño',
            'Inhabilitada',
            'Acta',
            'Responsable Prueba',
            'Tratamiento Sugerido',
            'Descripción',
            'Observaciones',
            'Personal',
            'Fecha Creación',
        ];
    }

    /**
     * @param Salud $salud
     * @return array
     */
    public function map($salud): array
    {
        return [
            $salud->id_salud,
            $salud->vaca->codigo ?? 'N/A',
            $salud->tipo_registro,
            $salud->tipo_prueba ?? 'N/A',
            $salud->fecha->format('d/m/Y'),
            $salud->resultado ?? 'N/A',
            $salud->fecha_resultado ? $salud->fecha_resultado->format('d/m/Y') : 'N/A',
            $salud->severidad ?? 'N/A',
            $salud->restriccion_ordeño ? 'Sí' : 'No',
            $salud->inhabilitada ? 'Sí' : 'No',
            $salud->acta ?? 'N/A',
            $salud->responsable_prueba ?? 'N/A',
            $salud->tratamiento_sugerido ?? 'N/A',
            $salud->descripcion ?? 'N/A',
            $salud->observaciones ?? 'N/A',
            $salud->personal ? ($salud->personal->nombre . ' ' . ($salud->personal->apellido ?? '')) : 'N/A',
            $salud->created_at->format('d/m/Y H:i'),
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
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }
}

