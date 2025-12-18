@extends('layouts.master')

@section('title', 'Gestión de Vacas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-cow"></i> Gestión de Vacas
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Vacas</li>
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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Lista de Vacas
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.vacas.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Vaca
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros de búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.vacas.index') }}" class="form-inline">
                            <div class="input-group mr-2">
                                <input type="text" name="search" class="form-control" placeholder="Buscar por código o raza..." 
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="input-group mr-2">
                                <select name="estado_salud" class="form-control">
                                    <option value="">Todos los estados de salud</option>
                                    <option value="Sana" {{ request('estado_salud') == 'Sana' ? 'selected' : '' }}>Sana</option>
                                    <option value="En tratamiento" {{ request('estado_salud') == 'En tratamiento' ? 'selected' : '' }}>En tratamiento</option>
                                    <option value="En observación" {{ request('estado_salud') == 'En observación' ? 'selected' : '' }}>En observación</option>
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="estado_reproductivo" class="form-control">
                                    <option value="">Todos los estados reproductivos</option>
                                    <option value="Celo" {{ request('estado_reproductivo') == 'Celo' ? 'selected' : '' }}>Celo</option>
                                    <option value="Preñada" {{ request('estado_reproductivo') == 'Preñada' ? 'selected' : '' }}>Preñada</option>
                                    <option value="Lactancia" {{ request('estado_reproductivo') == 'Lactancia' ? 'selected' : '' }}>Lactancia</option>
                                    <option value="Descanso" {{ request('estado_reproductivo') == 'Descanso' ? 'selected' : '' }}>Descanso</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Filtrar</button>
                            <a href="{{ route('admin.vacas.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                        </form>
                    </div>
                </div>

                <!-- Tabla de vacas -->
                @if($vacas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Código</th>
                                    <th width="15%">Raza</th>
                                    <th width="12%">Edad</th>
                                    <th width="15%">Estado Salud</th>
                                    <th width="15%">Estado Reproductivo</th>
                                    <th width="13%">Potrero</th>
                                    <th width="10%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vacas as $vaca)
                                    <tr>
                                        <td>{{ $vaca->id_vaca }}</td>
                                        <td>
                                            <strong>{{ $vaca->codigo }}</strong>
                                        </td>
                                        <td>{{ $vaca->raza ?? 'No especificada' }}</td>
                                        <td>
                                            @if($vaca->fecha_nacimiento)
                                                {{ $vaca->fecha_nacimiento->age }} años
                                            @else
                                                <span class="text-muted">No registrada</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $vaca->estado_salud == 'Sana' ? 'success' : ($vaca->estado_salud == 'En tratamiento' ? 'warning' : 'danger') }}">
                                                {{ $vaca->estado_salud }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $vaca->estado_reproductivo == 'Lactancia' ? 'success' : ($vaca->estado_reproductivo == 'Preñada' ? 'warning' : ($vaca->estado_reproductivo == 'Celo' ? 'info' : 'secondary')) }}">
                                                {{ $vaca->estado_reproductivo }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($vaca->potrero)
                                                <span class="badge badge-info">
                                                    {{ $vaca->potrero->nombre }}
                                                </span>
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('view', $vaca)
                                                <a href="{{ route('admin.vacas.show', $vaca->id_vaca) }}" 
                                                   class="btn btn-info btn-sm" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('update', $vaca)
                                                <a href="{{ route('admin.vacas.edit', $vaca->id_vaca) }}" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete', $vaca)
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="confirmarEliminacion({{ $vaca->id_vaca }})" title="Eliminar">
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

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $vacas->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-cow fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron vacas</h5>
                        <p class="text-muted">No hay vacas registradas en el sistema.</p>
                        @can('create', App\Models\Vaca::class)
                        <a href="{{ route('admin.vacas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Registrar Primera Vaca
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        @if($vacas->count() > 0)
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-cow"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Vacas</span>
                            <span class="info-box-number">{{ $vacas->total() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-heart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Sanas</span>
                            <span class="info-box-number">{{ $vacas->where('estado_salud', 'Sana')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-baby"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">En Lactancia</span>
                            <span class="info-box-number">{{ $vacas->where('estado_reproductivo', 'Lactancia')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">En Tratamiento</span>
                            <span class="info-box-number">{{ $vacas->where('estado_salud', 'En tratamiento')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
</style>
@endpush
