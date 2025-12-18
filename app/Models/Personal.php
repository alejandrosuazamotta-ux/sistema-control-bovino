<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Personal extends Model
{
    use LogsActivity;
    protected $table = 'personal';
    protected $primaryKey = 'id_personal';

    protected $fillable = [
        'nombre',
        'rol',
        'fecha_contratacion',
        'email',
        'telefono',
        'direccion',
        'user_id' // importante para la relación
    ];

    protected $casts = [
        'fecha_contratacion' => 'date'
    ];

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relaciones con otros módulos
    public function registrosReproductivos(): HasMany
    {
        return $this->hasMany(RegistroReproductivo::class, 'id_personal', 'id_personal');
    }

    public function produccionLechera(): HasMany
    {
        return $this->hasMany(ProduccionLechera::class, 'id_personal', 'id_personal');
    }

    public function salud(): HasMany
    {
        return $this->hasMany(Salud::class, 'id_personal', 'id_personal');
    }

    public function alimentacion(): HasMany
    {
        return $this->hasMany(Alimentacion::class, 'id_personal', 'id_personal');
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'rol', 'fecha_contratacion', 'email', 'telefono', 'user_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Personal {$eventName}");
    }
}
