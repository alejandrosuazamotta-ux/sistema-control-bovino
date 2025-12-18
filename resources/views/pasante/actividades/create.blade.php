@extends('layouts.master')

@section('title', 'Nueva Actividad')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-plus-circle"></i> Nueva Actividad</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pasante.actividades.index') }}">Actividades</a></li>
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
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Información de la Actividad</h3>
            </div>
            <form method="POST" action="{{ route('pasante.actividades.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="titulo" class="required"><i class="fas fa-heading"></i> Título</label>
                                <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                       id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                                @error('titulo')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tipo_actividad" class="required"><i class="fas fa-tag"></i> Tipo de Actividad</label>
                                <select class="form-control @error('tipo_actividad') is-invalid @enderror" 
                                        id="tipo_actividad" name="tipo_actividad" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Ordeño" {{ old('tipo_actividad') == 'Ordeño' ? 'selected' : '' }}>Ordeño</option>
                                    <option value="Reproductivo" {{ old('tipo_actividad') == 'Reproductivo' ? 'selected' : '' }}>Reproductivo</option>
                                    <option value="Rotación Potreros" {{ old('tipo_actividad') == 'Rotación Potreros' ? 'selected' : '' }}>Rotación Potreros</option>
                                    <option value="Alimentación" {{ old('tipo_actividad') == 'Alimentación' ? 'selected' : '' }}>Alimentación</option>
                                    <option value="Salud" {{ old('tipo_actividad') == 'Salud' ? 'selected' : '' }}>Salud</option>
                                    <option value="General" {{ old('tipo_actividad') == 'General' ? 'selected' : '' }}>General</option>
                                </select>
                                @error('tipo_actividad')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="fecha_actividad" class="required"><i class="fas fa-calendar"></i> Fecha de Actividad</label>
                                <input type="date" class="form-control @error('fecha_actividad') is-invalid @enderror" 
                                       id="fecha_actividad" name="fecha_actividad" 
                                       value="{{ old('fecha_actividad', date('Y-m-d')) }}" required>
                                @error('fecha_actividad')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hora_inicio"><i class="fas fa-clock"></i> Hora Inicio</label>
                                        <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror" 
                                               id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') }}">
                                        @error('hora_inicio')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hora_fin"><i class="fas fa-clock"></i> Hora Fin</label>
                                        <input type="time" class="form-control @error('hora_fin') is-invalid @enderror" 
                                               id="hora_fin" name="hora_fin" value="{{ old('hora_fin') }}">
                                        @error('hora_fin')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado"><i class="fas fa-info-circle"></i> Estado</label>
                                <select class="form-control @error('estado') is-invalid @enderror" 
                                        id="estado" name="estado">
                                    <option value="Pendiente" {{ old('estado', 'Pendiente') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="En Progreso" {{ old('estado') == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="Completada" {{ old('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                                    <option value="Cancelada" {{ old('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                                @error('estado')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="evidencia_foto"><i class="fas fa-camera"></i> Foto de Evidencia</label>
                                <input type="file" class="form-control-file @error('evidencia_foto') is-invalid @enderror" 
                                       id="evidencia_foto" name="evidencia_foto" accept="image/*">
                                @error('evidencia_foto')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="form-text text-muted">Formatos: JPG, PNG, GIF. Máximo 5MB</small>
                            </div>

                            <div class="form-group">
                                <label for="evidencia_documento"><i class="fas fa-file"></i> Documento de Evidencia</label>
                                <input type="file" class="form-control-file @error('evidencia_documento') is-invalid @enderror" 
                                       id="evidencia_documento" name="evidencia_documento" accept=".pdf,.doc,.docx">
                                @error('evidencia_documento')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="form-text text-muted">Formatos: PDF, DOC, DOCX. Máximo 10MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="descripcion" class="required"><i class="fas fa-align-left"></i> Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                          id="descripcion" name="descripcion" rows="4" required>{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
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
                    <a href="{{ route('pasante.actividades.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

