@extends('layouts.master')

@section('title', 'Editar Registro de Salud')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-edit"></i> Editar Registro de Salud
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.salud.index') }}">Salud</a></li>
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
                            <i class="fas fa-vial"></i> Información del Registro
                        </h3>
                    </div>
                    <form action="{{ route('admin.salud.update', $registro->id_salud) }}" method="POST" id="formSalud">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <!-- Vaca -->
                            <div class="form-group">
                                <label for="id_vaca">
                                    <i class="fas fa-cow"></i> Vaca <span class="text-danger">*</span>
                                </label>
                                <select name="id_vaca" id="id_vaca" class="form-control @error('id_vaca') is-invalid @enderror" required>
                                    <option value="">Seleccione una vaca</option>
                                    @foreach($vacas as $vaca)
                                        <option value="{{ $vaca->id_vaca }}" {{ old('id_vaca', $registro->id_vaca) == $vaca->id_vaca ? 'selected' : '' }}>
                                            {{ $vaca->codigo }} - {{ $vaca->nombre ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_vaca')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tipo de Registro -->
                            <div class="form-group">
                                <label for="tipo_registro">
                                    <i class="fas fa-tag"></i> Tipo de Registro <span class="text-danger">*</span>
                                </label>
                                <select name="tipo_registro" id="tipo_registro" class="form-control @error('tipo_registro') is-invalid @enderror" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="Vacunación" {{ old('tipo_registro', $registro->tipo_registro) == 'Vacunación' ? 'selected' : '' }}>Vacunación</option>
                                    <option value="Tratamiento" {{ old('tipo_registro', $registro->tipo_registro) == 'Tratamiento' ? 'selected' : '' }}>Tratamiento</option>
                                    <option value="Prueba mastitis" {{ old('tipo_registro', $registro->tipo_registro) == 'Prueba mastitis' ? 'selected' : '' }}>Prueba Mastitis</option>
                                    <option value="Prueba Brucelosis" {{ old('tipo_registro', $registro->tipo_registro) == 'Prueba Brucelosis' ? 'selected' : '' }}>Prueba Brucelosis</option>
                                    <option value="Prueba Tuberculosis" {{ old('tipo_registro', $registro->tipo_registro) == 'Prueba Tuberculosis' ? 'selected' : '' }}>Prueba Tuberculosis</option>
                                    <option value="Otro" {{ old('tipo_registro', $registro->tipo_registro) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo_registro')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Fecha -->
                            <div class="form-group">
                                <label for="fecha">
                                    <i class="fas fa-calendar-alt"></i> Fecha <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="fecha" id="fecha" 
                                       class="form-control @error('fecha') is-invalid @enderror" 
                                       value="{{ old('fecha', $registro->fecha->format('Y-m-d')) }}" required>
                                @error('fecha')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Campos para Pruebas Sanitarias -->
                            <div id="camposPruebaSanitaria" style="display: {{ in_array($registro->tipo_registro, ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis']) ? 'block' : 'none' }};">
                                <hr>
                                <h5 class="text-primary">
                                    <i class="fas fa-vial"></i> Información de Prueba Sanitaria
                                </h5>

                                <!-- Tipo de Prueba -->
                                <div class="form-group">
                                    <label for="tipo_prueba">
                                        <i class="fas fa-flask"></i> Tipo de Prueba <span class="text-danger">*</span>
                                    </label>
                                    <select name="tipo_prueba" id="tipo_prueba" class="form-control @error('tipo_prueba') is-invalid @enderror">
                                        <option value="">Seleccione</option>
                                        <option value="Mastitis" {{ old('tipo_prueba', $registro->tipo_prueba) == 'Mastitis' ? 'selected' : '' }}>Mastitis</option>
                                        <option value="Brucelosis" {{ old('tipo_prueba', $registro->tipo_prueba) == 'Brucelosis' ? 'selected' : '' }}>Brucelosis</option>
                                        <option value="Tuberculosis" {{ old('tipo_prueba', $registro->tipo_prueba) == 'Tuberculosis' ? 'selected' : '' }}>Tuberculosis</option>
                                        <option value="Otra" {{ old('tipo_prueba', $registro->tipo_prueba) == 'Otra' ? 'selected' : '' }}>Otra</option>
                                    </select>
                                    @error('tipo_prueba')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Resultado -->
                                <div class="form-group">
                                    <label for="resultado">
                                        <i class="fas fa-check-circle"></i> Resultado <span class="text-danger">*</span>
                                    </label>
                                    <select name="resultado" id="resultado" class="form-control @error('resultado') is-invalid @enderror">
                                        <option value="">Seleccione</option>
                                        <option value="Positivo" {{ old('resultado', $registro->resultado) == 'Positivo' ? 'selected' : '' }}>Positivo</option>
                                        <option value="Negativo" {{ old('resultado', $registro->resultado) == 'Negativo' ? 'selected' : '' }}>Negativo</option>
                                        <option value="Pendiente" {{ old('resultado', $registro->resultado) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
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
                                           value="{{ old('fecha_resultado', $registro->fecha_resultado ? $registro->fecha_resultado->format('Y-m-d') : '') }}">
                                    @error('fecha_resultado')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Severidad (solo para Mastitis) -->
                                <div class="form-group" id="campoSeveridad" style="display: {{ $registro->tipo_prueba === 'Mastitis' ? 'block' : 'none' }};">
                                    <label for="severidad">
                                        <i class="fas fa-exclamation-triangle"></i> Severidad
                                    </label>
                                    <select name="severidad" id="severidad" class="form-control @error('severidad') is-invalid @enderror">
                                        <option value="">Seleccione</option>
                                        <option value="Leve" {{ old('severidad', $registro->severidad) == 'Leve' ? 'selected' : '' }}>Leve</option>
                                        <option value="Moderada" {{ old('severidad', $registro->severidad) == 'Moderada' ? 'selected' : '' }}>Moderada</option>
                                        <option value="Severa" {{ old('severidad', $registro->severidad) == 'Severa' ? 'selected' : '' }}>Severa</option>
                                    </select>
                                    @error('severidad')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Tratamiento Sugerido (solo para Mastitis) -->
                                <div class="form-group" id="campoTratamiento" style="display: {{ $registro->tipo_prueba === 'Mastitis' ? 'block' : 'none' }};">
                                    <label for="tratamiento_sugerido">
                                        <i class="fas fa-pills"></i> Tratamiento Sugerido
                                    </label>
                                    <textarea name="tratamiento_sugerido" id="tratamiento_sugerido" rows="3" 
                                              class="form-control @error('tratamiento_sugerido') is-invalid @enderror" 
                                              placeholder="Describa el tratamiento sugerido...">{{ old('tratamiento_sugerido', $registro->tratamiento_sugerido) }}</textarea>
                                    @error('tratamiento_sugerido')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Acta (solo para Brucelosis/Tuberculosis) -->
                                <div class="form-group" id="campoActa" style="display: {{ in_array($registro->tipo_prueba, ['Brucelosis', 'Tuberculosis']) ? 'block' : 'none' }};">
                                    <label for="acta">
                                        <i class="fas fa-file-alt"></i> Número de Acta
                                    </label>
                                    <input type="text" name="acta" id="acta" 
                                           class="form-control @error('acta') is-invalid @enderror" 
                                           value="{{ old('acta', $registro->acta) }}" placeholder="Número de acta oficial">
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
                                           value="{{ old('responsable_prueba', $registro->responsable_prueba) }}" placeholder="Nombre del responsable">
                                    @error('responsable_prueba')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="descripcion">
                                    <i class="fas fa-align-left"></i> Descripción
                                </label>
                                <textarea name="descripcion" id="descripcion" rows="3" 
                                          class="form-control @error('descripcion') is-invalid @enderror" 
                                          placeholder="Descripción del registro...">{{ old('descripcion', $registro->descripcion) }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Observaciones -->
                            <div class="form-group" id="campoObservaciones" style="display: {{ in_array($registro->tipo_registro, ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis']) ? 'block' : 'none' }};">
                                <label for="observaciones">
                                    <i class="fas fa-sticky-note"></i> Observaciones
                                </label>
                                <textarea name="observaciones" id="observaciones" rows="3" 
                                          class="form-control @error('observaciones') is-invalid @enderror" 
                                          placeholder="Observaciones adicionales...">{{ old('observaciones', $registro->observaciones) }}</textarea>
                                @error('observaciones')
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
                                        <option value="{{ $persona->id_personal }}" {{ old('id_personal', $registro->id_personal) == $persona->id_personal ? 'selected' : '' }}>
                                            {{ $persona->nombre }} {{ $persona->apellido ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_personal')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Registro
                            </button>
                            <a href="{{ route('admin.salud.index') }}" class="btn btn-secondary">
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
    const tipoRegistro = document.getElementById('tipo_registro');
    const camposPruebaSanitaria = document.getElementById('camposPruebaSanitaria');
    const tipoPrueba = document.getElementById('tipo_prueba');
    const campoSeveridad = document.getElementById('campoSeveridad');
    const campoTratamiento = document.getElementById('campoTratamiento');
    const campoActa = document.getElementById('campoActa');
    const campoObservaciones = document.getElementById('campoObservaciones');

    function toggleCamposPrueba() {
        const esPruebaSanitaria = ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis'].includes(tipoRegistro.value);
        camposPruebaSanitaria.style.display = esPruebaSanitaria ? 'block' : 'none';
        campoObservaciones.style.display = esPruebaSanitaria ? 'block' : 'none';
        
        if (esPruebaSanitaria) {
            if (tipoRegistro.value === 'Prueba mastitis') {
                tipoPrueba.value = 'Mastitis';
            } else if (tipoRegistro.value === 'Prueba Brucelosis') {
                tipoPrueba.value = 'Brucelosis';
            } else if (tipoRegistro.value === 'Prueba Tuberculosis') {
                tipoPrueba.value = 'Tuberculosis';
            }
            toggleCamposEspecificos();
        }
    }

    function toggleCamposEspecificos() {
        const esMastitis = tipoPrueba.value === 'Mastitis';
        const esBrucelosisTuberculosis = ['Brucelosis', 'Tuberculosis'].includes(tipoPrueba.value);
        
        campoSeveridad.style.display = esMastitis ? 'block' : 'none';
        campoTratamiento.style.display = esMastitis ? 'block' : 'none';
        campoActa.style.display = esBrucelosisTuberculosis ? 'block' : 'none';
    }

    tipoRegistro.addEventListener('change', toggleCamposPrueba);
    tipoPrueba.addEventListener('change', toggleCamposEspecificos);
});
</script>
@endpush
@endsection

