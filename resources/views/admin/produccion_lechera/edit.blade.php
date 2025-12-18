@extends('layouts.master')

@section('title', 'Editar Producción Lechera')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-edit"></i> Editar Producción Lechera
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.produccion-lechera.index') }}">Producción Lechera</a></li>
                    <li class="breadcrumb-item active">Editar Registro</li>
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
                    <form method="POST" action="{{ route('admin.produccion-lechera.update', $registro->id_produccion) }}" id="produccion-form">
                        @csrf
                        @method('PUT')
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
                                                        {{ old('id_vaca', $registro->id_vaca) == $vaca->id_vaca ? 'selected' : '' }}>
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
                                               value="{{ old('fecha', $registro->fecha->format('Y-m-d')) }}"
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
                                            <option value="AM" {{ old('turno', $registro->turno ?? '') == 'AM' ? 'selected' : '' }}>AM (Mañana)</option>
                                            <option value="PM" {{ old('turno', $registro->turno ?? '') == 'PM' ? 'selected' : '' }}>PM (Tarde)</option>
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
                                               value="{{ old('cantidad_leche', $registro->cantidad_leche) }}" 
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
                                            <option value="Agroindustria" {{ old('destino', $registro->destino ?? '') == 'Agroindustria' ? 'selected' : '' }}>Agroindustria</option>
                                            <option value="Lechero" {{ old('destino', $registro->destino ?? '') == 'Lechero' ? 'selected' : '' }}>Lechero</option>
                                            <option value="Particular" {{ old('destino', $registro->destino ?? '') == 'Particular' ? 'selected' : '' }}>Particular</option>
                                            <option value="Consumo" {{ old('destino', $registro->destino ?? '') == 'Consumo' ? 'selected' : '' }}>Consumo</option>
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
                                               value="{{ old('valor_unidad', $registro->valor_unidad) }}" 
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
                                                        {{ old('id_personal', $registro->id_personal) == $persona->id_personal ? 'selected' : '' }}>
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
                                                  placeholder="Observaciones adicionales sobre la producción...">{{ old('observaciones', $registro->observaciones) }}</textarea>
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

                                    <!-- Información del registro -->
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-info-circle"></i> Información del Registro</h5>
                                        <ul class="mb-0">
                                            <li><strong>Creado:</strong> {{ $registro->created_at->format('d/m/Y H:i') }}</li>
                                            <li><strong>Última actualización:</strong> {{ $registro->updated_at->format('d/m/Y H:i') }}</li>
                                            <li><strong>ID del registro:</strong> #{{ $registro->id_produccion }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Registro
                                    </button>
                                    <a href="{{ route('admin.produccion-lechera.show', $registro->id_produccion) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
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
<script>
$(document).ready(function() {
    // Validación del formulario
    $('#produccion-form').on('submit', function(e) {
        var cantidad = parseFloat($('#cantidad_leche').val());
        var fecha = $('#fecha').val();
        var hoy = new Date().toISOString().split('T')[0];
        var errors = [];
        
        if (cantidad <= 0) {
            errors.push('La cantidad de leche debe ser mayor a 0');
            $('#cantidad_leche').addClass('is-invalid');
        } else {
            $('#cantidad_leche').removeClass('is-invalid');
        }
        
        if (fecha > hoy) {
            errors.push('La fecha no puede ser posterior al día actual');
            $('#fecha').addClass('is-invalid');
        } else {
            $('#fecha').removeClass('is-invalid');
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

    // Calcular al cargar la página
    calcularValorTotal();
});
</script>
@endpush
@endsection
