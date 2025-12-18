<?php

namespace App\Repositories;

use App\Repositories\ProduccionLecheraRepository;
use App\Repositories\RegistroReproductivoRepository;
use App\Repositories\SaludRepository;
use App\Repositories\MortalidadRepository;
use App\Repositories\MedicamentoRepository;
use App\Repositories\UsoMedicamentoRepository;
use App\Models\ProduccionLechera;
use App\Models\RegistroReproductivo;
use App\Models\Salud;
use App\Models\Mortalidad;
use App\Models\Medicamento;
use App\Models\UsoMedicamento;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteRepository
{
    protected ProduccionLecheraRepository $produccionRepository;
    protected RegistroReproductivoRepository $reproductivoRepository;
    protected SaludRepository $saludRepository;
    protected MortalidadRepository $mortalidadRepository;
    protected MedicamentoRepository $medicamentoRepository;
    protected UsoMedicamentoRepository $usoMedicamentoRepository;

    public function __construct(
        ProduccionLecheraRepository $produccionRepository,
        RegistroReproductivoRepository $reproductivoRepository,
        SaludRepository $saludRepository,
        MortalidadRepository $mortalidadRepository,
        MedicamentoRepository $medicamentoRepository,
        UsoMedicamentoRepository $usoMedicamentoRepository
    ) {
        $this->produccionRepository = $produccionRepository;
        $this->reproductivoRepository = $reproductivoRepository;
        $this->saludRepository = $saludRepository;
        $this->mortalidadRepository = $mortalidadRepository;
        $this->medicamentoRepository = $medicamentoRepository;
        $this->usoMedicamentoRepository = $usoMedicamentoRepository;
    }

    /**
     * Obtener datos para reporte de producción
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function getDatosReporteProduccion(string $fechaInicio, string $fechaFin): array
    {
        // Total producción (excluyendo retiros y sanidad)
        $totalProduccion = ProduccionLechera::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinExclusiones()
            ->sum('cantidad_leche');

        // Promedio diario
        $diasConProduccion = ProduccionLechera::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinExclusiones()
            ->distinct('fecha')
            ->count('fecha');
        
        $promedioDiario = $diasConProduccion > 0 ? $totalProduccion / $diasConProduccion : 0;

        // Producción diaria
        $produccionDiaria = ProduccionLechera::selectRaw('DATE(fecha) as dia, SUM(cantidad_leche) as total_leche')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinExclusiones()
            ->groupBy('dia')
            ->orderBy('dia', 'asc')
            ->get();

        // Producción mensual
        $produccionMensual = ProduccionLechera::selectRaw('YEAR(fecha) as año, MONTH(fecha) as mes, SUM(cantidad_leche) as total_leche')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinExclusiones()
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // Producción por turno
        $produccionPorTurno = ProduccionLechera::selectRaw('turno, SUM(cantidad_leche) as total_leche')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinExclusiones()
            ->groupBy('turno')
            ->get();

        // Producción por destino
        $produccionPorDestino = ProduccionLechera::selectRaw('destino, SUM(cantidad_leche) as total_leche')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinExclusiones()
            ->groupBy('destino')
            ->get();

        // Top 10 vacas productoras
        $topVacas = ProduccionLechera::selectRaw('id_vaca, SUM(cantidad_leche) as total_leche')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinExclusiones()
            ->groupBy('id_vaca')
            ->orderBy('total_leche', 'desc')
            ->limit(10)
            ->with('vaca:id_vaca,codigo')
            ->get();

        return [
            'total_produccion' => $totalProduccion,
            'promedio_diario' => $promedioDiario,
            'dias_con_produccion' => $diasConProduccion,
            'produccion_diaria' => $produccionDiaria,
            'produccion_mensual' => $produccionMensual,
            'produccion_por_turno' => $produccionPorTurno,
            'produccion_por_destino' => $produccionPorDestino,
            'top_vacas' => $topVacas,
        ];
    }

    /**
     * Obtener datos para reporte reproductivo
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function getDatosReporteReproductivo(string $fechaInicio, string $fechaFin): array
    {
        // Eventos por tipo
        $eventosPorTipo = RegistroReproductivo::selectRaw('tipo_evento, COUNT(*) as total')
            ->whereBetween('fecha_evento', [$fechaInicio, $fechaFin])
            ->groupBy('tipo_evento')
            ->get();

        // Palpaciones por resultado
        $palpacionesPorResultado = RegistroReproductivo::selectRaw('resultado_palpacion, COUNT(*) as total')
            ->whereBetween('fecha_evento', [$fechaInicio, $fechaFin])
            ->where('tipo_evento', 'Palpación')
            ->whereNotNull('resultado_palpacion')
            ->groupBy('resultado_palpacion')
            ->get();

        // Partos por mes
        $partosPorMes = RegistroReproductivo::selectRaw('YEAR(fecha_evento) as año, MONTH(fecha_evento) as mes, COUNT(*) as total')
            ->whereBetween('fecha_evento', [$fechaInicio, $fechaFin])
            ->where('tipo_evento', 'Parto')
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // Vacas próximas al parto
        $vacasProximasParto = RegistroReproductivo::whereNotNull('fecha_probable_parto')
            ->where('fecha_probable_parto', '>=', now())
            ->where('fecha_probable_parto', '<=', now()->addDays(30))
            ->with('vaca:id_vaca,codigo')
            ->get();

        // Tasa de preñez (últimos 12 meses)
        $inseminaciones = RegistroReproductivo::where('tipo_evento', 'Inseminación Artificial')
            ->whereBetween('fecha_evento', [now()->subMonths(12), $fechaFin])
            ->count();

        $palpacionesPositivas = RegistroReproductivo::where('tipo_evento', 'Palpación')
            ->where('resultado_palpacion', 'Positivo')
            ->whereBetween('fecha_evento', [now()->subMonths(12), $fechaFin])
            ->count();

        $tasaPreñez = $inseminaciones > 0 ? ($palpacionesPositivas / $inseminaciones) * 100 : 0;

        return [
            'eventos_por_tipo' => $eventosPorTipo,
            'palpaciones_por_resultado' => $palpacionesPorResultado,
            'partos_por_mes' => $partosPorMes,
            'vacas_proximas_parto' => $vacasProximasParto,
            'tasa_preñez' => $tasaPreñez,
            'total_inseminaciones' => $inseminaciones,
            'total_palpaciones_positivas' => $palpacionesPositivas,
        ];
    }

    /**
     * Obtener datos para reporte sanitario
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function getDatosReporteSanitario(string $fechaInicio, string $fechaFin): array
    {
        // Registros por tipo
        $registrosPorTipo = Salud::selectRaw('tipo_registro, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('tipo_registro')
            ->get();

        // Pruebas sanitarias por resultado
        $pruebasPorResultado = Salud::selectRaw('resultado, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->whereNotNull('tipo_prueba')
            ->groupBy('resultado')
            ->get();

        // Mastitis por severidad
        $mastitisPorSeveridad = Salud::selectRaw('severidad, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('tipo_prueba', 'Mastitis')
            ->whereNotNull('severidad')
            ->groupBy('severidad')
            ->get();

        // Registros por mes
        $registrosPorMes = Salud::selectRaw('YEAR(fecha) as año, MONTH(fecha) as mes, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // Vacas con restricción de ordeño
        $vacasConRestriccion = Salud::where('restriccion_ordeño', true)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with('vaca:id_vaca,codigo')
            ->distinct('id_vaca')
            ->get();

        return [
            'registros_por_tipo' => $registrosPorTipo,
            'pruebas_por_resultado' => $pruebasPorResultado,
            'mastitis_por_severidad' => $mastitisPorSeveridad,
            'registros_por_mes' => $registrosPorMes,
            'vacas_con_restriccion' => $vacasConRestriccion,
        ];
    }

    /**
     * Obtener datos para reporte de mortalidad
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function getDatosReporteMortalidad(string $fechaInicio, string $fechaFin): array
    {
        // Total de muertes
        $totalMuertes = Mortalidad::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->count();

        // Muertes por tipo de animal
        $muertesPorTipo = Mortalidad::selectRaw('animal_type, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('animal_type')
            ->get();

        // Muertes por clasificación
        $muertesPorClasificacion = Mortalidad::selectRaw('clasificacion, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('clasificacion')
            ->get();

        // Muertes por causa (top 10)
        $muertesPorCausa = Mortalidad::selectRaw('causa, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('causa')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Muertes por mes
        $muertesPorMes = Mortalidad::selectRaw('YEAR(fecha) as año, MONTH(fecha) as mes, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'total_muertes' => $totalMuertes,
            'muertes_por_tipo' => $muertesPorTipo,
            'muertes_por_clasificacion' => $muertesPorClasificacion,
            'muertes_por_causa' => $muertesPorCausa,
            'muertes_por_mes' => $muertesPorMes,
        ];
    }

    /**
     * Obtener datos para reporte de medicamentos
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function getDatosReporteMedicamentos(string $fechaInicio, string $fechaFin): array
    {
        // Usos por medicamento (top 10)
        $usosPorMedicamento = UsoMedicamento::selectRaw('id_medicamento, COUNT(*) as total_usos, SUM(dosis_aplicada) as total_dosis')
            ->whereBetween('fecha_aplicacion', [$fechaInicio, $fechaFin])
            ->groupBy('id_medicamento')
            ->orderBy('total_usos', 'desc')
            ->limit(10)
            ->with('medicamento:id_medicamento,nombre')
            ->get();

        // Usos por tipo de medicamento
        $usosPorTipo = UsoMedicamento::join('medicamentos', 'uso_medicamentos.id_medicamento', '=', 'medicamentos.id_medicamento')
            ->selectRaw('medicamentos.tipo, COUNT(*) as total_usos')
            ->whereBetween('uso_medicamentos.fecha_aplicacion', [$fechaInicio, $fechaFin])
            ->groupBy('medicamentos.tipo')
            ->get();

        // Usos por mes
        $usosPorMes = UsoMedicamento::selectRaw('YEAR(fecha_aplicacion) as año, MONTH(fecha_aplicacion) as mes, COUNT(*) as total')
            ->whereBetween('fecha_aplicacion', [$fechaInicio, $fechaFin])
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // Medicamentos con stock bajo
        $medicamentosStockBajo = Medicamento::where('stock_actual', '<=', DB::raw('stock_minimo'))
            ->where('activo', true)
            ->get();

        // Medicamentos próximos a vencer (30 días)
        $medicamentosProximosVencer = Medicamento::whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays(30)])
            ->where('activo', true)
            ->get();

        return [
            'usos_por_medicamento' => $usosPorMedicamento,
            'usos_por_tipo' => $usosPorTipo,
            'usos_por_mes' => $usosPorMes,
            'medicamentos_stock_bajo' => $medicamentosStockBajo,
            'medicamentos_proximos_vencer' => $medicamentosProximosVencer,
        ];
    }
}

