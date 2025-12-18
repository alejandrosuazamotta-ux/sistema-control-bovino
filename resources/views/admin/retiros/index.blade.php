@extends('layouts.master')

@section('title', 'Gestión de Retiros')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-ban"></i> Gestión de Retiros</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Retiros</li>
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

        <!-- Retiros Activos -->
        @if($retirosActivos->count() > 0)
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle"></i> Retiros Activos: {{ $retirosActivos->count() }}</h5>
            <p>Hay {{ $retirosActivos->count() }} vaca(s) actualmente en período de retiro.</p>
        </div>
        @endif

        <!-- Retiros Próximos a Vencer -->
        @if($retirosProximos->count() > 0)
        <div class="alert alert-info">
            <h5><i class="fas fa-clock"></i> Retiros Próximos a Vencer (7 días): {{ $retirosProximos->count() }}</h5>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Lista de Retiros</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.retiros.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Retiro
                    </a>
                    <a href="{{ route('admin.retiros.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.retiros.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.retiros.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.retiros.index') }}" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar por código de vaca..." value="{{ request('search') }}">
                    <select name="id_vaca" class="form-control mr-2">
                        <option value="">Todas las vacas</option>
                        @foreach($vacas as $vaca)
                            <option value="{{ $vaca->id_vaca }}" {{ request('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                {{ $vaca->codigo }}
                            </option>
                        @endforeach
                    </select>
                    <select name="tipo_retiro" class="form-control mr-2">
                        <option value="">Todos los tipos</option>
                        <option value="Ordeño" {{ request('tipo_retiro') == 'Ordeño' ? 'selected' : '' }}>Ordeño</option>
                        <option value="Producción" {{ request('tipo_retiro') == 'Producción' ? 'selected' : '' }}>Producción</option>
                    </select>
                    <select name="activo" class="form-control mr-2">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('admin.retiros.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                </form>

                @if($retiros->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Vaca</th>
                                    <th>Tipo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Días Restantes</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($retiros as $retiro)
                                    <tr class="{{ $retiro->estaActivo() ? 'table-warning' : '' }}">
                                        <td>{{ $retiros->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $retiro->vaca->codigo }}</strong><br>
                                            <small>{{ $retiro->vaca->raza }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $retiro->tipo_retiro == 'Ordeño' ? 'warning' : 'danger' }}">
                                                {{ $retiro->tipo_retiro }}
                                            </span>
                                        </td>
                                        <td>{{ $retiro->fecha_inicio->format('d/m/Y') }}</td>
                                        <td>{{ $retiro->fecha_fin->format('d/m/Y') }}</td>
                                        <td>
                                            @if($retiro->estaActivo())
                                                <span class="badge badge-danger">{{ $retiro->diasRestantes() }} días</span>
                                            @else
                                                <span class="badge badge-secondary">Finalizado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($retiro->activo && $retiro->estaActivo())
                                                <span class="badge badge-danger">Activo</span>
                                            @elseif($retiro->activo)
                                                <span class="badge badge-warning">Programado</span>
                                            @else
                                                <span class="badge badge-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.retiros.show', $retiro->id_retiro) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.retiros.edit', $retiro->id_retiro) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $retiro->id_retiro }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $retiros->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-ban fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay retiros registrados</h5>
                        <a href="{{ route('admin.retiros.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Registrar Primer Retiro
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Retiros por Tipo -->
            @if(isset($datosGraficas['por_tipo']) && count($datosGraficas['por_tipo']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Retiros por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPorTipo" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Retiros por Mes -->
            @if(isset($datosGraficas['retiros_por_mes']) && count($datosGraficas['retiros_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Retiros por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartRetirosPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Activos vs Inactivos -->
            @if(isset($datosGraficas['activos_vs_inactivos']) && count($datosGraficas['activos_vs_inactivos']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Activos vs Inactivos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartActivosVsInactivos" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</section>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para las gráficas
    const datosPorTipo = @json($datosGraficas['por_tipo'] ?? ['labels' => [], 'data' => []]);
    const datosRetirosPorMes = @json($datosGraficas['retiros_por_mes'] ?? ['labels' => [], 'data' => []]);
    const datosActivosVsInactivos = @json($datosGraficas['activos_vs_inactivos'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Retiros por Tipo (Donut)
    if (datosPorTipo.labels.length > 0) {
        const chartPorTipo = new ApexCharts(document.querySelector("#chartPorTipo"), {
            series: datosPorTipo.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosPorTipo.labels,
            colors: ['#DC3545', '#FFC107'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' retiros' } }
            }
        });
        chartPorTipo.render();
    }

    // Gráfica: Retiros por Mes (Línea)
    if (datosRetirosPorMes.labels.length > 0) {
        const chartRetirosPorMes = new ApexCharts(document.querySelector("#chartRetirosPorMes"), {
            series: [{
                name: 'Retiros',
                data: datosRetirosPorMes.data
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
                categories: datosRetirosPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' retiros' } }
            }
        });
        chartRetirosPorMes.render();
    }

    // Gráfica: Activos vs Inactivos (Barras)
    if (datosActivosVsInactivos.labels.length > 0) {
        const chartActivosVsInactivos = new ApexCharts(document.querySelector("#chartActivosVsInactivos"), {
            series: [{
                name: 'Cantidad',
                data: datosActivosVsInactivos.data
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            colors: ['#28A745', '#6C757D'],
            xaxis: {
                categories: datosActivosVsInactivos.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' retiros' } }
            }
        });
        chartActivosVsInactivos.render();
    }
});
</script>
<script>
function confirmarEliminacion(id) {
    if (confirm('¿Está seguro de eliminar este retiro?')) {
        document.getElementById('delete-form').action = '{{ route("admin.retiros.index") }}/' + id;
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection

