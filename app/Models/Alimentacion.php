<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Alimentacion extends Model
{
    use LogsActivity;
    protected $table = 'alimentacion';
    protected $primaryKey = 'id_alimentacion';
    
    protected $fillable = [
        'id_vaca',
        'fecha',
        'tipo_alimento',
        'cantidad',
        'observaciones',
        'id_personal'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'decimal:2'
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
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_vaca', 'fecha', 'tipo_alimento', 'cantidad', 'id_personal'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Alimentación {$eventName}");
    }
}
