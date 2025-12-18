@extends('layouts.master')

@section('title', 'Reporte de Salud')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-heartbeat mr-2"></i>
                    Reporte de Salud
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reporte de Salud</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filtros -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter mr-2"></i>
                            Filtros de Fecha
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.reportes.salud') }}" class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                           value="{{ $fechaInicio }}" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_fin">Fecha de Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                           value="{{ $fechaFin }}" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search mr-1"></i> Filtrar
                                        </button>
                                        <a href="{{ route('admin.reportes.salud') }}" class="btn btn-secondary">
                                            <i class="fas fa-undo mr-1"></i> Limpiar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalRegistros }}</h3>
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
                        <h3>{{ $registrosPorTipo->where('tipo_registro', 'Vacunación')->first()->total ?? 0 }}</h3>
                        <p>Vacunaciones</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-syringe"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $registrosPorTipo->where('tipo_registro', 'Tratamiento')->first()->total ?? 0 }}</h3>
                        <p>Tratamientos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $registrosPorTipo->where('tipo_registro', 'Prueba mastitis')->first()->total ?? 0 }}</h3>
                        <p>Pruebas Mastitis</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-microscope"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-2"></i>
                            Registros por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartRegistrosTipo" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-2"></i>
                            Registros Mensuales
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartRegistrosMensual" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vacas con más registros -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Vacas con Más Registros de Salud
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($vacasConMasRegistros->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Posición</th>
                                            <th>Código de Vaca</th>
                                            <th>Total Registros</th>
                                            <th>Promedio Mensual</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vacasConMasRegistros as $index => $vaca)
                                            <tr>
                                                <td>
                                                    @if($index == 0)
                                                        <span class="badge badge-warning">🥇 1º</span>
                                                    @elseif($index == 1)
                                                        <span class="badge badge-secondary">🥈 2º</span>
                                                    @elseif($index == 2)
                                                        <span class="badge badge-info">🥉 3º</span>
                                                    @else
                                                        <span class="badge badge-light">{{ $index + 1 }}º</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $vaca->codigo }}</strong>
                                                </td>
                                                <td>
                                                    <span class="text-primary font-weight-bold">
                                                        {{ $vaca->total_registros }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ number_format($vaca->total_registros / 12, 1) }}
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Ver Historial
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay registros de salud en el período seleccionado</h5>
                                <p class="text-muted">Intenta cambiar las fechas del filtro para ver más datos.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle por tipo de registro -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table mr-2"></i>
                            Detalle por Tipo de Registro
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($registrosPorTipo->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="tablaRegistros">
                                    <thead>
                                        <tr>
                                            <th>Tipo de Registro</th>
                                            <th>Cantidad</th>
                                            <th>Porcentaje</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registrosPorTipo as $registro)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-{{ 
                                                        $registro->tipo_registro == 'Vacunación' ? 'success' : 
                                                        ($registro->tipo_registro == 'Tratamiento' ? 'warning' : 
                                                        ($registro->tipo_registro == 'Prueba mastitis' ? 'danger' : 'info'))
                                                    }}">
                                                        {{ $registro->tipo_registro }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong>{{ $registro->total }}</strong>
                                                </td>
                                                <td>
                                                    {{ $totalRegistros > 0 ? round(($registro->total / $totalRegistros) * 100, 1) : 0 }}%
                                                </td>
                                                <td>
                                                    @if($registro->tipo_registro == 'Vacunación')
                                                        <small class="text-muted">Registros de vacunación preventiva</small>
                                                    @elseif($registro->tipo_registro == 'Tratamiento')
                                                        <small class="text-muted">Tratamientos médicos aplicados</small>
                                                    @elseif($registro->tipo_registro == 'Prueba mastitis')
                                                        <small class="text-muted">Pruebas de detección de mastitis</small>
                                                    @else
                                                        <small class="text-muted">Otros registros de salud</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay datos de registros de salud</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Gráfico de registros por tipo
    const ctxTipo = document.getElementById('chartRegistrosTipo').getContext('2d');
    new Chart(ctxTipo, {
        type: 'pie',
        data: {
            labels: {!! json_encode($registrosPorTipo->pluck('tipo_registro')) !!},
            datasets: [{
                data: {!! json_encode($registrosPorTipo->pluck('total')) !!},
                backgroundColor: [
                    '#28a745', // Vacunación
                    '#ffc107', // Tratamiento
                    '#dc3545', // Prueba mastitis
                    '#17a2b8'  // Otro
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Distribución por Tipo de Registro'
                }
            }
        }
    });

    // Gráfico de registros mensuales
    const ctxMensual = document.getElementById('chartRegistrosMensual').getContext('2d');
    new Chart(ctxMensual, {
        type: 'line',
        data: {
            labels: {!! json_encode($registrosMensual->map(function($item) { return $item->año . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT); })) !!},
            datasets: [{
                label: 'Registros',
                data: {!! json_encode($registrosMensual->pluck('total')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Registros de Salud Mensuales'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Registros'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Mes'
                    }
                }
            }
        }
    });

    // DataTable para la tabla de registros
    $('#tablaRegistros').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[1, "desc"]],
        "pageLength": 10
    });
});
</script>
@endpush
