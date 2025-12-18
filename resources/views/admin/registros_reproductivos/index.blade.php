@extends('layouts.master')

@section('title', 'Registros Reproductivos')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-heart"></i> Registros Reproductivos
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Registros Reproductivos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes con SweetAlert2 -->
        @include('components.sweet-alert')

        <!-- Alertas -->
        @if($vacasProximasParto->count() > 0)
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle"></i> Vacas Próximas al Parto (21 días): {{ $vacasProximasParto->count() }}</h5>
            <ul class="mb-0">
                @foreach($vacasProximasParto->take(5) as $registro)
                    <li>{{ $registro->vaca->codigo }} - Parto probable: {{ $registro->fecha_probable_parto->format('d/m/Y') }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($vacasNecesitanCelo->count() > 0)
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Vacas que Necesitan Revisión de Celo: {{ $vacasNecesitanCelo->count() }}</h5>
        </div>
        @endif

        <!-- Estadísticas -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticas['total_registros'] }}</h3>
                        <p>Total Registros</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $estadisticas['palpaciones_preñadas'] }}</h3>
                        <p>Palpaciones Preñadas</p>
                    </div>
                    <div class="icon"><i class="fas fa-baby"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $estadisticas['vacas_proximas_parto'] }}</h3>
                        <p>Próximas al Parto</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['promedio_dias_abiertos'], 0) }}</h3>
                        <p>Promedio Días Abiertos</p>
                    </div>
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Eventos por Tipo -->
            @if(isset($datosGraficas['por_tipo_evento']) && count($datosGraficas['por_tipo_evento']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Eventos por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartEventosPorTipo" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Vacas Preñadas por Mes -->
            @if(isset($datosGraficas['preñadas_por_mes']) && count($datosGraficas['preñadas_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Palpaciones Preñadas por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPreñadasPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Días Abiertos -->
            @if(isset($datosGraficas['dias_abiertos']) && count($datosGraficas['dias_abiertos']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Distribución Días Abiertos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartDiasAbiertos" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Lista de Registros</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.registros-reproductivos.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    <a href="{{ route('admin.registros-reproductivos.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.registros-reproductivos.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.registros-reproductivos.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.registros-reproductivos.index') }}" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar por código de vaca..." value="{{ request('search') }}">
                    <select name="tipo_evento" class="form-control mr-2">
                        <option value="">Todos los tipos</option>
                        <option value="Inseminación" {{ request('tipo_evento') == 'Inseminación' ? 'selected' : '' }}>Inseminación</option>
                        <option value="Parto" {{ request('tipo_evento') == 'Parto' ? 'selected' : '' }}>Parto</option>
                        <option value="Celo" {{ request('tipo_evento') == 'Celo' ? 'selected' : '' }}>Celo</option>
                        <option value="Palpación" {{ request('tipo_evento') == 'Palpación' ? 'selected' : '' }}>Palpación</option>
                        <option value="Días abiertos" {{ request('tipo_evento') == 'Días abiertos' ? 'selected' : '' }}>Días abiertos</option>
                    </select>
                    <select name="resultado_palpacion" class="form-control mr-2">
                        <option value="">Todos los resultados</option>
                        <option value="Preñada" {{ request('resultado_palpacion') == 'Preñada' ? 'selected' : '' }}>Preñada</option>
                        <option value="Vacia" {{ request('resultado_palpacion') == 'Vacia' ? 'selected' : '' }}>Vacia</option>
                    </select>
                    <select name="id_vaca" class="form-control mr-2">
                        <option value="">Todas las vacas</option>
                        @foreach($vacas as $vaca)
                            <option value="{{ $vaca->id_vaca }}" {{ request('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                {{ $vaca->codigo }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('admin.registros-reproductivos.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                </form>

                @if($registros->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Vaca</th>
                                    <th>Tipo Evento</th>
                                    <th>Fecha</th>
                                    <th>Resultado</th>
                                    <th>Fecha Probable Parto</th>
                                    <th>Días Abiertos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registros as $registro)
                                    <tr>
                                        <td>{{ $registros->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $registro->vaca->codigo }}</strong><br>
                                            <small>{{ $registro->vaca->raza }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $registro->tipo_evento == 'Parto' ? 'success' : ($registro->tipo_evento == 'Palpación' ? 'info' : 'warning') }}">
                                                {{ $registro->tipo_evento }}
                                            </span>
                                        </td>
                                        <td>{{ $registro->fecha_evento->format('d/m/Y') }}</td>
                                        <td>
                                            @if($registro->resultado_palpacion)
                                                <span class="badge badge-{{ $registro->resultado_palpacion == 'Preñada' ? 'success' : 'secondary' }}">
                                                    {{ $registro->resultado_palpacion }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($registro->fecha_probable_parto)
                                                {{ $registro->fecha_probable_parto->format('d/m/Y') }}
                                                @if($registro->estaProximoAlParto())
                                                    <br><small class="badge badge-warning">Próximo</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($registro->dias_abiertos)
                                                {{ $registro->dias_abiertos }} días
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.registros-reproductivos.show', $registro->id_registro) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.registros-reproductivos.edit', $registro->id_registro) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $registro->id_registro }})">
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
                        {{ $registros->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay registros reproductivos</h5>
                        <a href="{{ route('admin.registros-reproductivos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Registrar Primer Evento
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
    // Datos para las gráficas
    const datosPorTipoEvento = @json($datosGraficas['por_tipo_evento'] ?? ['labels' => [], 'data' => []]);
    const datosPreñadasPorMes = @json($datosGraficas['preñadas_por_mes'] ?? ['labels' => [], 'data' => []]);
    const datosDiasAbiertos = @json($datosGraficas['dias_abiertos'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Eventos por Tipo (Donut)
    if (datosPorTipoEvento.labels.length > 0) {
        const chartEventosPorTipo = new ApexCharts(document.querySelector("#chartEventosPorTipo"), {
            series: datosPorTipoEvento.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosPorTipoEvento.labels,
            colors: ['#17A2B8', '#28A745', '#FFC107', '#DC3545', '#6C757D'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' eventos' } }
            }
        });
        chartEventosPorTipo.render();
    }

    // Gráfica: Preñadas por Mes (Línea)
    if (datosPreñadasPorMes.labels.length > 0) {
        const chartPreñadasPorMes = new ApexCharts(document.querySelector("#chartPreñadasPorMes"), {
            series: [{
                name: 'Palpaciones Preñadas',
                data: datosPreñadasPorMes.data
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
                categories: datosPreñadasPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' palpaciones' } }
            }
        });
        chartPreñadasPorMes.render();
    }

    // Gráfica: Días Abiertos (Barras)
    if (datosDiasAbiertos.labels.length > 0) {
        const chartDiasAbiertos = new ApexCharts(document.querySelector("#chartDiasAbiertos"), {
            series: [{
                name: 'Cantidad',
                data: datosDiasAbiertos.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: true }
            },
            colors: ['#17A2B8'],
            xaxis: {
                categories: datosDiasAbiertos.labels
            },
            yaxis: {
                title: { text: 'Cantidad de Vacas' }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false
                }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' vacas' } }
            }
        });
        chartDiasAbiertos.render();
    }
});

function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará el registro reproductivo. Esta acción no se puede deshacer.",
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
            form.action = '{{ route("admin.registros-reproductivos.index") }}/' + id;
            
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

