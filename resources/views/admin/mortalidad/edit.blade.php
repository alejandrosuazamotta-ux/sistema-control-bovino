@extends('layouts.master')

@section('title', 'Editar Registro de Mortalidad')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-edit"></i> Editar Registro de Mortalidad
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.mortalidad.index') }}">Mortalidad</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-list"></i> Información del Registro
                </h3>
            </div>
            <form action="{{ route('admin.mortalidad.update', $mortalidad->id_mortalidad) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <!-- Información del Animal (solo lectura) -->
                    <div class="alert alert-info">
                        <strong>Animal:</strong> 
                        @if($mortalidad->animal)
                            @if($mortalidad->esVaca())
                                <i class="fas fa-cow"></i> Vaca: {{ $mortalidad->animal->codigo }}
                            @else
                                <i class="fas fa-baby"></i> Cría: {{ $mortalidad->animal->nombre_cria ?? 'Cría #' . $mortalidad->animal_id }}
                            @endif
                        @else
                            <span class="text-muted">Animal no encontrado</span>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha">Fecha de Muerte <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" id="fecha" 
                                       class="form-control @error('fecha') is-invalid @enderror" 
                                       value="{{ old('fecha', $mortalidad->fecha->format('Y-m-d')) }}" 
                                       max="{{ date('Y-m-d') }}" required>
                                @error('fecha')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora">Hora de Muerte</label>
                                <input type="time" name="hora" id="hora" 
                                       class="form-control @error('hora') is-invalid @enderror" 
                                       value="{{ old('hora', $mortalidad->hora ? \Carbon\Carbon::parse($mortalidad->hora)->format('H:i') : '') }}">
                                @error('hora')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clasificacion">Clasificación <span class="text-danger">*</span></label>
                                <select name="clasificacion" id="clasificacion" 
                                        class="form-control @error('clasificacion') is-invalid @enderror" required>
                                    <option value="">Seleccione una clasificación</option>
                                    <option value="Ternero" {{ old('clasificacion', $mortalidad->clasificacion) == 'Ternero' ? 'selected' : '' }}>Ternero</option>
                                    <option value="Novilla" {{ old('clasificacion', $mortalidad->clasificacion) == 'Novilla' ? 'selected' : '' }}>Novilla</option>
                                    <option value="Vaca" {{ old('clasificacion', $mortalidad->clasificacion) == 'Vaca' ? 'selected' : '' }}>Vaca</option>
                                    <option value="Toro" {{ old('clasificacion', $mortalidad->clasificacion) == 'Toro' ? 'selected' : '' }}>Toro</option>
                                    <option value="Becerro" {{ old('clasificacion', $mortalidad->clasificacion) == 'Becerro' ? 'selected' : '' }}>Becerro</option>
                                    <option value="Becerra" {{ old('clasificacion', $mortalidad->clasificacion) == 'Becerra' ? 'selected' : '' }}>Becerra</option>
                                </select>
                                @error('clasificacion')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="peso">Peso (kg)</label>
                                <input type="number" name="peso" id="peso" step="0.01" min="0" max="2000"
                                       class="form-control @error('peso') is-invalid @enderror" 
                                       value="{{ old('peso', $mortalidad->peso) }}" placeholder="Ej: 450.50">
                                @error('peso')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="causa">Causa de Muerte <span class="text-danger">*</span></label>
                        <textarea name="causa" id="causa" rows="3" 
                                  class="form-control @error('causa') is-invalid @enderror" 
                                  required placeholder="Describa la causa de la muerte...">{{ old('causa', $mortalidad->causa) }}</textarea>
                        @error('causa')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="acta">Acta o Documento</label>
                        <textarea name="acta" id="acta" rows="2" 
                                  class="form-control @error('acta') is-invalid @enderror" 
                                  placeholder="Número de acta o referencia de documento...">{{ old('acta', $mortalidad->acta) }}</textarea>
                        @error('acta')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($mortalidad->acta_path)
                    <div class="form-group">
                        <label>Evidencia Actual</label>
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $mortalidad->acta_path) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver Evidencia Actual
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="acta_archivo">
                            <i class="fas fa-file-upload"></i> {{ $mortalidad->acta_path ? 'Reemplazar Evidencia' : 'Evidencia (Acta PDF o Imagen)' }}
                        </label>
                        <input type="file" name="acta_archivo" id="acta_archivo" 
                               class="form-control @error('acta_archivo') is-invalid @enderror" 
                               accept="image/*,.pdf">
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG, PDF (máx. 10MB)</small>
                        @error('acta_archivo')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3" 
                                  class="form-control @error('observaciones') is-invalid @enderror" 
                                  placeholder="Observaciones adicionales...">{{ old('observaciones', $mortalidad->observaciones) }}</textarea>
                        @error('observaciones')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('admin.mortalidad.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

