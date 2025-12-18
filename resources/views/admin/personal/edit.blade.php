@extends('layouts.master')

@section('title', 'Editar Personal')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-user-edit mr-2"></i>
                    Editar Personal
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.personal.index') }}">Personal</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.personal.show', $personal->id_personal) }}">Detalles</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-2"></i>
                            Formulario de Edición
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.personal.show', $personal->id_personal) }}" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                            <a href="{{ route('admin.personal.index') }}" 
                               class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('admin.personal.update', $personal->id_personal) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="font-weight-bold">
                                            <i class="fas fa-user mr-1"></i> Nombre Completo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" 
                                               name="nombre" 
                                               value="{{ old('nombre', $personal->nombre) }}" 
                                               placeholder="Ingrese el nombre completo"
                                               required>
                                        @error('nombre')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rol" class="font-weight-bold">
                                            <i class="fas fa-briefcase mr-1"></i> Rol <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control @error('rol') is-invalid @enderror" 
                                                id="rol" 
                                                name="rol" 
                                                required>
                                            <option value="">Seleccione un rol</option>
                                            <option value="Pasante" {{ old('rol', $personal->rol) == 'Pasante' ? 'selected' : '' }}>
                                                Pasante
                                            </option>
                                            <option value="Supervisor" {{ old('rol', $personal->rol) == 'Supervisor' ? 'selected' : '' }}>
                                                Supervisor
                                            </option>
                                            <option value="Otro" {{ old('rol', $personal->rol) == 'Otro' ? 'selected' : '' }}>
                                                Otro
                                            </option>
                                        </select>
                                        @error('rol')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_contratacion" class="font-weight-bold">
                                            <i class="fas fa-calendar-alt mr-1"></i> Fecha de Contratación
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha_contratacion') is-invalid @enderror" 
                                               id="fecha_contratacion" 
                                               name="fecha_contratacion" 
                                               value="{{ old('fecha_contratacion', $personal->fecha_contratacion ? $personal->fecha_contratacion->format('Y-m-d') : '') }}"
                                               max="{{ date('Y-m-d') }}">
                                        @error('fecha_contratacion')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">
                                            <i class="fas fa-envelope mr-1"></i> Email
                                        </label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $personal->email) }}" 
                                               placeholder="ejemplo@correo.com">
                                        @error('email')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono" class="font-weight-bold">
                                            <i class="fas fa-phone mr-1"></i> Teléfono
                                        </label>
                                        <input type="tel" 
                                               class="form-control @error('telefono') is-invalid @enderror" 
                                               id="telefono" 
                                               name="telefono" 
                                               value="{{ old('telefono', $personal->telefono) }}" 
                                               placeholder="+1234567890">
                                        @error('telefono')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion" class="font-weight-bold">
                                            <i class="fas fa-map-marker-alt mr-1"></i> Dirección
                                        </label>
                                        <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                                  id="direccion" 
                                                  name="direccion" 
                                                  rows="3" 
                                                  placeholder="Ingrese la dirección completa">{{ old('direccion', $personal->direccion) }}</textarea>
                                        @error('direccion')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="fas fa-save mr-1"></i> Actualizar Personal
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('admin.personal.show', $personal->id_personal) }}" 
                                       class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times mr-1"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>
                            Información Actual
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">ID del Personal</span>
                                <span class="info-box-number">{{ $personal->id_personal }}</span>
                            </div>
                        </div>

                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-user"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Nombre Actual</span>
                                <span class="info-box-number">{{ $personal->nombre }}</span>
                            </div>
                        </div>

                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-briefcase"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Rol Actual</span>
                                <span class="info-box-number">
                                    <span class="badge badge-{{ $personal->rol == 'Supervisor' ? 'success' : ($personal->rol == 'Pasante' ? 'info' : 'warning') }}">
                                        {{ $personal->rol }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-clock"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Última Actualización</span>
                                <span class="info-box-number">{{ $personal->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Notas Importantes
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success mr-1"></i>
                                Los campos marcados con <span class="text-danger">*</span> son obligatorios
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-info-circle text-info mr-1"></i>
                                La fecha de contratación no puede ser futura
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-envelope text-warning mr-1"></i>
                                El email debe tener un formato válido
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-phone text-primary mr-1"></i>
                                El teléfono es opcional pero recomendado
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.form-control:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.info-box {
    margin-bottom: 1rem;
}

.card-outline {
    border-top: 3px solid #ffc107 !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Validación del formulario
document.getElementById('editForm').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const rol = document.getElementById('rol').value;
    
    if (!nombre) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Campo requerido',
            text: 'El nombre es obligatorio'
        });
        document.getElementById('nombre').focus();
        return false;
    }
    
    if (!rol) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Campo requerido',
            text: 'Debe seleccionar un rol'
        });
        document.getElementById('rol').focus();
        return false;
    }
    
    // Validar fecha de contratación
    const fechaContratacion = document.getElementById('fecha_contratacion').value;
    if (fechaContratacion) {
        const fecha = new Date(fechaContratacion);
        const hoy = new Date();
        if (fecha > hoy) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Fecha inválida',
                text: 'La fecha de contratación no puede ser futura'
            });
            document.getElementById('fecha_contratacion').focus();
            return false;
        }
    }
    
    // Validar email si se proporciona
    const email = document.getElementById('email').value.trim();
    if (email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Email inválido',
                text: 'Por favor ingrese un email válido'
            });
            document.getElementById('email').focus();
            return false;
        }
    }
});

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

// Confirmar antes de salir si hay cambios
let formChanged = false;
const form = document.getElementById('editForm');
const inputs = form.querySelectorAll('input, select, textarea');

inputs.forEach(input => {
    input.addEventListener('change', () => {
        formChanged = true;
    });
});

window.addEventListener('beforeunload', (e) => {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Resetear el flag cuando se envía el formulario
form.addEventListener('submit', () => {
    formChanged = false;
});
</script>
@endpush
