<?php

namespace App\Exports;

use App\Models\PruebaSanitaria;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PruebaSanitariaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = PruebaSanitaria::with(['vaca', 'personal', 'usuario']);

        // Aplicar filtros
        if (isset($this->filters['tipo_prueba']) && !empty($this->filters['tipo_prueba'])) {
            $query->where('tipo_prueba', $this->filters['tipo_prueba']);
        }

        if (isset($this->filters['resultado']) && !empty($this->filters['resultado'])) {
            $query->where('resultado', $this->filters['resultado']);
        }

        if (isset($this->filters['fecha_inicio']) && !empty($this->filters['fecha_inicio'])) {
            $query->where('fecha_prueba', '>=', $this->filters['fecha_inicio']);
        }

        if (isset($this->filters['fecha_fin']) && !empty($this->filters['fecha_fin'])) {
            $query->where('fecha_prueba', '<=', $this->filters['fecha_fin']);
        }

        return $query->orderBy('fecha_prueba', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Código Vaca',
            'Tipo Prueba',
            'Fecha Prueba',
            'Resultado',
            'Fecha Resultado',
            'Severidad',
            'Restricción Ordeño',
            'Inhabilitada',
            'Acta',
            'Responsable',
            'Personal',
            'Usuario',
            'Cerrada',
            'Observaciones',
            'Fecha Creación'
        ];
    }

    /**
     * @param PruebaSanitaria $prueba
     * @return array
     */
    public function map($prueba): array
    {
        return [
            $prueba->id_prueba,
            $prueba->vaca->codigo ?? 'N/A',
            $prueba->tipo_prueba,
            $prueba->fecha_prueba->format('d/m/Y'),
            $prueba->resultado,
            $prueba->fecha_resultado ? $prueba->fecha_resultado->format('d/m/Y') : 'N/A',
            $prueba->severidad ?? 'N/A',
            $prueba->restriccion_ordeño ? 'Sí' : 'No',
            $prueba->inhabilitada ? 'Sí' : 'No',
            $prueba->acta ?? 'N/A',
            $prueba->responsable_prueba ?? 'N/A',
            $prueba->personal->nombre ?? 'N/A',
            $prueba->usuario->name ?? 'N/A',
            $prueba->cerrada ? 'Sí' : 'No',
            $prueba->observaciones ?? 'N/A',
            $prueba->created_at->format('d/m/Y H:i')
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

