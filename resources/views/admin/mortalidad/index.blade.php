@extends('layouts.master')

@section('title', 'Gestión de Mortalidad')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-skull"></i> Gestión de Mortalidad
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Mortalidad</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Estadísticas -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $estadisticas['total_mortalidad'] }}</h3>
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
                        <h3>{{ $estadisticas['mortalidad_este_mes'] }}</h3>
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
                        <h3>{{ $estadisticas['mortalidad_ultimos_30_dias'] }}</h3>
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
                        <h3>{{ $estadisticas['mortalidad_por_tipo']['vacas'] }}</h3>
                        <p>Vacas / {{ $estadisticas['mortalidad_por_tipo']['crias'] }} Crías</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Registros de Mortalidad
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.mortalidad.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    <a href="{{ route('admin.mortalidad.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.mortalidad.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.mortalidad.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" action="{{ route('admin.mortalidad.index') }}" class="form-inline mb-3">
                    <div class="form-group mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por código, causa..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="form-group mr-2">
                        <select name="animal_type" class="form-control">
                            <option value="">Todos los tipos</option>
                            <option value="App\Models\Vaca" {{ request('animal_type') == 'App\Models\Vaca' ? 'selected' : '' }}>Vacas</option>
                            <option value="App\Models\Cria" {{ request('animal_type') == 'App\Models\Cria' ? 'selected' : '' }}>Crías</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select name="clasificacion" class="form-control">
                            <option value="">Todas las clasificaciones</option>
                            <option value="Ternero" {{ request('clasificacion') == 'Ternero' ? 'selected' : '' }}>Ternero</option>
                            <option value="Novilla" {{ request('clasificacion') == 'Novilla' ? 'selected' : '' }}>Novilla</option>
                            <option value="Vaca" {{ request('clasificacion') == 'Vaca' ? 'selected' : '' }}>Vaca</option>
                            <option value="Toro" {{ request('clasificacion') == 'Toro' ? 'selected' : '' }}>Toro</option>
                            <option value="Becerro" {{ request('clasificacion') == 'Becerro' ? 'selected' : '' }}>Becerro</option>
                            <option value="Becerra" {{ request('clasificacion') == 'Becerra' ? 'selected' : '' }}>Becerra</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <input type="date" name="fecha_inicio" class="form-control" placeholder="Fecha inicio" 
                               value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="form-group mr-2">
                        <input type="date" name="fecha_fin" class="form-control" placeholder="Fecha fin" 
                               value="{{ request('fecha_fin') }}">
                    </div>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.mortalidad.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </form>

                @if($mortalidades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Animal</th>
                                    <th>Tipo</th>
                                    <th>Clasificación</th>
                                    <th>Peso (kg)</th>
                                    <th>Causa</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mortalidades as $mortalidad)
                                    <tr>
                                        <td>{{ $mortalidad->id_mortalidad }}</td>
                                        <td>{{ $mortalidad->fecha->format('d/m/Y') }}</td>
                                        <td>{{ $mortalidad->hora ? \Carbon\Carbon::parse($mortalidad->hora)->format('H:i') : '-' }}</td>
                                        <td>
                                            @if($mortalidad->animal)
                                                @if($mortalidad->esVaca())
                                                    <span class="badge badge-primary">
                                                        <i class="fas fa-cow"></i> {{ $mortalidad->animal->codigo }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-baby"></i> {{ $mortalidad->animal->nombre_cria ?? 'Cría #' . $mortalidad->animal_id }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($mortalidad->esVaca())
                                                <span class="badge badge-primary">Vaca</span>
                                            @else
                                                <span class="badge badge-info">Cría</span>
                                            @endif
                                        </td>
                                        <td>{{ $mortalidad->clasificacion }}</td>
                                        <td>{{ $mortalidad->peso ? number_format($mortalidad->peso, 2) . ' kg' : '-' }}</td>
                                        <td>
                                            <span title="{{ $mortalidad->causa }}">
                                                {{ Str::limit($mortalidad->causa, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.mortalidad.show', $mortalidad->id_mortalidad) }}" 
                                                   class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.mortalidad.edit', $mortalidad->id_mortalidad) }}" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.mortalidad.destroy', $mortalidad->id_mortalidad) }}" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('¿Está seguro de eliminar este registro?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $mortalidades->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No se encontraron registros de mortalidad.
                    </div>
                @endif
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Mortalidad por Mes -->
            @if(isset($datosGraficas['mortalidad_por_mes']) && count($datosGraficas['mortalidad_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Mortalidad por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartMortalidadPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Por Tipo de Animal -->
            @if(isset($datosGraficas['por_tipo_animal']) && count($datosGraficas['por_tipo_animal']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Mortalidad por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPorTipoAnimal" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Por Clasificación -->
            @if(isset($datosGraficas['por_clasificacion']) && count($datosGraficas['por_clasificacion']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Por Clasificación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPorClasificacion" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para las gráficas
    const datosMortalidadPorMes = @json($datosGraficas['mortalidad_por_mes'] ?? ['labels' => [], 'data' => []]);
    const datosPorTipoAnimal = @json($datosGraficas['por_tipo_animal'] ?? ['labels' => [], 'data' => []]);
    const datosPorClasificacion = @json($datosGraficas['por_clasificacion'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Mortalidad por Mes (Línea)
    if (datosMortalidadPorMes.labels.length > 0) {
        const chartMortalidadPorMes = new ApexCharts(document.querySelector("#chartMortalidadPorMes"), {
            series: [{
                name: 'Mortalidad',
                data: datosMortalidadPorMes.data
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: true },
                zoom: { enabled: false }
            },
            colors: ['#DC3545'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: datosMortalidadPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' muertes' } }
            }
        });
        chartMortalidadPorMes.render();
    }

    // Gráfica: Por Tipo de Animal (Donut)
    if (datosPorTipoAnimal.labels.length > 0) {
        const chartPorTipoAnimal = new ApexCharts(document.querySelector("#chartPorTipoAnimal"), {
            series: datosPorTipoAnimal.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosPorTipoAnimal.labels,
            colors: ['#FFC107', '#DC3545'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' muertes' } }
            }
        });
        chartPorTipoAnimal.render();
    }

    // Gráfica: Por Clasificación (Barras)
    if (datosPorClasificacion.labels.length > 0) {
        const chartPorClasificacion = new ApexCharts(document.querySelector("#chartPorClasificacion"), {
            series: [{
                name: 'Cantidad',
                data: datosPorClasificacion.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: true }
            },
            colors: ['#6C757D'],
            xaxis: {
                categories: datosPorClasificacion.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false
                }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' muertes' } }
            }
        });
        chartPorClasificacion.render();
    }
});
</script>
@endpush
@endsection

