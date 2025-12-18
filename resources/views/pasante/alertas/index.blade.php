@extends('layouts.master')

@section('title', 'Mis Alertas')

@section('content')
@php
    // Protección: Las alertas para Pasante no están implementadas
    header('Location: ' . route('pasante.dashboard'));
    exit;
@endphp
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-bell"></i> Mis Alertas
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
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
                        <h3>{{ $contador['total'] ?? 0 }}</h3>
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
                        <h3>{{ $contador['urgentes'] ?? 0 }}</h3>
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
                        <h3>{{ $contador['pendientes'] ?? 0 }}</h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $contador['vistas'] ?? 0 }}</h3>
                        <p>Vistas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-eye"></i>
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
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Mis Alertas Asignadas
                </h3>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('pasante.alertas.index') }}" class="form-inline">
                            <div class="input-group mr-2">
                                <select name="tipo" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="preparto" {{ request('tipo') == 'preparto' ? 'selected' : '' }}>Preparto</option>
                                    <option value="celo" {{ request('tipo') == 'celo' ? 'selected' : '' }}>Celo</option>
                                    <option value="destete" {{ request('tipo') == 'destete' ? 'selected' : '' }}>Destete</option>
                                    <option value="retiro" {{ request('tipo') == 'retiro' ? 'selected' : '' }}>Retiro</option>
                                    <option value="mastitis" {{ request('tipo') == 'mastitis' ? 'selected' : '' }}>Mastitis</option>
                                    <option value="inventario_stock_bajo" {{ request('tipo') == 'inventario_stock_bajo' ? 'selected' : '' }}>Stock Bajo</option>
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
                                <select name="estado" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="vista" {{ request('estado') == 'vista' ? 'selected' : '' }}>Vista</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Filtrar</button>
                            <a href="{{ route('pasante.alertas.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
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
                                    <th width="30%">Mensaje</th>
                                    <th width="8%">Estado</th>
                                    <th width="7%">Fecha</th>
                                    <th width="10%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alertas as $alerta)
                                    <tr class="{{ $alerta->estado === 'pendiente' ? 'table-warning' : '' }}">
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
                                            @elseif($alerta->tipo === 'mastitis')
                                                <i class="fas fa-virus"></i> Mastitis
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
                                            @else
                                                <span class="badge badge-secondary">{{ $alerta->estado ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $alerta->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($alerta->estado === 'pendiente')
                                                <button type="button" class="btn btn-sm btn-primary" onclick="marcarVista({{ $alerta->id_notificacion }})" title="Marcar como vista">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @else
                                                <span class="badge badge-success">Vista</span>
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
                        <i class="fas fa-info-circle"></i> No tienes alertas asignadas con los filtros seleccionados.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
function marcarVista(id) {
    fetch(`{{ route('pasante.alertas.marcar-vista', '') }}/${id}`, {
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
            colors: ['#ffc107', '#17a2b8'],
            legend: { position: 'bottom' }
        };
        var chartPorEstado = new ApexCharts(document.querySelector("#graficaPorEstado"), optionsPorEstado);
        chartPorEstado.render();
        @endif
    @endif
});
</script>
@endpush
@endsection

