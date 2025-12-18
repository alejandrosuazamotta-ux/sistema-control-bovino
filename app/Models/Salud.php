<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Salud extends Model
{
    use LogsActivity;
    protected $table = 'salud';
    protected $primaryKey = 'id_salud';
    
    protected $fillable = [
        'id_vaca',
        'tipo_registro',
        'tipo_prueba',
        'fecha',
        'descripcion',
        'resultado_prueba',
        'resultado',
        'fecha_resultado',
        'severidad',
        'tratamiento_sugerido',
        'restriccion_ordeño',
        'inhabilitada',
        'acta',
        'responsable_prueba',
        'observaciones',
        'id_personal'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'fecha_resultado' => 'date',
        'restriccion_ordeño' => 'boolean',
        'inhabilitada' => 'boolean',
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
     * Scopes para consultas frecuentes
     */
    public function scopePruebasSanitarias($query)
    {
        return $query->whereIn('tipo_registro', ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis']);
    }

    public function scopeMastitis($query)
    {
        return $query->where('tipo_prueba', 'Mastitis');
    }

    public function scopeBrucelosis($query)
    {
        return $query->where('tipo_prueba', 'Brucelosis');
    }

    public function scopeTuberculosis($query)
    {
        return $query->where('tipo_prueba', 'Tuberculosis');
    }

    public function scopeResultadoPositivo($query)
    {
        return $query->where('resultado', 'Positivo');
    }

    public function scopeConRestriccionOrdeño($query)
    {
        return $query->where('restriccion_ordeño', true);
    }

    public function scopeInhabilitadas($query)
    {
        return $query->where('inhabilitada', true);
    }

    /**
     * Accessors
     */
    public function getEsPruebaSanitariaAttribute(): bool
    {
        return in_array($this->tipo_registro, ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis']);
    }

    public function getRequiereAccionAttribute(): bool
    {
        return $this->resultado === 'Positivo' && ($this->restriccion_ordeño || $this->inhabilitada);
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_vaca', 'tipo_registro', 'tipo_prueba', 'resultado', 'fecha', 'restriccion_ordeño', 'inhabilitada'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Registro de salud {$eventName}");
    }
}
