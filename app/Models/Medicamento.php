<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Medicamento extends Model
{
    use LogsActivity;
    protected $table = 'medicamentos';
    protected $primaryKey = 'id_medicamento';
    
    protected $fillable = [
        'nombre',
        'tipo',
        'principio_activo',
        'via_administracion',
        'dosis',
        'periodo_retiro_dias',
        'activo',
        'observaciones'
    ];
    
    protected $casts = [
        'periodo_retiro_dias' => 'integer',
        'activo' => 'boolean'
    ];
    
    /**
     * Relación con usos de medicamentos
     */
    public function usosMedicamentos(): HasMany
    {
        return $this->hasMany(UsoMedicamento::class, 'id_medicamento', 'id_medicamento');
    }
    
    /**
     * Relación con retiros generados
     */
    public function retiros(): HasMany
    {
        return $this->hasManyThrough(
            Retiro::class,
            UsoMedicamento::class,
            'id_medicamento',
            'id_uso_medicamento',
            'id_medicamento',
            'id_uso'
        );
    }
    
    /**
     * Scope para medicamentos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    
    /**
     * Verificar si tiene retiro de ordeño
     */
    public function tieneRetiroOrdeño(): bool
    {
        return $this->periodo_retiro_dias > 0;
    }

    /**
     * Configuración de auditoría Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'tipo', 'principio_activo', 'via_administracion', 'dosis', 'periodo_retiro_dias', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Medicamento {$eventName}");
    }
}
