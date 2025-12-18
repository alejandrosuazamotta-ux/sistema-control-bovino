@extends('layouts.master')

@section('title', 'Detalles de Registro de Salud')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-eye"></i> Detalles de Registro de Salud
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.salud.index') }}">Salud</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-vial"></i> Información del Registro
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.salud.edit', $registro->id_salud) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $registro->id_salud }})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong><i class="fas fa-cow"></i> Vaca:</strong></td>
                                <td>{{ $registro->vaca->codigo ?? 'N/A' }} - {{ $registro->vaca->nombre ?? 'Sin nombre' }}</td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-tag"></i> Tipo de Registro:</strong></td>
                                <td><span class="badge badge-info">{{ $registro->tipo_registro }}</span></td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-calendar-alt"></i> Fecha:</strong></td>
                                <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                            </tr>
                            @if($registro->tipo_prueba)
                            <tr>
                                <td><strong><i class="fas fa-flask"></i> Tipo de Prueba:</strong></td>
                                <td>
                                    @if($registro->tipo_prueba === 'Mastitis')
                                        <span class="badge badge-warning"><i class="fas fa-vial"></i> {{ $registro->tipo_prueba }}</span>
                                    @elseif($registro->tipo_prueba === 'Brucelosis')
                                        <span class="badge badge-danger"><i class="fas fa-biohazard"></i> {{ $registro->tipo_prueba }}</span>
                                    @elseif($registro->tipo_prueba === 'Tuberculosis')
                                        <span class="badge badge-danger"><i class="fas fa-lungs"></i> {{ $registro->tipo_prueba }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $registro->tipo_prueba }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @if($registro->resultado)
                            <tr>
                                <td><strong><i class="fas fa-check-circle"></i> Resultado:</strong></td>
                                <td>
                                    @if($registro->resultado === 'Positivo')
                                        <span class="badge badge-danger">{{ $registro->resultado }}</span>
                                    @elseif($registro->resultado === 'Negativo')
                                        <span class="badge badge-success">{{ $registro->resultado }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $registro->resultado }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @if($registro->fecha_resultado)
                            <tr>
                                <td><strong><i class="fas fa-calendar-check"></i> Fecha Resultado:</strong></td>
                                <td>{{ $registro->fecha_resultado->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            @if($registro->severidad)
                            <tr>
                                <td><strong><i class="fas fa-exclamation-triangle"></i> Severidad:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $registro->severidad === 'Severa' ? 'danger' : ($registro->severidad === 'Moderada' ? 'warning' : 'info') }}">
                                        {{ $registro->severidad }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong><i class="fas fa-ban"></i> Restricción Ordeño:</strong></td>
                                <td>
                                    @if($registro->restriccion_ordeño)
                                        <span class="badge badge-danger"><i class="fas fa-ban"></i> Sí</span>
                                    @else
                                        <span class="badge badge-success">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-times-circle"></i> Inhabilitada:</strong></td>
                                <td>
                                    @if($registro->inhabilitada)
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Sí</span>
                                    @else
                                        <span class="badge badge-success">No</span>
                                    @endif
                                </td>
                            </tr>
                            @if($registro->acta)
                            <tr>
                                <td><strong><i class="fas fa-file-alt"></i> Número de Acta:</strong></td>
                                <td>{{ $registro->acta }}</td>
                            </tr>
                            @endif
                            @if($registro->responsable_prueba)
                            <tr>
                                <td><strong><i class="fas fa-user-md"></i> Responsable:</strong></td>
                                <td>{{ $registro->responsable_prueba }}</td>
                            </tr>
                            @endif
                            @if($registro->tratamiento_sugerido)
                            <tr>
                                <td><strong><i class="fas fa-pills"></i> Tratamiento Sugerido:</strong></td>
                                <td>{{ $registro->tratamiento_sugerido }}</td>
                            </tr>
                            @endif
                            @if($registro->descripcion)
                            <tr>
                                <td><strong><i class="fas fa-align-left"></i> Descripción:</strong></td>
                                <td>{{ $registro->descripcion }}</td>
                            </tr>
                            @endif
                            @if($registro->observaciones)
                            <tr>
                                <td><strong><i class="fas fa-sticky-note"></i> Observaciones:</strong></td>
                                <td>{{ $registro->observaciones }}</td>
                            </tr>
                            @endif
                            @if($registro->personal)
                            <tr>
                                <td><strong><i class="fas fa-user"></i> Personal:</strong></td>
                                <td>{{ $registro->personal->nombre }} {{ $registro->personal->apellido ?? '' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong><i class="fas fa-clock"></i> Fecha Creación:</strong></td>
                                <td>{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-edit"></i> Última Actualización:</strong></td>
                                <td>{{ $registro->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información de la Vaca
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($registro->vaca)
                            <p><strong>Código:</strong> {{ $registro->vaca->codigo }}</p>
                            <p><strong>Raza:</strong> {{ $registro->vaca->raza ?? 'N/A' }}</p>
                            <p><strong>Estado Salud:</strong> 
                                <span class="badge badge-{{ $registro->vaca->estado_salud === 'Sana' ? 'success' : 'danger' }}">
                                    {{ $registro->vaca->estado_salud }}
                                </span>
                            </p>
                            <p><strong>Estado Reproductivo:</strong> 
                                <span class="badge badge-info">{{ $registro->vaca->estado_reproductivo }}</span>
                            </p>
                            <a href="{{ route('admin.vacas.show', $registro->vaca->id_vaca) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver Detalles de la Vaca
                            </a>
                        @else
                            <p class="text-muted">Vaca no encontrada</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/salud/${id}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection

