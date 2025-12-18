@extends('layouts.master')

@section('title', 'Inventario de Bodega')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-warehouse"></i> Inventario de Bodega
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Inventario Bodega</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['total_productos'] ?? 0) }}</h3>
                        <p>Total Productos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['stock_bajo'] ?? 0) }}</h3>
                        <p>Stock Bajo</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['proximos_vencer'] ?? 0) }}</h3>
                        <p>Próximos a Vencer</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>${{ number_format($estadisticas['valor_total_stock'] ?? 0, 2) }}</h3>
                        <p>Valor Total Stock</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Stock por Tipo -->
            @if(isset($datosGraficas['stock_por_tipo']) && count($datosGraficas['stock_por_tipo']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Stock por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartStockPorTipo" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Movimientos por Mes -->
            @if(isset($datosGraficas['movimientos_por_mes']) && count($datosGraficas['movimientos_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Movimientos por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartMovimientosPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Próximos a Vencer -->
            @if(isset($datosGraficas['proximos_vencer']) && count($datosGraficas['proximos_vencer']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Próximos a Vencer
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartProximosVencer" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Card principal con tabla -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Lista de Productos
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventario-bodega.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </a>
                    <a href="{{ route('admin.inventario-bodega.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.inventario-bodega.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.inventario-bodega.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.inventario-bodega.index') }}" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar código, nombre o proveedor..." value="{{ request('search') }}">
                    <select name="tipo_producto" class="form-control mr-2">
                        <option value="">Todos los tipos</option>
                        <option value="Medicamento" {{ request('tipo_producto') == 'Medicamento' ? 'selected' : '' }}>Medicamento</option>
                        <option value="Insumo" {{ request('tipo_producto') == 'Insumo' ? 'selected' : '' }}>Insumo</option>
                        <option value="Alimento" {{ request('tipo_producto') == 'Alimento' ? 'selected' : '' }}>Alimento</option>
                        <option value="Equipo" {{ request('tipo_producto') == 'Equipo' ? 'selected' : '' }}>Equipo</option>
                        <option value="Otro" {{ request('tipo_producto') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    <div class="form-check mr-2">
                        <input type="checkbox" name="stock_bajo" class="form-check-input" id="stock_bajo" value="1" {{ request('stock_bajo') ? 'checked' : '' }}>
                        <label class="form-check-label" for="stock_bajo">Stock Bajo</label>
                    </div>
                    <div class="form-check mr-2">
                        <input type="checkbox" name="proximos_vencer" class="form-check-input" id="proximos_vencer" value="1" {{ request('proximos_vencer') ? 'checked' : '' }}>
                        <label class="form-check-label" for="proximos_vencer">Próximos a Vencer</label>
                    </div>
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('admin.inventario-bodega.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                </form>

                @if($productos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Precio Unit.</th>
                                    <th>Valor Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $producto)
                                    <tr class="{{ $producto->tieneStockBajo() ? 'table-warning' : '' }}">
                                        <td>{{ $productos->firstItem() + $loop->index }}</td>
                                        <td><strong>{{ $producto->codigo }}</strong></td>
                                        <td>{{ $producto->nombre }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $producto->tipo_producto }}</span>
                                        </td>
                                        <td>
                                            <span class="{{ $producto->tieneStockBajo() ? 'text-danger font-weight-bold' : '' }}">
                                                {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($producto->stock_minimo, 2) }} {{ $producto->unidad_medida }}</td>
                                        <td>${{ number_format($producto->precio_unitario, 2) }}</td>
                                        <td><strong>${{ number_format($producto->valor_total_stock, 2) }}</strong></td>
                                        <td>
                                            @if($producto->activo)
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-secondary">Inactivo</span>
                                            @endif
                                            @if($producto->estaProximoAVencer())
                                                <span class="badge badge-warning">Próximo a vencer</span>
                                            @endif
                                            @if($producto->estaVencido())
                                                <span class="badge badge-danger">Vencido</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.inventario-bodega.show', $producto->id_inventario) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.inventario-bodega.edit', $producto->id_inventario) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $producto->id_inventario }})" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $productos->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay productos en inventario para mostrar con los filtros seleccionados.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para las gráficas
    const datosStockPorTipo = @json($datosGraficas['stock_por_tipo'] ?? ['labels' => [], 'data' => []]);
    const datosMovimientosPorMes = @json($datosGraficas['movimientos_por_mes'] ?? ['labels' => [], 'entradas' => [], 'salidas' => []]);
    const datosProximosVencer = @json($datosGraficas['proximos_vencer'] ?? ['labels' => [], 'dias' => []]);

    // Gráfica: Stock por Tipo (Donut)
    if (datosStockPorTipo.labels.length > 0) {
        const chartStockPorTipo = new ApexCharts(document.querySelector("#chartStockPorTipo"), {
            series: datosStockPorTipo.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosStockPorTipo.labels,
            colors: ['#17A2B8', '#28A745', '#FFC107', '#DC3545', '#6C757D'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' unidades' } }
            }
        });
        chartStockPorTipo.render();
    }

    // Gráfica: Movimientos por Mes (Línea)
    if (datosMovimientosPorMes.labels.length > 0) {
        const chartMovimientosPorMes = new ApexCharts(document.querySelector("#chartMovimientosPorMes"), {
            series: [
                {
                    name: 'Entradas',
                    data: datosMovimientosPorMes.entradas
                },
                {
                    name: 'Salidas',
                    data: datosMovimientosPorMes.salidas
                }
            ],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: true },
                zoom: { enabled: false }
            },
            colors: ['#28A745', '#DC3545'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: datosMovimientosPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val.toFixed(2) + ' unidades' } }
            },
            legend: {
                position: 'top'
            }
        });
        chartMovimientosPorMes.render();
    }

    // Gráfica: Próximos a Vencer (Barras)
    if (datosProximosVencer.labels.length > 0) {
        const chartProximosVencer = new ApexCharts(document.querySelector("#chartProximosVencer"), {
            series: [{
                name: 'Días hasta vencimiento',
                data: datosProximosVencer.dias
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: true }
            },
            colors: ['#FFC107'],
            xaxis: {
                categories: datosProximosVencer.labels
            },
            yaxis: {
                title: { text: 'Días' }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true
                }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' días' } }
            }
        });
        chartProximosVencer.render();
    }
});

function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').action = '{{ route("admin.inventario-bodega.index") }}/' + id;
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endpush
@endsection

