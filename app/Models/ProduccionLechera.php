<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\ClearsDashboardCache;

class ProduccionLechera extends Model
{
    use LogsActivity, ClearsDashboardCache;
    protected $table = 'produccion_lechera';
    protected $primaryKey = 'id_produccion';
    
    protected $fillable = [
        'id_vaca',
        'fecha',
        'turno',
        'cantidad_leche',
        'destino',
        'valor_unidad',
        'valor_total',
        'excluida_por_retiro',
        'excluida_por_sanidad',
        'id_personal',
        'observaciones'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'cantidad_leche' => 'decimal:2',
        'valor_unidad' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'excluida_por_retiro' => 'boolean',
        'excluida_por_sanidad' => 'boolean'
    ];
    
    // Relaciones
    public function vaca(): BelongsTo
    {
        return $this->belongsTo(Vaca::class, 'id_vaca', 'id_vaca');
    }
    
    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'id_personal', 'id_personal');
    }
    
    /**
     * Boot del modelo - calcular valor_total automáticamente
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($produccion) {
            // Calcular valor_total si se proporciona valor_unidad y cantidad_leche
            if ($produccion->valor_unidad && $produccion->cantidad_leche) {
                $produccion->valor_total = $produccion->cantidad_leche * $produccion->valor_unidad;
            } else {
                // Si no hay valor_unidad o cantidad_leche, limpiar valor_total
                $produccion->valor_total = null;
            }
        });
    }
    
    /**
     * Scope para excluir registros de vacas en retiro
     */
    public function scopeSinRetiro($query)
    {
        return $query->where('excluida_por_retiro', false);
    }
    
    /**
     * Scope para excluir registros excluidos por sanidad
     */
    public function scopeSinSanidad($query)
    {
        return $query->where('excluida_por_sanidad', false);
    }
    
    /**
     * Scope para excluir registros excluidos (retiro o sanidad)
     */
    public function scopeSinExclusiones($query)
    {
        return $query->where('excluida_por_retiro', false)
                    ->where('excluida_por_sanidad', false);
    }
    
    /**
     * Scope para filtrar por turno
     */
    public function scopePorTurno($query, string $turno)
    {
        return $query->where('turno', $turno);
    }
    
    /**
     * Scope para filtrar por destino
     */
    public function scopePorDestino($query, string $destino)
    {
        return $query->where('destino', $destino);
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_vaca', 'fecha', 'turno', 'cantidad_leche', 'destino', 'valor_unidad', 'valor_total', 'excluida_por_retiro', 'excluida_por_sanidad'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Producción lechera {$eventName}");
    }
}
