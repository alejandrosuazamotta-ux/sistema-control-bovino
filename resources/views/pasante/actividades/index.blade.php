@extends('layouts.master')

@section('title', 'Mis Actividades')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-tasks"></i> Mis Actividades</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Actividades</li>
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
                <h3 class="card-title"><i class="fas fa-list"></i> Lista de Actividades</h3>
                <div class="card-tools">
                    <a href="{{ route('pasante.actividades.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Actividad
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pasante.actividades.index') }}" class="form-inline mb-3">
                    <select name="tipo_actividad" class="form-control mr-2">
                        <option value="">Todos los tipos</option>
                        <option value="Ordeño" {{ request('tipo_actividad') == 'Ordeño' ? 'selected' : '' }}>Ordeño</option>
                        <option value="Reproductivo" {{ request('tipo_actividad') == 'Reproductivo' ? 'selected' : '' }}>Reproductivo</option>
                        <option value="Rotación Potreros" {{ request('tipo_actividad') == 'Rotación Potreros' ? 'selected' : '' }}>Rotación Potreros</option>
                        <option value="Alimentación" {{ request('tipo_actividad') == 'Alimentación' ? 'selected' : '' }}>Alimentación</option>
                        <option value="Salud" {{ request('tipo_actividad') == 'Salud' ? 'selected' : '' }}>Salud</option>
                        <option value="General" {{ request('tipo_actividad') == 'General' ? 'selected' : '' }}>General</option>
                    </select>
                    <select name="estado" class="form-control mr-2">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="En Progreso" {{ request('estado') == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                        <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    <input type="date" name="fecha_inicio" class="form-control mr-2" value="{{ request('fecha_inicio') }}" placeholder="Fecha inicio">
                    <input type="date" name="fecha_fin" class="form-control mr-2" value="{{ request('fecha_fin') }}" placeholder="Fecha fin">
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('pasante.actividades.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                </form>

                @if($actividades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Aprobada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($actividades as $actividad)
                            <tr>
                                <td>{{ $loop->iteration + ($actividades->currentPage() - 1) * $actividades->perPage() }}</td>
                                <td>{{ Str::limit($actividad->titulo, 40) }}</td>
                                <td><span class="badge badge-info">{{ $actividad->tipo_actividad }}</span></td>
                                <td>{{ $actividad->fecha_actividad->format('d/m/Y') }}</td>
                                <td>
                                    @if($actividad->estado == 'Completada')
                                        <span class="badge badge-success">Completada</span>
                                    @elseif($actividad->estado == 'En Progreso')
                                        <span class="badge badge-warning">En Progreso</span>
                                    @elseif($actividad->estado == 'Pendiente')
                                        <span class="badge badge-secondary">Pendiente</span>
                                    @else
                                        <span class="badge badge-danger">Cancelada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($actividad->aprobada)
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Aprobada</span>
                                    @else
                                        <span class="badge badge-warning">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('pasante.actividades.show', $actividad->id_actividad) }}" class="btn btn-info btn-sm" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$actividad->aprobada)
                                        <a href="{{ route('pasante.actividades.edit', $actividad->id_actividad) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $actividad->id_actividad }})" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $actividades->appends(request()->query())->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay actividades registradas.
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = '{{ route("pasante.actividades.index") }}/' + id;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection

