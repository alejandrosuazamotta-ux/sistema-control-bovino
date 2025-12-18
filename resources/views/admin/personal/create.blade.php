@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Registrar Personal</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Personal</a></li>
                    <li class="breadcrumb-item active">Registrar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Información del Personal</h3>
                    </div>
                    
                    <form action="{{ route('admin.personal.store') }}" method="POST" id="personalForm">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <!-- Columna izquierda -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre Completo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" name="nombre" value="{{ old('nombre') }}" 
                                               placeholder="Ingrese el nombre completo" maxlength="100" required>
                                        @error('nombre')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}" 
                                               placeholder="ejemplo@correo.com" required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Contraseña <span class="text-danger">*</span></label>
                                        <input type="password" name="password" id="password" 
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="Ingrese una contraseña segura" required>
                                        @error('password')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Columna derecha -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rol">Rol <span class="text-danger">*</span></label>
                                        <select class="form-control @error('rol') is-invalid @enderror" 
                                                id="rol" name="rol" required>
                                            <option value="">Seleccione un rol</option>
                                            <option value="Pasante" {{ old('rol') == 'Pasante' ? 'selected' : '' }}>Pasante</option>
                                            <option value="Supervisor" {{ old('rol') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                            <option value="Otro" {{ old('rol') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        @error('rol')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="fecha_contratacion">Fecha de Contratación</label>
                                        <input type="date" class="form-control @error('fecha_contratacion') is-invalid @enderror" 
                                               id="fecha_contratacion" name="fecha_contratacion" 
                                               value="{{ old('fecha_contratacion') }}">
                                        @error('fecha_contratacion')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <input type="tel" class="form-control @error('telefono') is-invalid @enderror" 
                                               id="telefono" name="telefono" value="{{ old('telefono') }}" 
                                               placeholder="+57 300 123 4567">
                                        @error('telefono')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="direccion">Dirección</label>
                                        <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                                  id="direccion" name="direccion" rows="3" 
                                                  placeholder="Ingrese la dirección">{{ old('direccion') }}</textarea>
                                        @error('direccion')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Personal
                                    </button>
                                    <a href="{{ route('admin.personal.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="reset" class="btn btn-warning">
                                        <i class="fas fa-undo"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validación del formulario
    $('#personalForm').on('submit', function(e) {
        let isValid = true;

        if ($('#nombre').val().trim() === '') {
            $('#nombre').addClass('is-invalid');
            isValid = false;
        }

        if ($('#rol').val() === '') {
            $('#rol').addClass('is-invalid');
            isValid = false;
        }

        const email = $('#email').val();
        if (email && !isValidEmail(email)) {
            $('#email').addClass('is-invalid');
            isValid = false;
        }

        if ($('#password').val().length < 6) {
            $('#password').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                text: 'Por favor, complete todos los campos requeridos correctamente.'
            });
        }
    });

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    $('button[type="reset"]').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Está seguro?',
            text: "Se limpiarán todos los campos del formulario",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#personalForm')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.card-primary {
    border-top: 3px solid #007bff;
}
.form-group label {
    font-weight: 600;
    color: #495057;
}
.text-danger {
    color: #dc3545 !important;
}
.btn {
    border-radius: 5px;
}
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>
@endpush
