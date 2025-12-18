@extends('layouts.master')

@section('title', 'Detalles de Producción Lechera')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-eye"></i> Detalles de Producción Lechera
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.produccion-lechera.index') }}">Producción Lechera</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
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

        <div class="row">
            <!-- Información principal -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Información del Registro
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.produccion-lechera.edit', $registro->id_produccion) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" 
                                    class="btn btn-danger btn-sm" 
                                    onclick="confirmarEliminacion({{ $registro->id_produccion }})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong><i class="fas fa-cow"></i> Vaca:</strong></td>
                                        <td>{{ $registro->vaca->codigo }} - {{ $registro->vaca->raza }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-calendar"></i> Fecha:</strong></td>
                                        <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-clock"></i> Turno:</strong></td>
                                        <td>
                                            @if($registro->turno == 'AM')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-sun"></i> AM (Mañana)
                                                </span>
                                            @else
                                                <span class="badge badge-info">
                                                    <i class="fas fa-moon"></i> PM (Tarde)
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-tint"></i> Cantidad:</strong></td>
                                        <td>
                                            <span class="badge badge-primary">
                                                {{ number_format($registro->cantidad_leche, 2) }} Litros
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-route"></i> Destino:</strong></td>
                                        <td>
                                            @php
                                                $destinoColors = [
                                                    'Agroindustria' => 'success',
                                                    'Lechero' => 'info',
                                                    'Particular' => 'warning',
                                                    'Consumo' => 'secondary'
                                                ];
                                                $color = $destinoColors[$registro->destino] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">
                                                {{ $registro->destino }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($registro->valor_unidad)
                                    <tr>
                                        <td><strong><i class="fas fa-dollar-sign"></i> Valor Unitario:</strong></td>
                                        <td>${{ number_format($registro->valor_unidad, 2) }} COP</td>
                                    </tr>
                                    @endif
                                    @if($registro->valor_total)
                                    <tr>
                                        <td><strong><i class="fas fa-calculator"></i> Valor Total:</strong></td>
                                        <td>
                                            <span class="badge badge-success">
                                                ${{ number_format($registro->valor_total, 2) }} COP
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($registro->excluida_por_retiro)
                                    <tr>
                                        <td><strong><i class="fas fa-exclamation-triangle"></i> Estado:</strong></td>
                                        <td>
                                            <span class="badge badge-danger">
                                                <i class="fas fa-ban"></i> Excluida por Retiro
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($registro->excluida_por_sanidad)
                                    <tr>
                                        <td><strong><i class="fas fa-exclamation-triangle"></i> Estado:</strong></td>
                                        <td>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-vial"></i> Excluida por Sanidad (Mastitis)
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong><i class="fas fa-user"></i> Personal:</strong></td>
                                        <td>{{ $registro->personal->nombre ?? 'N/A' }} @if($registro->personal)({{ $registro->personal->rol }})@endif</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong><i class="fas fa-clock"></i> Creado:</strong></td>
                                        <td>{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-edit"></i> Actualizado:</strong></td>
                                        <td>{{ $registro->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @if(!$registro->excluida_por_retiro && !$registro->excluida_por_sanidad)
                                    <tr>
                                        <td><strong><i class="fas fa-chart-line"></i> Evaluación:</strong></td>
                                        <td>
                                            @if($registro->cantidad_leche >= 20)
                                                <span class="badge badge-success">Excelente</span>
                                            @elseif($registro->cantidad_leche >= 15)
                                                <span class="badge badge-warning">Buena</span>
                                            @elseif($registro->cantidad_leche >= 10)
                                                <span class="badge badge-info">Regular</span>
                                            @else
                                                <span class="badge badge-danger">Baja</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($registro->observaciones)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-sticky-note"></i> Observaciones:</h5>
                                    <div class="alert alert-info">
                                        {{ $registro->observaciones }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estadísticas de la vaca -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Estadísticas de la Vaca
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-tint"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Producción</span>
                                <span class="info-box-number">{{ number_format($estadisticasVaca['total_produccion'], 2) }} L</span>
                            </div>
                        </div>

                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Promedio Mensual</span>
                                <span class="info-box-number">{{ number_format($estadisticasVaca['promedio_mensual'], 2) }} L/día</span>
                            </div>
                        </div>

                        @if($estadisticasVaca['pico_produccion'])
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pico de Producción</span>
                                <span class="info-box-number">{{ number_format($estadisticasVaca['pico_produccion']->cantidad_leche, 2) }} L</span>
                                <small>{{ $estadisticasVaca['pico_produccion']->fecha->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Información de la vaca -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cow"></i> Información de la Vaca
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Código:</strong> {{ $registro->vaca->codigo }}</p>
                        <p><strong>Raza:</strong> {{ $registro->vaca->raza }}</p>
                        <p><strong>Estado de Salud:</strong> 
                            <span class="badge badge-{{ $registro->vaca->estado_salud == 'Sana' ? 'success' : ($registro->vaca->estado_salud == 'En tratamiento' ? 'warning' : 'danger') }}">
                                {{ $registro->vaca->estado_salud }}
                            </span>
                        </p>
                        <p><strong>Estado Reproductivo:</strong> 
                            <span class="badge badge-{{ $registro->vaca->estado_reproductivo == 'Lactancia' ? 'success' : 'info' }}">
                                {{ $registro->vaca->estado_reproductivo }}
                            </span>
                        </p>
                        @if($registro->vaca->fecha_nacimiento)
                            <p><strong>Fecha de Nacimiento:</strong> {{ $registro->vaca->fecha_nacimiento->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <a href="{{ route('admin.produccion-lechera.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a la Lista
                        </a>
                        <a href="{{ route('admin.produccion-lechera.edit', $registro->id_produccion) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Registro
                        </a>
                        <a href="{{ route('admin.vacas.show', $registro->vaca->id_vaca) }}" class="btn btn-info">
                            <i class="fas fa-cow"></i> Ver Vaca
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar este registro de producción?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmarEliminacion(id) {
    $('#deleteForm').attr('action', '{{ route("admin.produccion-lechera.index") }}/' + id);
    $('#deleteModal').modal('show');
}
</script>
@endpush
@endsection
