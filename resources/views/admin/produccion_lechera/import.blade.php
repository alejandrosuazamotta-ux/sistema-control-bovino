@extends('layouts.master')

@section('title', 'Importar Producción Lechera')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Importar Producción Lechera</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.produccion-lechera.index') }}">Producción Lechera</a></li>
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
                        <form action="{{ route('admin.produccion-lechera.preview-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Instrucciones</h5>
                                    <p>Seleccione un archivo Excel (.xlsx, .xls, .csv) con los siguientes campos:</p>
                                    <ul>
                                        <li><strong>codigo_vaca</strong> o <strong>codigo</strong> - Código de la vaca (requerido)</li>
                                        <li><strong>fecha</strong> - Fecha de producción (requerido)</li>
                                        <li><strong>turno</strong> - AM o PM (opcional, por defecto: AM)</li>
                                        <li><strong>cantidad_leche</strong> o <strong>litros</strong> - Cantidad en litros (requerido)</li>
                                        <li><strong>destino</strong> - Agroindustria, Lechero, Particular, Consumo (opcional)</li>
                                        <li><strong>valor_unidad</strong> - Valor por unidad (opcional)</li>
                                        <li><strong>encargado</strong> o <strong>personal</strong> - Nombre del encargado (opcional)</li>
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
                                <a href="{{ route('admin.produccion-lechera.index') }}" class="btn btn-secondary">
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
                            <a href="{{ route('admin.produccion-lechera.download-template') }}" class="btn btn-info">
                                <i class="fas fa-download"></i> Descargar Plantilla Excel
                            </a>
                            <p class="mt-2 text-muted">
                                <small>La plantilla incluye los encabezados correctos y una fila de ejemplo para referencia.</small>
                            </p>
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

