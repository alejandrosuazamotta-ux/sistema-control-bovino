@extends('layouts.master')

@section('title', 'Registrar Nuevo Potrero')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-plus-circle"></i> Registrar Nuevo Potrero
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.potreros.index') }}">Potreros</a></li>
                    <li class="breadcrumb-item active">Nuevo Potrero</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Formulario principal -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt"></i> Información del Potrero
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('admin.potreros.store') }}" id="potrero-form">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <!-- Columna izquierda -->
                                <div class="col-md-6">
                                    <!-- Nombre del Potrero -->
                                    <div class="form-group">
                                        <label for="nombre" class="required">
                                            <i class="fas fa-tag"></i> Nombre del Potrero
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" 
                                               name="nombre" 
                                               value="{{ old('nombre') }}" 
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
                                               value="{{ old('ubicacion') }}" 
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
                                </div>

                                <!-- Columna derecha -->
                                <div class="col-md-6">
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
                                                   value="{{ old('capacidad') }}" 
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
                                                   value="{{ old('area_hectareas') }}" 
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
                                                   value="{{ old('dias_descanso_recomendado', 30) }}" 
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
                                            Días recomendados de descanso entre rotaciones (por defecto: 30 días)
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
                                                   value="{{ old('aforo_maximo') }}" 
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
                                                  placeholder="Información adicional sobre el potrero (tipo de pasto, características especiales, etc.)">{{ old('descripcion') }}</textarea>
                                        @error('descripcion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-save"></i> Registrar Potrero
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('admin.potreros.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <button type="reset" class="btn btn-warning btn-block">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Confirmación antes de enviar
    $('#potrero-form').on('submit', function(e) {
        const nombre = $('#nombre').val().trim();
        const capacidad = $('#capacidad').val();

        if (!nombre || !capacidad) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campos requeridos',
                text: 'Por favor completa todos los campos obligatorios.'
            });
            return false;
        }

        // Mostrar confirmación
        e.preventDefault();
        Swal.fire({
            title: '¿Registrar potrero?',
            text: `¿Estás seguro de que quieres registrar el potrero "${nombre}" con capacidad para ${capacidad} animales?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, registrar',
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
</style>
@endpush
