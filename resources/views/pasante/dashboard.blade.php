@extends('layouts.master')

@section('title', 'Dashboard Pasante')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-chart-line"></i> Dashboard Pasante
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Inicio</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <!-- Tarjetas de Estadísticas -->
        <div class="row mb-3">
            <!-- Actividades -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticasActividades['total'] ?? 0 }}</h3>
                        <p>Total Actividades</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <a href="{{ route('pasante.actividades.index') }}" class="small-box-footer">
                        Ver todas <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Tareas -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $estadisticasTareas['pendientes'] ?? 0 }}</h3>
                        <p>Tareas Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <a href="{{ route('pasante.tareas.index') }}" class="small-box-footer">
                        Ver todas <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Apoyo Ordeño -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $estadisticasOrdeño['total'] ?? 0 }}</h3>
                        <p>Registros Ordeño</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-glass-water"></i>
                    </div>
                    <a href="{{ route('pasante.apoyo-ordeno.index') }}" class="small-box-footer">
                        Ver todos <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Actividades Aprobadas -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $estadisticasActividades['aprobadas'] ?? 0 }}</h3>
                        <p>Actividades Aprobadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('pasante.actividades.index', ['aprobada' => 1]) }}" class="small-box-footer">
                        Ver todas <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        <div class="row mb-3">
            <!-- Gráfica Actividades por Tipo -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Actividades por Tipo</h3>
                    </div>
                    <div class="card-body">
                        <div id="chartActividadesPorTipo"></div>
                    </div>
                </div>
            </div>

            <!-- Gráfica Tareas por Prioridad -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Tareas por Prioridad</h3>
                    </div>
                    <div class="card-body">
                        <div id="chartTareasPorPrioridad"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Gráfica Ordeño por Turno -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> Ordeño por Turno</h3>
                    </div>
                    <div class="card-body">
                        <div id="chartOrdeñoPorTurno"></div>
                    </div>
                </div>
            </div>

            <!-- Gráfica Apoyo Reproductivo por Tipo -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-area"></i> Apoyo Reproductivo por Tipo</h3>
                    </div>
                    <div class="card-body">
                        <div id="chartReproductivoPorTipo"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividades Recientes y Tareas Pendientes -->
        <div class="row">
            <!-- Actividades Recientes -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history"></i> Actividades Recientes</h3>
                    </div>
                    <div class="card-body">
                        @if($actividadesRecientes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Tipo</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($actividadesRecientes as $actividad)
                                        <tr>
                                            <td>{{ Str::limit($actividad->titulo, 30) }}</td>
                                            <td><span class="badge badge-info">{{ $actividad->tipo_actividad }}</span></td>
                                            <td>{{ $actividad->fecha_actividad->format('d/m/Y') }}</td>
                                            <td>
                                                @if($actividad->aprobada)
                                                    <span class="badge badge-success">Aprobada</span>
                                                @else
                                                    <span class="badge badge-warning">{{ $actividad->estado }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No hay actividades recientes.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tareas Pendientes -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Tareas Pendientes</h3>
                    </div>
                    <div class="card-body">
                        @if($tareasPendientes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Prioridad</th>
                                            <th>Fecha Límite</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tareasPendientes as $tarea)
                                        <tr class="{{ $tarea->esta_vencida ? 'table-danger' : '' }}">
                                            <td>{{ Str::limit($tarea->titulo, 30) }}</td>
                                            <td>
                                                @if($tarea->prioridad == 'Urgente')
                                                    <span class="badge badge-danger">Urgente</span>
                                                @elseif($tarea->prioridad == 'Alta')
                                                    <span class="badge badge-warning">Alta</span>
                                                @else
                                                    <span class="badge badge-info">{{ $tarea->prioridad }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y') : '-' }}</td>
                                            <td><span class="badge badge-warning">{{ $tarea->estado }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No hay tareas pendientes.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Gráfica Actividades por Tipo
    var actividadesPorTipo = {
        series: @json(array_values($datosGraficas['actividades_por_tipo'] ?? [])),
        chart: {
            type: 'donut',
            height: 300
        },
        labels: @json(array_keys($datosGraficas['actividades_por_tipo'] ?? [])),
        colors: ['#1F713E', '#4BAE4F', '#89C65B', '#FFC107', '#FF9800', '#2196F3'],
        legend: {
            position: 'bottom'
        }
    };
    var chartActividades = new ApexCharts(document.querySelector("#chartActividadesPorTipo"), actividadesPorTipo);
    chartActividades.render();

    // Gráfica Tareas por Prioridad
    var tareasPorPrioridad = {
        series: [{
            name: 'Tareas',
            data: @json(array_values($datosGraficas['tareas_por_prioridad'] ?? []))
        }],
        chart: {
            type: 'bar',
            height: 300
        },
        xaxis: {
            categories: @json(array_keys($datosGraficas['tareas_por_prioridad'] ?? []))
        },
        colors: ['#1F713E']
    };
    var chartTareas = new ApexCharts(document.querySelector("#chartTareasPorPrioridad"), tareasPorPrioridad);
    chartTareas.render();

    // Gráfica Ordeño por Turno
    var ordenoPorTurno = {
        series: @json(array_column($datosGraficas['ordeno_por_turno'] ?? [], 'total')),
        chart: {
            type: 'pie',
            height: 300
        },
        labels: @json(array_keys($datosGraficas['ordeno_por_turno'] ?? [])),
        colors: ['#4BAE4F', '#89C65B']
    };
    var chartOrdeño = new ApexCharts(document.querySelector("#chartOrdeñoPorTurno"), ordenoPorTurno);
    chartOrdeño.render();

    // Gráfica Reproductivo por Tipo
    var reproductivoPorTipo = {
        series: @json(array_values($datosGraficas['reproductivo_por_tipo'] ?? [])),
        chart: {
            type: 'bar',
            height: 300
        },
        xaxis: {
            categories: @json(array_keys($datosGraficas['reproductivo_por_tipo'] ?? []))
        },
        colors: ['#FF9800']
    };
    var chartReproductivo = new ApexCharts(document.querySelector("#chartReproductivoPorTipo"), reproductivoPorTipo);
    chartReproductivo.render();
</script>
@endpush
@endsection
