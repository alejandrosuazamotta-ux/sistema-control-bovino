@extends('layouts.master')

@section('title', 'Importar Mortalidad')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Importar Mortalidad</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.mortalidad.index') }}">Mortalidad</a></li>
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
                        <form action="{{ route('admin.mortalidad.preview-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Instrucciones</h5>
                                    <p>Seleccione un archivo Excel (.xlsx, .xls, .csv) con los siguientes campos:</p>
                                    <ul>
                                        <li><strong>identificacion</strong> o <strong>codigo</strong> - Código del animal (requerido)</li>
                                        <li><strong>tipo_animal</strong> o <strong>tipo</strong> - Vaca o Cria (opcional, por defecto: Vaca)</li>
                                        <li><strong>fecha</strong> - Fecha de muerte (requerido)</li>
                                        <li><strong>hora</strong> - Hora de muerte (opcional)</li>
                                        <li><strong>clasificacion</strong> - Ternero, Novilla, Vaca, Toro, Becerro (opcional)</li>
                                        <li><strong>peso</strong> - Peso en kg (opcional)</li>
                                        <li><strong>causa</strong> - Causa de muerte (requerido)</li>
                                        <li><strong>acta</strong> - Número de acta (opcional)</li>
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
                                <a href="{{ route('admin.mortalidad.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.querySelector('#archivo').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Seleccionar archivo...';
        e.target.nextElementSibling.textContent = fileName;
    });
</script>
@endsection

