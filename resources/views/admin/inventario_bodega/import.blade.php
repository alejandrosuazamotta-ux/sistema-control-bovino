@extends('layouts.master')

@section('title', 'Importar Productos de Inventario')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-file-excel"></i> Importar Productos de Inventario
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventario-bodega.index') }}">Inventario Bodega</a></li>
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
                    <form action="{{ route('admin.inventario-bodega.preview-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Instrucciones</h5>
                                <p>Seleccione un archivo Excel (.xlsx, .xls, .csv) con los siguientes campos:</p>
                                <ul>
                                    <li><strong>codigo</strong> - Código único del producto (requerido)</li>
                                    <li><strong>nombre</strong> - Nombre del producto (requerido)</li>
                                    <li><strong>tipo_producto</strong> - Tipo: Medicamento, Insumo, Alimento, Equipo, Otro (opcional, por defecto: Insumo)</li>
                                    <li><strong>medicamento</strong> - Nombre del medicamento relacionado (opcional, solo si tipo_producto = Medicamento)</li>
                                    <li><strong>unidad_medida</strong> - Unidad de medida (opcional, por defecto: Unidad)</li>
                                    <li><strong>stock_actual</strong> - Stock actual (opcional, por defecto: 0)</li>
                                    <li><strong>stock_minimo</strong> - Stock mínimo (opcional, por defecto: 0)</li>
                                    <li><strong>stock_maximo</strong> - Stock máximo (opcional)</li>
                                    <li><strong>precio_unitario</strong> - Precio unitario (opcional, por defecto: 0)</li>
                                    <li><strong>proveedor</strong> - Proveedor (opcional)</li>
                                    <li><strong>fecha_vencimiento</strong> - Fecha de vencimiento (opcional)</li>
                                    <li><strong>lote</strong> - Número de lote (opcional)</li>
                                    <li><strong>ubicacion_bodega</strong> - Ubicación en bodega (opcional)</li>
                                    <li><strong>observaciones</strong> - Observaciones (opcional)</li>
                                    <li><strong>activo</strong> - Activo (Sí/No, opcional, por defecto: Sí)</li>
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
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Previsualizar
                            </button>
                            <a href="{{ route('admin.inventario-bodega.index') }}" class="btn btn-secondary">
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

