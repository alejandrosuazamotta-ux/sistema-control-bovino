@extends('layouts.master')

@section('title', 'Registrar Evento Reproductivo')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-heart"></i> Registrar Evento Reproductivo</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.registros-reproductivos.index') }}">Registros Reproductivos</a></li>
                    <li class="breadcrumb-item active">Nuevo</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes con SweetAlert2 -->
        @include('components.sweet-alert')
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Información del Evento</h3>
            </div>
            <form method="POST" action="{{ route('admin.registros-reproductivos.store') }}" id="registro-form">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_vaca" class="required"><i class="fas fa-cow"></i> Vaca</label>
                                <select class="form-control @error('id_vaca') is-invalid @enderror" 
                                        id="id_vaca" name="id_vaca" required>
                                    <option value="">Seleccionar vaca</option>
                                    @foreach($vacas as $vaca)
                                        <option value="{{ $vaca->id_vaca }}" {{ old('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                            {{ $vaca->codigo }} - {{ $vaca->raza }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_vaca')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tipo_evento" class="required"><i class="fas fa-tag"></i> Tipo de Evento</label>
                                <select class="form-control @error('tipo_evento') is-invalid @enderror" 
                                        id="tipo_evento" name="tipo_evento" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="Inseminación" {{ old('tipo_evento') == 'Inseminación' ? 'selected' : '' }}>Inseminación</option>
                                    <option value="Parto" {{ old('tipo_evento') == 'Parto' ? 'selected' : '' }}>Parto</option>
                                    <option value="Celo" {{ old('tipo_evento') == 'Celo' ? 'selected' : '' }}>Celo</option>
                                    <option value="Palpación" {{ old('tipo_evento') == 'Palpación' ? 'selected' : '' }}>Palpación</option>
                                    <option value="Días abiertos" {{ old('tipo_evento') == 'Días abiertos' ? 'selected' : '' }}>Días abiertos</option>
                                </select>
                                @error('tipo_evento')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="fecha_evento" class="required"><i class="fas fa-calendar"></i> Fecha del Evento</label>
                                <input type="date" class="form-control @error('fecha_evento') is-invalid @enderror" 
                                       id="fecha_evento" name="fecha_evento" 
                                       value="{{ old('fecha_evento', date('Y-m-d')) }}" 
                                       max="{{ date('Y-m-d') }}" required>
                                @error('fecha_evento')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <!-- Campos específicos para Palpación -->
                            <div id="campos-palpacion" style="display: none;">
                                <div class="form-group">
                                    <label for="resultado_palpacion" class="required"><i class="fas fa-check-circle"></i> Resultado de Palpación</label>
                                    <select class="form-control @error('resultado_palpacion') is-invalid @enderror" 
                                            id="resultado_palpacion" name="resultado_palpacion">
                                        <option value="">Seleccionar resultado</option>
                                        <option value="Vacia" {{ old('resultado_palpacion') == 'Vacia' ? 'selected' : '' }}>Vacia</option>
                                        <option value="Preñada" {{ old('resultado_palpacion') == 'Preñada' ? 'selected' : '' }}>Preñada</option>
                                    </select>
                                    @error('resultado_palpacion')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="form-group" id="tiempo-gestacion-group" style="display: none;">
                                    <label for="tiempo_gestacion_dias" class="required"><i class="fas fa-calendar-alt"></i> Tiempo de Gestación (días)</label>
                                    <input type="number" class="form-control @error('tiempo_gestacion_dias') is-invalid @enderror" 
                                           id="tiempo_gestacion_dias" name="tiempo_gestacion_dias" 
                                           value="{{ old('tiempo_gestacion_dias') }}" 
                                           min="0" max="283" placeholder="Ej: 120">
                                    @error('tiempo_gestacion_dias')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Días de gestación al momento de la palpación (máximo 283 días)
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="especialista"><i class="fas fa-user-md"></i> Especialista</label>
                                    <input type="text" class="form-control @error('especialista') is-invalid @enderror" 
                                           id="especialista" name="especialista" 
                                           value="{{ old('especialista') }}" 
                                           placeholder="Nombre del especialista">
                                    @error('especialista')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="alert alert-info" id="fecha-probable-info" style="display: none;">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Fecha Probable de Parto:</strong> <span id="fecha-probable-text"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_personal"><i class="fas fa-user"></i> Personal Responsable</label>
                                <select class="form-control @error('id_personal') is-invalid @enderror" 
                                        id="id_personal" name="id_personal">
                                    <option value="">Seleccionar personal</option>
                                    @foreach($personal as $persona)
                                        <option value="{{ $persona->id_personal }}" {{ old('id_personal') == $persona->id_personal ? 'selected' : '' }}>
                                            {{ $persona->nombre }} - {{ $persona->rol }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_personal')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="observaciones"><i class="fas fa-sticky-note"></i> Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" name="observaciones" rows="5">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('admin.registros-reproductivos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoEvento = document.getElementById('tipo_evento');
    const camposPalpacion = document.getElementById('campos-palpacion');
    const resultadoPalpacion = document.getElementById('resultado_palpacion');
    const tiempoGestacionGroup = document.getElementById('tiempo-gestacion-group');
    const fechaProbableInfo = document.getElementById('fecha-probable-info');
    const fechaProbableText = document.getElementById('fecha-probable-text');
    const fechaEvento = document.getElementById('fecha_evento');
    const tiempoGestacion = document.getElementById('tiempo_gestacion_dias');

    function toggleCamposPalpacion() {
        if (tipoEvento.value === 'Palpación') {
            camposPalpacion.style.display = 'block';
        } else {
            camposPalpacion.style.display = 'none';
        }
    }

    function toggleTiempoGestacion() {
        if (resultadoPalpacion.value === 'Preñada') {
            tiempoGestacionGroup.style.display = 'block';
        } else {
            tiempoGestacionGroup.style.display = 'none';
            fechaProbableInfo.style.display = 'none';
        }
    }

    function calcularFechaProbable() {
        if (resultadoPalpacion.value === 'Preñada' && tiempoGestacion.value && fechaEvento.value) {
            const fecha = new Date(fechaEvento.value);
            const diasRestantes = 283 - parseInt(tiempoGestacion.value);
            fecha.setDate(fecha.getDate() + diasRestantes);
            
            const fechaFormateada = fecha.toLocaleDateString('es-ES');
            fechaProbableText.textContent = fechaFormateada;
            fechaProbableInfo.style.display = 'block';
        } else {
            fechaProbableInfo.style.display = 'none';
        }
    }

    tipoEvento.addEventListener('change', toggleCamposPalpacion);
    resultadoPalpacion.addEventListener('change', function() {
        toggleTiempoGestacion();
        calcularFechaProbable();
    });
    tiempoGestacion.addEventListener('input', calcularFechaProbable);
    fechaEvento.addEventListener('change', calcularFechaProbable);

    // Inicializar al cargar
    toggleCamposPalpacion();
    toggleTiempoGestacion();
    calcularFechaProbable();
});
</script>
@endpush
@endsection

