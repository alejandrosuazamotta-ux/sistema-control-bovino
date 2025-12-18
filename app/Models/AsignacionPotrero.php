<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class AsignacionPotrero extends Model
{
    use LogsActivity;
    protected $table = 'asignacion_potreros';
    protected $primaryKey = 'id_asignacion';
    
    protected $fillable = [
        'id_potrero',
        'id_vaca',
        'fecha_asignacion',
        'fecha_salida',
        'dias_estancia',
        'dias_descanso',
        'ugg_total',
        'carga_ugg',
        'aforo_kg',
        'peso_ingreso',
        'observaciones'
    ];
    
    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_salida' => 'date',
        'dias_estancia' => 'integer',
        'dias_descanso' => 'integer',
        'ugg_total' => 'decimal:2',
        'carga_ugg' => 'decimal:2',
        'aforo_kg' => 'decimal:2',
        'peso_ingreso' => 'decimal:2'
    ];
    
    // Relaciones
    public function potrero(): BelongsTo
    {
        return $this->belongsTo(Potrero::class, 'id_potrero', 'id_potrero');
    }
    
    public function vaca(): BelongsTo
    {
        return $this->belongsTo(Vaca::class, 'id_vaca', 'id_vaca');
    }
    
    /**
     * Calcular días de estancia automáticamente y campos avanzados
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($asignacion) {
            // Calcular días de estancia
            if ($asignacion->fecha_asignacion && $asignacion->fecha_salida) {
                $asignacion->dias_estancia = $asignacion->fecha_asignacion->diffInDays($asignacion->fecha_salida);
            } elseif ($asignacion->fecha_asignacion && !$asignacion->fecha_salida) {
                // Si no hay fecha de salida, calcular desde la asignación hasta hoy
                $asignacion->dias_estancia = $asignacion->fecha_asignacion->diffInDays(Carbon::now());
            }
            
            // Guardar peso de ingreso si no está establecido y hay vaca
            if (!$asignacion->peso_ingreso && $asignacion->vaca && $asignacion->vaca->peso_kg) {
                $asignacion->peso_ingreso = $asignacion->vaca->peso_kg;
            }
            
            // Calcular UGG si hay peso de la vaca
            if ($asignacion->vaca && $asignacion->vaca->peso_kg) {
                $ugg = $asignacion->vaca->peso_kg / 450; // 450 kg = 1 UGG estándar
                $asignacion->ugg_total = $ugg * ($asignacion->dias_estancia ?? 1);
            }
            
            // Calcular carga UGG por hectárea si hay potrero y área
            if ($asignacion->potrero && $asignacion->potrero->area_hectareas && $asignacion->potrero->area_hectareas > 0) {
                $uggIndividual = ($asignacion->peso_ingreso ?? $asignacion->vaca->peso_kg ?? 0) / 450;
                $asignacion->carga_ugg = $uggIndividual / $asignacion->potrero->area_hectareas;
            }
            
            // Calcular aforo en kg por hectárea
            if ($asignacion->potrero && $asignacion->potrero->area_hectareas && $asignacion->potrero->area_hectareas > 0) {
                $pesoTotal = $asignacion->peso_ingreso ?? $asignacion->vaca->peso_kg ?? 0;
                $asignacion->aforo_kg = $pesoTotal / $asignacion->potrero->area_hectareas;
            }
        });
    }
    
    /**
     * Calcular UGG (Unidades Gran Ganado)
     * UGG = peso_kg / 450
     */
    public function calcularUGG(): float
    {
        if (!$this->vaca || !$this->vaca->peso_kg) {
            return 0;
        }
        
        return $this->vaca->peso_kg / 450; // 450 kg = 1 UGG estándar
    }
    
    /**
     * Verificar si la asignación está activa
     */
    public function estaActiva(): bool
    {
        return $this->fecha_salida === null;
    }
    
    /**
     * Scope para asignaciones activas
     */
    public function scopeActivas($query)
    {
        return $query->whereNull('fecha_salida');
    }
    
    /**
     * Scope para asignaciones finalizadas
     */
    public function scopeFinalizadas($query)
    {
        return $query->whereNotNull('fecha_salida');
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_potrero', 'id_vaca', 'fecha_asignacion', 'fecha_salida', 'dias_estancia', 'peso_ingreso'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Asignación de potrero {$eventName}");
    }
}
