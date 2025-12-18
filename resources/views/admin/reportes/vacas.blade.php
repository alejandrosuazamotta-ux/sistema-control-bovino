@extends('layouts.master')

@section('title', 'Reporte de Estadísticas de Vacas')

@section('content')

{{-- Fix para variables no definidas --}}
@php
    // Establecer valores por defecto si las variables no están definidas
    $totalVacas = $totalVacas ?? 0;
    $vacasLactancia = $vacasLactancia ?? 0;
    $vacasPreñadas = $vacasPreñadas ?? 0;
    $criasEsteAno = $criasEsteAno ?? 0;
    $edadPromedio = $edadPromedio ?? (object)['edad_promedio' => 0];
    $vacasPorSalud = $vacasPorSalud ?? collect();
    $vacasPorReproductivo = $vacasPorReproductivo ?? collect();
    $vacasPorRaza = $vacasPorRaza ?? collect();
    $vacasPorPotrero = $vacasPorPotrero ?? collect();
@endphp

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-cow mr-2"></i>
                    Reporte de Estadísticas de Vacas
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reporte de Vacas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Estadísticas Generales -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalVacas }}</h3>
                        <p>Total de Vacas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cow"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $vacasLactancia }}</h3>
                        <p>En Lactancia</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-milk"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $vacasPreñadas }}</h3>
                        <p>Preñadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-baby"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $criasEsteAno }}</h3>
                        <p>Crías Este Año</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
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
                            Vacas por Estado de Salud
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartVacasSalud" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-doughnut mr-2"></i>
                            Vacas por Estado Reproductivo
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartVacasReproductivo" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Vacas por Raza
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartVacasRaza" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Vacas por Potrero
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartVacasPotrero" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Detallada -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>
                            Información General
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-birthday-cake"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Edad Promedio</span>
                                        <span class="info-box-number">
                                            {{ $edadPromedio ? number_format($edadPromedio->edad_promedio, 1) : 'N/A' }} años
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-heart"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sanas</span>
                                        <span class="info-box-number">
                                            {{ $vacasPorSalud->where('estado_salud', 'Sana')->first()->total ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-stethoscope"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">En Tratamiento</span>
                                        <span class="info-box-number">
                                            {{ $vacasPorSalud->where('estado_salud', 'En tratamiento')->first()->total ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-danger">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">En Observación</span>
                                        <span class="info-box-number">
                                            {{ $vacasPorSalud->where('estado_salud', 'En observación')->first()->total ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table mr-2"></i>
                            Resumen por Estado
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Cantidad</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vacasPorReproductivo as $estado)
                                        <tr>
                                            <td>
                                                <span class="badge badge-{{ 
                                                    $estado->estado_reproductivo == 'Lactancia' ? 'success' : 
                                                    ($estado->estado_reproductivo == 'Preñada' ? 'warning' : 
                                                    ($estado->estado_reproductivo == 'Celo' ? 'info' : 'secondary'))
                                                }}">
                                                    {{ $estado->estado_reproductivo }}
                                                </span>
                                            </td>
                                            <td>{{ $estado->total }}</td>
                                            <td>{{ $totalVacas > 0 ? round(($estado->total / $totalVacas) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablas Detalladas -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Vacas por Raza
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($vacasPorRaza->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Raza</th>
                                            <th>Cantidad</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vacasPorRaza as $raza)
                                            <tr>
                                                <td><strong>{{ $raza->raza }}</strong></td>
                                                <td>{{ $raza->total }}</td>
                                                <td>{{ $totalVacas > 0 ? round(($raza->total / $totalVacas) * 100, 1) : 0 }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay datos de razas registradas</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Vacas por Potrero
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($vacasPorPotrero->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Potrero</th>
                                            <th>Cantidad</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vacasPorPotrero as $potrero)
                                            <tr>
                                                <td><strong>{{ $potrero->potrero ?? 'Sin asignar' }}</strong></td>
                                                <td>{{ $potrero->total }}</td>
                                                <td>{{ $totalVacas > 0 ? round(($potrero->total / $totalVacas) * 100, 1) : 0 }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay datos de potreros</h5>
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
    // Datos seguros para gráficos (evitar errores JS)
    const saludLabels = @json($vacasPorSalud->pluck('estado_salud'));
    const saludData = @json($vacasPorSalud->pluck('total'));
    const reproductivoLabels = @json($vacasPorReproductivo->pluck('estado_reproductivo'));
    const reproductivoData = @json($vacasPorReproductivo->pluck('total'));
    const razaLabels = @json($vacasPorRaza->pluck('raza'));
    const razaData = @json($vacasPorRaza->pluck('total'));
    const potreroLabels = @json($vacasPorPotrero->pluck('potrero'));
    const potreroData = @json($vacasPorPotrero->pluck('total'));

    // Gráfico de vacas por estado de salud
    const ctxSalud = document.getElementById('chartVacasSalud').getContext('2d');
    new Chart(ctxSalud, {
        type: 'pie',
        data: {
            labels: saludLabels.length > 0 ? saludLabels : ['Sin datos'],
            datasets: [{
                data: saludData.length > 0 ? saludData : [1],
                backgroundColor: saludData.length > 0 ? [
                    '#28a745', // Sana
                    '#ffc107', // En tratamiento
                    '#dc3545'  // En observación
                ] : ['#e9ecef'],
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
                    text: 'Distribución por Estado de Salud'
                }
            }
        }
    });

    // Gráfico de vacas por estado reproductivo
    const ctxReproductivo = document.getElementById('chartVacasReproductivo').getContext('2d');
    new Chart(ctxReproductivo, {
        type: 'doughnut',
        data: {
            labels: reproductivoLabels.length > 0 ? reproductivoLabels : ['Sin datos'],
            datasets: [{
                data: reproductivoData.length > 0 ? reproductivoData : [1],
                backgroundColor: reproductivoData.length > 0 ? [
                    '#007bff', // Celo
                    '#28a745', // Lactancia
                    '#ffc107', // Preñada
                    '#6c757d'  // Descanso
                ] : ['#e9ecef'],
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
                    text: 'Distribución por Estado Reproductivo'
                }
            }
        }
    });

    // Gráfico de vacas por raza
    const ctxRaza = document.getElementById('chartVacasRaza').getContext('2d');
    new Chart(ctxRaza, {
        type: 'bar',
        data: {
            labels: razaLabels.length > 0 ? razaLabels : ['Sin datos'],
            datasets: [{
                label: 'Cantidad de Vacas',
                data: razaData.length > 0 ? razaData : [0],
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Distribución por Raza'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Gráfico de vacas por potrero
    const ctxPotrero = document.getElementById('chartVacasPotrero').getContext('2d');
    new Chart(ctxPotrero, {
        type: 'bar',
        data: {
            labels: potreroLabels.length > 0 ? potreroLabels : ['Sin datos'],
            datasets: [{
                label: 'Cantidad de Vacas',
                data: potreroData.length > 0 ? potreroData : [0],
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Distribución por Potrero'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush