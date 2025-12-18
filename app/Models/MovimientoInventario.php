<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'id_movimiento';
    
    protected $fillable = [
        'id_inventario',
        'tipo_movimiento',
        'cantidad',
        'precio_unitario',
        'valor_total',
        'fecha_movimiento',
        'motivo',
        'id_personal',
        'id_vaca',
        'id_uso_medicamento',
        'observaciones'
    ];
    
    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'fecha_movimiento' => 'date'
    ];
    
    /**
     * Relación con inventario
     */
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(InventarioBodega::class, 'id_inventario', 'id_inventario');
    }
    
    /**
     * Relación con personal
     */
    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'id_personal', 'id_personal');
    }
    
    /**
     * Relación con vaca
     */
    public function vaca(): BelongsTo
    {
        return $this->belongsTo(Vaca::class, 'id_vaca', 'id_vaca');
    }
    
    /**
     * Relación con uso de medicamento
     */
    public function usoMedicamento(): BelongsTo
    {
        return $this->belongsTo(UsoMedicamento::class, 'id_uso_medicamento', 'id_uso');
    }
    
    /**
     * Scope para entradas
     */
    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'Entrada');
    }
    
    /**
     * Scope para salidas
     */
    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'Salida');
    }
    
    /**
     * Scope para ajustes
     */
    public function scopeAjustes($query)
    {
        return $query->where('tipo_movimiento', 'Ajuste');
    }
    
    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
    }
}
