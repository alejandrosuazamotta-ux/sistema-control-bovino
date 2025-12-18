@extends('layouts.master')

@section('title', 'Gestión de Pruebas Sanitarias')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-vial"></i> Gestión de Pruebas Sanitarias
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pruebas Sanitarias</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticas['total'] ?? 0 }}</h3>
                        <p>Total Pruebas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-vial"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $estadisticas['positivas'] ?? 0 }}</h3>
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
                        <h3>{{ $estadisticas['pendientes'] ?? 0 }}</h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $estadisticas['con_restriccion_ordeño'] ?? 0 }}</h3>
                        <p>Con Restricción Ordeño</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            @if(isset($datosGraficas['por_tipo']) && count($datosGraficas['por_tipo']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Pruebas por Tipo (30 días)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPruebasPorTipo" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datosGraficas['por_resultado']) && count($datosGraficas['por_resultado']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Pruebas por Resultado (30 días)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPruebasPorResultado" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Registros de Pruebas Sanitarias
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.pruebas-sanitarias.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Prueba
                    </a>
                    <a href="{{ route('admin.pruebas-sanitarias.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.pruebas-sanitarias.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.pruebas-sanitarias.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" action="{{ route('admin.pruebas-sanitarias.index') }}" class="form-inline mb-3">
                    <div class="form-group mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por código de vaca..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="form-group mr-2">
                        <select name="tipo_prueba" class="form-control">
                            <option value="">Todos los tipos</option>
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
                    <div class="form-group mr-2">
                        <select name="cerrada" class="form-control">
                            <option value="">Todas</option>
                            <option value="0" {{ request('cerrada') === '0' ? 'selected' : '' }}>Abiertas</option>
                            <option value="1" {{ request('cerrada') === '1' ? 'selected' : '' }}>Cerradas</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.pruebas-sanitarias.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </form>

                @if($pruebas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Vaca</th>
                                    <th width="12%">Tipo Prueba</th>
                                    <th width="8%">Resultado</th>
                                    <th width="8%">Severidad</th>
                                    <th width="10%">Fecha Prueba</th>
                                    <th width="8%">Restricción</th>
                                    <th width="8%">Inhabilitada</th>
                                    <th width="8%">Estado</th>
                                    <th width="23%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pruebas as $index => $prueba)
                                    <tr class="{{ $prueba->resultado === 'Positivo' ? 'table-danger' : ($prueba->resultado === 'Pendiente' ? 'table-warning' : '') }}">
                                        <td>{{ $pruebas->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $prueba->vaca->codigo ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            @if($prueba->tipo_prueba === 'Mastitis')
                                                <span class="badge badge-warning"><i class="fas fa-vial"></i> {{ $prueba->tipo_prueba }}</span>
                                            @elseif($prueba->tipo_prueba === 'Brucelosis')
                                                <span class="badge badge-danger"><i class="fas fa-biohazard"></i> {{ $prueba->tipo_prueba }}</span>
                                            @elseif($prueba->tipo_prueba === 'Tuberculosis')
                                                <span class="badge badge-danger"><i class="fas fa-lungs"></i> {{ $prueba->tipo_prueba }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($prueba->resultado === 'Positivo')
                                                <span class="badge badge-danger">{{ $prueba->resultado }}</span>
                                            @elseif($prueba->resultado === 'Negativo')
                                                <span class="badge badge-success">{{ $prueba->resultado }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ $prueba->resultado }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($prueba->severidad)
                                                <span class="badge badge-{{ $prueba->severidad === 'Severa' ? 'danger' : ($prueba->severidad === 'Moderada' ? 'warning' : 'info') }}">
                                                    {{ $prueba->severidad }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $prueba->fecha_prueba->format('d/m/Y') }}</td>
                                        <td>
                                            @if($prueba->restriccion_ordeño)
                                                <span class="badge badge-danger"><i class="fas fa-ban"></i> Sí</span>
                                            @else
                                                <span class="badge badge-success">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($prueba->inhabilitada)
                                                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Sí</span>
                                            @else
                                                <span class="badge badge-success">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($prueba->cerrada)
                                                <span class="badge badge-secondary"><i class="fas fa-lock"></i> Cerrada</span>
                                            @else
                                                <span class="badge badge-success"><i class="fas fa-unlock"></i> Abierta</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.pruebas-sanitarias.show', $prueba->id_prueba) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$prueba->cerrada)
                                                    <a href="{{ route('admin.pruebas-sanitarias.edit', $prueba->id_prueba) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @can('delete', $prueba)
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $prueba->id_prueba }})" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $pruebas->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay pruebas sanitarias</h4>
                        <p class="text-muted">No se encontraron pruebas sanitarias con los filtros aplicados.</p>
                        <a href="{{ route('admin.pruebas-sanitarias.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Prueba
                        </a>
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
    const datosGraficas = @json($datosGraficas ?? []);

    // Gráfica: Pruebas por Tipo
    if (datosGraficas.por_tipo && datosGraficas.por_tipo.labels.length > 0) {
        const chartPruebasPorTipo = new ApexCharts(document.querySelector("#chartPruebasPorTipo"), {
            series: datosGraficas.por_tipo.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosGraficas.por_tipo.labels,
            colors: ['#28A745', '#DC3545', '#FFC107'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' pruebas' } }
            }
        });
        chartPruebasPorTipo.render();
    }

    // Gráfica: Pruebas por Resultado
    if (datosGraficas.por_resultado && datosGraficas.por_resultado.labels.length > 0) {
        const chartPruebasPorResultado = new ApexCharts(document.querySelector("#chartPruebasPorResultado"), {
            series: [{
                name: 'Cantidad',
                data: datosGraficas.por_resultado.data
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            colors: ['#0D6EFD'],
            xaxis: {
                categories: datosGraficas.por_resultado.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            dataLabels: {
                enabled: true
            }
        });
        chartPruebasPorResultado.render();
    }

    // Gráfica: Pruebas por Mes
    if (datosGraficas.por_mes && datosGraficas.por_mes.labels.length > 0) {
        const chartPruebasPorMes = new ApexCharts(document.querySelector("#chartPruebasPorMes"), {
            series: [{
                name: 'Pruebas',
                data: datosGraficas.por_mes.data
            }],
            chart: {
                type: 'line',
                height: 300
            },
            colors: ['#28A745'],
            xaxis: {
                categories: datosGraficas.por_mes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            }
        });
        chartPruebasPorMes.render();
    }
});

function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará la prueba sanitaria. Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.pruebas-sanitarias.index") }}/' + id;
            
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

