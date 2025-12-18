<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsDashboardCache
{
    /**
     * Limpiar caché del dashboard cuando se actualizan datos críticos
     */
    protected static function bootClearsDashboardCache()
    {
        static::created(function () {
            static::clearDashboardCache();
        });

        static::updated(function () {
            static::clearDashboardCache();
        });

        static::deleted(function () {
            static::clearDashboardCache();
        });
    }

    /**
     * Limpiar todas las claves de caché del dashboard
     */
    protected static function clearDashboardCache(): void
    {
        $keys = [
            'dashboard.estadisticas_generales',
            'dashboard.produccion_diaria.30',
            'dashboard.produccion_mensual.12',
            'dashboard.vacas_por_estado',
            'dashboard.produccion_por_potrero',
            'dashboard.ranking_vacas.10',
            'dashboard.estado_reproductivo',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Limpiar también variaciones con diferentes parámetros
        for ($i = 1; $i <= 90; $i++) {
            Cache::forget("dashboard.produccion_diaria.{$i}");
        }

        for ($i = 1; $i <= 24; $i++) {
            Cache::forget("dashboard.produccion_mensual.{$i}");
            Cache::forget("dashboard.ranking_vacas.{$i}");
        }
    }
}

