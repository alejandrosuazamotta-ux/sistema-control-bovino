@extends('layouts.master')

@section('title', 'Reporte de Producción')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-glass-water"></i> Reporte de Producción
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Producción</li>
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
                <form method="GET" action="{{ route('admin.reportes.produccion') }}" class="row">
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
                                <a href="{{ route('admin.reportes.produccion') }}" class="btn btn-secondary">
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
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ number_format($datos['total_produccion'], 2) }} L</h3>
                        <p>Total Producción</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-glass-water"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($datos['promedio_diario'], 2) }} L</h3>
                        <p>Promedio Diario</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $datos['dias_con_produccion'] }}</h3>
                        <p>Días con Producción</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $datos['top_vacas']->count() }}</h3>
                        <p>Top Vacas Analizadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cow"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row">
            @if(isset($datos['graficas']['produccion_diaria']) && count($datos['graficas']['produccion_diaria']['labels']) > 0)
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Producción Diaria
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaProduccionDiaria" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datos['graficas']['produccion_por_turno']) && count($datos['graficas']['produccion_por_turno']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Producción por Turno
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaProduccionPorTurno" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datos['graficas']['produccion_por_destino']) && count($datos['graficas']['produccion_por_destino']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-doughnut mr-1"></i>
                            Producción por Destino
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaProduccionPorDestino" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Top Vacas Productoras -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy"></i> Top 10 Vacas Productoras
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.reportes.produccion.export.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('admin.reportes.produccion.export.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código Vaca</th>
                            <th>Total Producción (L)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datos['top_vacas'] as $index => $vaca)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $vaca->vaca->codigo ?? 'N/A' }}</td>
                                <td><strong>{{ number_format($vaca->total_leche, 2) }} L</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay datos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($datos['graficas']['produccion_diaria']) && count($datos['graficas']['produccion_diaria']['labels']) > 0)
    // Gráfica Producción Diaria
    var optionsProduccionDiaria = {
        series: [{
            name: 'Producción (L)',
            data: @json($datos['graficas']['produccion_diaria']['data'])
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: { show: true }
        },
        colors: ['#007bff'],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: @json($datos['graficas']['produccion_diaria']['labels'])
        },
        yaxis: {
            title: { text: 'Litros' }
        },
        tooltip: {
            y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
        }
    };
    var chartProduccionDiaria = new ApexCharts(document.querySelector("#graficaProduccionDiaria"), optionsProduccionDiaria);
    chartProduccionDiaria.render();
    @endif

    @if(isset($datos['graficas']['produccion_por_turno']) && count($datos['graficas']['produccion_por_turno']['labels']) > 0)
    // Gráfica Producción por Turno
    var optionsProduccionPorTurno = {
        series: @json($datos['graficas']['produccion_por_turno']['data']),
        chart: {
            type: 'donut',
            height: 300
        },
        labels: @json($datos['graficas']['produccion_por_turno']['labels']),
        colors: ['#28a745', '#ffc107', '#17a2b8'],
        legend: { position: 'bottom' }
    };
    var chartProduccionPorTurno = new ApexCharts(document.querySelector("#graficaProduccionPorTurno"), optionsProduccionPorTurno);
    chartProduccionPorTurno.render();
    @endif

    @if(isset($datos['graficas']['produccion_por_destino']) && count($datos['graficas']['produccion_por_destino']['labels']) > 0)
    // Gráfica Producción por Destino
    var optionsProduccionPorDestino = {
        series: @json($datos['graficas']['produccion_por_destino']['data']),
        chart: {
            type: 'pie',
            height: 300
        },
        labels: @json($datos['graficas']['produccion_por_destino']['labels']),
        colors: ['#dc3545', '#28a745', '#ffc107'],
        legend: { position: 'bottom' }
    };
    var chartProduccionPorDestino = new ApexCharts(document.querySelector("#graficaProduccionPorDestino"), optionsProduccionPorDestino);
    chartProduccionPorDestino.render();
    @endif
});
</script>
@endpush
@endsection
