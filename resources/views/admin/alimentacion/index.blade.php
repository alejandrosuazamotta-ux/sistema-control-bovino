@extends('layouts.master')

@section('title', 'Gestión de Alimentación')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-seedling"></i> Gestión de Alimentación
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Alimentación</li>
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
                    <i class="fas fa-list"></i> Registros de Alimentación
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.alimentacion.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    <a href="{{ route('admin.alimentacion.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.alimentacion.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.alimentacion.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.alimentacion.index') }}" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar por código de vaca..." value="{{ request('search') }}">
                    <select name="id_vaca" class="form-control mr-2">
                        <option value="">Todas las vacas</option>
                        @foreach($vacas as $vaca)
                            <option value="{{ $vaca->id_vaca }}" {{ request('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                {{ $vaca->codigo }}
                            </option>
                        @endforeach
                    </select>
                    <select name="tipo_alimento" class="form-control mr-2">
                        <option value="">Todos los tipos</option>
                        <option value="Ensilaje" {{ request('tipo_alimento') == 'Ensilaje' ? 'selected' : '' }}>Ensilaje</option>
                        <option value="Pasto" {{ request('tipo_alimento') == 'Pasto' ? 'selected' : '' }}>Pasto</option>
                        <option value="Concentrado" {{ request('tipo_alimento') == 'Concentrado' ? 'selected' : '' }}>Concentrado</option>
                        <option value="Subproducto" {{ request('tipo_alimento') == 'Subproducto' ? 'selected' : '' }}>Subproducto</option>
                        <option value="Otro" {{ request('tipo_alimento') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    <input type="date" name="fecha_inicio" class="form-control mr-2" placeholder="Fecha inicio" value="{{ request('fecha_inicio') }}">
                    <input type="date" name="fecha_fin" class="form-control mr-2" placeholder="Fecha fin" value="{{ request('fecha_fin') }}">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.alimentacion.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </form>

                @if($registros->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Vaca</th>
                                    <th>Fecha</th>
                                    <th>Tipo Alimento</th>
                                    <th>Cantidad (kg)</th>
                                    <th>Personal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registros as $registro)
                                    <tr>
                                        <td>{{ $loop->iteration + ($registros->currentPage() - 1) * $registros->perPage() }}</td>
                                        <td>{{ $registro->vaca->codigo ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}</td>
                                        <td><span class="badge badge-info">{{ $registro->tipo_alimento }}</span></td>
                                        <td>{{ number_format($registro->cantidad, 2) }}</td>
                                        <td>{{ $registro->personal->nombre ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.alimentacion.show', $registro->id_alimentacion) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.alimentacion.edit', $registro->id_alimentacion) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $registro->id_alimentacion }})" title="Eliminar">
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
                        {{ $registros->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay registros de alimentación para mostrar con los filtros seleccionados.
                    </div>
                @endif
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Alimentación por Tipo -->
            @if(isset($datosGraficas['por_tipo_alimento']) && count($datosGraficas['por_tipo_alimento']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Alimentación por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPorTipoAlimento" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Consumo por Mes -->
            @if(isset($datosGraficas['consumo_por_mes']) && count($datosGraficas['consumo_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Consumo por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartConsumoPorMes" style="min-height: 300px;"></div>
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
                            Top 10 Vacas por Consumo
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
    const datosPorTipoAlimento = @json($datosGraficas['por_tipo_alimento'] ?? ['labels' => [], 'data' => []]);
    const datosConsumoPorMes = @json($datosGraficas['consumo_por_mes'] ?? ['labels' => [], 'data' => []]);
    const datosTopVacas = @json($datosGraficas['top_vacas'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Alimentación por Tipo (Donut)
    if (datosPorTipoAlimento.labels.length > 0) {
        const chartPorTipoAlimento = new ApexCharts(document.querySelector("#chartPorTipoAlimento"), {
            series: datosPorTipoAlimento.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosPorTipoAlimento.labels,
            colors: ['#17A2B8', '#28A745', '#FFC107', '#DC3545', '#6C757D'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' kg' } }
            }
        });
        chartPorTipoAlimento.render();
    }

    // Gráfica: Consumo por Mes (Línea)
    if (datosConsumoPorMes.labels.length > 0) {
        const chartConsumoPorMes = new ApexCharts(document.querySelector("#chartConsumoPorMes"), {
            series: [{
                name: 'Consumo (kg)',
                data: datosConsumoPorMes.data
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
                categories: datosConsumoPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad (kg)' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' kg' } }
            }
        });
        chartConsumoPorMes.render();
    }

    // Gráfica: Top Vacas (Barras horizontales)
    if (datosTopVacas.labels.length > 0) {
        const chartTopVacas = new ApexCharts(document.querySelector("#chartTopVacas"), {
            series: [{
                name: 'Consumo (kg)',
                data: datosTopVacas.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                horizontal: true
            },
            colors: ['#17A2B8'],
            xaxis: {
                categories: datosTopVacas.labels
            },
            yaxis: {
                title: { text: 'Cantidad (kg)' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' kg' } }
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
            form.action = '{{ route("admin.alimentacion.index") }}/' + id;
            
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

