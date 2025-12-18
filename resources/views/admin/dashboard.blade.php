@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<!-- Header con Breadcrumb -->
<div class="mb-6 animate-fade-in-down">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-display mb-2">
                <i class="fas fa-chart-line text-spg-primary mr-3 animate-bounce-subtle"></i>
                Dashboard
            </h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-spg-primary transition-colors duration-300">
                            <i class="fas fa-home mr-2"></i>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-gray-500">Dashboard Admin</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Cards de Estadísticas con Animaciones -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Vacas -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-card shadow-saas-lg p-6 text-white transform hover:scale-105 transition-all duration-300 card-hover animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center icon-rotate">
                <i class="fas fa-cow text-3xl"></i>
            </div>
        </div>
        <h3 class="text-sm font-medium text-white/90 mb-1">Total Vacas</h3>
        <p class="text-4xl font-bold mb-2">{{ number_format($estadisticas['total_vacas'] ?? $totalVacas ?? 0) }}</p>
        <p class="text-xs text-white/80 flex items-center">
            <i class="fas fa-check-circle mr-1"></i>
            {{ $estadisticas['vacas_activas'] ?? 0 }} activas
        </p>
    </div>

    <!-- Vacas en Ordeño -->
    <div class="bg-gradient-to-br from-spg-primary to-spg-secondary rounded-card shadow-saas-lg p-6 text-white transform hover:scale-105 transition-all duration-300 card-hover animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center icon-rotate">
                <i class="fas fa-glass-water text-3xl"></i>
            </div>
        </div>
        <h3 class="text-sm font-medium text-white/90 mb-1">Vacas en Ordeño</h3>
        <p class="text-4xl font-bold mb-2">{{ $estadisticas['vacas_lactancia'] ?? 0 }}</p>
        <p class="text-xs text-white/80 flex items-center">
            <i class="fas fa-tint mr-1"></i>
            En producción
        </p>
    </div>

    <!-- Vacas Preñadas -->
    <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-card shadow-saas-lg p-6 text-white transform hover:scale-105 transition-all duration-300 card-hover animate-fade-in-up" style="animation-delay: 0.3s;">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center icon-rotate">
                <i class="fas fa-seedling text-3xl"></i>
            </div>
        </div>
        <h3 class="text-sm font-medium text-white/90 mb-1">Vacas Preñadas</h3>
        <p class="text-4xl font-bold mb-2">{{ $estadisticas['vacas_preñadas'] ?? 0 }}</p>
        <p class="text-xs text-white/80 flex items-center">
            <i class="fas fa-heart mr-1"></i>
            Estado reproductivo
        </p>
    </div>

    <!-- Producción Hoy -->
    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-card shadow-saas-lg p-6 text-white transform hover:scale-105 transition-all duration-300 card-hover animate-fade-in-up" style="animation-delay: 0.4s;">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center icon-rotate">
                <i class="fas fa-chart-line text-3xl"></i>
            </div>
        </div>
        <h3 class="text-sm font-medium text-white/90 mb-1">Producción Hoy</h3>
        <p class="text-4xl font-bold mb-2">{{ number_format($estadisticas['produccion_hoy'] ?? $produccionHoy ?? 0, 2) }} L</p>
        <p class="text-xs text-white/80 flex items-center">
            <i class="fas fa-calendar mr-1"></i>
            Mes: {{ number_format($estadisticas['produccion_mes'] ?? 0, 2) }} L
        </p>
    </div>
</div>

