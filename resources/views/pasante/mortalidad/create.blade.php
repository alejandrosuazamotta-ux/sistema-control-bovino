@extends('layouts.master')

@section('title', 'Nuevo Registro de Mortalidad')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-skull"></i> Nuevo Registro de Mortalidad
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pasante.mortalidad.index') }}">Mortalidad</a></li>
                    <li class="breadcrumb-item active">Nuevo</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus-circle"></i> Registrar Mortalidad
                </h3>
            </div>
            <form action="{{ route('pasante.mortalidad.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Nota:</strong> Como pasante, puedes registrar mortalidad y subir evidencia (acta PDF o imagen). El estado del animal se actualizará automáticamente.
                    </div>

                    <!-- Tipo de Animal -->
                    <div class="form-group">
                        <label for="animal_type">Tipo de Animal <span class="text-danger">*</span></label>
                        <select name="animal_type" id="animal_type" class="form-control @error('animal_type') is-invalid @enderror" required>
                            <option value="">Seleccione el tipo de animal</option>
                            <option value="App\Models\Vaca" {{ old('animal_type') == 'App\Models\Vaca' ? 'selected' : '' }}>Vaca</option>
                            <option value="App\Models\Cria" {{ old('animal_type') == 'App\Models\Cria' ? 'selected' : '' }}>Cría</option>
                        </select>
                        @error('animal_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Animal -->
                    <div class="form-group" id="animal_vaca_group" style="display: none;">
                        <label for="animal_id_vaca">Vaca <span class="text-danger">*</span></label>
                        <select name="animal_id" id="animal_id_vaca" class="form-control @error('animal_id') is-invalid @enderror">
                            <option value="">Seleccione una vaca</option>
                            @foreach($vacas as $vaca)
                                <option value="{{ $vaca->id_vaca }}" {{ old('animal_id') == $vaca->id_vaca ? 'selected' : '' }}>
                                    {{ $vaca->codigo }} - {{ $vaca->raza ?? 'Sin raza' }}
                                </option>
                            @endforeach
                        </select>
                        @error('animal_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" id="animal_cria_group" style="display: none;">
                        <label for="animal_id_cria">Cría <span class="text-danger">*</span></label>
                        <select name="animal_id" id="animal_id_cria" class="form-control @error('animal_id') is-invalid @enderror">
                            <option value="">Seleccione una cría</option>
                            @foreach($crias as $cria)
                                <option value="{{ $cria->id_cria }}" {{ old('animal_id') == $cria->id_cria ? 'selected' : '' }}>
                                    {{ $cria->nombre_cria ?? 'Cría #' . $cria->id_cria }} - Madre: {{ $cria->vacaMadre->codigo ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('animal_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha">Fecha de Muerte <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" id="fecha" 
                                       class="form-control @error('fecha') is-invalid @enderror" 
                                       value="{{ old('fecha', date('Y-m-d')) }}" 
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
                                       value="{{ old('hora') }}">
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
                                    <option value="Ternero" {{ old('clasificacion') == 'Ternero' ? 'selected' : '' }}>Ternero</option>
                                    <option value="Novilla" {{ old('clasificacion') == 'Novilla' ? 'selected' : '' }}>Novilla</option>
                                    <option value="Vaca" {{ old('clasificacion') == 'Vaca' ? 'selected' : '' }}>Vaca</option>
                                    <option value="Toro" {{ old('clasificacion') == 'Toro' ? 'selected' : '' }}>Toro</option>
                                    <option value="Becerro" {{ old('clasificacion') == 'Becerro' ? 'selected' : '' }}>Becerro</option>
                                    <option value="Becerra" {{ old('clasificacion') == 'Becerra' ? 'selected' : '' }}>Becerra</option>
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
                                       value="{{ old('peso') }}" placeholder="Ej: 450.50">
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
                                  required placeholder="Describa la causa de la muerte...">{{ old('causa') }}</textarea>
                        @error('causa')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="acta">Acta o Documento</label>
                        <textarea name="acta" id="acta" rows="2" 
                                  class="form-control @error('acta') is-invalid @enderror" 
                                  placeholder="Número de acta o referencia de documento...">{{ old('acta') }}</textarea>
                        @error('acta')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="acta_archivo">
                            <i class="fas fa-file-upload"></i> Evidencia (Acta PDF o Imagen) <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="acta_archivo" id="acta_archivo" 
                               class="form-control @error('acta_archivo') is-invalid @enderror" 
                               accept="image/*,.pdf" required>
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG, PDF (máx. 10MB). La evidencia es obligatoria para pasantes.</small>
                        @error('acta_archivo')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3" 
                                  class="form-control @error('observaciones') is-invalid @enderror" 
                                  placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('pasante.mortalidad.index') }}" class="btn btn-secondary">
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
        const animalType = document.getElementById('animal_type');
        const animalVacaGroup = document.getElementById('animal_vaca_group');
        const animalCriaGroup = document.getElementById('animal_cria_group');
        const animalIdVaca = document.getElementById('animal_id_vaca');
        const animalIdCria = document.getElementById('animal_id_cria');

        animalType.addEventListener('change', function() {
            if (this.value === 'App\Models\Vaca') {
                animalVacaGroup.style.display = 'block';
                animalCriaGroup.style.display = 'none';
                animalIdVaca.required = true;
                animalIdCria.required = false;
                animalIdCria.value = '';
            } else if (this.value === 'App\Models\Cria') {
                animalVacaGroup.style.display = 'none';
                animalCriaGroup.style.display = 'block';
                animalIdVaca.required = false;
                animalIdCria.required = true;
                animalIdVaca.value = '';
            } else {
                animalVacaGroup.style.display = 'none';
                animalCriaGroup.style.display = 'none';
                animalIdVaca.required = false;
                animalIdCria.required = false;
            }
        });

        // Mostrar el grupo correspondiente si hay un valor en old
        @if(old('animal_type'))
            animalType.dispatchEvent(new Event('change'));
        @endif
    });
</script>
@endpush
@endsection

