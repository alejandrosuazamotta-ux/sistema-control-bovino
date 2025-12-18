<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Potrero extends Model
{
    use LogsActivity;
    protected $table = 'potreros';
    protected $primaryKey = 'id_potrero';
    
    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad',
        'area_hectareas',
        'dias_descanso_recomendado',
        'aforo_actual',
        'aforo_maximo',
        'en_mantenimiento',
        'descripcion'
    ];
    
    protected $casts = [
        'area_hectareas' => 'decimal:2',
        'dias_descanso_recomendado' => 'integer',
        'aforo_actual' => 'decimal:2',
        'aforo_maximo' => 'decimal:2',
        'en_mantenimiento' => 'boolean'
    ];
    
    public $timestamps = true;
    
    // Relaciones
    public function vacas(): HasMany
    {
        return $this->hasMany(Vaca::class, 'id_potrero', 'id_potrero');
    }
    
    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionPotrero::class, 'id_potrero', 'id_potrero');
    }
    
    /**
     * Calcular aforo actual (UGG/ha)
     * Aforo = Total UGG de vacas activas / Área en hectáreas
     */
    public function calcularAforoActual(): float
    {
        if (!$this->area_hectareas || $this->area_hectareas <= 0) {
            return 0;
        }
        
        $totalUGG = 0;
        foreach ($this->vacas as $vaca) {
            if ($vaca->peso_kg) {
                $totalUGG += $vaca->peso_kg / 450; // 450 kg = 1 UGG
            }
        }
        
        return $totalUGG / $this->area_hectareas;
    }
    
    /**
     * Verificar si el potrero necesita descanso
     */
    public function necesitaDescanso(): bool
    {
        $ultimaAsignacion = $this->asignaciones()
            ->whereNotNull('fecha_salida')
            ->orderBy('fecha_salida', 'desc')
            ->first();
        
        if (!$ultimaAsignacion || !$ultimaAsignacion->fecha_salida) {
            return false;
        }
        
        $diasDesdeSalida = $ultimaAsignacion->fecha_salida->diffInDays(now());
        return $diasDesdeSalida < $this->dias_descanso_recomendado;
    }
    
    /**
     * Obtener días restantes de descanso
     */
    public function getDiasRestantesDescanso(): ?int
    {
        $ultimaAsignacion = $this->asignaciones()
            ->whereNotNull('fecha_salida')
            ->orderBy('fecha_salida', 'desc')
            ->first();
        
        if (!$ultimaAsignacion || !$ultimaAsignacion->fecha_salida) {
            return null;
        }
        
        $diasDesdeSalida = $ultimaAsignacion->fecha_salida->diffInDays(now());
        $diasRestantes = $this->dias_descanso_recomendado - $diasDesdeSalida;
        
        return $diasRestantes > 0 ? $diasRestantes : 0;
    }
    
    /**
     * Verificar si el aforo está dentro del rango recomendado
     */
    public function aforoDentroRango(): bool
    {
        $aforoActual = $this->calcularAforoActual();
        
        if ($this->aforo_maximo && $aforoActual > $this->aforo_maximo) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Scope para potreros disponibles (no en mantenimiento)
     */
    public function scopeDisponibles($query)
    {
        return $query->where('en_mantenimiento', false);
    }
    
    /**
     * Scope para potreros en mantenimiento
     */
    public function scopeEnMantenimiento($query)
    {
        return $query->where('en_mantenimiento', true);
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'ubicacion', 'capacidad', 'area_hectareas', 'dias_descanso_recomendado', 'aforo_maximo', 'en_mantenimiento'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Potrero {$eventName}");
    }
}
