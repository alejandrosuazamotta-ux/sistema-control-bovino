@extends('layouts.master')

@section('title', 'Reporte Sanitario')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-heartbeat"></i> Reporte Sanitario
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Sanitario</li>
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
                <form method="GET" action="{{ route('admin.reportes.sanitario') }}" class="row">
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
                                <a href="{{ route('admin.reportes.sanitario') }}" class="btn btn-secondary">
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
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $datos['registros_por_tipo']->sum('total') }}</h3>
                        <p>Total Registros</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $datos['pruebas_por_resultado']->where('resultado', 'Negativo')->first()->total ?? 0 }}</h3>
                        <p>Pruebas Negativas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $datos['pruebas_por_resultado']->where('resultado', 'Positivo')->first()->total ?? 0 }}</h3>
                        <p>Pruebas Positivas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $datos['vacas_con_restriccion']->count() }}</h3>
                        <p>Vacas con Restricción</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row">
            @if(isset($datos['graficas']['registros_por_tipo']) && count($datos['graficas']['registros_por_tipo']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Registros por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaRegistrosPorTipo" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datos['graficas']['pruebas_por_resultado']) && count($datos['graficas']['pruebas_por_resultado']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-doughnut mr-1"></i>
                            Pruebas por Resultado
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaPruebasPorResultado" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datos['graficas']['mastitis_por_severidad']) && count($datos['graficas']['mastitis_por_severidad']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Mastitis por Severidad
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaMastitisPorSeveridad" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datos['graficas']['registros_por_mes']) && count($datos['graficas']['registros_por_mes']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Registros por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaRegistrosPorMes" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Vacas con Restricción -->
        @if($datos['vacas_con_restriccion']->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-ban"></i> Vacas con Restricción de Ordeño
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.reportes.sanitario.export.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('admin.reportes.sanitario.export.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Código Vaca</th>
                            <th>Fecha Registro</th>
                            <th>Tipo Prueba</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datos['vacas_con_restriccion'] as $registro)
                            <tr>
                                <td>{{ $registro->vaca->codigo ?? 'N/A' }}</td>
                                <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                                <td>{{ $registro->tipo_prueba ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $registro->resultado == 'Positivo' ? 'danger' : 'success' }}">
                                        {{ $registro->resultado ?? 'N/A' }}
                                    </span>
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
    @if(isset($datos['graficas']['registros_por_tipo']) && count($datos['graficas']['registros_por_tipo']['labels']) > 0)
    var optionsRegistrosPorTipo = {
        series: @json($datos['graficas']['registros_por_tipo']['data']),
        chart: { type: 'donut', height: 300 },
        labels: @json($datos['graficas']['registros_por_tipo']['labels']),
        colors: ['#28a745', '#ffc107', '#dc3545', '#17a2b8'],
        legend: { position: 'bottom' }
    };
    var chartRegistrosPorTipo = new ApexCharts(document.querySelector("#graficaRegistrosPorTipo"), optionsRegistrosPorTipo);
    chartRegistrosPorTipo.render();
    @endif

    @if(isset($datos['graficas']['pruebas_por_resultado']) && count($datos['graficas']['pruebas_por_resultado']['labels']) > 0)
    var optionsPruebasPorResultado = {
        series: @json($datos['graficas']['pruebas_por_resultado']['data']),
        chart: { type: 'pie', height: 300 },
        labels: @json($datos['graficas']['pruebas_por_resultado']['labels']),
        colors: ['#28a745', '#dc3545', '#ffc107'],
        legend: { position: 'bottom' }
    };
    var chartPruebasPorResultado = new ApexCharts(document.querySelector("#graficaPruebasPorResultado"), optionsPruebasPorResultado);
    chartPruebasPorResultado.render();
    @endif

    @if(isset($datos['graficas']['mastitis_por_severidad']) && count($datos['graficas']['mastitis_por_severidad']['labels']) > 0)
    var optionsMastitisPorSeveridad = {
        series: [{ name: 'Cantidad', data: @json($datos['graficas']['mastitis_por_severidad']['data']) }],
        chart: { type: 'bar', height: 300, toolbar: { show: true } },
        colors: ['#dc3545'],
        xaxis: { categories: @json($datos['graficas']['mastitis_por_severidad']['labels']) },
        yaxis: { title: { text: 'Cantidad' } }
    };
    var chartMastitisPorSeveridad = new ApexCharts(document.querySelector("#graficaMastitisPorSeveridad"), optionsMastitisPorSeveridad);
    chartMastitisPorSeveridad.render();
    @endif

    @if(isset($datos['graficas']['registros_por_mes']) && count($datos['graficas']['registros_por_mes']['labels']) > 0)
    var optionsRegistrosPorMes = {
        series: [{ name: 'Registros', data: @json($datos['graficas']['registros_por_mes']['data']) }],
        chart: { type: 'line', height: 300, toolbar: { show: true } },
        colors: ['#17a2b8'],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { categories: @json($datos['graficas']['registros_por_mes']['labels']) },
        yaxis: { title: { text: 'Cantidad de Registros' } }
    };
    var chartRegistrosPorMes = new ApexCharts(document.querySelector("#graficaRegistrosPorMes"), optionsRegistrosPorMes);
    chartRegistrosPorMes.render();
    @endif
});
</script>
@endpush
@endsection

