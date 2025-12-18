@extends('layouts.master')

@section('title', 'Detalles de la Vaca')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-cow"></i> Detalles de la Vaca
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vacas.index') }}">Vacas</a></li>
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
                            <i class="fas fa-info-circle"></i> Información General
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.vacas.edit', $vaca->id_vaca) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong><i class="fas fa-barcode"></i> Código:</strong></td>
                                        <td>{{ $vaca->codigo }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-tag"></i> Raza:</strong></td>
                                        <td>{{ $vaca->raza ?? 'No especificada' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-calendar"></i> Fecha de Nacimiento:</strong></td>
                                        <td>
                                            @if($vaca->fecha_nacimiento)
                                                {{ $vaca->fecha_nacimiento->format('d/m/Y') }}
                                                <small class="text-muted">({{ $vaca->fecha_nacimiento->age }} años)</small>
                                            @else
                                                <span class="text-muted">No registrada</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-clock"></i> Registrada:</strong></td>
                                        <td>{{ $vaca->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong><i class="fas fa-heartbeat"></i> Estado de Salud:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $vaca->estado_salud == 'Sana' ? 'success' : ($vaca->estado_salud == 'En tratamiento' ? 'warning' : 'danger') }}">
                                                {{ $vaca->estado_salud }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-baby"></i> Estado Reproductivo:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $vaca->estado_reproductivo == 'Lactancia' ? 'success' : ($vaca->estado_reproductivo == 'Preñada' ? 'warning' : ($vaca->estado_reproductivo == 'Celo' ? 'info' : 'secondary')) }}">
                                                {{ $vaca->estado_reproductivo }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-map-marker-alt"></i> Potrero:</strong></td>
                                        <td>
                                            @if($vaca->potrero)
                                                <span class="badge badge-info">
                                                    {{ $vaca->potrero->nombre }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    Ubicación: {{ $vaca->potrero->ubicacion ?? 'No especificada' }}
                                                </small>
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-sync"></i> Última Actualización:</strong></td>
                                        <td>{{ $vaca->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-baby"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Crías</span>
                                <span class="info-box-number">{{ $vaca->crias->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-heart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Registros Salud</span>
                                <span class="info-box-number">{{ $vaca->salud->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-milk"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Producción Leche</span>
                                <span class="info-box-number">{{ $vaca->produccionLechera->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-utensils"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Alimentación</span>
                                <span class="info-box-number">{{ $vaca->alimentacion->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Crías -->
                @if($vaca->crias->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-baby"></i> Crías
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha Nacimiento</th>
                                            <th>Sexo</th>
                                            <th>Peso</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vaca->crias as $cria)
                                            <tr>
                                                <td>{{ $cria->id_cria }}</td>
                                                <td>{{ $cria->fecha_nacimiento ? $cria->fecha_nacimiento->format('d/m/Y') : 'No registrada' }}</td>
                                                <td>{{ $cria->sexo }}</td>
                                                <td>{{ $cria->peso ?? 'No registrado' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $cria->estado == 'Vivo' ? 'success' : 'danger' }}">
                                                        {{ $cria->estado }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Registros Reproductivos -->
                @if($vaca->registrosReproductivos->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-heart"></i> Registros Reproductivos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vaca->registrosReproductivos as $registro)
                                            <tr>
                                                <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                                                <td>{{ $registro->tipo }}</td>
                                                <td>{{ $registro->observaciones ?? 'Sin observaciones' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Panel lateral -->
            <div class="col-md-4">
                <!-- Acciones rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tools"></i> Acciones
                        </h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.vacas.edit', $vaca->id_vaca) }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-edit"></i> Editar Vaca
                        </a>
                        <a href="{{ route('admin.vacas.index') }}" class="btn btn-secondary btn-block mb-2">
                            <i class="fas fa-list"></i> Ver Todas las Vacas
                        </a>
                        <button type="button" class="btn btn-danger btn-block" onclick="confirmarEliminacion({{ $vaca->id_vaca }})">
                            <i class="fas fa-trash"></i> Eliminar Vaca
                        </button>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Resumen
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info"><i class="fas fa-cow"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Vaca #{{ $vaca->id_vaca }}</span>
                                <span class="info-box-number">{{ $vaca->codigo }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6><i class="fas fa-info-circle"></i> Información Adicional:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> {{ $vaca->crias->count() }} crías registradas</li>
                                <li><i class="fas fa-check text-success"></i> {{ $vaca->salud->count() }} registros de salud</li>
                                <li><i class="fas fa-check text-success"></i> {{ $vaca->produccionLechera->count() }} registros de producción</li>
                                <li><i class="fas fa-check text-success"></i> {{ $vaca->alimentacion->count() }} registros de alimentación</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formulario para eliminación -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer. Se eliminará la vaca y todos sus registros asociados.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/vacas/${id}`;
            form.submit();
        }
    });
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>
@endpush

@push('styles')
<style>
.info-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}
.info-box-icon {
    border-radius: 0.25rem 0 0 0.25rem;
}
.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}
</style>
@endpush
