@extends('layouts.master')

@section('title', 'Importar Crías')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Importar Crías/Nacimientos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.crias.index') }}">Crías</a></li>
                        <li class="breadcrumb-item active">Importar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-excel"></i> Importar desde Excel
                            </h3>
                        </div>
                        <form action="{{ route('admin.crias.preview-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Instrucciones</h5>
                                    <p>Seleccione un archivo Excel (.xlsx, .xls, .csv) con los siguientes campos:</p>
                                    <ul>
                                        <li><strong>codigo_madre</strong> o <strong>madre</strong> o <strong>codigo_vaca</strong> - Código de la vaca madre (requerido)</li>
                                        <li><strong>nombre_cria</strong> o <strong>nombre</strong> - Nombre de la cría (opcional)</li>
                                        <li><strong>sexo</strong> - Macho o Hembra (requerido)</li>
                                        <li><strong>fecha_nacimiento</strong> o <strong>fecha</strong> - Fecha de nacimiento (requerido)</li>
                                        <li><strong>peso</strong> - Peso al nacer en kg (opcional)</li>
                                        <li><strong>fecha_tatuado</strong> - Fecha de tatuado (opcional)</li>
                                        <li><strong>concepcion</strong> - IA, Monta Natural, Transferencia Embrionaria (opcional)</li>
                                        <li><strong>sinigan</strong> - Código SINIGAN (opcional)</li>
                                        <li><strong>observaciones</strong> - Observaciones (opcional)</li>
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

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="preview" name="preview" checked>
                                    <label class="form-check-label" for="preview">
                                        Previsualizar antes de importar (recomendado)
                                    </label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Previsualizar
                                </button>
                                <a href="{{ route('admin.crias.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-download"></i> Descargar Plantilla
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Descargue la plantilla de ejemplo para asegurar el formato correcto:</p>
                            <a href="#" class="btn btn-info" onclick="alert('Plantilla de ejemplo - Descargar desde el sistema');">
                                <i class="fas fa-download"></i> Descargar Plantilla Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    // Mostrar nombre del archivo seleccionado
    document.querySelector('#archivo').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Seleccionar archivo...';
        e.target.nextElementSibling.textContent = fileName;
    });
</script>
@endsection