<!-- Cards Adicionales -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Total Crías -->
    <div class="bg-gradient-to-br from-spg-soft to-spg-secondary rounded-card shadow-saas p-6 transform hover:scale-[1.02] transition-all duration-300 card-hover animate-fade-in-up" style="animation-delay: 0.5s;">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-600 mb-1">Total Crías</h3>
                <p class="text-3xl font-bold text-spg-primary">{{ number_format($estadisticas['total_crias'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 mt-1 flex items-center">
                    <i class="fas fa-check mr-1"></i>
                    {{ $estadisticas['crias_destetadas'] ?? 0 }} destetadas
                </p>
            </div>
            <div class="w-20 h-20 bg-spg-primary/10 rounded-full flex items-center justify-center icon-rotate">
                <i class="fas fa-baby text-4xl text-spg-primary"></i>
            </div>
        </div>
    </div>

    <!-- Promedio por Vaca -->
    <div class="bg-gradient-to-br from-spg-beige to-white rounded-card shadow-saas p-6 border border-spg-soft transform hover:scale-[1.02] transition-all duration-300 card-hover animate-fade-in-up" style="animation-delay: 0.6s;">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-600 mb-1">Promedio por Vaca</h3>
                <p class="text-3xl font-bold text-spg-deepblue">
                    @php
                        $totalVacasActivas = $estadisticas['vacas_activas'] ?? 1;
                        $produccionHoyCalculo = $estadisticas['produccion_hoy'] ?? ($produccionHoy ?? 0);
                        $promedio = $totalVacasActivas > 0 ? $produccionHoyCalculo / $totalVacasActivas : 0;
                    @endphp
                    {{ number_format($promedio, 2) }} L
                </p>
                <p class="text-xs text-gray-500 mt-1 flex items-center">
                    <i class="fas fa-calculator mr-1"></i>
                    Producción diaria promedio
                </p>
            </div>
            <div class="w-20 h-20 bg-spg-deepblue/10 rounded-full flex items-center justify-center icon-rotate">
                <i class="fas fa-chart-bar text-4xl text-spg-deepblue"></i>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas Principales -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <x-chart id="chartProduccionDiaria" type="line" height="300" title="Producción Diaria (Últimos 30 días)" class="animate-fade-in-up" style="animation-delay: 0.7s;" />
    <x-chart id="chartProduccionMensual" type="bar" height="300" title="Producción Mensual (Últimos 12 meses)" class="animate-fade-in-up" style="animation-delay: 0.8s;" />
</div>

<!-- Gráficas Secundarias -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <x-chart id="chartVacasPorEstado" type="donut" height="300" title="Vacas por Estado" class="animate-fade-in-up" style="animation-delay: 0.9s;" />
    <x-chart id="chartProduccionPorPotrero" type="pie" height="300" title="Producción por Potrero" class="animate-fade-in-up" style="animation-delay: 1s;" />
    <x-chart id="chartEstadoReproductivo" type="bar" height="300" title="Estado Reproductivo" class="animate-fade-in-up" style="animation-delay: 1.1s;" />
</div>

<!-- Ranking de Vacas -->
<div class="mb-8 animate-fade-in-up" style="animation-delay: 1.2s;">
    <x-chart id="chartRankingVacas" type="bar" height="350" title="Ranking Top 10 Vacas Productivas (Últimos 30 días)" />
</div>

<!-- Tablas de Información -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Personal por Rol -->
    <x-card title="Personal por Rol" icon="fas fa-users" hover="true" class="animate-fade-in-up" style="animation-delay: 1.3s;">
        @if(isset($personalPorRol) && $personalPorRol->count() > 0)
            <x-table>
                <x-table.header>
                    <x-table.header-cell>Rol</x-table.header-cell>
                    <x-table.header-cell>Cantidad</x-table.header-cell>
                    <x-table.header-cell>Porcentaje</x-table.header-cell>
                </x-table.header>
                <x-table.body>
                    @foreach($personalPorRol as $index => $rol)
                        <x-table.row class="animate-fade-in-up" style="animation-delay: {{ 1.4 + ($index * 0.1) }}s;">
                            <x-table.cell>
                                <x-badge variant="{{ $rol->rol == 'Supervisor' ? 'success' : ($rol->rol == 'Pasante' ? 'info' : 'warning') }}" animated>
                                    {{ $rol->rol }}
                                </x-badge>
                            </x-table.cell>
                            <x-table.cell class="font-semibold text-gray-900">{{ $rol->total }}</x-table.cell>
                            <x-table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        @php
                                            $porcentaje = $totalPersonal > 0 ? round(($rol->total / $totalPersonal) * 100, 1) : 0;
                                            $colorBarra = $rol->rol == 'Supervisor' ? 'bg-green-500' : ($rol->rol == 'Pasante' ? 'bg-blue-500' : 'bg-yellow-500');
                                        @endphp
                                        <div class="{{ $colorBarra }} h-full rounded-full transition-all duration-1000" 
                                             style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 min-w-[3rem]">{{ $porcentaje }}%</span>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">No hay personal registrado</p>
            </div>
        @endif
    </x-card>

    <!-- Últimas Vacas -->
    <x-card title="Últimas Vacas Registradas" icon="fas fa-cow" hover="true" class="animate-fade-in-up" style="animation-delay: 1.4s;">
        <div class="flex justify-end mb-4">
            <a href="{{ route('admin.vacas.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-spg-primary text-white rounded-lg text-sm font-medium hover:bg-spg-secondary transition-all duration-300 shadow-saas hover:shadow-saas-md hover:scale-105 btn-ripple">
                <i class="fas fa-list mr-2"></i> Ver Todas
            </a>
        </div>
        @if(isset($ultimasVacas) && $ultimasVacas->count() > 0)
            <x-table>
                <x-table.header>
                    <x-table.header-cell>Código</x-table.header-cell>
                    <x-table.header-cell>Raza</x-table.header-cell>
                    <x-table.header-cell>Estado</x-table.header-cell>
                    <x-table.header-cell>Potrero</x-table.header-cell>
                </x-table.header>
                <x-table.body>
                    @foreach($ultimasVacas as $index => $vaca)
                        <x-table.row class="animate-fade-in-up" style="animation-delay: {{ 1.5 + ($index * 0.1) }}s;">
                            <x-table.cell>
                                <a href="{{ route('admin.vacas.show', $vaca->id_vaca) }}" 
                                   class="font-semibold text-spg-primary hover:text-spg-secondary transition-colors duration-300">
                                    {{ $vaca->codigo }}
                                </a>
                            </x-table.cell>
                            <x-table.cell>{{ $vaca->raza ?? 'N/A' }}</x-table.cell>
                            <x-table.cell>
                                <x-badge variant="{{ $vaca->estado_salud == 'Sana' ? 'success' : ($vaca->estado_salud == 'En tratamiento' ? 'warning' : ($vaca->estado_salud == 'Muerta' ? 'danger' : 'info')) }}" animated>
                                    {{ $vaca->estado_salud }}
                                </x-badge>
                            </x-table.cell>
                            <x-table.cell>{{ $vaca->potrero->nombre ?? 'Sin asignar' }}</x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table>
        @else
            <div class="text-center py-12">
                <i class="fas fa-cow text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">No hay vacas registradas</p>
            </div>
        @endif
    </x-card>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de números contadores
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.textContent = Math.floor(progress * (end - start) + start).toLocaleString();
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Datos para las gráficas
    const produccionDiaria = @json($produccionDiaria ?? ['labels' => [], 'data' => []]);
    const produccionMensual = @json($produccionMensual ?? ['labels' => [], 'data' => []]);
    const vacasPorEstado = @json($vacasPorEstado ?? ['labels' => [], 'data' => [], 'colors' => []]);
    const produccionPorPotrero = @json($produccionPorPotrero ?? ['labels' => [], 'data' => []]);
    const rankingVacas = @json($rankingVacas ?? ['labels' => [], 'data' => []]);
    const estadoReproductivo = @json($estadoReproductivo ?? ['labels' => [], 'data' => []]);

    // Gráfica de Producción Diaria (Línea)
    if (produccionDiaria.labels.length > 0) {
        const chartProduccionDiaria = new ApexCharts(document.querySelector("#chartProduccionDiaria"), {
            series: [{
                name: 'Producción (L)',
                data: produccionDiaria.data
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: true },
                zoom: { enabled: true },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#1F713E'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#89C65B'],
                    inverseColors: false,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 100]
                }
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
            }
        });
        chartProduccionDiaria.render();
    }

    // Gráfica de Producción Mensual (Barras)
    if (produccionMensual.labels.length > 0) {
        const chartProduccionMensual = new ApexCharts(document.querySelector("#chartProduccionMensual"), {
            series: [{
                name: 'Producción (L)',
                data: produccionMensual.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: true },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#4BAE4F'],
            xaxis: {
                categories: produccionMensual.labels
            },
            yaxis: {
                title: { text: 'Litros' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    horizontal: false
                }
            }
        });
        chartProduccionMensual.render();
    }

    // Gráfica de Vacas por Estado (Donut)
    if (vacasPorEstado.labels.length > 0) {
        const chartVacasPorEstado = new ApexCharts(document.querySelector("#chartVacasPorEstado"), {
            series: vacasPorEstado.data,
            chart: {
                type: 'donut',
                height: 300,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            labels: vacasPorEstado.labels,
            colors: vacasPorEstado.colors.length > 0 ? vacasPorEstado.colors : ['#1F713E', '#4BAE4F', '#89C65B', '#DC3545'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' vacas' } }
            }
        });
        chartVacasPorEstado.render();
    }

    // Gráfica de Producción por Potrero (Pastel)
    if (produccionPorPotrero.labels.length > 0) {
        const chartProduccionPorPotrero = new ApexCharts(document.querySelector("#chartProduccionPorPotrero"), {
            series: produccionPorPotrero.data,
            chart: {
                type: 'pie',
                height: 300,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            labels: produccionPorPotrero.labels,
            colors: ['#1F713E', '#4BAE4F', '#89C65B', '#B48A61', '#EBC365'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
            }
        });
        chartProduccionPorPotrero.render();
    }

    // Gráfica de Estado Reproductivo (Barras)
    if (estadoReproductivo.labels.length > 0) {
        const chartEstadoReproductivo = new ApexCharts(document.querySelector("#chartEstadoReproductivo"), {
            series: [{
                name: 'Cantidad',
                data: estadoReproductivo.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: true },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#2D4059'],
            xaxis: {
                categories: estadoReproductivo.labels
            },
            yaxis: {
                title: { text: 'Cantidad de Vacas' }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    horizontal: true
                }
            }
        });
        chartEstadoReproductivo.render();
    }

    // Gráfica de Ranking de Vacas (Barras horizontales)
    if (rankingVacas.labels.length > 0) {
        const chartRankingVacas = new ApexCharts(document.querySelector("#chartRankingVacas"), {
            series: [{
                name: 'Producción (L)',
                data: rankingVacas.data
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: true },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#1F713E'],
            xaxis: {
                categories: rankingVacas.labels
            },
            yaxis: {
                title: { text: 'Litros' }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    horizontal: true
                }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' L' } }
            }
        });
        chartRankingVacas.render();
    }
});
</script>
@endpush
