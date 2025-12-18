<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    
    protected $fillable = [
        'tipo',
        'nivel',
        'titulo',
        'mensaje',
        'entidad_tipo',
        'entidad_id',
        'fecha_referencia',
        'leida',
        'fecha_leida',
        'estado',
        'user_id',
        'fecha_atendida',
    ];
    
    protected $casts = [
        'fecha_referencia' => 'date',
        'fecha_leida' => 'datetime',
        'fecha_atendida' => 'datetime',
        'leida' => 'boolean',
    ];
    
    /**
     * Relación polimórfica con la entidad relacionada
     */
    public function entidad(): MorphTo
    {
        return $this->morphTo('entidad');
    }

    /**
     * Relación con el usuario asignado
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Scope para alertas no leídas
     */
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }
    
    /**
     * Scope para alertas leídas
     */
    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }
    
    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    
    /**
     * Scope para filtrar por nivel
     */
    public function scopePorNivel($query, string $nivel)
    {
        return $query->where('nivel', $nivel);
    }
    
    /**
     * Scope para alertas urgentes
     */
    public function scopeUrgentes($query)
    {
        return $query->where('nivel', 'urgente');
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para alertas pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para alertas vistas
     */
    public function scopeVistas($query)
    {
        return $query->where('estado', 'vista');
    }

    /**
     * Scope para alertas atendidas
     */
    public function scopeAtendidas($query)
    {
        return $query->where('estado', 'atendida');
    }

    /**
     * Scope para alertas asignadas a un usuario
     */
    public function scopeAsignadasA($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para alertas no asignadas (para admin)
     */
    public function scopeNoAsignadas($query)
    {
        return $query->whereNull('user_id');
    }
    
    /**
     * Marcar como leída
     */
    public function marcarComoLeida(): bool
    {
        $this->leida = true;
        $this->fecha_leida = now();
        return $this->save();
    }

    /**
     * Marcar como vista (para pasante)
     */
    public function marcarComoVista(): bool
    {
        $this->estado = 'vista';
        $this->leida = true;
        $this->fecha_leida = now();
        return $this->save();
    }

    /**
     * Marcar como atendida (solo admin)
     */
    public function marcarComoAtendida(): bool
    {
        $this->estado = 'atendida';
        $this->fecha_atendida = now();
        return $this->save();
    }
}
