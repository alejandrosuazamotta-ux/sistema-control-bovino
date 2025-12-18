@extends('layouts.master')

@section('title', 'Registrar Retiro')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-ban"></i> Registrar Retiro</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.retiros.index') }}">Retiros</a></li>
                    <li class="breadcrumb-item active">Nuevo</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Información del Retiro</h3>
            </div>
            <form method="POST" action="{{ route('admin.retiros.store') }}">
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
                                <label for="id_uso_medicamento"><i class="fas fa-pills"></i> Uso de Medicamento (Opcional)</label>
                                <select class="form-control @error('id_uso_medicamento') is-invalid @enderror" 
                                        id="id_uso_medicamento" name="id_uso_medicamento">
                                    <option value="">Sin asociar</option>
                                    @foreach($usosMedicamentos as $uso)
                                        <option value="{{ $uso->id_uso }}" {{ old('id_uso_medicamento') == $uso->id_uso ? 'selected' : '' }}>
                                            {{ $uso->vaca->codigo }} - {{ $uso->medicamento->nombre }} ({{ $uso->fecha_aplicacion->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_uso_medicamento')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="form-text text-muted">
                                    Si selecciona un uso de medicamento, las fechas se calcularán automáticamente
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="tipo_retiro" class="required"><i class="fas fa-tag"></i> Tipo de Retiro</label>
                                <select class="form-control @error('tipo_retiro') is-invalid @enderror" 
                                        id="tipo_retiro" name="tipo_retiro" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="Ordeño" {{ old('tipo_retiro') == 'Ordeño' ? 'selected' : '' }}>Ordeño</option>
                                    <option value="Producción" {{ old('tipo_retiro') == 'Producción' ? 'selected' : '' }}>Producción</option>
                                </select>
                                @error('tipo_retiro')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio" class="required"><i class="fas fa-calendar"></i> Fecha Inicio</label>
                                <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                       id="fecha_inicio" name="fecha_inicio" 
                                       value="{{ old('fecha_inicio') }}" required>
                                @error('fecha_inicio')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="fecha_fin" class="required"><i class="fas fa-calendar-times"></i> Fecha Fin</label>
                                <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                       id="fecha_fin" name="fecha_fin" 
                                       value="{{ old('fecha_fin') }}" required>
                                @error('fecha_fin')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" 
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observaciones"><i class="fas fa-sticky-note"></i> Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Importante:</strong> Durante el período de retiro, la vaca no podrá registrar producción lechera.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('admin.retiros.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
// Si se selecciona un uso de medicamento, calcular fechas automáticamente
document.getElementById('id_uso_medicamento').addEventListener('change', function() {
    const usoId = this.value;
    if (usoId) {
        // Aquí se podría hacer una petición AJAX para obtener las fechas
        // Por ahora, el usuario debe ingresarlas manualmente
    }
});
</script>
@endpush
@endsection

