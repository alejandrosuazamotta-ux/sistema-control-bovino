@extends('layouts.master')

@section('title', 'Registrar Producción Lechera')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-milk"></i> Registrar Producción Lechera
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.produccion-lechera.index') }}">Producción Lechera</a></li>
                    <li class="breadcrumb-item active">Nuevo Registro</li>
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
                            <i class="fas fa-clipboard-list"></i> Información de Producción
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('admin.produccion-lechera.store') }}" id="produccion-form">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <!-- Columna izquierda -->
                                <div class="col-md-6">
                                    <!-- Vaca -->
                                    <div class="form-group">
                                        <label for="id_vaca" class="required">
                                            <i class="fas fa-cow"></i> Vaca
                                        </label>
                                        <select class="form-control @error('id_vaca') is-invalid @enderror" 
                                                id="id_vaca" 
                                                name="id_vaca" 
                                                required>
                                            <option value="">Seleccionar vaca</option>
                                            @foreach($vacas as $vaca)
                                                <option value="{{ $vaca->id_vaca }}" 
                                                        {{ old('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                                    {{ $vaca->codigo }} - {{ $vaca->raza }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_vaca')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Seleccione la vaca para registrar su producción
                                        </small>
                                    </div>

                                    <!-- Fecha -->
                                    <div class="form-group">
                                        <label for="fecha" class="required">
                                            <i class="fas fa-calendar"></i> Fecha de Producción
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha') is-invalid @enderror" 
                                               id="fecha" 
                                               name="fecha" 
                                               value="{{ old('fecha', date('Y-m-d')) }}"
                                               max="{{ date('Y-m-d') }}"
                                               required>
                                        @error('fecha')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Fecha en que se registra la producción
                                        </small>
                                    </div>

                                    <!-- Turno -->
                                    <div class="form-group">
                                        <label for="turno" class="required">
                                            <i class="fas fa-clock"></i> Turno
                                        </label>
                                        <select class="form-control @error('turno') is-invalid @enderror" 
                                                id="turno" 
                                                name="turno" 
                                                required>
                                            <option value="">Seleccionar turno</option>
                                            <option value="AM" {{ old('turno') == 'AM' ? 'selected' : '' }}>AM (Mañana)</option>
                                            <option value="PM" {{ old('turno') == 'PM' ? 'selected' : '' }}>PM (Tarde)</option>
                                        </select>
                                        @error('turno')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Turno de ordeño (AM o PM)
                                        </small>
                                    </div>

                                    <!-- Cantidad de Leche -->
                                    <div class="form-group">
                                        <label for="cantidad_leche" class="required">
                                            <i class="fas fa-tint"></i> Cantidad de Leche (Litros)
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('cantidad_leche') is-invalid @enderror" 
                                               id="cantidad_leche" 
                                               name="cantidad_leche" 
                                               value="{{ old('cantidad_leche') }}" 
                                               placeholder="0.00"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               required>
                                        @error('cantidad_leche')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Cantidad de leche producida en litros (máximo 100 litros)
                                        </small>
                                    </div>
                                </div>

                                <!-- Columna derecha -->
                                <div class="col-md-6">
                                    <!-- Destino -->
                                    <div class="form-group">
                                        <label for="destino" class="required">
                                            <i class="fas fa-route"></i> Destino del Producto
                                        </label>
                                        <select class="form-control @error('destino') is-invalid @enderror" 
                                                id="destino" 
                                                name="destino" 
                                                required>
                                            <option value="">Seleccionar destino</option>
                                            <option value="Agroindustria" {{ old('destino') == 'Agroindustria' ? 'selected' : '' }}>Agroindustria</option>
                                            <option value="Lechero" {{ old('destino') == 'Lechero' ? 'selected' : '' }}>Lechero</option>
                                            <option value="Particular" {{ old('destino') == 'Particular' ? 'selected' : '' }}>Particular</option>
                                            <option value="Consumo" {{ old('destino') == 'Consumo' ? 'selected' : '' }}>Consumo</option>
                                        </select>
                                        @error('destino')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Destino final de la leche producida
                                        </small>
                                    </div>

                                    <!-- Valor Unitario -->
                                    <div class="form-group">
                                        <label for="valor_unidad">
                                            <i class="fas fa-dollar-sign"></i> Valor Unitario (COP)
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('valor_unidad') is-invalid @enderror" 
                                               id="valor_unidad" 
                                               name="valor_unidad" 
                                               value="{{ old('valor_unidad') }}" 
                                               placeholder="0.00"
                                               step="0.01"
                                               min="0"
                                               max="999999.99">
                                        @error('valor_unidad')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Precio por litro (opcional, se calculará el valor total automáticamente)
                                        </small>
                                    </div>

                                    <!-- Personal Responsable -->
                                    <div class="form-group">
                                        <label for="id_personal" class="required">
                                            <i class="fas fa-user"></i> Personal Responsable
                                        </label>
                                        <select class="form-control @error('id_personal') is-invalid @enderror" 
                                                id="id_personal" 
                                                name="id_personal" 
                                                required>
                                            <option value="">Seleccionar personal</option>
                                            @foreach($personal as $persona)
                                                <option value="{{ $persona->id_personal }}" 
                                                        {{ old('id_personal') == $persona->id_personal ? 'selected' : '' }}>
                                                    {{ $persona->nombre }} - {{ $persona->rol }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_personal')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Personal que realizó el ordeño y registro
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
                                                  placeholder="Observaciones adicionales sobre la producción...">{{ old('observaciones') }}</textarea>
                                        @error('observaciones')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Comentarios adicionales sobre la producción (opcional)
                                        </small>
                                    </div>

                                    <!-- Valor Total (calculado) -->
                                    <div class="form-group">
                                        <div id="valor_total_display" class="alert alert-info" style="display: none;">
                                            <i class="fas fa-calculator"></i> <strong id="valor_total_text"></strong>
                                        </div>
                                    </div>

                                    <!-- Información adicional -->
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-info-circle"></i> Información Importante</h5>
                                        <ul class="mb-0">
                                            <li>La fecha no puede ser posterior al día actual</li>
                                            <li>La cantidad debe ser un valor positivo</li>
                                            <li>Se recomienda registrar la producción diariamente</li>
                                            <li>Verifique que la vaca esté en estado de lactancia</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Registro
                                    </button>
                                    <a href="{{ route('admin.produccion-lechera.index') }}" class="btn btn-secondary">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Validación del formulario mejorada
    $('#produccion-form').on('submit', function(e) {
        var cantidad = parseFloat($('#cantidad_leche').val());
        var fecha = $('#fecha').val();
        var hoy = new Date().toISOString().split('T')[0];
        var valorUnidad = $('#valor_unidad').val();
        var errors = [];
        
        // Validar cantidad
        if (!cantidad || cantidad <= 0) {
            errors.push('La cantidad de leche debe ser mayor a 0');
            $('#cantidad_leche').addClass('is-invalid');
        } else {
            $('#cantidad_leche').removeClass('is-invalid');
        }
        
        // Validar fecha
        if (fecha > hoy) {
            errors.push('La fecha no puede ser posterior al día actual');
            $('#fecha').addClass('is-invalid');
        } else {
            $('#fecha').removeClass('is-invalid');
        }
        
        // Validar valor_unidad si se proporciona
        if (valorUnidad && (parseFloat(valorUnidad) <= 0)) {
            errors.push('El valor por unidad debe ser mayor a 0');
            $('#valor_unidad').addClass('is-invalid');
        } else {
            $('#valor_unidad').removeClass('is-invalid');
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

    // Formatear cantidad de leche
    $('#cantidad_leche').on('input', function() {
        var valor = $(this).val();
        if (valor && !isNaN(valor)) {
            $(this).val(parseFloat(valor).toFixed(2));
        }
        calcularValorTotal();
    });

    // Calcular valor total automáticamente
    $('#valor_unidad').on('input', function() {
        calcularValorTotal();
    });

    function calcularValorTotal() {
        var cantidad = parseFloat($('#cantidad_leche').val()) || 0;
        var valorUnidad = parseFloat($('#valor_unidad').val()) || 0;
        var valorTotal = cantidad * valorUnidad;
        
        if (valorTotal > 0) {
            $('#valor_total_display').text('Valor Total: $' + valorTotal.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#valor_total_display').show();
        } else {
            $('#valor_total_display').hide();
        }
    }
});
</script>
@endpush
@endsection
