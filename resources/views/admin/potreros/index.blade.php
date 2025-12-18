@extends('layouts.master')

@section('title', 'Gestión de Potreros')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-map-marker-alt"></i> Gestión de Potreros
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Potreros</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Lista de Potreros
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.potreros.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Potrero
                    </a>
                    <a href="{{ route('admin.potreros.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.potreros.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.potreros.export.pdf', request()->query()) }}">
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
                        <form method="GET" action="{{ route('admin.potreros.index') }}" class="form-inline">
                            <div class="input-group mr-2">
                                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o ubicación..." 
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="input-group mr-2">
                                <select name="capacidad" class="form-control">
                                    <option value="">Todas las capacidades</option>
                                    <option value="1-10" {{ request('capacidad') == '1-10' ? 'selected' : '' }}>1-10 animales</option>
                                    <option value="11-20" {{ request('capacidad') == '11-20' ? 'selected' : '' }}>11-20 animales</option>
                                    <option value="21-50" {{ request('capacidad') == '21-50' ? 'selected' : '' }}>21-50 animales</option>
                                    <option value="50+" {{ request('capacidad') == '50+' ? 'selected' : '' }}>Más de 50 animales</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Filtrar</button>
                            <a href="{{ route('admin.potreros.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                        </form>
                    </div>
                </div>

                <!-- Tabla de potreros -->
                @if($potreros->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Nombre</th>
                                    <th width="25%">Ubicación</th>
                                    <th width="15%">Capacidad</th>
                                    <th width="15%">Ocupación</th>
                                    <th width="10%">Estado</th>
                                    <th width="10%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($potreros as $potrero)
                                    <tr>
                                        <td>{{ $potrero->id_potrero }}</td>
                                        <td>
                                            <strong>{{ $potrero->nombre }}</strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                            {{ $potrero->ubicacion ?? 'No especificada' }}
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $potrero->capacidad }} animales
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $ocupacion = $potrero->vacas->count();
                                                $porcentaje = $potrero->capacidad > 0 ? round(($ocupacion / $potrero->capacidad) * 100, 1) : 0;
                                                $color = $porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success');
                                            @endphp
                                            <div class="progress-group">
                                                <span class="float-right">
                                                    <b>{{ $ocupacion }}</b>/{{ $potrero->capacidad }}
                                                </span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $porcentaje }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $porcentaje }}% ocupado</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($porcentaje >= 90)
                                                <span class="badge badge-danger">Lleno</span>
                                            @elseif($porcentaje >= 70)
                                                <span class="badge badge-warning">Ocupado</span>
                                            @else
                                                <span class="badge badge-success">Disponible</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.potreros.show', $potrero->id_potrero) }}" 
                                                   class="btn btn-info btn-sm" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.potreros.edit', $potrero->id_potrero) }}" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="confirmarEliminacion({{ $potrero->id_potrero }})" title="Eliminar">
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
                        {{ $potreros->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron potreros</h5>
                        <p class="text-muted">No hay potreros registrados en el sistema.</p>
                        <a href="{{ route('admin.potreros.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Registrar Primer Potrero
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        @if($potreros->count() > 0)
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-map-marker-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Potreros</span>
                            <span class="info-box-number">{{ $potreros->total() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Disponibles</span>
                            <span class="info-box-number">{{ $potreros->where('vacas_count', '<', 'capacidad')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ocupados</span>
                            <span class="info-box-number">{{ $potreros->where('vacas_count', '>=', 'capacidad * 0.7')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Llenos</span>
                            <span class="info-box-number">{{ $potreros->where('vacas_count', '>=', 'capacidad')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Potreros por Capacidad -->
            @if(isset($datosGraficas['por_capacidad']) && count($datosGraficas['por_capacidad']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Potreros por Capacidad
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPorCapacidad" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Ocupación por Potrero -->
            @if(isset($datosGraficas['ocupacion_por_potrero']) && count($datosGraficas['ocupacion_por_potrero']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Ocupación por Potrero (Top 10)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartOcupacionPorPotrero" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Uso por Mes -->
            @if(isset($datosGraficas['uso_por_mes']) && count($datosGraficas['uso_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Uso de Potreros por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartUsoPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</section>

<!-- Formulario para eliminación -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para las gráficas
    const datosPorCapacidad = @json($datosGraficas['por_capacidad'] ?? ['labels' => [], 'data' => []]);
    const datosOcupacionPorPotrero = @json($datosGraficas['ocupacion_por_potrero'] ?? ['labels' => [], 'data' => []]);
    const datosUsoPorMes = @json($datosGraficas['uso_por_mes'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Potreros por Capacidad (Donut)
    if (datosPorCapacidad.labels.length > 0) {
        const chartPorCapacidad = new ApexCharts(document.querySelector("#chartPorCapacidad"), {
            series: datosPorCapacidad.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosPorCapacidad.labels,
            colors: ['#17A2B8', '#28A745', '#FFC107', '#DC3545'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' potreros' } }
            }
        });
        chartPorCapacidad.render();
    }

    // Gráfica: Ocupación por Potrero (Barras horizontales)
    if (datosOcupacionPorPotrero.labels.length > 0) {
        const chartOcupacionPorPotrero = new ApexCharts(document.querySelector("#chartOcupacionPorPotrero"), {
            series: [{
                name: 'Vacas',
                data: datosOcupacionPorPotrero.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                horizontal: true
            },
            colors: ['#28A745'],
            xaxis: {
                categories: datosOcupacionPorPotrero.labels
            },
            yaxis: {
                title: { text: 'Cantidad de vacas' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' vacas' } }
            }
        });
        chartOcupacionPorPotrero.render();
    }

    // Gráfica: Uso por Mes (Línea)
    if (datosUsoPorMes.labels.length > 0) {
        const chartUsoPorMes = new ApexCharts(document.querySelector("#chartUsoPorMes"), {
            series: [{
                name: 'Asignaciones',
                data: datosUsoPorMes.data
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: true },
                zoom: { enabled: false }
            },
            colors: ['#17A2B8'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: datosUsoPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' asignaciones' } }
            }
        });
        chartUsoPorMes.render();
    }
});
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer. Se eliminará el potrero y todas sus asignaciones.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/potreros/${id}`;
            form.submit();
        }
    });
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>
@endpush

@push('styles')
<style>
.progress-group {
    margin-bottom: 0;
}
.progress-group .progress {
    margin-bottom: 5px;
}
.info-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}
.info-box-icon {
    border-radius: 0.25rem 0 0 0.25rem;
}
</style>
@endpush
