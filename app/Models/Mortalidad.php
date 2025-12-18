<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Mortalidad extends Model
{
    use LogsActivity;
    protected $table = 'mortalidad';
    protected $primaryKey = 'id_mortalidad';
    
    protected $fillable = [
        'fecha',
        'hora',
        'animal_type',
        'animal_id',
        'clasificacion',
        'peso',
        'causa',
        'acta',
        'acta_path',
        'observaciones'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
        'peso' => 'decimal:2'
    ];
    
    /**
     * Relación polimórfica con Vaca o Cria
     */
    public function animal(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Scope para filtrar por tipo de animal
     */
    public function scopePorTipoAnimal($query, string $tipo)
    {
        return $query->where('animal_type', $tipo);
    }
    
    /**
     * Scope para filtrar por clasificación
     */
    public function scopePorClasificacion($query, string $clasificacion)
    {
        return $query->where('clasificacion', $clasificacion);
    }
    
    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopePorRangoFechas($query, string $fechaInicio, string $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }
    
    /**
     * Scope para obtener muertes recientes
     */
    public function scopeRecientes($query, int $dias = 30)
    {
        return $query->where('fecha', '>=', now()->subDays($dias));
    }
    
    /**
     * Verificar si es una vaca
     */
    public function esVaca(): bool
    {
        return $this->animal_type === Vaca::class;
    }
    
    /**
     * Verificar si es una cría
     */
    public function esCria(): bool
    {
        return $this->animal_type === Cria::class;
    }
    
    /**
     * Obtener el código o identificador del animal
     */
    public function getCodigoAnimalAttribute(): ?string
    {
        if ($this->esVaca() && $this->animal) {
            return $this->animal->codigo;
        }
        
        if ($this->esCria() && $this->animal) {
            return $this->animal->nombre_cria ?? 'Cría #' . $this->animal_id;
        }
        
        return null;
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fecha', 'animal_type', 'animal_id', 'clasificacion', 'causa'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Mortalidad {$eventName}");
    }
}

