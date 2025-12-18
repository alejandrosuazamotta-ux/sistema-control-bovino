@extends('layouts.master')

@section('title', 'Alertas y Notificaciones')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-bell"></i> Alertas y Notificaciones
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Alertas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <!-- Contador de Alertas -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $contador['total'] }}</h3>
                        <p>Total Alertas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $contador['urgentes'] }}</h3>
                        <p>Urgentes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $contador['advertencias'] }}</h3>
                        <p>Advertencias</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $contador['informacion'] }}</h3>
                        <p>Información</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            @if(isset($datosGraficas['por_tipo']) && count($datosGraficas['por_tipo']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Alertas por Tipo (Últimos 30 días)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaPorTipo" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datosGraficas['por_estado']) && count($datosGraficas['por_estado']['labels']) > 0)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-doughnut mr-1"></i>
                            Alertas por Estado
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaPorEstado" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($datosGraficas['por_mes']) && count($datosGraficas['por_mes']['labels']) > 0)
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Alertas por Mes (Últimos 6 meses)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="graficaPorMes" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Lista de Alertas
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="generarAlertas()">
                        <i class="fas fa-sync"></i> Generar Alertas
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="marcarTodasLeidas()">
                        <i class="fas fa-check-double"></i> Marcar Todas como Leídas
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.alertas.index') }}" class="form-inline">
                            <div class="input-group mr-2">
                                <select name="tipo" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="preparto" {{ request('tipo') == 'preparto' ? 'selected' : '' }}>Preparto</option>
                                    <option value="celo" {{ request('tipo') == 'celo' ? 'selected' : '' }}>Celo</option>
                                    <option value="destete" {{ request('tipo') == 'destete' ? 'selected' : '' }}>Destete</option>
                                    <option value="retiro" {{ request('tipo') == 'retiro' ? 'selected' : '' }}>Retiro</option>
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="nivel" class="form-control">
                                    <option value="">Todos los niveles</option>
                                    <option value="urgente" {{ request('nivel') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                    <option value="advertencia" {{ request('nivel') == 'advertencia' ? 'selected' : '' }}>Advertencia</option>
                                    <option value="informacion" {{ request('nivel') == 'informacion' ? 'selected' : '' }}>Información</option>
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="leida" class="form-control">
                                    <option value="">Todas</option>
                                    <option value="0" {{ request('leida') === '0' ? 'selected' : '' }}>No Leídas</option>
                                    <option value="1" {{ request('leida') === '1' ? 'selected' : '' }}>Leídas</option>
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="estado" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="vista" {{ request('estado') == 'vista' ? 'selected' : '' }}>Vista</option>
                                    <option value="atendida" {{ request('estado') == 'atendida' ? 'selected' : '' }}>Atendida</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Filtrar</button>
                            <a href="{{ route('admin.alertas.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Alertas -->
                @if($alertas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Nivel</th>
                                    <th width="15%">Tipo</th>
                                    <th width="25%">Título</th>
                                    <th width="35%">Mensaje</th>
                                    <th width="8%">Estado</th>
                                    <th width="8%">Fecha</th>
                                    <th width="12%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alertas as $alerta)
                                    <tr class="{{ $alerta->leida ? '' : 'table-warning' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($alerta->nivel === 'urgente')
                                                <span class="badge badge-danger">Urgente</span>
                                            @elseif($alerta->nivel === 'advertencia')
                                                <span class="badge badge-warning">Advertencia</span>
                                            @else
                                                <span class="badge badge-info">Información</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($alerta->tipo === 'preparto')
                                                <i class="fas fa-baby"></i> Preparto
                                            @elseif($alerta->tipo === 'celo')
                                                <i class="fas fa-heart"></i> Celo
                                            @elseif($alerta->tipo === 'destete')
                                                <i class="fas fa-baby-carriage"></i> Destete
                                            @elseif($alerta->tipo === 'retiro')
                                                <i class="fas fa-ban"></i> Retiro
                                            @else
                                                {{ $alerta->tipo }}
                                            @endif
                                        </td>
                                        <td>{{ $alerta->titulo }}</td>
                                        <td>{{ $alerta->mensaje }}</td>
                                        <td>
                                            @if($alerta->estado === 'pendiente')
                                                <span class="badge badge-warning">Pendiente</span>
                                            @elseif($alerta->estado === 'vista')
                                                <span class="badge badge-info">Vista</span>
                                            @elseif($alerta->estado === 'atendida')
                                                <span class="badge badge-success">Atendida</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $alerta->estado ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $alerta->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($alerta->estado !== 'atendida')
                                                <button type="button" class="btn btn-sm btn-primary" onclick="marcarAtendida({{ $alerta->id_notificacion }})" title="Marcar como atendida">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
                                            @if(!$alerta->leida)
                                                <button type="button" class="btn btn-sm btn-success" onclick="marcarLeida({{ $alerta->id_notificacion }})" title="Marcar como leída">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $alertas->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay alertas para mostrar con los filtros seleccionados.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function marcarLeida(id) {
    fetch(`{{ route('admin.alertas.marcar-leida', '') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al marcar la alerta',
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al procesar la solicitud',
            confirmButtonText: 'Aceptar'
        });
    });
}

function marcarAtendida(id) {
    fetch(`{{ route('admin.alertas.marcar-atendida', '') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al marcar la alerta',
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al procesar la solicitud',
            confirmButtonText: 'Aceptar'
        });
    });
}

function marcarTodasLeidas() {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Se marcarán todas las alertas como leídas',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, marcar todas',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('admin.alertas.marcar-todas-leidas') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al marcar las alertas',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}

function generarAlertas() {
    Swal.fire({
        title: 'Generando alertas...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('{{ route('admin.alertas.generar') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                html: `<p>${data.message}</p>
                       <ul class="text-left mt-3">
                           <li>Preparto (21 días): ${data.resultados.preparto_21}</li>
                           <li>Preparto (7 días): ${data.resultados.preparto_7}</li>
                           <li>Celo: ${data.resultados.celo}</li>
                           <li>Destete: ${data.resultados.destete}</li>
                           <li>Retiros Activos: ${data.resultados.retiros_activos}</li>
                       </ul>`,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al generar las alertas',
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al procesar la solicitud',
            confirmButtonText: 'Aceptar'
        });
    });
}

// Gráficas ApexCharts
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($datosGraficas))
        @if(isset($datosGraficas['por_tipo']) && count($datosGraficas['por_tipo']['labels']) > 0)
        // Gráfica por Tipo
        var optionsPorTipo = {
            series: @json($datosGraficas['por_tipo']['data']),
            chart: {
                type: 'donut',
                height: 300
            },
            labels: @json($datosGraficas['por_tipo']['labels']),
            colors: ['#dc3545', '#ffc107', '#17a2b8', '#28a745', '#6c757d'],
            legend: { position: 'bottom' }
        };
        var chartPorTipo = new ApexCharts(document.querySelector("#graficaPorTipo"), optionsPorTipo);
        chartPorTipo.render();
        @endif

        @if(isset($datosGraficas['por_estado']) && count($datosGraficas['por_estado']['labels']) > 0)
        // Gráfica por Estado
        var optionsPorEstado = {
            series: @json($datosGraficas['por_estado']['data']),
            chart: {
                type: 'pie',
                height: 300
            },
            labels: @json($datosGraficas['por_estado']['labels']),
            colors: ['#ffc107', '#17a2b8', '#28a745'],
            legend: { position: 'bottom' }
        };
        var chartPorEstado = new ApexCharts(document.querySelector("#graficaPorEstado"), optionsPorEstado);
        chartPorEstado.render();
        @endif

        @if(isset($datosGraficas['por_mes']) && count($datosGraficas['por_mes']['labels']) > 0)
        // Gráfica por Mes
        var optionsPorMes = {
            series: [{
                name: 'Alertas',
                data: @json($datosGraficas['por_mes']['data'])
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: true }
            },
            colors: ['#dc3545'],
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: @json($datosGraficas['por_mes']['labels'])
            },
            yaxis: {
                title: { text: 'Cantidad de Alertas' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' alertas' } }
            }
        };
        var chartPorMes = new ApexCharts(document.querySelector("#graficaPorMes"), optionsPorMes);
        chartPorMes.render();
        @endif
    @endif
});
</script>
@endpush
@endsection

