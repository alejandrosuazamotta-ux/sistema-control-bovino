<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class UsoMedicamento extends Model
{
    use LogsActivity;
    protected $table = 'uso_medicamentos';
    protected $primaryKey = 'id_uso';
    
    protected $fillable = [
        'id_medicamento',
        'id_vaca',
        'fecha_aplicacion',
        'dosis_aplicada',
        'unidad_dosis',
        'id_personal',
        'user_id',
        'observaciones',
        'evidencia_path'
    ];
    
    protected $casts = [
        'fecha_aplicacion' => 'date',
        'dosis_aplicada' => 'decimal:2'
    ];
    
    /**
     * Relación con medicamento
     */
    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(Medicamento::class, 'id_medicamento', 'id_medicamento');
    }
    
    /**
     * Relación con vaca
     */
    public function vaca(): BelongsTo
    {
        return $this->belongsTo(Vaca::class, 'id_vaca', 'id_vaca');
    }
    
    /**
     * Relación con personal
     */
    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'id_personal', 'id_personal');
    }

    /**
     * Relación con usuario (pasante que registró)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    
    /**
     * Relación con retiro generado (si existe)
     */
    public function retiro(): HasOne
    {
        return $this->hasOne(Retiro::class, 'id_uso_medicamento', 'id_uso');
    }
    
    /**
     * Calcular fecha fin de retiro basado en el medicamento
     */
    public function calcularFechaFinRetiro(): ?\Carbon\Carbon
    {
        if (!$this->medicamento || $this->medicamento->periodo_retiro_dias <= 0) {
            return null;
        }
        
        return $this->fecha_aplicacion->copy()->addDays($this->medicamento->periodo_retiro_dias);
    }
    
    /**
     * Verificar si ya tiene un retiro asociado
     */
    public function tieneRetiro(): bool
    {
        return $this->retiro()->exists();
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_medicamento', 'id_vaca', 'fecha_aplicacion', 'dosis_aplicada', 'id_personal'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Uso de medicamento {$eventName}");
    }
}
