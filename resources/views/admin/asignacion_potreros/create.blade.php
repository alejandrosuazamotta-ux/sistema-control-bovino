@extends('layouts.master')

@section('title', 'Nueva Asignación de Potrero')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-exchange-alt"></i> Nueva Asignación de Potrero</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.asignacion-potreros.index') }}">Asignación Potreros</a></li>
                    <li class="breadcrumb-item active">Nueva</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Información de la Asignación</h3>
            </div>
            <form method="POST" action="{{ route('admin.asignacion-potreros.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_potrero" class="required"><i class="fas fa-map-marker-alt"></i> Potrero</label>
                                <select class="form-control @error('id_potrero') is-invalid @enderror" id="id_potrero" name="id_potrero" required>
                                    <option value="">Seleccione un potrero...</option>
                                    @foreach($potreros as $potrero)
                                        <option value="{{ $potrero->id_potrero }}" {{ old('id_potrero', request('potrero_id')) == $potrero->id_potrero ? 'selected' : '' }}>
                                            {{ $potrero->nombre }} 
                                            (Capacidad: {{ $potrero->vacas->count() }}/{{ $potrero->capacidad }})
                                            @if($potrero->area_hectareas)
                                                - {{ number_format($potrero->area_hectareas, 2) }} ha
                                                @if($potrero->aforo_actual)
                                                    - Aforo: {{ number_format($potrero->aforo_actual, 2) }} UGG/ha
                                                @endif
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_potrero')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="id_vaca" class="required"><i class="fas fa-cow"></i> Vaca</label>
                                <select class="form-control @error('id_vaca') is-invalid @enderror" id="id_vaca" name="id_vaca" required>
                                    <option value="">Seleccione una vaca...</option>
                                    @foreach($vacas as $vaca)
                                        <option value="{{ $vaca->id_vaca }}" {{ old('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                            {{ $vaca->codigo }} 
                                            @if($vaca->peso_kg)
                                                ({{ number_format($vaca->peso_kg, 0) }} kg - {{ number_format($vaca->peso_kg / 450, 2) }} UGG)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_vaca')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="fecha_asignacion" class="required"><i class="fas fa-calendar"></i> Fecha de Asignación</label>
                                <input type="date" class="form-control @error('fecha_asignacion') is-invalid @enderror" 
                                       id="fecha_asignacion" name="fecha_asignacion" 
                                       value="{{ old('fecha_asignacion', date('Y-m-d')) }}" required>
                                @error('fecha_asignacion')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_salida"><i class="fas fa-calendar-times"></i> Fecha de Salida (Opcional)</label>
                                <input type="date" class="form-control @error('fecha_salida') is-invalid @enderror" 
                                       id="fecha_salida" name="fecha_salida" 
                                       value="{{ old('fecha_salida') }}">
                                @error('fecha_salida')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="form-text text-muted">
                                    Deje vacío si la asignación está activa
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="peso_ingreso"><i class="fas fa-weight"></i> Peso al Ingreso (kg)</label>
                                <input type="number" step="0.01" class="form-control @error('peso_ingreso') is-invalid @enderror" 
                                       id="peso_ingreso" name="peso_ingreso" 
                                       value="{{ old('peso_ingreso') }}" min="0">
                                @error('peso_ingreso')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="form-text text-muted">
                                    Peso de la vaca al ingresar al potrero (se tomará del peso actual si no se especifica)
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="dias_descanso"><i class="fas fa-moon"></i> Días de Descanso del Potrero</label>
                                <input type="number" class="form-control @error('dias_descanso') is-invalid @enderror" 
                                       id="dias_descanso" name="dias_descanso" 
                                       value="{{ old('dias_descanso') }}" min="0" max="365">
                                @error('dias_descanso')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="form-text text-muted">
                                    Días de descanso del potrero después de la salida (se calculará automáticamente si no se especifica)
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="carga_ugg"><i class="fas fa-chart-line"></i> Carga UGG por Hectárea (Opcional)</label>
                                <input type="number" step="0.01" class="form-control @error('carga_ugg') is-invalid @enderror" 
                                       id="carga_ugg" name="carga_ugg" 
                                       value="{{ old('carga_ugg') }}" min="0">
                                @error('carga_ugg')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="form-text text-muted">
                                    Carga UGG por hectárea (se calculará automáticamente si no se especifica)
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="aforo_kg"><i class="fas fa-balance-scale"></i> Aforo en kg/ha (Opcional)</label>
                                <input type="number" step="0.01" class="form-control @error('aforo_kg') is-invalid @enderror" 
                                       id="aforo_kg" name="aforo_kg" 
                                       value="{{ old('aforo_kg') }}" min="0">
                                @error('aforo_kg')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="form-text text-muted">
                                    Aforo en kilogramos por hectárea (se calculará automáticamente si no se especifica)
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="observaciones"><i class="fas fa-sticky-note"></i> Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
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
                    <a href="{{ route('admin.asignacion-potreros.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.getElementById('fecha_salida').addEventListener('change', function() {
    const fechaAsignacion = document.getElementById('fecha_asignacion').value;
    const fechaSalida = this.value;
    
    if (fechaAsignacion && fechaSalida) {
        const diasEstancia = Math.floor((new Date(fechaSalida) - new Date(fechaAsignacion)) / (1000 * 60 * 60 * 24));
        if (diasEstancia > 0) {
            // Mostrar días de estancia calculados
            console.log('Días de estancia calculados: ' + diasEstancia);
        }
    }
});
</script>
@endpush
@endsection

