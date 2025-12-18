@extends('layouts.master')

@section('title', 'Gestión de Producción Lechera')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-milk"></i> Gestión de Producción Lechera
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Producción Lechera</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes con SweetAlert2 -->
        @include('components.sweet-alert')

        <!-- Tarjetas de resumen -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['total_produccion_mes'], 2) }}</h3>
                        <p>Total Producción (L)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-chart-line"></i> Este mes
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $registros->total() }}</h3>
                        <p>Registros Totales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-list"></i> Ver todos
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['promedio_diario'], 2) }}</h3>
                        <p>Promedio Diario (L)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-calculator"></i> Últimos 30 días
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $estadisticas['vacas_activas'] }}</h3>
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
        </div>

        <!-- Gráficas de Producción -->
        <div class="row mb-3">
            <!-- Producción Diaria -->
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
                        <div id="chartProduccionDiaria" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Producción por Turno -->
            @if(isset($produccionPorTurno) && count($produccionPorTurno['labels']) > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Producción por Turno (Últimos 30 días)
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Producción por Destino (Últimos 30 días)
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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Registros de Producción Lechera
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.produccion-lechera.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    <a href="{{ route('admin.produccion-lechera.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.produccion-lechera.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.produccion-lechera.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros de búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.produccion-lechera.index') }}" class="form-inline">
                            <div class="input-group mr-2">
                                <input type="text" name="search" class="form-control" placeholder="Buscar por código de vaca..." 
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="input-group mr-2">
                                <input type="date" name="fecha_inicio" class="form-control" 
                                       placeholder="Fecha inicio" value="{{ request('fecha_inicio') }}">
                            </div>
                            <div class="input-group mr-2">
                                <input type="date" name="fecha_fin" class="form-control" 
                                       placeholder="Fecha fin" value="{{ request('fecha_fin') }}">
                            </div>
                            <div class="input-group mr-2">
                                <select name="id_vaca" class="form-control">
                                    <option value="">Todas las vacas</option>
                                    @foreach($vacas as $vaca)
                                        <option value="{{ $vaca->id_vaca }}" 
                                                {{ request('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                            {{ $vaca->codigo }} - {{ $vaca->raza }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="id_personal" class="form-control">
                                    <option value="">Todo el personal</option>
                                    @foreach($personal as $persona)
                                        <option value="{{ $persona->id_personal }}" 
                                                {{ request('id_personal') == $persona->id_personal ? 'selected' : '' }}>
                                            {{ $persona->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="turno" class="form-control">
                                    <option value="">Todos los turnos</option>
                                    <option value="AM" {{ request('turno') == 'AM' ? 'selected' : '' }}>AM</option>
                                    <option value="PM" {{ request('turno') == 'PM' ? 'selected' : '' }}>PM</option>
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="destino" class="form-control">
                                    <option value="">Todos los destinos</option>
                                    <option value="Agroindustria" {{ request('destino') == 'Agroindustria' ? 'selected' : '' }}>Agroindustria</option>
                                    <option value="Lechero" {{ request('destino') == 'Lechero' ? 'selected' : '' }}>Lechero</option>
                                    <option value="Particular" {{ request('destino') == 'Particular' ? 'selected' : '' }}>Particular</option>
                                    <option value="Consumo" {{ request('destino') == 'Consumo' ? 'selected' : '' }}>Consumo</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Filtrar</button>
                            <a href="{{ route('admin.produccion-lechera.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                        </form>
                    </div>
                </div>

                <!-- Tabla de registros -->
                @if($registros->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="4%">#</th>
                                    <th width="10%">Vaca</th>
                                    <th width="8%">Fecha</th>
                                    <th width="6%">Turno</th>
                                    <th width="8%">Cantidad (L)</th>
                                    <th width="10%">Destino</th>
                                    <th width="10%">Valor Total</th>
                                    <th width="12%">Personal</th>
                                    <th width="8%">Estado</th>
                                    <th width="14%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registros as $index => $registro)
                                    <tr>
                                        <td>{{ $registros->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $registro->vaca->codigo }}</strong><br>
                                            <small class="text-muted">{{ $registro->vaca->raza }}</small>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar"></i> 
                                            {{ $registro->fecha->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            @if($registro->turno == 'AM')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-sun"></i> AM
                                                </span>
                                            @else
                                                <span class="badge badge-info">
                                                    <i class="fas fa-moon"></i> PM
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">
                                                <i class="fas fa-tint"></i> 
                                                {{ number_format($registro->cantidad_leche, 2) }} L
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $destinoColors = [
                                                    'Agroindustria' => 'success',
                                                    'Lechero' => 'info',
                                                    'Particular' => 'warning',
                                                    'Consumo' => 'secondary'
                                                ];
                                                $color = $destinoColors[$registro->destino] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">
                                                {{ $registro->destino }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($registro->valor_total)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-dollar-sign"></i> 
                                                    ${{ number_format($registro->valor_total, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-user"></i> 
                                            {{ $registro->personal->nombre ?? 'N/A' }}<br>
                                            @if($registro->personal)
                                                <small class="text-muted">{{ $registro->personal->rol }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($registro->excluida_por_retiro)
                                                <span class="badge badge-danger"><i class="fas fa-ban"></i> Retiro</span>
                                            @elseif($registro->excluida_por_sanidad)
                                                <span class="badge badge-warning"><i class="fas fa-vial"></i> Excluida por Sanidad</span>
                                            @elseif($registro->cantidad_leche >= 20)
                                                <span class="badge badge-success">Excelente</span>
                                            @elseif($registro->cantidad_leche >= 15)
                                                <span class="badge badge-warning">Buena</span>
                                            @elseif($registro->cantidad_leche >= 10)
                                                <span class="badge badge-info">Regular</span>
                                            @else
                                                <span class="badge badge-danger">Baja</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.produccion-lechera.show', $registro->id_produccion) }}" 
                                                   class="btn btn-info btn-sm" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.produccion-lechera.edit', $registro->id_produccion) }}" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        onclick="confirmarEliminacion({{ $registro->id_produccion }})"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $registros->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay registros de producción</h4>
                        <p class="text-muted">No se encontraron registros de producción lechera con los filtros aplicados.</p>
                        <a href="{{ route('admin.produccion-lechera.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Registro
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Gráfico de producción -->
        @if($registros->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Gráfico de Producción
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="produccionChart" width="400" height="200"></canvas>
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para las gráficas
    const produccionDiaria = @json($produccionDiaria ?? ['labels' => [], 'data' => []]);
    const produccionPorTurno = @json($produccionPorTurno ?? ['labels' => [], 'data' => [], 'colors' => []]);
    const produccionPorDestino = @json($produccionPorDestino ?? ['labels' => [], 'data' => []]);

    // Gráfica de Producción Diaria (Línea)
    if (produccionDiaria.labels.length > 0) {
        const chartProduccionDiaria = new ApexCharts(document.querySelector("#chartProduccionDiaria"), {
            series: [{
                name: 'Producción (L)',
                data: produccionDiaria.data
            }],
            chart: {
                type: 'line',
                height: 350,
                toolbar: { show: true },
                zoom: { enabled: true }
            },
            colors: ['#28A745'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 4,
                hover: { size: 6 }
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

    // Gráfica de Producción por Turno (Donut)
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

    // Gráfica de Producción por Destino (Barras)
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
});

function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará el registro de producción. Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.produccion-lechera.index") }}/' + id;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Exportación ahora se maneja con dropdown directo
</script>
@endpush
@endsection
