<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PruebaSanitaria extends Model
{
    use LogsActivity;

    protected $table = 'pruebas_sanitarias';
    protected $primaryKey = 'id_prueba';

    protected $fillable = [
        'id_vaca',
        'tipo_prueba',
        'fecha_prueba',
        'resultado',
        'fecha_resultado',
        'severidad',
        'tratamiento_sugerido',
        'restriccion_ordeño',
        'inhabilitada',
        'acta',
        'responsable_prueba',
        'id_personal',
        'user_id',
        'evidencia_archivo',
        'evidencia_tipo',
        'observaciones',
        'cerrada',
        'fecha_cierre'
    ];

    protected $casts = [
        'fecha_prueba' => 'date',
        'fecha_resultado' => 'date',
        'fecha_cierre' => 'datetime',
        'restriccion_ordeño' => 'boolean',
        'inhabilitada' => 'boolean',
        'cerrada' => 'boolean'
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

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
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

    public function scopeResultadoNegativo($query)
    {
        return $query->where('resultado', 'Negativo');
    }

    public function scopePendientes($query)
    {
        return $query->where('resultado', 'Pendiente');
    }

    public function scopeConRestriccionOrdeño($query)
    {
        return $query->where('restriccion_ordeño', true);
    }

    public function scopeInhabilitadas($query)
    {
        return $query->where('inhabilitada', true);
    }

    public function scopeCerradas($query)
    {
        return $query->where('cerrada', true);
    }

    public function scopeAbiertas($query)
    {
        return $query->where('cerrada', false);
    }

    public function scopePorVaca($query, int $vacaId)
    {
        return $query->where('id_vaca', $vacaId);
    }

    public function scopeRecientes($query, int $dias = 30)
    {
        return $query->where('fecha_prueba', '>=', now()->subDays($dias));
    }

    // Accessors
    public function getEsMastitisAttribute(): bool
    {
        return $this->tipo_prueba === 'Mastitis';
    }

    public function getEsBrucelosisAttribute(): bool
    {
        return $this->tipo_prueba === 'Brucelosis';
    }

    public function getEsTuberculosisAttribute(): bool
    {
        return $this->tipo_prueba === 'Tuberculosis';
    }

    public function getRequiereAccionAttribute(): bool
    {
        return $this->resultado === 'Positivo' && ($this->restriccion_ordeño || $this->inhabilitada);
    }

    public function getPuedeEditarAttribute(): bool
    {
        return !$this->cerrada;
    }

    public function getTieneEvidenciaAttribute(): bool
    {
        return !empty($this->evidencia_archivo);
    }

    // Métodos
    public function cerrar(): void
    {
        $this->cerrada = true;
        $this->fecha_cierre = now();
        $this->save();
    }

    public function abrir(): void
    {
        $this->cerrada = false;
        $this->fecha_cierre = null;
        $this->save();
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_vaca', 'tipo_prueba', 'resultado', 'fecha_prueba', 'restriccion_ordeño', 'inhabilitada', 'cerrada'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Prueba sanitaria {$eventName}");
    }
}
