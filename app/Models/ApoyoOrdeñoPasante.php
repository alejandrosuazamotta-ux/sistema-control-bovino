<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApoyoOrdeñoPasante extends Model
{
    protected $table = 'apoyo_ordeno_pasantes';
    protected $primaryKey = 'id_apoyo_ordeno';
    
    protected $fillable = [
        'user_id', 'id_vaca', 'fecha_ordeno', 'turno', 'cantidad_leche',
        'observaciones', 'calidad_leche', 'evidencia_foto', 'aprobada',
        'aprobada_por', 'fecha_aprobacion'
    ];
    
    protected $casts = [
        'fecha_ordeno' => 'date',
        'cantidad_leche' => 'decimal:2',
        'aprobada' => 'boolean',
        'fecha_aprobacion' => 'datetime'
    ];
    
    // Relaciones
    public function pasante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function vaca(): BelongsTo
    {
        return $this->belongsTo(Vaca::class, 'id_vaca', 'id_vaca');
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
    
    public function scopePorTurno($query, string $turno)
    {
        return $query->where('turno', $turno);
    }
    
    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }
    
    public function scopePendientesAprobacion($query)
    {
        return $query->where('aprobada', false);
    }
}

