@extends('layouts.master')

@section('title', 'Reporte Reproductivo')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-heart"></i> Reporte Reproductivo
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Reproductivo</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filtros -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Filtros
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes.reproductivo') }}" class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
                                   value="{{ $fechaInicio }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
                                   value="{{ $fechaFin }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('admin.reportes.reproductivo') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-3">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($datos['tasa_preñez'], 2) }}%</h3>
                        <p>Tasa de Preñez</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $datos['total_inseminaciones'] }}</h3>
                        <p>Total Inseminaciones</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-syringe"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $datos['total_palpaciones_positivas'] }}</h3>
                        <p>Palpaciones Positivas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row">
            @if(isset($datos['graficas']['eventos_por_tipo']) && count($datos['graficas']['eventos_por_tipo']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Eventos por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaEventosPorTipo" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datos['graficas']['palpaciones_por_resultado']) && count($datos['graficas']['palpaciones_por_resultado']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-doughnut mr-1"></i>
                            Palpaciones por Resultado
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaPalpacionesPorResultado" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datos['graficas']['partos_por_mes']) && count($datos['graficas']['partos_por_mes']['labels']) > 0)
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Partos por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaPartosPorMes" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Vacas Próximas al Parto -->
        @if($datos['vacas_proximas_parto']->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-baby"></i> Vacas Próximas al Parto (Próximos 30 días)
                </h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Código Vaca</th>
                            <th>Fecha Probable Parto</th>
                            <th>Días Restantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datos['vacas_proximas_parto'] as $registro)
                            <tr>
                                <td>{{ $registro->vaca->codigo ?? 'N/A' }}</td>
                                <td>{{ $registro->fecha_probable_parto ? $registro->fecha_probable_parto->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @if($registro->fecha_probable_parto)
                                        <span class="badge badge-{{ now()->diffInDays($registro->fecha_probable_parto) <= 7 ? 'danger' : 'warning' }}">
                                            {{ now()->diffInDays($registro->fecha_probable_parto) }} días
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($datos['graficas']['eventos_por_tipo']) && count($datos['graficas']['eventos_por_tipo']['labels']) > 0)
    var optionsEventosPorTipo = {
        series: @json($datos['graficas']['eventos_por_tipo']['data']),
        chart: { type: 'donut', height: 300 },
        labels: @json($datos['graficas']['eventos_por_tipo']['labels']),
        colors: ['#28a745', '#17a2b8', '#ffc107', '#dc3545'],
        legend: { position: 'bottom' }
    };
    var chartEventosPorTipo = new ApexCharts(document.querySelector("#graficaEventosPorTipo"), optionsEventosPorTipo);
    chartEventosPorTipo.render();
    @endif

    @if(isset($datos['graficas']['palpaciones_por_resultado']) && count($datos['graficas']['palpaciones_por_resultado']['labels']) > 0)
    var optionsPalpacionesPorResultado = {
        series: @json($datos['graficas']['palpaciones_por_resultado']['data']),
        chart: { type: 'pie', height: 300 },
        labels: @json($datos['graficas']['palpaciones_por_resultado']['labels']),
        colors: ['#28a745', '#dc3545'],
        legend: { position: 'bottom' }
    };
    var chartPalpacionesPorResultado = new ApexCharts(document.querySelector("#graficaPalpacionesPorResultado"), optionsPalpacionesPorResultado);
    chartPalpacionesPorResultado.render();
    @endif

    @if(isset($datos['graficas']['partos_por_mes']) && count($datos['graficas']['partos_por_mes']['labels']) > 0)
    var optionsPartosPorMes = {
        series: [{ name: 'Partos', data: @json($datos['graficas']['partos_por_mes']['data']) }],
        chart: { type: 'bar', height: 300, toolbar: { show: true } },
        colors: ['#28a745'],
        xaxis: { categories: @json($datos['graficas']['partos_por_mes']['labels']) },
        yaxis: { title: { text: 'Cantidad de Partos' } }
    };
    var chartPartosPorMes = new ApexCharts(document.querySelector("#graficaPartosPorMes"), optionsPartosPorMes);
    chartPartosPorMes.render();
    @endif
});
</script>
@endpush
@endsection

