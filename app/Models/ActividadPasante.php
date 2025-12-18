<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActividadPasante extends Model
{
    protected $table = 'actividades_pasantes';
    protected $primaryKey = 'id_actividad';
    
    protected $fillable = [
        'user_id', 'titulo', 'descripcion', 'tipo_actividad', 'fecha_actividad',
        'hora_inicio', 'hora_fin', 'estado', 'observaciones', 'evidencia_foto',
        'evidencia_documento', 'aprobada', 'aprobada_por', 'fecha_aprobacion'
    ];
    
    protected $casts = [
        'fecha_actividad' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'aprobada' => 'boolean',
        'fecha_aprobacion' => 'datetime'
    ];
    
    // Relaciones
    public function pasante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobada_por');
    }
    
    // Scopes
    public function scopePorPasante($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_actividad', $tipo);
    }
    
    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }
    
    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }
    
    public function scopePendientesAprobacion($query)
    {
        return $query->where('aprobada', false)->where('estado', 'Completada');
    }
}
