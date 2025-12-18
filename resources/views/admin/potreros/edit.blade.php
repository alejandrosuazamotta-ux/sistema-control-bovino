@extends('layouts.master')

@section('title', 'Editar Potrero')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-edit"></i> Editar Potrero
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.potreros.index') }}">Potreros</a></li>
                    <li class="breadcrumb-item active">Editar Potrero</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt"></i> Información del Potrero
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('admin.potreros.update', $potrero->id_potrero) }}" id="potrero-form">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <!-- Nombre del Potrero -->
                            <div class="form-group">
                                <label for="nombre" class="required">
                                    <i class="fas fa-tag"></i> Nombre del Potrero
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre', $potrero->nombre) }}" 
                                       placeholder="Ej: Potrero Norte, Potrero A, etc."
                                       maxlength="50"
                                       required>
                                @error('nombre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Nombre único para identificar el potrero (máximo 50 caracteres)
                                </small>
                            </div>

                            <!-- Ubicación -->
                            <div class="form-group">
                                <label for="ubicacion">
                                    <i class="fas fa-map-marker-alt"></i> Ubicación
                                </label>
                                <input type="text" 
                                       class="form-control @error('ubicacion') is-invalid @enderror" 
                                       id="ubicacion" 
                                       name="ubicacion" 
                                       value="{{ old('ubicacion', $potrero->ubicacion) }}" 
                                       placeholder="Ej: Zona norte de la finca, cerca del río, etc."
                                       maxlength="100">
                                @error('ubicacion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Descripción de la ubicación del potrero (opcional)
                                </small>
                            </div>

                            <!-- Capacidad -->
                            <div class="form-group">
                                <label for="capacidad" class="required">
                                    <i class="fas fa-users"></i> Capacidad de Animales
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('capacidad') is-invalid @enderror" 
                                           id="capacidad" 
                                           name="capacidad" 
                                           value="{{ old('capacidad', $potrero->capacidad) }}" 
                                           min="1" 
                                           max="1000"
                                           placeholder="Ej: 25"
                                           required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">animales</span>
                                    </div>
                                </div>
                                @error('capacidad')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Número máximo de animales que puede albergar el potrero
                                </small>
                            </div>

                            <!-- Área en Hectáreas -->
                            <div class="form-group">
                                <label for="area_hectareas">
                                    <i class="fas fa-ruler-combined"></i> Área (Hectáreas)
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           step="0.01"
                                           class="form-control @error('area_hectareas') is-invalid @enderror" 
                                           id="area_hectareas" 
                                           name="area_hectareas" 
                                           value="{{ old('area_hectareas', $potrero->area_hectareas) }}" 
                                           min="0" 
                                           max="10000"
                                           placeholder="Ej: 5.5">
                                    <div class="input-group-append">
                                        <span class="input-group-text">ha</span>
                                    </div>
                                </div>
                                @error('area_hectareas')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Área del potrero en hectáreas (para cálculo de aforo)
                                </small>
                            </div>

                            <!-- Días de Descanso Recomendado -->
                            <div class="form-group">
                                <label for="dias_descanso_recomendado">
                                    <i class="fas fa-calendar-alt"></i> Días de Descanso Recomendado
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('dias_descanso_recomendado') is-invalid @enderror" 
                                           id="dias_descanso_recomendado" 
                                           name="dias_descanso_recomendado" 
                                           value="{{ old('dias_descanso_recomendado', $potrero->dias_descanso_recomendado ?? 30) }}" 
                                           min="0" 
                                           max="365"
                                           placeholder="30">
                                    <div class="input-group-append">
                                        <span class="input-group-text">días</span>
                                    </div>
                                </div>
                                @error('dias_descanso_recomendado')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Días recomendados de descanso entre rotaciones
                                </small>
                            </div>

                            <!-- Aforo Máximo -->
                            <div class="form-group">
                                <label for="aforo_maximo">
                                    <i class="fas fa-chart-line"></i> Aforo Máximo (UGG/ha)
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           step="0.01"
                                           class="form-control @error('aforo_maximo') is-invalid @enderror" 
                                           id="aforo_maximo" 
                                           name="aforo_maximo" 
                                           value="{{ old('aforo_maximo', $potrero->aforo_maximo) }}" 
                                           min="0" 
                                           max="100"
                                           placeholder="Ej: 2.5">
                                    <div class="input-group-append">
                                        <span class="input-group-text">UGG/ha</span>
                                    </div>
                                </div>
                                @error('aforo_maximo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Aforo máximo recomendado en Unidades Gran Ganado por hectárea
                                </small>
                            </div>

                            <!-- Información adicional -->
                            <div class="form-group">
                                <label for="descripcion">
                                    <i class="fas fa-info-circle"></i> Descripción Adicional
                                </label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                          id="descripcion" 
                                          name="descripcion" 
                                          rows="3" 
                                          placeholder="Información adicional sobre el potrero (tipo de pasto, características especiales, etc.)">{{ old('descripcion', $potrero->descripcion ?? '') }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-save"></i> Actualizar Potrero
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('admin.potreros.show', $potrero->id_potrero) }}" class="btn btn-info btn-block">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('admin.potreros.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel lateral con información -->
            <div class="col-md-4">
                <!-- Información del potrero -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información Actual
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-map-marker-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Potrero Actual</span>
                                <span class="info-box-number">{{ $potrero->nombre }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Capacidad</span>
                                <span class="info-box-number">{{ $potrero->capacidad }} animales</span>
                            </div>
                        </div>

                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-cow"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Vacas Asignadas</span>
                                <span class="info-box-number">{{ $potrero->vacas->count() }}</span>
                            </div>
                        </div>

                        @if($potrero->ubicacion)
                            <div class="alert alert-info">
                                <h6><i class="fas fa-map-marker-alt"></i> Ubicación:</h6>
                                <p class="mb-0">{{ $potrero->ubicacion }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Advertencias -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i> Advertencias
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($potrero->vacas->count() > 0)
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-info-circle"></i> Atención:</h6>
                                <p class="mb-0">Este potrero tiene {{ $potrero->vacas->count() }} vacas asignadas. 
                                Cambiar la capacidad puede afectar la gestión de los animales.</p>
                            </div>
                        @endif

                        @if($potrero->vacas->count() >= $potrero->capacidad)
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle"></i> Potrero Lleno:</h6>
                                <p class="mb-0">Este potrero está al 100% de su capacidad. 
                                Considera aumentar la capacidad o reasignar algunas vacas.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Validación en tiempo real -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-check-circle"></i> Validación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="validation-status">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Validando...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Validación en tiempo real
    let validationTimeout;
    
    $('#nombre, #capacidad').on('input', function() {
        clearTimeout(validationTimeout);
        validationTimeout = setTimeout(validateForm, 500);
    });

    function validateForm() {
        const nombre = $('#nombre').val().trim();
        const capacidad = $('#capacidad').val();
        const vacasAsignadas = {{ $potrero->vacas->count() }};
        let isValid = true;
        let messages = [];

        // Validar nombre
        if (nombre.length < 3) {
            messages.push('El nombre debe tener al menos 3 caracteres');
            isValid = false;
        }

        // Validar capacidad
        if (capacidad < 1) {
            messages.push('La capacidad debe ser mayor a 0');
            isValid = false;
        } else if (capacidad > 1000) {
            messages.push('La capacidad no puede ser mayor a 1000');
            isValid = false;
        } else if (capacidad < vacasAsignadas) {
            messages.push(`La capacidad no puede ser menor a ${vacasAsignadas} (vacas actualmente asignadas)`);
            isValid = false;
        }

        // Mostrar resultado de validación
        const statusDiv = $('#validation-status');
        if (isValid) {
            statusDiv.html(`
                <div class="text-center text-success">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <p class="mt-2">Formulario válido</p>
                </div>
            `);
        } else {
            statusDiv.html(`
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                    <p class="mt-2">Errores encontrados:</p>
                    <ul class="text-left">
                        ${messages.map(msg => `<li>${msg}</li>`).join('')}
                    </ul>
                </div>
            `);
        }
    }

    // Validación inicial
    validateForm();

    // Confirmación antes de enviar
    $('#potrero-form').on('submit', function(e) {
        const nombre = $('#nombre').val().trim();
        const capacidad = $('#capacidad').val();
        const vacasAsignadas = {{ $potrero->vacas->count() }};

        if (!nombre || !capacidad) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campos requeridos',
                text: 'Por favor completa todos los campos obligatorios.'
            });
            return false;
        }

        if (capacidad < vacasAsignadas) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Capacidad insuficiente',
                text: `No puedes reducir la capacidad a menos de ${vacasAsignadas} ya que hay ${vacasAsignadas} vacas asignadas actualmente.`
            });
            return false;
        }

        // Mostrar confirmación
        e.preventDefault();
        Swal.fire({
            title: '¿Actualizar potrero?',
            text: `¿Estás seguro de que quieres actualizar el potrero "${nombre}" con capacidad para ${capacidad} animales?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#potrero-form')[0].submit();
            }
        });
    });

    // Auto-focus en el primer campo
    $('#nombre').focus();
});
</script>
@endpush

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.info-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    margin-bottom: 1rem;
}

.info-box-icon {
    border-radius: 0.25rem 0 0 0.25rem;
}

.card-header .card-title {
    margin-bottom: 0;
}

.form-group label {
    font-weight: 600;
    color: #495057;
}

.form-group label i {
    margin-right: 5px;
    color: #6c757d;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

#validation-status ul {
    margin-bottom: 0;
    padding-left: 20px;
}

#validation-status li {
    font-size: 0.9em;
}
</style>
@endpush
