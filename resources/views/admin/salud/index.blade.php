@extends('layouts.master')

@section('title', 'Gestión de Salud y Pruebas Sanitarias')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-vial"></i> Gestión de Salud y Pruebas Sanitarias
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Salud</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes con SweetAlert2 -->
        @include('components.sweet-alert')

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticas['total_registros'] ?? 0 }}</h3>
                        <p>Total Registros</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $estadisticas['resultados_positivos'] ?? 0 }}</h3>
                        <p>Resultados Positivos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $estadisticas['vacas_con_restriccion'] ?? 0 }}</h3>
                        <p>Con Restricción Ordeño</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $estadisticas['vacas_inhabilitadas'] ?? 0 }}</h3>
                        <p>Vacas Inhabilitadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Pruebas por Tipo -->
            @if(isset($datosGraficas['por_tipo']) && count($datosGraficas['por_tipo']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Pruebas por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPruebasPorTipo" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Resultados -->
            @if(isset($datosGraficas['por_resultado']) && count($datosGraficas['por_resultado']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Resultados de Pruebas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartResultados" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Pruebas por Mes -->
            @if(isset($datosGraficas['por_mes']) && count($datosGraficas['por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Pruebas por Mes (12 meses)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPruebasPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Card principal con tabla -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Registros de Salud
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.salud.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    <a href="{{ route('admin.salud.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.salud.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.salud.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" action="{{ route('admin.salud.index') }}" class="form-inline mb-3">
                    <div class="form-group mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por código de vaca..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="form-group mr-2">
                        <select name="tipo_registro" class="form-control">
                            <option value="">Todos los tipos</option>
                            <option value="Vacunación" {{ request('tipo_registro') == 'Vacunación' ? 'selected' : '' }}>Vacunación</option>
                            <option value="Tratamiento" {{ request('tipo_registro') == 'Tratamiento' ? 'selected' : '' }}>Tratamiento</option>
                            <option value="Prueba mastitis" {{ request('tipo_registro') == 'Prueba mastitis' ? 'selected' : '' }}>Prueba Mastitis</option>
                            <option value="Prueba Brucelosis" {{ request('tipo_registro') == 'Prueba Brucelosis' ? 'selected' : '' }}>Prueba Brucelosis</option>
                            <option value="Prueba Tuberculosis" {{ request('tipo_registro') == 'Prueba Tuberculosis' ? 'selected' : '' }}>Prueba Tuberculosis</option>
                            <option value="Otro" {{ request('tipo_registro') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select name="tipo_prueba" class="form-control">
                            <option value="">Todas las pruebas</option>
                            <option value="Mastitis" {{ request('tipo_prueba') == 'Mastitis' ? 'selected' : '' }}>Mastitis</option>
                            <option value="Brucelosis" {{ request('tipo_prueba') == 'Brucelosis' ? 'selected' : '' }}>Brucelosis</option>
                            <option value="Tuberculosis" {{ request('tipo_prueba') == 'Tuberculosis' ? 'selected' : '' }}>Tuberculosis</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select name="resultado" class="form-control">
                            <option value="">Todos los resultados</option>
                            <option value="Positivo" {{ request('resultado') == 'Positivo' ? 'selected' : '' }}>Positivo</option>
                            <option value="Negativo" {{ request('resultado') == 'Negativo' ? 'selected' : '' }}>Negativo</option>
                            <option value="Pendiente" {{ request('resultado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <input type="date" name="fecha_inicio" class="form-control" placeholder="Fecha inicio" 
                               value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="form-group mr-2">
                        <input type="date" name="fecha_fin" class="form-control" placeholder="Fecha fin" 
                               value="{{ request('fecha_fin') }}">
                    </div>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.salud.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </form>

                @if($registros->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Vaca</th>
                                    <th width="12%">Tipo Registro</th>
                                    <th width="10%">Tipo Prueba</th>
                                    <th width="8%">Resultado</th>
                                    <th width="8%">Severidad</th>
                                    <th width="10%">Fecha</th>
                                    <th width="8%">Restricción</th>
                                    <th width="8%">Inhabilitada</th>
                                    <th width="21%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registros as $registro)
                                    <tr class="{{ $registro->resultado === 'Positivo' ? 'table-danger' : ($registro->resultado === 'Pendiente' ? 'table-warning' : '') }}">
                                        <td>{{ $loop->iteration + ($registros->currentPage() - 1) * $registros->perPage() }}</td>
                                        <td>
                                            <strong>{{ $registro->vaca->codigo ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $registro->tipo_registro }}</span>
                                        </td>
                                        <td>
                                            @if($registro->tipo_prueba)
                                                @if($registro->tipo_prueba === 'Mastitis')
                                                    <span class="badge badge-warning"><i class="fas fa-vial"></i> {{ $registro->tipo_prueba }}</span>
                                                @elseif($registro->tipo_prueba === 'Brucelosis')
                                                    <span class="badge badge-danger"><i class="fas fa-biohazard"></i> {{ $registro->tipo_prueba }}</span>
                                                @elseif($registro->tipo_prueba === 'Tuberculosis')
                                                    <span class="badge badge-danger"><i class="fas fa-lungs"></i> {{ $registro->tipo_prueba }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $registro->tipo_prueba }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($registro->resultado)
                                                @if($registro->resultado === 'Positivo')
                                                    <span class="badge badge-danger">{{ $registro->resultado }}</span>
                                                @elseif($registro->resultado === 'Negativo')
                                                    <span class="badge badge-success">{{ $registro->resultado }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ $registro->resultado }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($registro->severidad)
                                                <span class="badge badge-{{ $registro->severidad === 'Severa' ? 'danger' : ($registro->severidad === 'Moderada' ? 'warning' : 'info') }}">
                                                    {{ $registro->severidad }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                                        <td>
                                            @if($registro->restriccion_ordeño)
                                                <span class="badge badge-danger"><i class="fas fa-ban"></i> Sí</span>
                                            @else
                                                <span class="badge badge-success">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($registro->inhabilitada)
                                                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Sí</span>
                                            @else
                                                <span class="badge badge-success">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.salud.show', $registro->id_salud) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.salud.edit', $registro->id_salud) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $registro->id_salud }})" title="Eliminar">
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
                    <div class="d-flex justify-content-center mt-3">
                        {{ $registros->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay registros de salud para mostrar con los filtros seleccionados.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para las gráficas
    const datosPorTipo = @json($datosGraficas['por_tipo'] ?? ['labels' => [], 'data' => []]);
    const datosPorResultado = @json($datosGraficas['por_resultado'] ?? ['labels' => [], 'data' => []]);
    const datosPorMes = @json($datosGraficas['por_mes'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Pruebas por Tipo (Donut)
    if (datosPorTipo.labels.length > 0) {
        const chartPorTipo = new ApexCharts(document.querySelector("#chartPruebasPorTipo"), {
            series: datosPorTipo.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosPorTipo.labels,
            colors: ['#FFC107', '#DC3545', '#17A2B8', '#6C757D'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' pruebas' } }
            }
        });
        chartPorTipo.render();
    }

    // Gráfica: Resultados (Barras)
    if (datosPorResultado.labels.length > 0) {
        const chartResultados = new ApexCharts(document.querySelector("#chartResultados"), {
            series: [{
                name: 'Cantidad',
                data: datosPorResultado.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: true }
            },
            colors: ['#28A745', '#DC3545', '#FFC107'],
            xaxis: {
                categories: datosPorResultado.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false
                }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' pruebas' } }
            }
        });
        chartResultados.render();
    }

    // Gráfica: Pruebas por Mes (Línea)
    if (datosPorMes.labels.length > 0) {
        const chartPorMes = new ApexCharts(document.querySelector("#chartPruebasPorMes"), {
            series: [{
                name: 'Pruebas',
                data: datosPorMes.data
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: true },
                zoom: { enabled: false }
            },
            colors: ['#0D6EFD'],
            xaxis: {
                categories: datosPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad de Pruebas' }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 5
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' pruebas' } }
            }
        });
        chartPorMes.render();
    }
});

// Confirmar eliminación
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario para enviar DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/salud/${id}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
