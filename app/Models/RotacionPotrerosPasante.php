<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RotacionPotrerosPasante extends Model
{
    protected $table = 'rotacion_potreros_pasantes';
    protected $primaryKey = 'id_rotacion';
    
    protected $fillable = [
        'user_id', 'id_potrero_origen', 'id_potrero_destino', 'id_vaca',
        'fecha_rotacion', 'motivo', 'observaciones', 'evidencia_foto',
        'aprobada', 'aprobada_por', 'fecha_aprobacion'
    ];
    
    protected $casts = [
        'fecha_rotacion' => 'date',
        'aprobada' => 'boolean',
        'fecha_aprobacion' => 'datetime'
    ];
    
    // Relaciones
    public function pasante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function potreroOrigen(): BelongsTo
    {
        return $this->belongsTo(Potrero::class, 'id_potrero_origen', 'id_potrero');
    }
    
    public function potreroDestino(): BelongsTo
    {
        return $this->belongsTo(Potrero::class, 'id_potrero_destino', 'id_potrero');
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
    
    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }
    
    public function scopePendientesAprobacion($query)
    {
        return $query->where('aprobada', false);
    }
}
