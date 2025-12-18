@extends('layouts.master')

@section('title', 'Detalles del Potrero')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-map-marker-alt"></i> Detalles del Potrero
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.potreros.index') }}">Potreros</a></li>
                    <li class="breadcrumb-item active">{{ $potrero->nombre }}</li>
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

        <div class="row">
            <!-- Información principal del potrero -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información del Potrero
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.potreros.edit', $potrero->id_potrero) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>{{ $potrero->id_potrero }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>{{ $potrero->nombre }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ubicación:</strong></td>
                                        <td>
                                            @if($potrero->ubicacion)
                                                <i class="fas fa-map-marker-alt text-muted"></i>
                                                {{ $potrero->ubicacion }}
                                            @else
                                                <span class="text-muted">No especificada</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Capacidad:</strong></td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $potrero->capacidad }} animales
                                            </span>
                                        </td>
                                    </tr>
                                    @if($potrero->area_hectareas)
                                    <tr>
                                        <td><strong>Área:</strong></td>
                                        <td>{{ number_format($potrero->area_hectareas, 2) }} hectáreas</td>
                                    </tr>
                                    @endif
                                    @if($potrero->aforo_actual)
                                    <tr>
                                        <td><strong>Aforo Actual:</strong></td>
                                        <td>
                                            <span class="badge {{ $potrero->aforoDentroRango() ? 'badge-success' : 'badge-danger' }}">
                                                {{ number_format($potrero->aforo_actual, 2) }} UGG/ha
                                            </span>
                                            @if($potrero->aforo_maximo)
                                                (Máx: {{ number_format($potrero->aforo_maximo, 2) }} UGG/ha)
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($potrero->dias_descanso_recomendado)
                                    <tr>
                                        <td><strong>Días Descanso Recomendado:</strong></td>
                                        <td>{{ $potrero->dias_descanso_recomendado }} días</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Estado:</strong></td>
                                        <td>
                                            @php
                                                $ocupacion = $potrero->vacas->count();
                                                $porcentaje = $potrero->capacidad > 0 ? round(($ocupacion / $potrero->capacidad) * 100, 1) : 0;
                                            @endphp
                                            @if($porcentaje >= 90)
                                                <span class="badge badge-danger">Lleno ({{ $porcentaje }}%)</span>
                                            @elseif($porcentaje >= 70)
                                                <span class="badge badge-warning">Ocupado ({{ $porcentaje }}%)</span>
                                            @else
                                                <span class="badge badge-success">Disponible ({{ $porcentaje }}%)</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Vacas Asignadas:</strong></td>
                                        <td>{{ $ocupacion }} de {{ $potrero->capacidad }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Espacios Libres:</strong></td>
                                        <td>{{ $potrero->capacidad - $ocupacion }}</td>
                                    </tr>
                                    @if($potrero->necesitaDescanso())
                                    <tr>
                                        <td><strong>Estado Descanso:</strong></td>
                                        <td>
                                            <span class="badge badge-warning">
                                                En descanso ({{ $potrero->getDiasRestantesDescanso() }} días restantes)
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Fecha Creación:</strong></td>
                                        <td>{{ $potrero->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Barra de progreso de ocupación -->
                        <div class="mt-3">
                            <label><strong>Ocupación del Potrero:</strong></label>
                            <div class="progress-group">
                                <span class="float-right">
                                    <b>{{ $ocupacion }}</b>/{{ $potrero->capacidad }}
                                </span>
                                <div class="progress progress-lg">
                                    @if($porcentaje >= 90)
                                        <div class="progress-bar bg-danger" style="width: {{ $porcentaje }}%"></div>
                                    @elseif($porcentaje >= 70)
                                        <div class="progress-bar bg-warning" style="width: {{ $porcentaje }}%"></div>
                                    @else
                                        <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $porcentaje }}% ocupado</small>
                            </div>
                        </div>

                        @if($potrero->descripcion)
                            <div class="mt-3">
                                <label><strong>Descripción:</strong></label>
                                <p class="text-muted">{{ $potrero->descripcion }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Lista de vacas asignadas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cow"></i> Vacas Asignadas ({{ $potrero->vacas->count() }})
                        </h3>
                        <div class="card-tools">
                            @if($potrero->capacidad > $potrero->vacas->count())
                                <a href="{{ route('admin.asignacion-potreros.create', ['potrero_id' => $potrero->id_potrero]) }}" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Asignar Vaca
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($potrero->vacas->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>Raza</th>
                                            <th>Estado Salud</th>
                                            <th>Estado Reproductivo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($potrero->vacas as $vaca)
                                            <tr>
                                                <td>{{ $vaca->id_vaca }}</td>
                                                <td>
                                                    <strong>{{ $vaca->codigo }}</strong>
                                                </td>
                                                <td>{{ $vaca->raza ?? 'No especificada' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $vaca->estado_salud == 'Sana' ? 'success' : ($vaca->estado_salud == 'En tratamiento' ? 'warning' : 'danger') }}">
                                                        {{ $vaca->estado_salud }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $vaca->estado_reproductivo == 'Lactancia' ? 'success' : ($vaca->estado_reproductivo == 'Preñada' ? 'warning' : 'info') }}">
                                                        {{ $vaca->estado_reproductivo }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.vacas.show', $vaca->id_vaca) }}" 
                                                           class="btn btn-info btn-sm" title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.vacas.edit', $vaca->id_vaca) }}" 
                                                           class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                onclick="confirmarDesasignacion({{ $vaca->id_vaca }}, '{{ $vaca->codigo }}')" 
                                                                title="Desasignar del potrero">
                                                            <i class="fas fa-unlink"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-cow fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay vacas asignadas</h5>
                                <p class="text-muted">Este potrero no tiene vacas asignadas actualmente.</p>
                                <a href="{{ route('admin.asignacion-potreros.create', ['potrero_id' => $potrero->id_potrero]) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-plus"></i> Asignar Primera Vaca
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="col-md-4">
                <!-- Estadísticas rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Estadísticas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Capacidad Total</span>
                                <span class="info-box-number">{{ $potrero->capacidad }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-cow"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Vacas Asignadas</span>
                                <span class="info-box-number">{{ $potrero->vacas->count() }}</span>
                            </div>
                        </div>

                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-space-shuttle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Espacios Libres</span>
                                <span class="info-box-number">{{ $potrero->capacidad - $potrero->vacas->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt"></i> Acciones Rápidas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.potreros.edit', $potrero->id_potrero) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Potrero
                            </a>
                            
                            @if($potrero->capacidad > $potrero->vacas->count())
                                <a href="{{ route('admin.asignacion-potreros.create', ['potrero_id' => $potrero->id_potrero]) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-plus"></i> Asignar Vaca
                                </a>
                            @endif
                            
                            <a href="{{ route('admin.potreros.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Ver Todos los Potreros
                            </a>
                            
                            @if($potrero->vacas->count() == 0)
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmarEliminacion({{ $potrero->id_potrero }})">
                                    <i class="fas fa-trash"></i> Eliminar Potrero
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información Adicional
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Última actualización:</strong></p>
                        <p class="text-muted">{{ $potrero->updated_at->format('d/m/Y H:i') }}</p>
                        
                        <p><strong>Días desde creación:</strong></p>
                        <p class="text-muted">{{ $potrero->created_at->diffForHumans() }}</p>
                        
                        @if($potrero->vacas->count() > 0)
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Nota:</h6>
                                <p class="mb-0">Este potrero tiene vacas asignadas. No se puede eliminar hasta desasignar todas las vacas.</p>
                            </div>
                        @endif
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

<!-- Formulario para desasignación -->
<form id="desasignacion-form" method="POST" style="display: none;">
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
        text: "Esta acción no se puede deshacer. Se eliminará el potrero permanentemente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/potreros/${id}`;
            form.submit();
        }
    });
}

function confirmarDesasignacion(vacaId, codigoVaca) {
    Swal.fire({
        title: '¿Desasignar vaca?',
        text: `¿Estás seguro de que quieres desasignar la vaca "${codigoVaca}" de este potrero?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, desasignar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('desasignacion-form');
            form.action = `/admin/asignacion-potreros/${vacaId}`;
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
.progress-group {
    margin-bottom: 0;
}
.progress-group .progress {
    margin-bottom: 5px;
}
.info-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    margin-bottom: 1rem;
}
.info-box-icon {
    border-radius: 0.25rem 0 0 0.25rem;
}
.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}
.d-grid.gap-2 {
    gap: 0.5rem !important;
}
</style>
@endpush
