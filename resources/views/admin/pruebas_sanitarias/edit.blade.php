@extends('layouts.master')

@section('title', 'Editar Prueba Sanitaria')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-edit"></i> Editar Prueba Sanitaria
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pruebas-sanitarias.index') }}">Pruebas Sanitarias</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-vial"></i> Información de la Prueba Sanitaria
                        </h3>
                    </div>
                    <form action="{{ route('admin.pruebas-sanitarias.update', $pruebaSanitaria->id_prueba) }}" method="POST" enctype="multipart/form-data" id="formPruebaSanitaria">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Vaca -->
                                    <div class="form-group">
                                        <label for="id_vaca">
                                            <i class="fas fa-cow"></i> Vaca <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_vaca" id="id_vaca" class="form-control @error('id_vaca') is-invalid @enderror" required>
                                            <option value="">Seleccione una vaca</option>
                                            @foreach($vacas as $vaca)
                                                <option value="{{ $vaca->id_vaca }}" {{ old('id_vaca', $pruebaSanitaria->id_vaca) == $vaca->id_vaca ? 'selected' : '' }}>
                                                    {{ $vaca->codigo }} - {{ $vaca->raza ?? 'Sin raza' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_vaca')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Tipo de Prueba -->
                                    <div class="form-group">
                                        <label for="tipo_prueba">
                                            <i class="fas fa-flask"></i> Tipo de Prueba <span class="text-danger">*</span>
                                        </label>
                                        <select name="tipo_prueba" id="tipo_prueba" class="form-control @error('tipo_prueba') is-invalid @enderror" required>
                                            <option value="">Seleccione</option>
                                            <option value="Mastitis" {{ old('tipo_prueba', $pruebaSanitaria->tipo_prueba) == 'Mastitis' ? 'selected' : '' }}>Mastitis</option>
                                            <option value="Brucelosis" {{ old('tipo_prueba', $pruebaSanitaria->tipo_prueba) == 'Brucelosis' ? 'selected' : '' }}>Brucelosis</option>
                                            <option value="Tuberculosis" {{ old('tipo_prueba', $pruebaSanitaria->tipo_prueba) == 'Tuberculosis' ? 'selected' : '' }}>Tuberculosis</option>
                                        </select>
                                        @error('tipo_prueba')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Fecha Prueba -->
                                    <div class="form-group">
                                        <label for="fecha_prueba">
                                            <i class="fas fa-calendar-alt"></i> Fecha de Prueba <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="fecha_prueba" id="fecha_prueba" 
                                               class="form-control @error('fecha_prueba') is-invalid @enderror" 
                                               value="{{ old('fecha_prueba', $pruebaSanitaria->fecha_prueba->format('Y-m-d')) }}" 
                                               max="{{ now()->format('Y-m-d') }}" required>
                                        @error('fecha_prueba')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Resultado -->
                                    <div class="form-group">
                                        <label for="resultado">
                                            <i class="fas fa-check-circle"></i> Resultado <span class="text-danger">*</span>
                                        </label>
                                        <select name="resultado" id="resultado" class="form-control @error('resultado') is-invalid @enderror" required>
                                            <option value="">Seleccione</option>
                                            <option value="Positivo" {{ old('resultado', $pruebaSanitaria->resultado) == 'Positivo' ? 'selected' : '' }}>Positivo</option>
                                            <option value="Negativo" {{ old('resultado', $pruebaSanitaria->resultado) == 'Negativo' ? 'selected' : '' }}>Negativo</option>
                                            <option value="Pendiente" {{ old('resultado', $pruebaSanitaria->resultado) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        </select>
                                        @error('resultado')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Fecha Resultado -->
                                    <div class="form-group">
                                        <label for="fecha_resultado">
                                            <i class="fas fa-calendar-check"></i> Fecha de Resultado
                                        </label>
                                        <input type="date" name="fecha_resultado" id="fecha_resultado" 
                                               class="form-control @error('fecha_resultado') is-invalid @enderror" 
                                               value="{{ old('fecha_resultado', $pruebaSanitaria->fecha_resultado ? $pruebaSanitaria->fecha_resultado->format('Y-m-d') : '') }}"
                                               max="{{ now()->format('Y-m-d') }}">
                                        @error('fecha_resultado')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Severidad (solo para Mastitis) -->
                                    <div class="form-group" id="campoSeveridad" style="display: {{ ($pruebaSanitaria->tipo_prueba == 'Mastitis' && $pruebaSanitaria->resultado == 'Positivo') ? 'block' : 'none' }};">
                                        <label for="severidad">
                                            <i class="fas fa-exclamation-triangle"></i> Severidad
                                        </label>
                                        <select name="severidad" id="severidad" class="form-control @error('severidad') is-invalid @enderror">
                                            <option value="">Seleccione</option>
                                            <option value="Leve" {{ old('severidad', $pruebaSanitaria->severidad) == 'Leve' ? 'selected' : '' }}>Leve</option>
                                            <option value="Moderada" {{ old('severidad', $pruebaSanitaria->severidad) == 'Moderada' ? 'selected' : '' }}>Moderada</option>
                                            <option value="Severa" {{ old('severidad', $pruebaSanitaria->severidad) == 'Severa' ? 'selected' : '' }}>Severa</option>
                                        </select>
                                        @error('severidad')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Tratamiento Sugerido (solo para Mastitis) -->
                                    <div class="form-group" id="campoTratamiento" style="display: {{ $pruebaSanitaria->tipo_prueba == 'Mastitis' ? 'block' : 'none' }};">
                                        <label for="tratamiento_sugerido">
                                            <i class="fas fa-pills"></i> Tratamiento Sugerido
                                        </label>
                                        <textarea name="tratamiento_sugerido" id="tratamiento_sugerido" rows="3" 
                                                  class="form-control @error('tratamiento_sugerido') is-invalid @enderror" 
                                                  placeholder="Describa el tratamiento sugerido...">{{ old('tratamiento_sugerido', $pruebaSanitaria->tratamiento_sugerido) }}</textarea>
                                        @error('tratamiento_sugerido')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Acta (solo para Brucelosis/Tuberculosis) -->
                                    <div class="form-group" id="campoActa" style="display: {{ in_array($pruebaSanitaria->tipo_prueba, ['Brucelosis', 'Tuberculosis']) ? 'block' : 'none' }};">
                                        <label for="acta">
                                            <i class="fas fa-file-alt"></i> Número de Acta
                                        </label>
                                        <input type="text" name="acta" id="acta" 
                                               class="form-control @error('acta') is-invalid @enderror" 
                                               value="{{ old('acta', $pruebaSanitaria->acta) }}" placeholder="Número de acta oficial">
                                        @error('acta')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Responsable de Prueba -->
                                    <div class="form-group">
                                        <label for="responsable_prueba">
                                            <i class="fas fa-user-md"></i> Responsable de la Prueba
                                        </label>
                                        <input type="text" name="responsable_prueba" id="responsable_prueba" 
                                               class="form-control @error('responsable_prueba') is-invalid @enderror" 
                                               value="{{ old('responsable_prueba', $pruebaSanitaria->responsable_prueba) }}" placeholder="Nombre del responsable">
                                        @error('responsable_prueba')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Personal -->
                                    <div class="form-group">
                                        <label for="id_personal">
                                            <i class="fas fa-user"></i> Personal Responsable
                                        </label>
                                        <select name="id_personal" id="id_personal" class="form-control @error('id_personal') is-invalid @enderror">
                                            <option value="">Seleccione (opcional)</option>
                                            @foreach($personal as $persona)
                                                <option value="{{ $persona->id_personal }}" {{ old('id_personal', $pruebaSanitaria->id_personal) == $persona->id_personal ? 'selected' : '' }}>
                                                    {{ $persona->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_personal')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Evidencia actual -->
                                    @if($pruebaSanitaria->evidencia_path)
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-file"></i> Evidencia Actual
                                            </label>
                                            <div class="mb-2">
                                                <a href="{{ asset('storage/' . $pruebaSanitaria->evidencia_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver Evidencia Actual
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Nueva Evidencia -->
                                    <div class="form-group">
                                        <label for="evidencia_archivo">
                                            <i class="fas fa-file-upload"></i> {{ $pruebaSanitaria->evidencia_path ? 'Reemplazar Evidencia' : 'Evidencia (Imagen o PDF)' }}
                                        </label>
                                        <input type="file" name="evidencia_archivo" id="evidencia_archivo" 
                                               class="form-control @error('evidencia_archivo') is-invalid @enderror" 
                                               accept="image/*,.pdf">
                                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG, PDF (máx. 10MB)</small>
                                        @error('evidencia_archivo')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Observaciones -->
                                    <div class="form-group">
                                        <label for="observaciones">
                                            <i class="fas fa-sticky-note"></i> Observaciones
                                        </label>
                                        <textarea name="observaciones" id="observaciones" rows="3" 
                                                  class="form-control @error('observaciones') is-invalid @enderror" 
                                                  placeholder="Observaciones adicionales...">{{ old('observaciones', $pruebaSanitaria->observaciones) }}</textarea>
                                        @error('observaciones')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Prueba
                            </button>
                            <a href="{{ route('admin.pruebas-sanitarias.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoPrueba = document.getElementById('tipo_prueba');
    const resultado = document.getElementById('resultado');
    const campoSeveridad = document.getElementById('campoSeveridad');
    const campoTratamiento = document.getElementById('campoTratamiento');
    const campoActa = document.getElementById('campoActa');
    const severidad = document.getElementById('severidad');

    function toggleCamposEspecificos() {
        const esMastitis = tipoPrueba.value === 'Mastitis';
        const esBrucelosisTuberculosis = ['Brucelosis', 'Tuberculosis'].includes(tipoPrueba.value);
        const esPositivo = resultado.value === 'Positivo';
        
        // Mostrar severidad solo si es Mastitis y Positivo
        campoSeveridad.style.display = (esMastitis && esPositivo) ? 'block' : 'none';
        if (esMastitis && esPositivo) {
            severidad.setAttribute('required', 'required');
        } else {
            severidad.removeAttribute('required');
        }
        
        campoTratamiento.style.display = esMastitis ? 'block' : 'none';
        campoActa.style.display = esBrucelosisTuberculosis ? 'block' : 'none';
    }

    tipoPrueba.addEventListener('change', toggleCamposEspecificos);
    resultado.addEventListener('change', toggleCamposEspecificos);
    
    // Ejecutar al cargar la página
    toggleCamposEspecificos();
});
</script>
@endpush
@endsection

