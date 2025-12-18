@extends('layouts.master')

@section('title', 'Importar Pruebas Sanitarias')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-file-excel"></i> Importar Pruebas Sanitarias
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pruebas-sanitarias.index') }}">Pruebas Sanitarias</a></li>
                    <li class="breadcrumb-item active">Importar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-excel"></i> Importar desde Excel
                        </h3>
                    </div>
                    <form action="{{ route('admin.pruebas-sanitarias.preview-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Instrucciones</h5>
                                <p>Seleccione un archivo Excel (.xlsx, .xls, .csv) con los siguientes campos:</p>
                                <ul>
                                    <li><strong>codigo_vaca</strong> o <strong>codigo</strong> - Código de la vaca (requerido)</li>
                                    <li><strong>tipo_prueba</strong> - Mastitis, Brucelosis, Tuberculosis, Otra (requerido)</li>
                                    <li><strong>fecha_prueba</strong> - Fecha de la prueba (requerido, formato: YYYY-MM-DD)</li>
                                    <li><strong>resultado</strong> - Positivo, Negativo, Pendiente (requerido)</li>
                                    <li><strong>fecha_resultado</strong> - Fecha del resultado (opcional, formato: YYYY-MM-DD)</li>
                                    <li><strong>severidad</strong> - Leve, Moderada, Severa (solo para Mastitis Positiva)</li>
                                    <li><strong>tratamiento_sugerido</strong> - Tratamiento sugerido (solo para Mastitis)</li>
                                    <li><strong>acta</strong> - Número de acta (solo para Brucelosis/Tuberculosis Positiva)</li>
                                    <li><strong>responsable_prueba</strong> - Nombre del responsable (opcional)</li>
                                    <li><strong>observaciones</strong> - Observaciones adicionales (opcional)</li>
                                    <li><strong>personal</strong> o <strong>encargado</strong> - Nombre del personal (opcional)</li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <label for="archivo">Archivo Excel</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('archivo') is-invalid @enderror" id="archivo" name="archivo" accept=".xlsx,.xls,.csv" required>
                                        <label class="custom-file-label" for="archivo">Seleccionar archivo...</label>
                                    </div>
                                </div>
                                @error('archivo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Tamaño máximo: 10MB. Formatos permitidos: .xlsx, .xls, .csv</small>
                            </div>

                            <div class="form-group">
                                <a href="{{ route('admin.pruebas-sanitarias.download-template') }}" class="btn btn-info">
                                    <i class="fas fa-download"></i> Descargar Plantilla
                                </a>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Previsualizar
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
    document.querySelector('#archivo').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Seleccionar archivo...';
        e.target.nextElementSibling.textContent = fileName;
    });
</script>
@endpush
@endsection

