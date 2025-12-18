@extends('layouts.master')

@section('title', 'Registrar Nueva Vaca')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-plus-circle"></i> Registrar Nueva Vaca
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vacas.index') }}">Vacas</a></li>
                    <li class="breadcrumb-item active">Nueva Vaca</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Formulario principal -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cow"></i> Información de la Vaca
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('admin.vacas.store') }}" id="vaca-form">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <!-- Columna izquierda -->
                                <div class="col-md-6">
                                    <!-- Código de la Vaca -->
                                    <div class="form-group">
                                        <label for="codigo" class="required">
                                            <i class="fas fa-barcode"></i> Código de Identificación
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('codigo') is-invalid @enderror" 
                                               id="codigo" 
                                               name="codigo" 
                                               value="{{ old('codigo') }}" 
                                               placeholder="Ej: V001, CHIP123, etc."
                                               maxlength="20"
                                               required>
                                        @error('codigo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Código único para identificar la vaca (máximo 20 caracteres)
                                        </small>
                                    </div>

                                    <!-- Raza -->
                                    <div class="form-group">
                                        <label for="raza">
                                            <i class="fas fa-tag"></i> Raza
                                        </label>
                                        <select class="form-control @error('raza') is-invalid @enderror" 
                                                id="raza" 
                                                name="raza">
                                            <option value="">Seleccionar raza</option>
                                            <option value="Holstein" {{ old('raza') == 'Holstein' ? 'selected' : '' }}>Holstein</option>
                                            <option value="Jersey" {{ old('raza') == 'Jersey' ? 'selected' : '' }}>Jersey</option>
                                            <option value="Gyr" {{ old('raza') == 'Gyr' ? 'selected' : '' }}>Gyr</option>
                                            <option value="Brahman" {{ old('raza') == 'Brahman' ? 'selected' : '' }}>Brahman</option>
                                            <option value="Angus" {{ old('raza') == 'Angus' ? 'selected' : '' }}>Angus</option>
                                            <option value="Hereford" {{ old('raza') == 'Hereford' ? 'selected' : '' }}>Hereford</option>
                                            <option value="Cebú" {{ old('raza') == 'Cebú' ? 'selected' : '' }}>Cebú</option>
                                            <option value="Criolla" {{ old('raza') == 'Criolla' ? 'selected' : '' }}>Criolla</option>
                                            <option value="Otra" {{ old('raza') == 'Otra' ? 'selected' : '' }}>Otra</option>
                                        </select>
                                        @error('raza')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Fecha de Nacimiento -->
                                    <div class="form-group">
                                        <label for="fecha_nacimiento">
                                            <i class="fas fa-calendar"></i> Fecha de Nacimiento
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                               id="fecha_nacimiento" 
                                               name="fecha_nacimiento" 
                                               value="{{ old('fecha_nacimiento') }}"
                                               max="{{ date('Y-m-d') }}">
                                        @error('fecha_nacimiento')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Fecha de nacimiento de la vaca (opcional)
                                        </small>
                                    </div>
                                </div>

                                <!-- Columna derecha -->
                                <div class="col-md-6">
                                    <!-- Estado de Salud -->
                                    <div class="form-group">
                                        <label for="estado_salud" class="required">
                                            <i class="fas fa-heartbeat"></i> Estado de Salud
                                        </label>
                                        <select class="form-control @error('estado_salud') is-invalid @enderror" 
                                                id="estado_salud" 
                                                name="estado_salud" 
                                                required>
                                            <option value="">Seleccionar estado</option>
                                            <option value="Sana" {{ old('estado_salud') == 'Sana' ? 'selected' : '' }}>Sana</option>
                                            <option value="En tratamiento" {{ old('estado_salud') == 'En tratamiento' ? 'selected' : '' }}>En tratamiento</option>
                                            <option value="En observación" {{ old('estado_salud') == 'En observación' ? 'selected' : '' }}>En observación</option>
                                        </select>
                                        @error('estado_salud')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Estado Reproductivo -->
                                    <div class="form-group">
                                        <label for="estado_reproductivo" class="required">
                                            <i class="fas fa-baby"></i> Estado Reproductivo
                                        </label>
                                        <select class="form-control @error('estado_reproductivo') is-invalid @enderror" 
                                                id="estado_reproductivo" 
                                                name="estado_reproductivo" 
                                                required>
                                            <option value="">Seleccionar estado</option>
                                            <option value="Celo" {{ old('estado_reproductivo') == 'Celo' ? 'selected' : '' }}>Celo</option>
                                            <option value="Preñada" {{ old('estado_reproductivo') == 'Preñada' ? 'selected' : '' }}>Preñada</option>
                                            <option value="Lactancia" {{ old('estado_reproductivo') == 'Lactancia' ? 'selected' : '' }}>Lactancia</option>
                                            <option value="Descanso" {{ old('estado_reproductivo') == 'Descanso' ? 'selected' : '' }}>Descanso</option>
                                        </select>
                                        @error('estado_reproductivo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Potrero -->
                                    <div class="form-group">
                                        <label for="id_potrero">
                                            <i class="fas fa-map-marker-alt"></i> Potrero
                                        </label>
                                        <select class="form-control @error('id_potrero') is-invalid @enderror" 
                                                id="id_potrero" 
                                                name="id_potrero">
                                            <option value="">Sin asignar</option>
                                            @foreach(\App\Models\Potrero::all() as $potrero)
                                                @php
                                                    $ocupacion = $potrero->vacas->count();
                                                    $disponible = $potrero->capacidad - $ocupacion;
                                                @endphp
                                                <option value="{{ $potrero->id_potrero }}" 
                                                        {{ old('id_potrero') == $potrero->id_potrero ? 'selected' : '' }}
                                                        {{ $disponible <= 0 ? 'disabled' : '' }}>
                                                    {{ $potrero->nombre }} 
                                                    ({{ $ocupacion }}/{{ $potrero->capacidad }} animales)
                                                    {{ $disponible <= 0 ? ' - LLENO' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_potrero')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Asignar a un potrero específico (opcional)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-save"></i> Registrar Vaca
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('admin.vacas.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <button type="reset" class="btn btn-warning btn-block">
                                        <i class="fas fa-undo"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Confirmación antes de enviar
    $('#vaca-form').on('submit', function(e) {
        const codigo = $('#codigo').val().trim();
        const estadoSalud = $('#estado_salud').val();
        const estadoReproductivo = $('#estado_reproductivo').val();

        if (!codigo || !estadoSalud || !estadoReproductivo) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campos requeridos',
                text: 'Por favor completa todos los campos obligatorios.'
            });
            return false;
        }

        // Mostrar confirmación
        e.preventDefault();
        Swal.fire({
            title: '¿Registrar vaca?',
            text: `¿Estás seguro de que quieres registrar la vaca con código "${codigo}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#vaca-form')[0].submit();
            }
        });
    });

    // Auto-focus en el primer campo
    $('#codigo').focus();
});
</script>
@endpush

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.form-group label {
    font-weight: 600;
    color: #495057;
}

.form-group label i {
    margin-right: 5px;
    color: #6c757d;
}
</style>
@endpush
