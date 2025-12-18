<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TareaPasante extends Model
{
    protected $table = 'tareas_pasantes';
    protected $primaryKey = 'id_tarea';
    
    protected $fillable = [
        'user_id', 'asignada_por', 'titulo', 'descripcion', 'prioridad', 'estado',
        'fecha_asignacion', 'fecha_limite', 'fecha_completada', 'observaciones',
        'evidencia_foto', 'evidencia_documento', 'aprobada', 'aprobada_por', 'fecha_aprobacion'
    ];
    
    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_limite' => 'date',
        'fecha_completada' => 'date',
        'aprobada' => 'boolean',
        'fecha_aprobacion' => 'datetime'
    ];
    
    // Relaciones
    public function pasante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function asignador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignada_por');
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
    
    public function scopePorPrioridad($query, string $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }
    
    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }
    
    public function scopeVencidas($query)
    {
        return $query->where('fecha_limite', '<', now())
                    ->where('estado', '!=', 'Completada');
    }
    
    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }
    
    public function scopePendientesAprobacion($query)
    {
        return $query->where('aprobada', false)->where('estado', 'Completada');
    }
    
    // Accessors
    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_limite && 
               $this->fecha_limite < now() && 
               $this->estado !== 'Completada';
    }
}
