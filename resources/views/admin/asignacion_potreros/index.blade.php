@extends('layouts.master')

@section('title', 'Asignación de Potreros')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-exchange-alt"></i> Asignación de Potreros
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Asignación Potreros</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Registros de Asignación
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.asignacion-potreros.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Asignación
                    </a>
                    <a href="{{ route('admin.asignacion-potreros.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.asignacion-potreros.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.asignacion-potreros.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.asignacion-potreros.index') }}" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar por código de vaca..." value="{{ request('search') }}">
                    <select name="id_vaca" class="form-control mr-2">
                        <option value="">Todas las vacas</option>
                        @foreach($vacas as $vaca)
                            <option value="{{ $vaca->id_vaca }}" {{ request('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                {{ $vaca->codigo }}
                            </option>
                        @endforeach
                    </select>
                    <select name="id_potrero" class="form-control mr-2">
                        <option value="">Todos los potreros</option>
                        @foreach($potreros as $potrero)
                            <option value="{{ $potrero->id_potrero }}" {{ request('id_potrero') == $potrero->id_potrero ? 'selected' : '' }}>
                                {{ $potrero->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="fecha_inicio" class="form-control mr-2" placeholder="Fecha inicio" value="{{ request('fecha_inicio') }}">
                    <input type="date" name="fecha_fin" class="form-control mr-2" placeholder="Fecha fin" value="{{ request('fecha_fin') }}">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.asignacion-potreros.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </form>

                @if($asignaciones->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Vaca</th>
                                    <th>Potrero</th>
                                    <th>Fecha Asignación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asignaciones as $asignacion)
                                    <tr>
                                        <td>{{ $loop->iteration + ($asignaciones->currentPage() - 1) * $asignaciones->perPage() }}</td>
                                        <td>{{ $asignacion->vaca->codigo ?? 'N/A' }}</td>
                                        <td>{{ $asignacion->potrero->nombre ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.asignacion-potreros.show', $asignacion->id_asignacion) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.asignacion-potreros.edit', $asignacion->id_asignacion) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $asignacion->id_asignacion }})" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $asignaciones->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay asignaciones de potreros para mostrar con los filtros seleccionados.
                    </div>
                @endif
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Asignaciones por Potrero -->
            @if(isset($datosGraficas['asignaciones_por_potrero']) && count($datosGraficas['asignaciones_por_potrero']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Asignaciones por Potrero
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartAsignacionesPorPotrero" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Rotaciones por Mes -->
            @if(isset($datosGraficas['rotaciones_por_mes']) && count($datosGraficas['rotaciones_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Rotaciones por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartRotacionesPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Potreros Más Usados -->
            @if(isset($datosGraficas['potreros_mas_usados']) && count($datosGraficas['potreros_mas_usados']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Potreros Más Usados (Top 10)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPotrerosMasUsados" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Carga UGG por Potrero -->
            @if(isset($datosGraficas['carga_ugg_por_potrero']) && count($datosGraficas['carga_ugg_por_potrero']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Carga UGG por Potrero (Top 10)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartCargaUGG" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Días de Descanso por Potrero -->
            @if(isset($datosGraficas['dias_descanso_por_potrero']) && count($datosGraficas['dias_descanso_por_potrero']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-area mr-1"></i>
                            Días de Descanso Promedio (Top 10)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartDiasDescanso" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Uso por Potrero (Días de Ocupación) -->
            @if(isset($datosGraficas['uso_por_potrero']) && count($datosGraficas['uso_por_potrero']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Días de Ocupación Total (Top 10)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartUsoPorPotrero" style="min-height: 300px;"></div>
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
    const datosAsignacionesPorPotrero = @json($datosGraficas['asignaciones_por_potrero'] ?? ['labels' => [], 'data' => []]);
    const datosRotacionesPorMes = @json($datosGraficas['rotaciones_por_mes'] ?? ['labels' => [], 'data' => []]);
    const datosPotrerosMasUsados = @json($datosGraficas['potreros_mas_usados'] ?? ['labels' => [], 'data' => []]);
    const datosCargaUGG = @json($datosGraficas['carga_ugg_por_potrero'] ?? ['labels' => [], 'data' => []]);
    const datosDiasDescanso = @json($datosGraficas['dias_descanso_por_potrero'] ?? ['labels' => [], 'data' => []]);
    const datosUsoPorPotrero = @json($datosGraficas['uso_por_potrero'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Asignaciones por Potrero (Donut)
    if (datosAsignacionesPorPotrero.labels.length > 0) {
        const chartAsignacionesPorPotrero = new ApexCharts(document.querySelector("#chartAsignacionesPorPotrero"), {
            series: datosAsignacionesPorPotrero.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosAsignacionesPorPotrero.labels,
            colors: ['#17A2B8', '#28A745', '#FFC107', '#DC3545', '#6C757D', '#6610F2', '#E83E8C', '#20C997'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' asignaciones' } }
            }
        });
        chartAsignacionesPorPotrero.render();
    }

    // Gráfica: Rotaciones por Mes (Línea)
    if (datosRotacionesPorMes.labels.length > 0) {
        const chartRotacionesPorMes = new ApexCharts(document.querySelector("#chartRotacionesPorMes"), {
            series: [{
                name: 'Rotaciones',
                data: datosRotacionesPorMes.data
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: true },
                zoom: { enabled: false }
            },
            colors: ['#28A745'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: datosRotacionesPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' rotaciones' } }
            }
        });
        chartRotacionesPorMes.render();
    }

    // Gráfica: Potreros Más Usados (Barras horizontales)
    if (datosPotrerosMasUsados.labels.length > 0) {
        const chartPotrerosMasUsados = new ApexCharts(document.querySelector("#chartPotrerosMasUsados"), {
            series: [{
                name: 'Vacas únicas',
                data: datosPotrerosMasUsados.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                horizontal: true
            },
            colors: ['#17A2B8'],
            xaxis: {
                categories: datosPotrerosMasUsados.labels
            },
            yaxis: {
                title: { text: 'Cantidad de vacas' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' vacas únicas' } }
            }
        });
        chartPotrerosMasUsados.render();
    }

    // Gráfica: Carga UGG por Potrero (Barras)
    if (datosCargaUGG.labels.length > 0) {
        const chartCargaUGG = new ApexCharts(document.querySelector("#chartCargaUGG"), {
            series: [{
                name: 'Carga UGG/ha',
                data: datosCargaUGG.data
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            colors: ['#FFC107'],
            xaxis: {
                categories: datosCargaUGG.labels
            },
            yaxis: {
                title: { text: 'UGG/ha' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' UGG/ha' } }
            }
        });
        chartCargaUGG.render();
    }

    // Gráfica: Días de Descanso por Potrero (Barras)
    if (datosDiasDescanso.labels.length > 0) {
        const chartDiasDescanso = new ApexCharts(document.querySelector("#chartDiasDescanso"), {
            series: [{
                name: 'Días promedio',
                data: datosDiasDescanso.data
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            colors: ['#6C757D'],
            xaxis: {
                categories: datosDiasDescanso.labels
            },
            yaxis: {
                title: { text: 'Días' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(0) + ' días' } }
            }
        });
        chartDiasDescanso.render();
    }

    // Gráfica: Uso por Potrero (Barras horizontales)
    if (datosUsoPorPotrero.labels.length > 0) {
        const chartUsoPorPotrero = new ApexCharts(document.querySelector("#chartUsoPorPotrero"), {
            series: [{
                name: 'Días totales',
                data: datosUsoPorPotrero.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                horizontal: true
            },
            colors: ['#DC3545'],
            xaxis: {
                categories: datosUsoPorPotrero.labels
            },
            yaxis: {
                title: { text: 'Días de ocupación' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' días' } }
            }
        });
        chartUsoPorPotrero.render();
    }
});
</script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "¡No podrá revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.asignacion-potreros.index") }}/' + id;
            
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
</script>
@endpush
@endsection

