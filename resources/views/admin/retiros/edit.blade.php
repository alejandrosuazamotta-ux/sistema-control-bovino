@extends('layouts.master')

@section('title', 'Editar Retiro')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-edit"></i> Editar Retiro</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.retiros.index') }}">Retiros</a></li>
                    <li class="breadcrumb-item active">Editar</li>
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
            <form method="POST" action="{{ route('admin.retiros.update', $retiro->id_retiro) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_vaca" class="required"><i class="fas fa-cow"></i> Vaca</label>
                                <select class="form-control @error('id_vaca') is-invalid @enderror" 
                                        id="id_vaca" name="id_vaca" required>
                                    <option value="">Seleccionar vaca</option>
                                    @foreach($vacas as $vaca)
                                        <option value="{{ $vaca->id_vaca }}" 
                                                {{ old('id_vaca', $retiro->id_vaca) == $vaca->id_vaca ? 'selected' : '' }}>
                                            {{ $vaca->codigo }} - {{ $vaca->raza }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_vaca')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="id_uso_medicamento"><i class="fas fa-pills"></i> Uso de Medicamento</label>
                                <select class="form-control @error('id_uso_medicamento') is-invalid @enderror" 
                                        id="id_uso_medicamento" name="id_uso_medicamento">
                                    <option value="">Sin asociar</option>
                                    @foreach($usosMedicamentos as $uso)
                                        <option value="{{ $uso->id_uso }}" 
                                                {{ old('id_uso_medicamento', $retiro->id_uso_medicamento) == $uso->id_uso ? 'selected' : '' }}>
                                            {{ $uso->vaca->codigo }} - {{ $uso->medicamento->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_uso_medicamento')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tipo_retiro" class="required"><i class="fas fa-tag"></i> Tipo de Retiro</label>
                                <select class="form-control @error('tipo_retiro') is-invalid @enderror" 
                                        id="tipo_retiro" name="tipo_retiro" required>
                                    <option value="Ordeño" {{ old('tipo_retiro', $retiro->tipo_retiro) == 'Ordeño' ? 'selected' : '' }}>Ordeño</option>
                                    <option value="Producción" {{ old('tipo_retiro', $retiro->tipo_retiro) == 'Producción' ? 'selected' : '' }}>Producción</option>
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
                                       value="{{ old('fecha_inicio', $retiro->fecha_inicio->format('Y-m-d')) }}" required>
                                @error('fecha_inicio')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="fecha_fin" class="required"><i class="fas fa-calendar-times"></i> Fecha Fin</label>
                                <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                       id="fecha_fin" name="fecha_fin" 
                                       value="{{ old('fecha_fin', $retiro->fecha_fin->format('Y-m-d')) }}" required>
                                @error('fecha_fin')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" 
                                           {{ old('activo', $retiro->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observaciones"><i class="fas fa-sticky-note"></i> Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $retiro->observaciones) }}</textarea>
                                @error('observaciones')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('admin.retiros.show', $retiro->id_retiro) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                    <a href="{{ route('admin.retiros.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

