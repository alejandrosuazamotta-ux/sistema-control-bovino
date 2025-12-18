@extends('layouts.master')

@section('title', 'Editar Producto en Inventario')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-edit"></i> Editar Producto en Inventario</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventario-bodega.index') }}">Inventario Bodega</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Información del Producto</h3>
            </div>
            <form method="POST" action="{{ route('admin.inventario-bodega.update', $producto->id_inventario) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo" class="required"><i class="fas fa-barcode"></i> Código</label>
                                <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                       id="codigo" name="codigo" value="{{ old('codigo', $producto->codigo) }}" required>
                                @error('codigo')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nombre" class="required"><i class="fas fa-tag"></i> Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                                @error('nombre')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tipo_producto" class="required"><i class="fas fa-box"></i> Tipo de Producto</label>
                                <select class="form-control @error('tipo_producto') is-invalid @enderror" 
                                        id="tipo_producto" name="tipo_producto" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Medicamento" {{ old('tipo_producto', $producto->tipo_producto) == 'Medicamento' ? 'selected' : '' }}>Medicamento</option>
                                    <option value="Insumo" {{ old('tipo_producto', $producto->tipo_producto) == 'Insumo' ? 'selected' : '' }}>Insumo</option>
                                    <option value="Alimento" {{ old('tipo_producto', $producto->tipo_producto) == 'Alimento' ? 'selected' : '' }}>Alimento</option>
                                    <option value="Equipo" {{ old('tipo_producto', $producto->tipo_producto) == 'Equipo' ? 'selected' : '' }}>Equipo</option>
                                    <option value="Otro" {{ old('tipo_producto', $producto->tipo_producto) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo_producto')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group" id="medicamento-group" style="display: {{ old('tipo_producto', $producto->tipo_producto) == 'Medicamento' ? 'block' : 'none' }};">
                                <label for="id_medicamento"><i class="fas fa-syringe"></i> Medicamento Relacionado</label>
                                <select class="form-control @error('id_medicamento') is-invalid @enderror" 
                                        id="id_medicamento" name="id_medicamento">
                                    <option value="">Seleccione un medicamento...</option>
                                    @foreach($medicamentos as $medicamento)
                                        <option value="{{ $medicamento->id_medicamento }}" {{ old('id_medicamento', $producto->id_medicamento) == $medicamento->id_medicamento ? 'selected' : '' }}>
                                            {{ $medicamento->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_medicamento')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="unidad_medida" class="required"><i class="fas fa-ruler"></i> Unidad de Medida</label>
                                <input type="text" class="form-control @error('unidad_medida') is-invalid @enderror" 
                                       id="unidad_medida" name="unidad_medida" value="{{ old('unidad_medida', $producto->unidad_medida) }}" required>
                                @error('unidad_medida')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="proveedor"><i class="fas fa-truck"></i> Proveedor</label>
                                <input type="text" class="form-control @error('proveedor') is-invalid @enderror" 
                                       id="proveedor" name="proveedor" value="{{ old('proveedor', $producto->proveedor) }}">
                                @error('proveedor')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock_minimo" class="required"><i class="fas fa-exclamation-triangle"></i> Stock Mínimo</label>
                                <input type="number" step="0.01" class="form-control @error('stock_minimo') is-invalid @enderror" 
                                       id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" min="0" required>
                                @error('stock_minimo')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="stock_maximo"><i class="fas fa-arrow-up"></i> Stock Máximo</label>
                                <input type="number" step="0.01" class="form-control @error('stock_maximo') is-invalid @enderror" 
                                       id="stock_maximo" name="stock_maximo" value="{{ old('stock_maximo', $producto->stock_maximo) }}" min="0">
                                @error('stock_maximo')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="precio_unitario" class="required"><i class="fas fa-dollar-sign"></i> Precio Unitario</label>
                                <input type="number" step="0.01" class="form-control @error('precio_unitario') is-invalid @enderror" 
                                       id="precio_unitario" name="precio_unitario" value="{{ old('precio_unitario', $producto->precio_unitario) }}" min="0" required>
                                @error('precio_unitario')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="fecha_vencimiento"><i class="fas fa-calendar-times"></i> Fecha de Vencimiento</label>
                                <input type="date" class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                       id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $producto->fecha_vencimiento ? $producto->fecha_vencimiento->format('Y-m-d') : '') }}">
                                @error('fecha_vencimiento')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="lote"><i class="fas fa-hashtag"></i> Lote</label>
                                <input type="text" class="form-control @error('lote') is-invalid @enderror" 
                                       id="lote" name="lote" value="{{ old('lote', $producto->lote) }}">
                                @error('lote')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="ubicacion_bodega"><i class="fas fa-map-marker-alt"></i> Ubicación en Bodega</label>
                                <input type="text" class="form-control @error('ubicacion_bodega') is-invalid @enderror" 
                                       id="ubicacion_bodega" name="ubicacion_bodega" value="{{ old('ubicacion_bodega', $producto->ubicacion_bodega) }}">
                                @error('ubicacion_bodega')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" 
                                           {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="observaciones"><i class="fas fa-sticky-note"></i> Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $producto->observaciones) }}</textarea>
                                @error('observaciones')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('admin.inventario-bodega.show', $producto->id_inventario) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                    <a href="{{ route('admin.inventario-bodega.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.getElementById('tipo_producto').addEventListener('change', function() {
    const medicamentoGroup = document.getElementById('medicamento-group');
    if (this.value === 'Medicamento') {
        medicamentoGroup.style.display = 'block';
    } else {
        medicamentoGroup.style.display = 'none';
        document.getElementById('id_medicamento').value = '';
    }
});
</script>
@endpush
@endsection

