<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class InventarioBodega extends Model
{
    use LogsActivity;
    protected $table = 'inventario_bodega';
    protected $primaryKey = 'id_inventario';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_producto',
        'id_medicamento',
        'unidad_medida',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'precio_unitario',
        'proveedor',
        'fecha_vencimiento',
        'lote',
        'ubicacion_bodega',
        'observaciones',
        'activo'
    ];
    
    protected $casts = [
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'stock_maximo' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'activo' => 'boolean'
    ];
    
    /**
     * Relación con medicamento (si es medicamento)
     */
    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(Medicamento::class, 'id_medicamento', 'id_medicamento');
    }
    
    /**
     * Relación con movimientos de inventario
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'id_inventario', 'id_inventario');
    }
    
    /**
     * Scope para productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    /**
     * Scope para filtrar por tipo de producto
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_producto', $tipo);
    }
    
    /**
     * Scope para productos con stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<=', 'stock_minimo');
    }
    
    /**
     * Scope para productos próximos a vencer (30 días)
     */
    public function scopeProximosAVencer($query, int $dias = 30)
    {
        return $query->whereNotNull('fecha_vencimiento')
                    ->where('fecha_vencimiento', '<=', Carbon::now()->addDays($dias))
                    ->where('fecha_vencimiento', '>=', Carbon::now());
    }
    
    /**
     * Scope para productos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->whereNotNull('fecha_vencimiento')
                    ->where('fecha_vencimiento', '<', Carbon::now());
    }
    
    /**
     * Verificar si el stock está bajo
     */
    public function tieneStockBajo(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
    
    /**
     * Verificar si está próximo a vencer
     */
    public function estaProximoAVencer(int $dias = 30): bool
    {
        if (!$this->fecha_vencimiento) {
            return false;
        }
        
        return $this->fecha_vencimiento->isFuture() 
            && $this->fecha_vencimiento->diffInDays(Carbon::now()) <= $dias;
    }
    
    /**
     * Verificar si está vencido
     */
    public function estaVencido(): bool
    {
        if (!$this->fecha_vencimiento) {
            return false;
        }
        
        return $this->fecha_vencimiento->isPast();
    }
    
    /**
     * Calcular valor total del stock
     */
    public function getValorTotalStockAttribute(): float
    {
        return $this->stock_actual * $this->precio_unitario;
    }
    
    /**
     * Obtener días hasta vencimiento
     */
    public function getDiasHastaVencimientoAttribute(): ?int
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }
        
        return $this->fecha_vencimiento->diffInDays(Carbon::now(), false);
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['codigo', 'nombre', 'tipo_producto', 'stock_actual', 'stock_minimo', 'stock_maximo', 'precio_unitario', 'fecha_vencimiento', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Inventario bodega {$eventName}");
    }
}
