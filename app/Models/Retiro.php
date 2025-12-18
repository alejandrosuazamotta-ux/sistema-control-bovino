<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Retiro extends Model
{
    use LogsActivity;
    protected $table = 'retiros';
    protected $primaryKey = 'id_retiro';
    
    protected $fillable = [
        'id_vaca',
        'id_uso_medicamento',
        'fecha_inicio',
        'fecha_fin',
        'tipo_retiro',
        'activo',
        'observaciones'
    ];
    
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean'
    ];
    
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
     * Scope para retiros activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    /**
     * Scope para retiros de ordeño
     */
    public function scopeOrdeño($query)
    {
        return $query->where('tipo_retiro', 'Ordeño');
    }
    
    /**
     * Scope para retiros de producción
     */
    public function scopeProduccion($query)
    {
        return $query->where('tipo_retiro', 'Producción');
    }
    
    /**
     * Scope para retiros activos en una fecha específica
     */
    public function scopeActivosEnFecha($query, Carbon $fecha)
    {
        return $query->where('activo', true)
            ->where('fecha_inicio', '<=', $fecha)
            ->where('fecha_fin', '>=', $fecha);
    }
    
    /**
     * Verificar si el retiro está activo en una fecha específica
     */
    public function estaActivoEnFecha(Carbon $fecha): bool
    {
        return $this->activo 
            && $this->fecha_inicio <= $fecha 
            && $this->fecha_fin >= $fecha;
    }
    
    /**
     * Verificar si el retiro está activo actualmente
     */
    public function estaActivo(): bool
    {
        return $this->estaActivoEnFecha(now());
    }
    
    /**
     * Calcular días restantes de retiro
     */
    public function diasRestantes(): int
    {
        if (!$this->estaActivo()) {
            return 0;
        }
        
        $hoy = now();
        if ($hoy > $this->fecha_fin) {
            return 0;
        }
        
        return $hoy->diffInDays($this->fecha_fin, false);
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_vaca', 'id_uso_medicamento', 'fecha_inicio', 'fecha_fin', 'tipo_retiro', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Retiro {$eventName}");
    }
}
