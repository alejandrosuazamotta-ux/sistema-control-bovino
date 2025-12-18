@extends('layouts.master')

@section('title', 'Registrar Nueva Cría')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-baby"></i> Registrar Nueva Cría
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crias.index') }}">Crías</a></li>
                    <li class="breadcrumb-item active">Nueva Cría</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes con SweetAlert2 -->
        @include('components.sweet-alert')
        
        <div class="row">
            <!-- Formulario principal -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Información de la Cría
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('admin.crias.store') }}" id="cria-form">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <!-- Columna izquierda -->
                                <div class="col-md-6">
                                    <!-- Vaca Madre -->
                                    <div class="form-group">
                                        <label for="id_vaca_madre" class="required">
                                            <i class="fas fa-cow"></i> Vaca Madre
                                        </label>
                                        <select class="form-control @error('id_vaca_madre') is-invalid @enderror" 
                                                id="id_vaca_madre" 
                                                name="id_vaca_madre" 
                                                required>
                                            <option value="">Seleccionar vaca madre</option>
                                            @foreach($vacas as $vaca)
                                                <option value="{{ $vaca->id_vaca }}" 
                                                        {{ old('id_vaca_madre') == $vaca->id_vaca ? 'selected' : '' }}>
                                                    {{ $vaca->codigo }} - {{ $vaca->raza }} 
                                                    ({{ $vaca->estado_reproductivo }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_vaca_madre')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Seleccione la vaca madre de la cría
                                        </small>
                                    </div>

                                    <!-- Nombre de la Cría -->
                                    <div class="form-group">
                                        <label for="nombre_cria">
                                            <i class="fas fa-tag"></i> Nombre de la Cría
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nombre_cria') is-invalid @enderror" 
                                               id="nombre_cria" 
                                               name="nombre_cria" 
                                               value="{{ old('nombre_cria') }}" 
                                               placeholder="Ej: Ternero 001">
                                        @error('nombre_cria')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Nombre identificador de la cría (opcional)
                                        </small>
                                    </div>

                                    <!-- Sexo -->
                                    <div class="form-group">
                                        <label for="sexo" class="required">
                                            <i class="fas fa-venus-mars"></i> Sexo
                                        </label>
                                        <select class="form-control @error('sexo') is-invalid @enderror" 
                                                id="sexo" 
                                                name="sexo" 
                                                required>
                                            <option value="">Seleccionar sexo</option>
                                            <option value="Macho" {{ old('sexo') == 'Macho' ? 'selected' : '' }}>Macho</option>
                                            <option value="Hembra" {{ old('sexo') == 'Hembra' ? 'selected' : '' }}>Hembra</option>
                                        </select>
                                        @error('sexo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Fecha de Nacimiento -->
                                    <div class="form-group">
                                        <label for="fecha_nacimiento" class="required">
                                            <i class="fas fa-calendar"></i> Fecha de Nacimiento
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                               id="fecha_nacimiento" 
                                               name="fecha_nacimiento" 
                                               value="{{ old('fecha_nacimiento', date('Y-m-d')) }}"
                                               max="{{ date('Y-m-d') }}"
                                               required>
                                        @error('fecha_nacimiento')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Fecha en que nació la cría
                                        </small>
                                    </div>

                                    <!-- Peso -->
                                    <div class="form-group">
                                        <label for="peso" class="required">
                                            <i class="fas fa-weight"></i> Peso al Nacer (kg)
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('peso') is-invalid @enderror" 
                                               id="peso" 
                                               name="peso" 
                                               value="{{ old('peso') }}" 
                                               placeholder="0.00"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               required>
                                        @error('peso')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Peso de la cría al nacer en kilogramos (máximo 100 kg)
                                        </small>
                                    </div>

                                    <!-- Fecha de Tatuado -->
                                    <div class="form-group">
                                        <label for="fecha_tatuado">
                                            <i class="fas fa-stamp"></i> Fecha de Tatuado
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha_tatuado') is-invalid @enderror" 
                                               id="fecha_tatuado" 
                                               name="fecha_tatuado" 
                                               value="{{ old('fecha_tatuado') }}"
                                               max="{{ date('Y-m-d') }}">
                                        @error('fecha_tatuado')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Fecha en que se tatuó la cría (opcional)
                                        </small>
                                    </div>
                                </div>

                                <!-- Columna derecha -->
                                <div class="col-md-6">
                                    <!-- Método de Concepción -->
                                    <div class="form-group">
                                        <label for="concepcion">
                                            <i class="fas fa-dna"></i> Método de Concepción
                                        </label>
                                        <select class="form-control @error('concepcion') is-invalid @enderror" 
                                                id="concepcion" 
                                                name="concepcion">
                                            <option value="">Seleccionar método</option>
                                            <option value="IA" {{ old('concepcion') == 'IA' ? 'selected' : '' }}>IA (Inseminación Artificial)</option>
                                            <option value="Monta Natural" {{ old('concepcion') == 'Monta Natural' ? 'selected' : '' }}>Monta Natural</option>
                                            <option value="Transferencia Embrionaria" {{ old('concepcion') == 'Transferencia Embrionaria' ? 'selected' : '' }}>Transferencia Embrionaria</option>
                                        </select>
                                        @error('concepcion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Código SINIGAN -->
                                    <div class="form-group">
                                        <label for="sinigan">
                                            <i class="fas fa-barcode"></i> Código SINIGAN
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('sinigan') is-invalid @enderror" 
                                               id="sinigan" 
                                               name="sinigan" 
                                               value="{{ old('sinigan') }}" 
                                               placeholder="Ej: SIN-123456">
                                        @error('sinigan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Código del Sistema Nacional de Identificación Ganadera (opcional)
                                        </small>
                                    </div>

                                    <!-- Estado de Destete -->
                                    <div class="form-group">
                                        <label for="estado_destete" class="required">
                                            <i class="fas fa-baby-carriage"></i> Estado de Destete
                                        </label>
                                        <select class="form-control @error('estado_destete') is-invalid @enderror" 
                                                id="estado_destete" 
                                                name="estado_destete" 
                                                required>
                                            <option value="">Seleccionar estado</option>
                                            <option value="No destetada" {{ old('estado_destete', 'No destetada') == 'No destetada' ? 'selected' : '' }}>No destetada</option>
                                            <option value="Destetada" {{ old('estado_destete') == 'Destetada' ? 'selected' : '' }}>Destetada</option>
                                        </select>
                                        @error('estado_destete')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Estado actual de destete de la cría
                                        </small>
                                    </div>

                                    <!-- Fecha de Destete -->
                                    <div class="form-group" id="fecha-destete-group" style="display: none;">
                                        <label for="fecha_destete">
                                            <i class="fas fa-calendar-times"></i> Fecha de Destete
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha_destete') is-invalid @enderror" 
                                               id="fecha_destete" 
                                               name="fecha_destete" 
                                               value="{{ old('fecha_destete') }}"
                                               max="{{ date('Y-m-d') }}">
                                        @error('fecha_destete')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Fecha en que se destetó la cría
                                        </small>
                                    </div>

                                    <!-- Observaciones -->
                                    <div class="form-group">
                                        <label for="observaciones">
                                            <i class="fas fa-sticky-note"></i> Observaciones
                                        </label>
                                        <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                                  id="observaciones" 
                                                  name="observaciones" 
                                                  rows="4"
                                                  placeholder="Observaciones sobre la cría, condiciones de nacimiento, etc...">{{ old('observaciones') }}</textarea>
                                        @error('observaciones')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Comentarios adicionales sobre la cría (opcional)
                                        </small>
                                    </div>

                                    <!-- Información adicional -->
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-info-circle"></i> Información Importante</h5>
                                        <ul class="mb-0">
                                            <li>La fecha de nacimiento no puede ser posterior al día actual</li>
                                            <li>El peso debe ser un valor positivo</li>
                                            <li>Se recomienda registrar el peso al nacer</li>
                                            <li>Verifique que la vaca madre esté en estado reproductivo adecuado</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Cría
                                    </button>
                                    <a href="{{ route('admin.crias.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> 
                                        Los campos marcados con <span class="text-danger">*</span> son obligatorios
                                    </small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
$(document).ready(function() {
    const estadoDestete = $('#estado_destete');
    const fechaDesteteGroup = $('#fecha-destete-group');
    const fechaDestete = $('#fecha_destete');
    const fechaNacimiento = $('#fecha_nacimiento');
    const fechaTatuado = $('#fecha_tatuado');

    // Mostrar/ocultar campo fecha_destete según estado_destete
    function toggleFechaDestete() {
        if (estadoDestete.val() === 'Destetada') {
            fechaDesteteGroup.show();
            if (!fechaDestete.val()) {
                fechaDestete.val('{{ date('Y-m-d') }}');
            }
        } else {
            fechaDesteteGroup.hide();
            fechaDestete.val('');
        }
    }

    estadoDestete.on('change', toggleFechaDestete);
    toggleFechaDestete(); // Inicializar

    // Validar que fecha_tatuado sea posterior a fecha_nacimiento
    fechaNacimiento.on('change', function() {
        if (fechaTatuado.val() && fechaTatuado.val() < $(this).val()) {
            fechaTatuado.val('');
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                text: 'La fecha de tatuado no puede ser anterior a la fecha de nacimiento',
                confirmButtonColor: '#dc3545'
            });
        }
    });

    fechaTatuado.on('change', function() {
        if (fechaNacimiento.val() && $(this).val() < fechaNacimiento.val()) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                text: 'La fecha de tatuado no puede ser anterior a la fecha de nacimiento',
                confirmButtonColor: '#dc3545'
            });
            $(this).val('');
        }
    });

    // Validar que fecha_destete sea posterior a fecha_nacimiento
    fechaDestete.on('change', function() {
        if (fechaNacimiento.val() && $(this).val() < fechaNacimiento.val()) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                text: 'La fecha de destete no puede ser anterior a la fecha de nacimiento',
                confirmButtonColor: '#dc3545'
            });
            $(this).val('');
        }
    });

    // Validación del formulario
    $('#cria-form').on('submit', function(e) {
        var peso = parseFloat($('#peso').val());
        var fecha = $('#fecha_nacimiento').val();
        var hoy = new Date().toISOString().split('T')[0];
        var errors = [];
        
        if (peso <= 0) {
            errors.push('El peso debe ser mayor a 0');
            $('#peso').addClass('is-invalid');
        } else {
            $('#peso').removeClass('is-invalid');
        }
        
        if (fecha > hoy) {
            errors.push('La fecha de nacimiento no puede ser posterior al día actual');
            $('#fecha_nacimiento').addClass('is-invalid');
        } else {
            $('#fecha_nacimiento').removeClass('is-invalid');
        }

        // Validar fecha_destete si estado es Destetada
        if (estadoDestete.val() === 'Destetada' && !fechaDestete.val()) {
            errors.push('Debe ingresar la fecha de destete cuando el estado es Destetada');
            fechaDestete.addClass('is-invalid');
        } else {
            fechaDestete.removeClass('is-invalid');
        }

        if (errors.length > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                html: '<ul class="text-left">' + errors.map(err => '<li>' + err + '</li>').join('') + '</ul>',
                confirmButtonText: 'Corregir',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
    });

    // Formatear peso
    $('#peso').on('input', function() {
        var valor = $(this).val();
        if (valor && !isNaN(valor)) {
            $(this).val(parseFloat(valor).toFixed(2));
        }
    });
});
</script>
@endpush
@endsection
