<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApoyoReproductivoPasante extends Model
{
    protected $table = 'apoyo_reproductivo_pasantes';
    protected $primaryKey = 'id_apoyo_reproductivo';
    
    protected $fillable = [
        'user_id', 'id_vaca', 'fecha_actividad', 'tipo_actividad', 'observaciones',
        'evidencia_foto', 'evidencia_documento', 'aprobada', 'aprobada_por', 'fecha_aprobacion'
    ];
    
    protected $casts = [
        'fecha_actividad' => 'date',
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
    
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_actividad', $tipo);
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
