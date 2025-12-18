@extends('layouts.master')

@section('title', 'Gestión de Medicamentos')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-syringe"></i> Gestión de Medicamentos
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Medicamentos</li>
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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Lista de Medicamentos</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.medicamentos.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Medicamento
                    </a>
                    <a href="{{ route('admin.medicamentos.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.medicamentos.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.medicamentos.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.medicamentos.index') }}" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar..." value="{{ request('search') }}">
                    <select name="tipo" class="form-control mr-2">
                        <option value="">Todos los tipos</option>
                        <option value="Antibiótico" {{ request('tipo') == 'Antibiótico' ? 'selected' : '' }}>Antibiótico</option>
                        <option value="Antiparasitario" {{ request('tipo') == 'Antiparasitario' ? 'selected' : '' }}>Antiparasitario</option>
                        <option value="Vacuna" {{ request('tipo') == 'Vacuna' ? 'selected' : '' }}>Vacuna</option>
                    </select>
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('admin.medicamentos.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                </form>

                @if($medicamentos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Principio Activo</th>
                                    <th>Vía Admin.</th>
                                    <th>Retiro (días)</th>
                                    <th>Usos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicamentos as $medicamento)
                                    <tr>
                                        <td>{{ $medicamentos->firstItem() + $loop->index }}</td>
                                        <td><strong>{{ $medicamento->nombre }}</strong></td>
                                        <td>{{ $medicamento->tipo ?? '-' }}</td>
                                        <td>{{ $medicamento->principio_activo ?? '-' }}</td>
                                        <td>{{ $medicamento->via_administracion ?? '-' }}</td>
                                        <td>
                                            @if($medicamento->periodo_retiro_dias > 0)
                                                <span class="badge badge-warning">{{ $medicamento->periodo_retiro_dias }} días</span>
                                            @else
                                                <span class="badge badge-success">Sin retiro</span>
                                            @endif
                                        </td>
                                        <td>{{ $medicamento->usos_medicamentos_count ?? 0 }}</td>
                                        <td>
                                            @if($medicamento->activo)
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.medicamentos.show', $medicamento->id_medicamento) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.medicamentos.edit', $medicamento->id_medicamento) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $medicamento->id_medicamento }})">
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
                        {{ $medicamentos->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-syringe fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay medicamentos registrados</h5>
                        <a href="{{ route('admin.medicamentos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Registrar Primer Medicamento
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Gráficas ApexCharts -->
    @if(isset($datosGraficas))
    <div class="row mb-3">
        <!-- Gráfica: Medicamentos por Tipo -->
        @if(isset($datosGraficas['por_tipo']) && count($datosGraficas['por_tipo']['labels']) > 0)
        <div class="col-md-4">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Medicamentos por Tipo
                    </h3>
                </div>
                <div class="card-body">
                    <div id="chartPorTipo" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        @endif

        <!-- Gráfica: Stock por Medicamento -->
        @if(isset($datosGraficas['stock_por_medicamento']) && count($datosGraficas['stock_por_medicamento']['labels']) > 0)
        <div class="col-md-4">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Top 10 Medicamentos (Usos)
                    </h3>
                </div>
                <div class="card-body">
                    <div id="chartStockPorMedicamento" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        @endif

        <!-- Gráfica: Próximos a Vencer -->
        @if(isset($datosGraficas['proximos_vencer']) && count($datosGraficas['proximos_vencer']['labels']) > 0)
        <div class="col-md-4">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Próximos a Vencer (30 días)
                    </h3>
                </div>
                <div class="card-body">
                    <div id="chartProximosVencer" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
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
    const datosStockPorMedicamento = @json($datosGraficas['stock_por_medicamento'] ?? ['labels' => [], 'data' => []]);
    const datosProximosVencer = @json($datosGraficas['proximos_vencer'] ?? ['labels' => [], 'dias' => []]);

    // Gráfica: Medicamentos por Tipo (Donut)
    if (datosPorTipo.labels.length > 0) {
        const chartPorTipo = new ApexCharts(document.querySelector("#chartPorTipo"), {
            series: datosPorTipo.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosPorTipo.labels,
            colors: ['#17A2B8', '#28A745', '#FFC107', '#DC3545', '#6C757D'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' medicamentos' } }
            }
        });
        chartPorTipo.render();
    }

    // Gráfica: Stock por Medicamento (Barras horizontales)
    if (datosStockPorMedicamento.labels.length > 0) {
        const chartStockPorMedicamento = new ApexCharts(document.querySelector("#chartStockPorMedicamento"), {
            series: [{
                name: 'Usos',
                data: datosStockPorMedicamento.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                horizontal: true
            },
            colors: ['#17A2B8'],
            xaxis: {
                categories: datosStockPorMedicamento.labels
            },
            yaxis: {
                title: { text: 'Cantidad de usos' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' usos' } }
            }
        });
        chartStockPorMedicamento.render();
    }

    // Gráfica: Próximos a Vencer (Barras)
    if (datosProximosVencer.labels.length > 0) {
        const chartProximosVencer = new ApexCharts(document.querySelector("#chartProximosVencer"), {
            series: [{
                name: 'Días restantes',
                data: datosProximosVencer.dias
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            colors: ['#FFC107'],
            xaxis: {
                categories: datosProximosVencer.labels
            },
            yaxis: {
                title: { text: 'Días' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' días restantes' } }
            }
        });
        chartProximosVencer.render();
    }
});
</script>
<script>
function confirmarEliminacion(id) {
    if (confirm('¿Está seguro de eliminar este medicamento?')) {
        document.getElementById('delete-form').action = '{{ route("admin.medicamentos.index") }}/' + id;
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection

