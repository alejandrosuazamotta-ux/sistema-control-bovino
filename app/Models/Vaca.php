<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Vaca extends Model
{
    use LogsActivity;
    protected $table = 'vacas';
    protected $primaryKey = 'id_vaca';
    
    protected $fillable = [
        'codigo',
        'fecha_nacimiento',
        'raza',
        'peso_kg',
        'foto',
        'estado_salud',
        'estado_reproductivo',
        'id_potrero'
    ];
    
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'peso_kg' => 'decimal:2'
    ];
    
    // Relaciones
    public function potrero(): BelongsTo
    {
        return $this->belongsTo(Potrero::class, 'id_potrero', 'id_potrero');
    }
    
    public function crias(): HasMany
    {
        return $this->hasMany(Cria::class, 'id_vaca_madre', 'id_vaca');
    }
    
    public function registrosReproductivos(): HasMany
    {
        return $this->hasMany(RegistroReproductivo::class, 'id_vaca', 'id_vaca');
    }
    
    public function produccionLechera(): HasMany
    {
        return $this->hasMany(ProduccionLechera::class, 'id_vaca', 'id_vaca');
    }
    
    public function salud(): HasMany
    {
        return $this->hasMany(Salud::class, 'id_vaca', 'id_vaca');
    }
    
    public function alimentacion(): HasMany
    {
        return $this->hasMany(Alimentacion::class, 'id_vaca', 'id_vaca');
    }
    
    public function asignacionesPotreros(): HasMany
    {
        return $this->hasMany(AsignacionPotrero::class, 'id_vaca', 'id_vaca');
    }
    
    public function usosMedicamentos(): HasMany
    {
        return $this->hasMany(UsoMedicamento::class, 'id_vaca', 'id_vaca');
    }
    
    public function retiros(): HasMany
    {
        return $this->hasMany(Retiro::class, 'id_vaca', 'id_vaca');
    }

    public function pruebasSanitarias(): HasMany
    {
        return $this->hasMany(PruebaSanitaria::class, 'id_vaca', 'id_vaca');
    }
    
    /**
     * Relación polimórfica con Mortalidad
     */
    public function mortalidad()
    {
        return $this->morphOne(Mortalidad::class, 'animal');
    }
    
    /**
     * Verificar si la vaca está muerta
     */
    public function estaMuerta(): bool
    {
        return $this->mortalidad()->exists();
    }
    
    /**
     * Obtener retiros activos de la vaca
     */
    public function retirosActivos()
    {
        return $this->retiros()->activos()->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now());
    }
    
    /**
     * Verificar si la vaca tiene retiro activo de ordeño
     */
    public function tieneRetiroOrdeñoActivo(): bool
    {
        return $this->retiros()
            ->activos()
            ->ordeño()
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->exists();
    }
    
    /**
     * Verificar si la vaca tiene retiro activo de producción
     */
    public function tieneRetiroProduccionActivo(): bool
    {
        return $this->retiros()
            ->activos()
            ->produccion()
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->exists();
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['codigo', 'raza', 'fecha_nacimiento', 'peso_kg', 'estado_salud', 'estado_reproductivo', 'id_potrero'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Vaca {$eventName}");
    }
}
