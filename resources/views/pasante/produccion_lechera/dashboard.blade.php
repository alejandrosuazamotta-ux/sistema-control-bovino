@extends('layouts.master')

@section('title', 'Dashboard Producción Lechera')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-chart-pie"></i> Dashboard Producción Lechera
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pasante.produccion-lechera.index') }}">Producción Lechera</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <!-- Tarjetas de resumen -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['total_produccion_mes'] ?? 0, 2) }}</h3>
                        <p>Total Producción Mes (L)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-calendar"></i> Mes Actual
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['promedio_diario'] ?? 0, 2) }}</h3>
                        <p>Promedio Diario (L)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-calculator"></i> Últimos 30 días
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $estadisticas['vacas_activas'] ?? 0 }}</h3>
                        <p>Vacas en Lactancia</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cow"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-heart"></i> Activas
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($produccionHoy ?? 0, 2) }}</h3>
                        <p>Producción Hoy (L)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-clock"></i> {{ now()->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas principales -->
        <div class="row mb-3">
            <!-- Producción Diaria (30 días) -->
            @if(isset($produccionDiaria) && count($produccionDiaria['labels']) > 0)
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Producción Diaria (Últimos 30 días)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chartProduccionDiaria" style="min-height: 400px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Producción Mensual (12 meses) -->
            @if(isset($produccionMensual) && count($produccionMensual['labels']) > 0)
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-area mr-1"></i>
                            Producción Mensual (Últimos 12 meses)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chartProduccionMensual" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="row">
            <!-- Producción por Turno -->
            @if(isset($produccionPorTurno) && count($produccionPorTurno['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Producción por Turno (30 días)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chartProduccionPorTurno" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Producción por Destino -->
            @if(isset($produccionPorDestino) && count($produccionPorDestino['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Producción por Destino (30 días)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chartProduccionPorDestino" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Top 10 Vacas Productivas -->
        @if(isset($topVacas) && count($topVacas['labels']) > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-trophy mr-1"></i>
                            Top 10 Vacas Más Productivas (Últimos 30 días)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chartTopVacas" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Acciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <a href="{{ route('pasante.produccion-lechera.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>
                        <a href="{{ route('pasante.dashboard') }}" class="btn btn-info">
                            <i class="fas fa-home"></i> Dashboard Principal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const produccionDiaria = @json($produccionDiaria ?? ['labels' => [], 'data' => []]);
    const produccionMensual = @json($produccionMensual ?? ['labels' => [], 'data' => []]);
    const produccionPorTurno = @json($produccionPorTurno ?? ['labels' => [], 'data' => [], 'colors' => []]);
    const produccionPorDestino = @json($produccionPorDestino ?? ['labels' => [], 'data' => []]);
    const topVacas = @json($topVacas ?? ['labels' => [], 'data' => []]);

    // Gráfica de Producción Diaria
    if (produccionDiaria.labels.length > 0) {
        const chartProduccionDiaria = new ApexCharts(document.querySelector("#chartProduccionDiaria"), {
            series: [{
                name: 'Producción (L)',
                data: produccionDiaria.data
            }],
            chart: {
                type: 'line',
                height: 400,
                toolbar: { show: true },
                zoom: { enabled: true }
            },
            colors: ['#28A745'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 5,
                hover: { size: 7 }
            },
            xaxis: {
                categories: produccionDiaria.labels
            },
            yaxis: {
                title: { text: 'Litros' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
            },
            grid: {
                borderColor: '#e0e0e0',
                strokeDashArray: 5
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    gradientToColors: ['#198754'],
                    inverseColors: false,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 100]
                }
            }
        });
        chartProduccionDiaria.render();
    }

    // Gráfica de Producción Mensual
    if (produccionMensual.labels.length > 0) {
        const chartProduccionMensual = new ApexCharts(document.querySelector("#chartProduccionMensual"), {
            series: [{
                name: 'Producción (L)',
                data: produccionMensual.data
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: true },
                zoom: { enabled: true }
            },
            colors: ['#0D6EFD'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: produccionMensual.labels
            },
            yaxis: {
                title: { text: 'Litros' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(0) + ' L';
                }
            }
        });
        chartProduccionMensual.render();
    }

    // Gráfica de Producción por Turno
    if (produccionPorTurno.labels.length > 0) {
        const chartProduccionPorTurno = new ApexCharts(document.querySelector("#chartProduccionPorTurno"), {
            series: produccionPorTurno.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: produccionPorTurno.labels,
            colors: produccionPorTurno.colors.length > 0 ? produccionPorTurno.colors : ['#28A745', '#0D6EFD'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function() {
                                    return produccionPorTurno.data.reduce((a, b) => a + b, 0).toFixed(2) + ' L';
                                }
                            }
                        }
                    }
                }
            }
        });
        chartProduccionPorTurno.render();
    }

    // Gráfica de Producción por Destino
    if (produccionPorDestino.labels.length > 0) {
        const chartProduccionPorDestino = new ApexCharts(document.querySelector("#chartProduccionPorDestino"), {
            series: [{
                name: 'Producción (L)',
                data: produccionPorDestino.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: true }
            },
            colors: ['#0D6EFD'],
            xaxis: {
                categories: produccionPorDestino.labels
            },
            yaxis: {
                title: { text: 'Litros' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(1) + ' L';
                }
            }
        });
        chartProduccionPorDestino.render();
    }

    // Gráfica Top 10 Vacas
    if (topVacas.labels.length > 0) {
        const chartTopVacas = new ApexCharts(document.querySelector("#chartTopVacas"), {
            series: [{
                name: 'Producción (L)',
                data: topVacas.data
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: true },
                horizontal: true
            },
            colors: ['#28A745'],
            xaxis: {
                categories: topVacas.labels
            },
            yaxis: {
                title: { text: 'Vacas' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    dataLabels: {
                        position: 'right'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(1) + ' L';
                }
            }
        });
        chartTopVacas.render();
    }
});
</script>
@endpush
@endsection

