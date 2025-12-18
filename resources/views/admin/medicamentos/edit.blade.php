@extends('layouts.master')

@section('title', 'Editar Medicamento')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-edit"></i> Editar Medicamento</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.medicamentos.index') }}">Medicamentos</a></li>
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
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Información del Medicamento</h3>
            </div>
            <form method="POST" action="{{ route('admin.medicamentos.update', $medicamento->id_medicamento) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="required"><i class="fas fa-tag"></i> Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre', $medicamento->nombre) }}" required>
                                @error('nombre')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tipo"><i class="fas fa-flask"></i> Tipo</label>
                                <input type="text" class="form-control @error('tipo') is-invalid @enderror" 
                                       id="tipo" name="tipo" value="{{ old('tipo', $medicamento->tipo) }}">
                                @error('tipo')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="principio_activo"><i class="fas fa-microscope"></i> Principio Activo</label>
                                <input type="text" class="form-control @error('principio_activo') is-invalid @enderror" 
                                       id="principio_activo" name="principio_activo" value="{{ old('principio_activo', $medicamento->principio_activo) }}">
                                @error('principio_activo')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="via_administracion"><i class="fas fa-route"></i> Vía de Administración</label>
                                <input type="text" class="form-control @error('via_administracion') is-invalid @enderror" 
                                       id="via_administracion" name="via_administracion" value="{{ old('via_administracion', $medicamento->via_administracion) }}">
                                @error('via_administracion')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dosis"><i class="fas fa-calculator"></i> Dosis Recomendada</label>
                                <input type="text" class="form-control @error('dosis') is-invalid @enderror" 
                                       id="dosis" name="dosis" value="{{ old('dosis', $medicamento->dosis) }}">
                                @error('dosis')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="periodo_retiro_dias" class="required">
                                    <i class="fas fa-calendar-times"></i> Período de Retiro (días)
                                </label>
                                <input type="number" class="form-control @error('periodo_retiro_dias') is-invalid @enderror" 
                                       id="periodo_retiro_dias" name="periodo_retiro_dias" 
                                       value="{{ old('periodo_retiro_dias', $medicamento->periodo_retiro_dias) }}" min="0" max="365" required>
                                @error('periodo_retiro_dias')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" 
                                           {{ old('activo', $medicamento->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observaciones"><i class="fas fa-sticky-note"></i> Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" name="observaciones" rows="4">{{ old('observaciones', $medicamento->observaciones) }}</textarea>
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
                    <a href="{{ route('admin.medicamentos.show', $medicamento->id_medicamento) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                    <a href="{{ route('admin.medicamentos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

