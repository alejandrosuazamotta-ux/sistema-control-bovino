@extends('layouts.master')

@section('title', 'Mortalidad - Consulta')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-skull"></i> Mortalidad
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Mortalidad</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <!-- Estadísticas -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $estadisticas['total_mortalidad'] ?? 0 }}</h3>
                        <p>Total de Muertes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-skull"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $estadisticas['mortalidad_este_mes'] ?? 0 }}</h3>
                        <p>Muertes Este Mes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticas['mortalidad_ultimos_30_dias'] ?? 0 }}</h3>
                        <p>Últimos 30 Días</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $estadisticas['mortalidad_por_tipo']['vacas'] ?? 0 }}</h3>
                        <p>Vacas / {{ $estadisticas['mortalidad_por_tipo']['crias'] ?? 0 }} Crías</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cow"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            @if(isset($datosGraficas['mortalidad_por_mes']) && count($datosGraficas['mortalidad_por_mes']['labels']) > 0)
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Mortalidad por Mes (Últimos 12 meses)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaMortalidadPorMes" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datosGraficas['por_tipo_animal']) && count($datosGraficas['por_tipo_animal']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Mortalidad por Tipo de Animal
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaPorTipoAnimal" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datosGraficas['por_clasificacion']) && count($datosGraficas['por_clasificacion']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Mortalidad por Clasificación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaPorClasificacion" style="height: 300px;"></div>
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
                <form method="GET" action="{{ route('pasante.mortalidad.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">Buscar</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="Código, causa, acta...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="animal_type">Tipo de Animal</label>
                                <select name="animal_type" id="animal_type" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="App\Models\Vaca" {{ request('animal_type') == 'App\Models\Vaca' ? 'selected' : '' }}>Vaca</option>
                                    <option value="App\Models\Cria" {{ request('animal_type') == 'App\Models\Cria' ? 'selected' : '' }}>Cría</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="clasificacion">Clasificación</label>
                                <select name="clasificacion" id="clasificacion" class="form-control">
                                    <option value="">Todas</option>
                                    <option value="Ternero" {{ request('clasificacion') == 'Ternero' ? 'selected' : '' }}>Ternero</option>
                                    <option value="Novilla" {{ request('clasificacion') == 'Novilla' ? 'selected' : '' }}>Novilla</option>
                                    <option value="Vaca" {{ request('clasificacion') == 'Vaca' ? 'selected' : '' }}>Vaca</option>
                                    <option value="Toro" {{ request('clasificacion') == 'Toro' ? 'selected' : '' }}>Toro</option>
                                    <option value="Becerro" {{ request('clasificacion') == 'Becerro' ? 'selected' : '' }}>Becerro</option>
                                    <option value="Becerra" {{ request('clasificacion') == 'Becerra' ? 'selected' : '' }}>Becerra</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Desde</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fecha_fin">Fecha Hasta</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('pasante.mortalidad.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Mortalidad -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Listado de Mortalidad
                </h3>
                <div class="card-tools">
                    <a href="{{ route('pasante.mortalidad.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Animal</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Clasificación</th>
                            <th>Causa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mortalidades as $mortalidad)
                            <tr>
                                <td>{{ $mortalidad->id_mortalidad }}</td>
                                <td>
                                    @if($mortalidad->animal)
                                        @if($mortalidad->esVaca())
                                            <strong>{{ $mortalidad->animal->codigo }}</strong>
                                        @else
                                            <strong>{{ $mortalidad->animal->nombre_cria ?? 'Cría #' . $mortalidad->animal_id }}</strong>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($mortalidad->esVaca())
                                        <span class="badge badge-primary"><i class="fas fa-cow"></i> Vaca</span>
                                    @else
                                        <span class="badge badge-info"><i class="fas fa-baby"></i> Cría</span>
                                    @endif
                                </td>
                                <td>{{ $mortalidad->fecha->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $mortalidad->clasificacion }}</span>
                                </td>
                                <td>
                                    <span title="{{ $mortalidad->causa }}">
                                        {{ Str::limit($mortalidad->causa, 50) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pasante.mortalidad.show', $mortalidad->id_mortalidad) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <p class="text-muted mt-3">No se encontraron registros de mortalidad.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $mortalidades->links() }}
            </div>
        </div>
    </div>
</section>

@push('scripts')
@if(isset($datosGraficas))
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($datosGraficas['mortalidad_por_mes']) && count($datosGraficas['mortalidad_por_mes']['labels']) > 0)
    // Gráfica de Mortalidad por Mes
    var optionsMortalidadPorMes = {
        series: [{
            name: 'Muertes',
            data: @json($datosGraficas['mortalidad_por_mes']['data'])
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: true }
        },
        colors: ['#dc3545'],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: @json($datosGraficas['mortalidad_por_mes']['labels'])
        },
        yaxis: {
            title: { text: 'Cantidad de Muertes' }
        },
        tooltip: {
            y: { formatter: function(val) { return val + ' muertes' } }
        }
    };
    var chartMortalidadPorMes = new ApexCharts(document.querySelector("#graficaMortalidadPorMes"), optionsMortalidadPorMes);
    chartMortalidadPorMes.render();
    @endif

    @if(isset($datosGraficas['por_tipo_animal']) && count($datosGraficas['por_tipo_animal']['labels']) > 0)
    // Gráfica por Tipo de Animal
    var optionsPorTipoAnimal = {
        series: @json($datosGraficas['por_tipo_animal']['data']),
        chart: {
            type: 'donut',
            height: 300
        },
        labels: @json($datosGraficas['por_tipo_animal']['labels']),
        colors: ['#007bff', '#17a2b8'],
        legend: { position: 'bottom' }
    };
    var chartPorTipoAnimal = new ApexCharts(document.querySelector("#graficaPorTipoAnimal"), optionsPorTipoAnimal);
    chartPorTipoAnimal.render();
    @endif

    @if(isset($datosGraficas['por_clasificacion']) && count($datosGraficas['por_clasificacion']['labels']) > 0)
    // Gráfica por Clasificación
    var optionsPorClasificacion = {
        series: [{
            name: 'Muertes',
            data: @json($datosGraficas['por_clasificacion']['data'])
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: true }
        },
        colors: ['#6c757d'],
        xaxis: {
            categories: @json($datosGraficas['por_clasificacion']['labels'])
        },
        yaxis: {
            title: { text: 'Cantidad' }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%'
            }
        }
    };
    var chartPorClasificacion = new ApexCharts(document.querySelector("#graficaPorClasificacion"), optionsPorClasificacion);
    chartPorClasificacion.render();
    @endif
});
</script>
@endif
@endpush
@endsection

