@extends('layouts.master')

@section('title', 'Uso de Medicamentos')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-pills"></i> Uso de Medicamentos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Uso de Medicamentos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes con SweetAlert2 -->
        @include('components.sweet-alert')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Registros de Uso</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.uso-medicamentos.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    <a href="{{ route('admin.uso-medicamentos.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.uso-medicamentos.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.uso-medicamentos.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.uso-medicamentos.index') }}" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar por código de vaca..." value="{{ request('search') }}">
                    <select name="id_vaca" class="form-control mr-2">
                        <option value="">Todas las vacas</option>
                        @foreach($vacas as $vaca)
                            <option value="{{ $vaca->id_vaca }}" {{ request('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                {{ $vaca->codigo }}
                            </option>
                        @endforeach
                    </select>
                    <select name="id_medicamento" class="form-control mr-2">
                        <option value="">Todos los medicamentos</option>
                        @foreach($medicamentos as $medicamento)
                            <option value="{{ $medicamento->id_medicamento }}" {{ request('id_medicamento') == $medicamento->id_medicamento ? 'selected' : '' }}>
                                {{ $medicamento->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('admin.uso-medicamentos.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                </form>

                @if($usos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Vaca</th>
                                    <th>Medicamento</th>
                                    <th>Fecha Aplicación</th>
                                    <th>Dosis</th>
                                    <th>Personal</th>
                                    <th>Retiro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usos as $uso)
                                    <tr>
                                        <td>{{ $usos->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $uso->vaca->codigo }}</strong><br>
                                            <small>{{ $uso->vaca->raza }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $uso->medicamento->nombre }}</strong><br>
                                            @if($uso->medicamento->periodo_retiro_dias > 0)
                                                <small class="badge badge-warning">{{ $uso->medicamento->periodo_retiro_dias }} días retiro</small>
                                            @endif
                                        </td>
                                        <td>{{ $uso->fecha_aplicacion->format('d/m/Y') }}</td>
                                        <td>
                                            @if($uso->dosis_aplicada)
                                                {{ number_format($uso->dosis_aplicada, 2) }} {{ $uso->unidad_dosis ?? '' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $uso->personal->nombre ?? '-' }}</td>
                                        <td>
                                            @if($uso->retiro)
                                                @if($uso->retiro->estaActivo())
                                                    <span class="badge badge-danger">
                                                        Activo hasta {{ $uso->retiro->fecha_fin->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">Finalizado</span>
                                                @endif
                                            @else
                                                <span class="badge badge-success">Sin retiro</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.uso-medicamentos.show', $uso->id_uso) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.uso-medicamentos.edit', $uso->id_uso) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $uso->id_uso }})">
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
                        {{ $usos->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay registros de uso</h5>
                        <a href="{{ route('admin.uso-medicamentos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Registrar Primer Uso
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Uso por Medicamento -->
            @if(isset($datosGraficas['uso_por_medicamento']) && count($datosGraficas['uso_por_medicamento']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Uso por Medicamento
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartUsoPorMedicamento" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Aplicaciones por Mes -->
            @if(isset($datosGraficas['aplicaciones_por_mes']) && count($datosGraficas['aplicaciones_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Aplicaciones por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartAplicacionesPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Top Vacas -->
            @if(isset($datosGraficas['top_vacas']) && count($datosGraficas['top_vacas']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Top 10 Vacas por Aplicaciones
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartTopVacas" style="min-height: 300px;"></div>
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
    const datosUsoPorMedicamento = @json($datosGraficas['uso_por_medicamento'] ?? ['labels' => [], 'data' => []]);
    const datosAplicacionesPorMes = @json($datosGraficas['aplicaciones_por_mes'] ?? ['labels' => [], 'data' => []]);
    const datosTopVacas = @json($datosGraficas['top_vacas'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Uso por Medicamento (Donut)
    if (datosUsoPorMedicamento.labels.length > 0) {
        const chartUsoPorMedicamento = new ApexCharts(document.querySelector("#chartUsoPorMedicamento"), {
            series: datosUsoPorMedicamento.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosUsoPorMedicamento.labels,
            colors: ['#17A2B8', '#28A745', '#FFC107', '#DC3545', '#6C757D', '#6610F2', '#E83E8C', '#20C997'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' aplicaciones' } }
            }
        });
        chartUsoPorMedicamento.render();
    }

    // Gráfica: Aplicaciones por Mes (Línea)
    if (datosAplicacionesPorMes.labels.length > 0) {
        const chartAplicacionesPorMes = new ApexCharts(document.querySelector("#chartAplicacionesPorMes"), {
            series: [{
                name: 'Aplicaciones',
                data: datosAplicacionesPorMes.data
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
                categories: datosAplicacionesPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' aplicaciones' } }
            }
        });
        chartAplicacionesPorMes.render();
    }

    // Gráfica: Top Vacas (Barras horizontales)
    if (datosTopVacas.labels.length > 0) {
        const chartTopVacas = new ApexCharts(document.querySelector("#chartTopVacas"), {
            series: [{
                name: 'Aplicaciones',
                data: datosTopVacas.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                horizontal: true
            },
            colors: ['#6C757D'],
            xaxis: {
                categories: datosTopVacas.labels
            },
            yaxis: {
                title: { text: 'Cantidad de aplicaciones' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' aplicaciones' } }
            }
        });
        chartTopVacas.render();
    }
});
</script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará el registro de uso de medicamento. Si tiene un retiro asociado, también será eliminado. Esta acción no se puede deshacer.",
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
            form.action = '{{ route("admin.uso-medicamentos.index") }}/' + id;
            
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

