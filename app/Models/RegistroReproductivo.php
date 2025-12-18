<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RegistroReproductivo extends Model
{
    use LogsActivity;
    protected $table = 'registros_reproductivos';
    protected $primaryKey = 'id_registro';
    
    protected $fillable = [
        'id_vaca',
        'tipo_evento',
        'fecha_evento',
        'resultado_palpacion',
        'tiempo_gestacion_dias',
        'fecha_probable_parto',
        'especialista',
        'dias_abiertos',
        'observaciones',
        'id_personal'
    ];
    
    protected $casts = [
        'fecha_evento' => 'date',
        'fecha_probable_parto' => 'date',
        'tiempo_gestacion_dias' => 'integer',
        'dias_abiertos' => 'integer'
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
     * Boot del modelo - calcular fecha_probable_parto y dias_abiertos automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($registro) {
            // Calcular fecha_probable_parto si es palpación preñada
            if ($registro->tipo_evento === 'Palpación' 
                && $registro->resultado_palpacion === 'Preñada' 
                && $registro->tiempo_gestacion_dias !== null) {
                
                // Fecha probable parto = fecha_evento + (283 - tiempo_gestacion_dias)
                $diasRestantes = 283 - $registro->tiempo_gestacion_dias;
                $registro->fecha_probable_parto = $registro->fecha_evento->copy()->addDays($diasRestantes);
            } elseif ($registro->tipo_evento !== 'Palpación' || $registro->resultado_palpacion !== 'Preñada') {
                // Limpiar fecha_probable_parto si no aplica
                $registro->fecha_probable_parto = null;
            }

            // Calcular días abiertos si es inseminación o palpación preñada
            if (in_array($registro->tipo_evento, ['Inseminación', 'Palpación']) 
                && ($registro->tipo_evento === 'Palpación' ? $registro->resultado_palpacion === 'Preñada' : true)) {
                
                $ultimoParto = static::where('id_vaca', $registro->id_vaca)
                    ->where('tipo_evento', 'Parto')
                    ->where('fecha_evento', '<', $registro->fecha_evento)
                    ->orderBy('fecha_evento', 'desc')
                    ->first();
                
                if ($ultimoParto) {
                    $registro->dias_abiertos = $ultimoParto->fecha_evento->diffInDays($registro->fecha_evento);
                } else {
                    $registro->dias_abiertos = null;
                }
            }
        });
    }

    /**
     * Scope para filtrar por tipo de evento
     */
    public function scopePorTipoEvento($query, string $tipo)
    {
        return $query->where('tipo_evento', $tipo);
    }

    /**
     * Scope para filtrar palpaciones preñadas
     */
    public function scopePalpacionesPreñadas($query)
    {
        return $query->where('tipo_evento', 'Palpación')
            ->where('resultado_palpacion', 'Preñada');
    }

    /**
     * Scope para filtrar por vaca
     */
    public function scopePorVaca($query, int $vacaId)
    {
        return $query->where('id_vaca', $vacaId);
    }

    /**
     * Verificar si está próximo al parto (21 días antes)
     */
    public function estaProximoAlParto(): bool
    {
        if (!$this->fecha_probable_parto) {
            return false;
        }
        
        return now()->diffInDays($this->fecha_probable_parto, false) <= 21 
            && now()->diffInDays($this->fecha_probable_parto, false) >= 0;
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_vaca', 'tipo_evento', 'fecha_evento', 'resultado_palpacion', 'fecha_probable_parto', 'tiempo_gestacion_dias'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Registro reproductivo {$eventName}");
    }
}
