@extends('layouts.master')

@section('title', 'Editar Uso de Medicamento')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-edit"></i> Editar Uso de Medicamento</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.uso-medicamentos.index') }}">Uso de Medicamentos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
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
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Información del Uso</h3>
            </div>
            <form method="POST" action="{{ route('admin.uso-medicamentos.update', $uso->id_uso) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_medicamento" class="required"><i class="fas fa-syringe"></i> Medicamento</label>
                                <select class="form-control @error('id_medicamento') is-invalid @enderror" 
                                        id="id_medicamento" name="id_medicamento" required>
                                    <option value="">Seleccionar medicamento</option>
                                    @foreach($medicamentos as $medicamento)
                                        <option value="{{ $medicamento->id_medicamento }}" 
                                                {{ old('id_medicamento', $uso->id_medicamento) == $medicamento->id_medicamento ? 'selected' : '' }}>
                                            {{ $medicamento->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_medicamento')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="id_vaca" class="required"><i class="fas fa-cow"></i> Vaca</label>
                                <select class="form-control @error('id_vaca') is-invalid @enderror" 
                                        id="id_vaca" name="id_vaca" required>
                                    <option value="">Seleccionar vaca</option>
                                    @foreach($vacas as $vaca)
                                        <option value="{{ $vaca->id_vaca }}" 
                                                {{ old('id_vaca', $uso->id_vaca) == $vaca->id_vaca ? 'selected' : '' }}>
                                            {{ $vaca->codigo }} - {{ $vaca->raza }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_vaca')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="fecha_aplicacion" class="required"><i class="fas fa-calendar"></i> Fecha de Aplicación</label>
                                <input type="date" class="form-control @error('fecha_aplicacion') is-invalid @enderror" 
                                       id="fecha_aplicacion" name="fecha_aplicacion" 
                                       value="{{ old('fecha_aplicacion', $uso->fecha_aplicacion->format('Y-m-d')) }}" 
                                       max="{{ date('Y-m-d') }}" required>
                                @error('fecha_aplicacion')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dosis_aplicada"><i class="fas fa-calculator"></i> Dosis Aplicada</label>
                                <input type="number" class="form-control @error('dosis_aplicada') is-invalid @enderror" 
                                       id="dosis_aplicada" name="dosis_aplicada" 
                                       value="{{ old('dosis_aplicada', $uso->dosis_aplicada) }}" step="0.01" min="0">
                                @error('dosis_aplicada')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="unidad_dosis"><i class="fas fa-ruler"></i> Unidad</label>
                                <input type="text" class="form-control @error('unidad_dosis') is-invalid @enderror" 
                                       id="unidad_dosis" name="unidad_dosis" 
                                       value="{{ old('unidad_dosis', $uso->unidad_dosis) }}">
                                @error('unidad_dosis')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="id_personal"><i class="fas fa-user"></i> Personal Responsable</label>
                                <select class="form-control @error('id_personal') is-invalid @enderror" 
                                        id="id_personal" name="id_personal">
                                    <option value="">Seleccionar personal</option>
                                    @foreach($personal as $persona)
                                        <option value="{{ $persona->id_personal }}" 
                                                {{ old('id_personal', $uso->id_personal) == $persona->id_personal ? 'selected' : '' }}>
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
                                          id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $uso->observaciones) }}</textarea>
                                @error('observaciones')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            @if($uso->retiro)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Retiro asociado:</strong> Este uso tiene un retiro asociado que se actualizará automáticamente.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('admin.uso-medicamentos.show', $uso->id_uso) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                    <a href="{{ route('admin.uso-medicamentos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

