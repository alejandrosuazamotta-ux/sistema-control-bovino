@extends('layouts.master')

@section('title', 'Registrar Uso de Medicamento')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-pills"></i> Registrar Uso de Medicamento</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.uso-medicamentos.index') }}">Uso de Medicamentos</a></li>
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
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Información del Uso</h3>
            </div>
            <form method="POST" action="{{ route('admin.uso-medicamentos.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_medicamento" class="required"><i class="fas fa-syringe"></i> Medicamento</label>
                                <select class="form-control @error('id_medicamento') is-invalid @enderror" 
                                        id="id_medicamento" name="id_medicamento" required>
                                    <option value="">Seleccionar medicamento</option>
                                    @foreach($medicamentos as $medicamento)
                                        <option value="{{ $medicamento->id_medicamento }}" {{ old('id_medicamento') == $medicamento->id_medicamento ? 'selected' : '' }}>
                                            {{ $medicamento->nombre }}
                                            @if($medicamento->periodo_retiro_dias > 0)
                                                (Retiro: {{ $medicamento->periodo_retiro_dias }} días)
                                            @endif
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
                                <label for="fecha_aplicacion" class="required"><i class="fas fa-calendar"></i> Fecha de Aplicación</label>
                                <input type="date" class="form-control @error('fecha_aplicacion') is-invalid @enderror" 
                                       id="fecha_aplicacion" name="fecha_aplicacion" 
                                       value="{{ old('fecha_aplicacion', date('Y-m-d')) }}" 
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
                                       value="{{ old('dosis_aplicada') }}" step="0.01" min="0">
                                @error('dosis_aplicada')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="unidad_dosis"><i class="fas fa-ruler"></i> Unidad</label>
                                <input type="text" class="form-control @error('unidad_dosis') is-invalid @enderror" 
                                       id="unidad_dosis" name="unidad_dosis" 
                                       value="{{ old('unidad_dosis') }}" placeholder="ml, mg, unidades">
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
                                          id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Nota:</strong> Si el medicamento tiene período de retiro, se generará automáticamente un retiro de ordeño.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('admin.uso-medicamentos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

