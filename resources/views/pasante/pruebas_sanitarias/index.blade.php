@extends('layouts.master')

@section('title', 'Pruebas Sanitarias - Consulta')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-vial"></i> Pruebas Sanitarias
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pruebas Sanitarias</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticas['total'] ?? 0 }}</h3>
                        <p>Total Pruebas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-vial"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $estadisticas['positivas'] ?? 0 }}</h3>
                        <p>Resultados Positivos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $estadisticas['pendientes'] ?? 0 }}</h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $estadisticas['negativas'] ?? 0 }}</h3>
                        <p>Resultados Negativos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas ApexCharts (Solo lectura) -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            @if(isset($datosGraficas['por_tipo']) && count($datosGraficas['por_tipo']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Pruebas por Tipo (30 días)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPruebasPorTipo" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datosGraficas['por_resultado']) && count($datosGraficas['por_resultado']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Pruebas por Resultado (30 días)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPruebasPorResultado" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datosGraficas['por_mes']) && count($datosGraficas['por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Pruebas por Mes (12 meses)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPruebasPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

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
                <form method="GET" action="{{ route('pasante.pruebas-sanitarias.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo_prueba">Tipo de Prueba</label>
                                <select name="tipo_prueba" id="tipo_prueba" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="Mastitis" {{ request('tipo_prueba') == 'Mastitis' ? 'selected' : '' }}>Mastitis</option>
                                    <option value="Brucelosis" {{ request('tipo_prueba') == 'Brucelosis' ? 'selected' : '' }}>Brucelosis</option>
                                    <option value="Tuberculosis" {{ request('tipo_prueba') == 'Tuberculosis' ? 'selected' : '' }}>Tuberculosis</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="resultado">Resultado</label>
                                <select name="resultado" id="resultado" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="Positivo" {{ request('resultado') == 'Positivo' ? 'selected' : '' }}>Positivo</option>
                                    <option value="Negativo" {{ request('resultado') == 'Negativo' ? 'selected' : '' }}>Negativo</option>
                                    <option value="Pendiente" {{ request('resultado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fecha_desde">Fecha Desde</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fecha_hasta">Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('pasante.pruebas-sanitarias.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Pruebas Sanitarias -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Listado de Pruebas Sanitarias
                </h3>
                <div class="card-tools">
                    <a href="{{ route('pasante.pruebas-sanitarias.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Prueba
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vaca</th>
                            <th>Tipo</th>
                            <th>Fecha Prueba</th>
                            <th>Resultado</th>
                            <th>Restricción Ordeño</th>
                            <th>Inhabilitada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pruebas as $prueba)
                            <tr>
                                <td>{{ $prueba->id_prueba }}</td>
                                <td>
                                    <strong>{{ $prueba->vaca->codigo ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $prueba->vaca->raza ?? 'Sin raza' }}</small>
                                </td>
                                <td>
                                    @if($prueba->tipo_prueba === 'Mastitis')
                                        <span class="badge badge-warning"><i class="fas fa-vial"></i> Mastitis</span>
                                    @elseif($prueba->tipo_prueba === 'Brucelosis')
                                        <span class="badge badge-danger"><i class="fas fa-biohazard"></i> Brucelosis</span>
                                    @elseif($prueba->tipo_prueba === 'Tuberculosis')
                                        <span class="badge badge-danger"><i class="fas fa-lungs"></i> Tuberculosis</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $prueba->tipo_prueba }}</span>
                                    @endif
                                </td>
                                <td>{{ $prueba->fecha_prueba->format('d/m/Y') }}</td>
                                <td>
                                    @if($prueba->resultado === 'Positivo')
                                        <span class="badge badge-danger">{{ $prueba->resultado }}</span>
                                    @elseif($prueba->resultado === 'Negativo')
                                        <span class="badge badge-success">{{ $prueba->resultado }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $prueba->resultado }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($prueba->restriccion_ordeño)
                                        <span class="badge badge-danger"><i class="fas fa-ban"></i> Sí</span>
                                    @else
                                        <span class="badge badge-success">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($prueba->inhabilitada)
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Sí</span>
                                    @else
                                        <span class="badge badge-success">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pasante.pruebas-sanitarias.show', $prueba->id_prueba) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <p class="text-muted mt-3">No se encontraron pruebas sanitarias.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $pruebas->links() }}
            </div>
        </div>
    </div>
</section>

@push('scripts')
@if(isset($datosGraficas))
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($datosGraficas['por_tipo']) && count($datosGraficas['por_tipo']['labels']) > 0)
    // Gráfica: Pruebas por Tipo (Donut)
    var optionsPruebasPorTipo = {
        series: @json($datosGraficas['por_tipo']['data']),
        chart: {
            type: 'donut',
            height: 300
        },
        labels: @json($datosGraficas['por_tipo']['labels']),
        colors: ['#ffc107', '#dc3545', '#17a2b8'],
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: { formatter: function(val) { return val + ' pruebas' } }
        }
    };
    var chartPruebasPorTipo = new ApexCharts(document.querySelector("#chartPruebasPorTipo"), optionsPruebasPorTipo);
    chartPruebasPorTipo.render();
    @endif

    @if(isset($datosGraficas['por_resultado']) && count($datosGraficas['por_resultado']['labels']) > 0)
    // Gráfica: Pruebas por Resultado (Barras)
    var optionsPruebasPorResultado = {
        series: [{
            name: 'Cantidad',
            data: @json($datosGraficas['por_resultado']['data'])
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: true }
        },
        colors: ['#dc3545', '#28a745', '#ffc107'],
        xaxis: {
            categories: @json($datosGraficas['por_resultado']['labels'])
        },
        yaxis: {
            title: { text: 'Cantidad de Pruebas' }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false
            }
        },
        tooltip: {
            y: { formatter: function(val) { return val + ' pruebas' } }
        }
    };
    var chartPruebasPorResultado = new ApexCharts(document.querySelector("#chartPruebasPorResultado"), optionsPruebasPorResultado);
    chartPruebasPorResultado.render();
    @endif

    @if(isset($datosGraficas['por_mes']) && count($datosGraficas['por_mes']['labels']) > 0)
    // Gráfica: Pruebas por Mes (Línea)
    var optionsPruebasPorMes = {
        series: [{
            name: 'Pruebas',
            data: @json($datosGraficas['por_mes']['data'])
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: true }
        },
        colors: ['#17a2b8'],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: @json($datosGraficas['por_mes']['labels'])
        },
        yaxis: {
            title: { text: 'Cantidad de Pruebas' }
        },
        tooltip: {
            y: { formatter: function(val) { return val + ' pruebas' } }
        }
    };
    var chartPruebasPorMes = new ApexCharts(document.querySelector("#chartPruebasPorMes"), optionsPruebasPorMes);
    chartPruebasPorMes.render();
    @endif
});
</script>
@endif
@endpush
@endsection

