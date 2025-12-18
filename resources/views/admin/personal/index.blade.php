@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Personal</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Personal</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-ban"></i> ¡Error!</h5>
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Personal</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.personal.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nuevo Personal
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($personal->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Rol</th>
                                            <th>Fecha Contratación</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($personal as $persona)
                                            <tr>
                                                <td>{{ $persona->id_personal }}</td>
                                                <td>{{ $persona->nombre }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $persona->rol == 'Supervisor' ? 'success' : ($persona->rol == 'Pasante' ? 'info' : 'warning') }}">
                                                        {{ $persona->rol }}
                                                    </span>
                                                </td>
                                                <td>{{ $persona->fecha_contratacion ? $persona->fecha_contratacion->format('d/m/Y') : 'No registrada' }}</td>
                                                <td>{{ $persona->email ?? 'No registrado' }}</td>
                                                <td>{{ $persona->telefono ?? 'No registrado' }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.personal.show', $persona->id_personal) }}" 
                                                           class="btn btn-info btn-sm" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.personal.edit', $persona->id_personal) }}" 
                                                           class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                onclick="confirmarEliminacion({{ $persona->id_personal }}, '{{ $persona->nombre }}')" 
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <form id="delete-form-{{ $persona->id_personal }}" 
                                                          action="{{ route('admin.personal.destroy', $persona->id_personal) }}" 
                                                          method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center">
                                {{ $personal->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay personal registrado</h5>
                                <p class="text-muted">Comienza agregando el primer miembro del personal.</p>
                                <a href="{{ route('admin.personal.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Registrar Personal
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function confirmarEliminacion(id, nombre) {
    Swal.fire({
        title: '¿Está seguro?',
        text: `¿Desea eliminar al personal "${nombre}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Auto-hide alerts after 5 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

@push('styles')
<style>
.table th {
    background-color: #f4f6f9;
    border-color: #dee2e6;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.8em;
}

.table-responsive {
    border-radius: 0.25rem;
}
</style>
@endpush
