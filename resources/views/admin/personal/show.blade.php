@extends('layouts.master')

@section('title', 'Detalles del Personal')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-user-circle mr-2"></i>
                    Detalles del Personal
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.personal.index') }}">Personal</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user mr-2"></i>
                            Información del Personal
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.personal.show', $personal->id_personal) }}" 
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="{{ route('admin.personal.show') }}" 
                               class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-id-card mr-1"></i> ID:
                                    </label>
                                    <p class="form-control-static">{{ $personal->id_personal }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-user mr-1"></i> Nombre:
                                    </label>
                                    <p class="form-control-static">{{ $personal->nombre }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-briefcase mr-1"></i> Rol:
                                    </label>
                                    <p class="form-control-static">
                                        <span class="badge badge-{{ $personal->rol == 'Supervisor' ? 'success' : ($personal->rol == 'Pasante' ? 'info' : 'warning') }}">
                                            {{ $personal->rol }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-calendar-alt mr-1"></i> Fecha de Contratación:
                                    </label>
                                    <p class="form-control-static">
                                        {{ $personal->fecha_contratacion ? $personal->fecha_contratacion->format('d/m/Y') : 'No especificada' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($personal->email)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-envelope mr-1"></i> Email:
                                    </label>
                                    <p class="form-control-static">
                                        <a href="mailto:{{ $personal->email }}">{{ $personal->email }}</a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-phone mr-1"></i> Teléfono:
                                    </label>
                                    <p class="form-control-static">
                                        @if($personal->telefono)
                                            <a href="tel:{{ $personal->telefono }}">{{ $personal->telefono }}</a>
                                        @else
                                            No especificado
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($personal->direccion)
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-map-marker-alt mr-1"></i> Dirección:
                                    </label>
                                    <p class="form-control-static">{{ $personal->direccion }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-clock mr-1"></i> Fecha de Registro:
                                    </label>
                                    <p class="form-control-static">{{ $personal->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">
                                        <i class="fas fa-edit mr-1"></i> Última Actualización:
                                    </label>
                                    <p class="form-control-static">{{ $personal->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Estadísticas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Antigüedad</span>
                                <span class="info-box-number">
                                    @if($personal->fecha_contratacion)
                                        {{ $personal->fecha_contratacion->diffForHumans() }}
                                    @else
                                        No especificada
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-tasks"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Registros de Salud</span>
                                <span class="info-box-number">{{ $personal->salud->count() }}</span>
                            </div>
                        </div>

                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-milk"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Registros de Producción</span>
                                <span class="info-box-number">{{ $personal->produccionLechera->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tools mr-2"></i>
                            Acciones
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical w-100">
                            <a href="{{ route('admin.personal.edit', $personal->id_personal) }}" 
                               class="btn btn-warning mb-2">
                                <i class="fas fa-edit mr-1"></i> Editar Personal
                            </a>
                            <button type="button" class="btn btn-danger mb-2" 
                                    onclick="confirmarEliminacion({{ $personal->id_personal }})">
                                <i class="fas fa-trash mr-1"></i> Eliminar Personal
                            </button>
                            <a href="{{ route('admin.personal.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-list mr-1"></i> Ver Todos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formulario oculto para eliminación -->
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
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/personal/${id}`;
            form.submit();
        }
    });
}

// Mostrar mensaje de éxito si existe
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif

// Mostrar mensaje de error si existe
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '{{ session('error') }}'
    });
@endif
</script>
@endpush
