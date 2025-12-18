@extends('layouts.master')

@section('title', 'Detalles de Producto en Inventario')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-eye"></i> Detalles de Producto</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventario-bodega.index') }}">Inventario Bodega</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información del Producto</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.inventario-bodega.edit', $producto->id_inventario) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Código:</strong></td>
                                <td><span class="badge badge-primary">{{ $producto->codigo }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Nombre:</strong></td>
                                <td>{{ $producto->nombre }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de Producto:</strong></td>
                                <td><span class="badge badge-info">{{ $producto->tipo_producto }}</span></td>
                            </tr>
                            @if($producto->medicamento)
                            <tr>
                                <td><strong>Medicamento Relacionado:</strong></td>
                                <td>{{ $producto->medicamento->nombre }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Unidad de Medida:</strong></td>
                                <td>{{ $producto->unidad_medida }}</td>
                            </tr>
                            <tr>
                                <td><strong>Stock Actual:</strong></td>
                                <td>
                                    <span class="{{ $producto->tieneStockBajo() ? 'text-danger font-weight-bold' : '' }}">
                                        {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}
                                    </span>
                                    @if($producto->tieneStockBajo())
                                        <span class="badge badge-warning">Stock Bajo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Stock Mínimo:</strong></td>
                                <td>{{ number_format($producto->stock_minimo, 2) }} {{ $producto->unidad_medida }}</td>
                            </tr>
                            @if($producto->stock_maximo)
                            <tr>
                                <td><strong>Stock Máximo:</strong></td>
                                <td>{{ number_format($producto->stock_maximo, 2) }} {{ $producto->unidad_medida }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Precio Unitario:</strong></td>
                                <td>${{ number_format($producto->precio_unitario, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Valor Total Stock:</strong></td>
                                <td><strong class="text-success">${{ number_format($producto->valor_total_stock, 2) }}</strong></td>
                            </tr>
                            @if($producto->proveedor)
                            <tr>
                                <td><strong>Proveedor:</strong></td>
                                <td>{{ $producto->proveedor }}</td>
                            </tr>
                            @endif
                            @if($producto->fecha_vencimiento)
                            <tr>
                                <td><strong>Fecha de Vencimiento:</strong></td>
                                <td>
                                    {{ $producto->fecha_vencimiento->format('d/m/Y') }}
                                    @if($producto->estaVencido())
                                        <span class="badge badge-danger">Vencido</span>
                                    @elseif($producto->estaProximoAVencer())
                                        <span class="badge badge-warning">Próximo a vencer ({{ $producto->dias_hasta_vencimiento }} días)</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @if($producto->lote)
                            <tr>
                                <td><strong>Lote:</strong></td>
                                <td>{{ $producto->lote }}</td>
                            </tr>
                            @endif
                            @if($producto->ubicacion_bodega)
                            <tr>
                                <td><strong>Ubicación en Bodega:</strong></td>
                                <td>{{ $producto->ubicacion_bodega }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    @if($producto->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-secondary">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                            @if($producto->observaciones)
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td>{{ $producto->observaciones }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Movimientos de Inventario -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exchange-alt"></i> Movimientos de Inventario</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.inventario-bodega.show', $producto->id_inventario) }}" class="form-inline mb-3">
                            <select name="tipo_movimiento" class="form-control mr-2">
                                <option value="">Todos los tipos</option>
                                <option value="Entrada" {{ request('tipo_movimiento') == 'Entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="Salida" {{ request('tipo_movimiento') == 'Salida' ? 'selected' : '' }}>Salida</option>
                                <option value="Ajuste" {{ request('tipo_movimiento') == 'Ajuste' ? 'selected' : '' }}>Ajuste</option>
                            </select>
                            <input type="date" name="fecha_inicio" class="form-control mr-2" value="{{ request('fecha_inicio') }}" placeholder="Fecha inicio">
                            <input type="date" name="fecha_fin" class="form-control mr-2" value="{{ request('fecha_fin') }}" placeholder="Fecha fin">
                            <button type="submit" class="btn btn-info">Filtrar</button>
                            <a href="{{ route('admin.inventario-bodega.show', $producto->id_inventario) }}" class="btn btn-secondary ml-2">Limpiar</a>
                        </form>

                        @if($movimientos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Valor Total</th>
                                        <th>Motivo</th>
                                        <th>Personal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movimientos as $movimiento)
                                    <tr>
                                        <td>{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</td>
                                        <td>
                                            @if($movimiento->tipo_movimiento == 'Entrada')
                                                <span class="badge badge-success">Entrada</span>
                                            @elseif($movimiento->tipo_movimiento == 'Salida')
                                                <span class="badge badge-danger">Salida</span>
                                            @else
                                                <span class="badge badge-warning">Ajuste</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($movimiento->cantidad, 2) }} {{ $producto->unidad_medida }}</td>
                                        <td>${{ number_format($movimiento->precio_unitario, 2) }}</td>
                                        <td>${{ number_format($movimiento->valor_total, 2) }}</td>
                                        <td>{{ $movimiento->motivo ?? '-' }}</td>
                                        <td>{{ $movimiento->personal->nombre ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay movimientos registrados para este producto.
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Acciones Rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-success btn-block mb-2" data-toggle="modal" data-target="#modalEntrada">
                            <i class="fas fa-plus-circle"></i> Registrar Entrada
                        </button>
                        <button type="button" class="btn btn-danger btn-block mb-2" data-toggle="modal" data-target="#modalSalida">
                            <i class="fas fa-minus-circle"></i> Registrar Salida
                        </button>
                        <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#modalAjuste">
                            <i class="fas fa-adjust"></i> Registrar Ajuste
                        </button>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Estadísticas</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-exchange-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Movimientos</span>
                                <span class="info-box-number">{{ $movimientos->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.inventario-bodega.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Modal Entrada -->
<div class="modal fade" id="modalEntrada" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.inventario-bodega.entrada', $producto->id_inventario) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Registrar Entrada</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" step="0.01" name="cantidad" class="form-control" required min="0.01">
                    </div>
                    <div class="form-group">
                        <label>Precio Unitario (opcional)</label>
                        <input type="number" step="0.01" name="precio_unitario" class="form-control" min="0">
                    </div>
                    <div class="form-group">
                        <label>Fecha Movimiento</label>
                        <input type="date" name="fecha_movimiento" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Motivo</label>
                        <input type="text" name="motivo" class="form-control" placeholder="Ej: Compra, Donación">
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Registrar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Salida -->
<div class="modal fade" id="modalSalida" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.inventario-bodega.salida', $producto->id_inventario) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Registrar Salida</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" step="0.01" name="cantidad" class="form-control" required min="0.01" max="{{ $producto->stock_actual }}">
                        <small class="text-muted">Stock disponible: {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}</small>
                    </div>
                    <div class="form-group">
                        <label>Fecha Movimiento</label>
                        <input type="date" name="fecha_movimiento" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Motivo</label>
                        <input type="text" name="motivo" class="form-control" placeholder="Ej: Uso, Venta, Desperdicio">
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Registrar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajuste -->
<div class="modal fade" id="modalAjuste" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.inventario-bodega.ajuste', $producto->id_inventario) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Registrar Ajuste</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Stock Actual:</strong> {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}
                    </div>
                    <div class="form-group">
                        <label>Stock Nuevo</label>
                        <input type="number" step="0.01" name="stock_nuevo" class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Fecha Movimiento</label>
                        <input type="date" name="fecha_movimiento" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Motivo</label>
                        <input type="text" name="motivo" class="form-control" placeholder="Ej: Inventario físico, Corrección">
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Registrar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

