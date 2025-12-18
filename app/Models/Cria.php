<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Cria extends Model
{
    use LogsActivity;
    protected $table = 'crias';
    protected $primaryKey = 'id_cria';
    
    protected $fillable = [
        'id_vaca_madre',
        'nombre_cria',
        'sexo',
        'fecha_nacimiento',
        'peso',
        'foto',
        'fecha_tatuado',
        'concepcion',
        'sinigan',
        'estado_destete',
        'fecha_destete',
        'observaciones'
    ];
    
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_tatuado' => 'date',
        'fecha_destete' => 'date',
        'peso' => 'decimal:2'
    ];
    
    // Relaciones
    public function vacaMadre(): BelongsTo
    {
        return $this->belongsTo(Vaca::class, 'id_vaca_madre', 'id_vaca');
    }
    
    /**
     * Relación polimórfica con Mortalidad
     */
    public function mortalidad()
    {
        return $this->morphOne(Mortalidad::class, 'animal');
    }
    
    /**
     * Verificar si la cría está muerta
     */
    public function estaMuerta(): bool
    {
        return $this->mortalidad()->exists();
    }

    /**
     * Boot del modelo - calcular estado_destete automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cria) {
            // Si se establece fecha_destete, actualizar estado_destete automáticamente
            if ($cria->fecha_destete !== null) {
                $cria->estado_destete = 'Destetada';
            } elseif ($cria->estado_destete === 'Destetada' && $cria->fecha_destete === null) {
                // Si se cambia estado a "No destetada", limpiar fecha_destete
                $cria->fecha_destete = null;
            }
        });
    }

    /**
     * Scope para filtrar por sexo
     */
    public function scopePorSexo($query, string $sexo)
    {
        return $query->where('sexo', $sexo);
    }

    /**
     * Scope para filtrar por método de concepción
     */
    public function scopePorConcepcion($query, string $concepcion)
    {
        return $query->where('concepcion', $concepcion);
    }

    /**
     * Scope para filtrar crías destetadas
     */
    public function scopeDestetadas($query)
    {
        return $query->where('estado_destete', 'Destetada');
    }

    /**
     * Scope para filtrar crías no destetadas
     */
    public function scopeNoDestetadas($query)
    {
        return $query->where('estado_destete', 'No destetada');
    }

    /**
     * Calcular edad en días
     */
    public function getEdadDiasAttribute(): int
    {
        return $this->fecha_nacimiento->diffInDays(now());
    }

    /**
     * Calcular edad en meses
     */
    public function getEdadMesesAttribute(): int
    {
        return $this->fecha_nacimiento->diffInMonths(now());
    }

    /**
     * Verificar si está próxima al destete (60 días de edad)
     */
    public function estaProximaAlDestete(): bool
    {
        return $this->estado_destete === 'No destetada' && $this->edadDias >= 50 && $this->edadDias <= 70;
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_vaca_madre', 'nombre_cria', 'sexo', 'fecha_nacimiento', 'peso', 'concepcion', 'estado_destete', 'fecha_destete'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Cría {$eventName}");
    }
}
