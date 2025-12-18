<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario de Bodega</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1F713E;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1F713E;
            margin: 0;
        }
        .info {
            margin-bottom: 15px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #1F713E;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .stats {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
        }
        .stat-box {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .stat-box h4 {
            margin: 0;
            color: #1F713E;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-warning {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-warehouse"></i> Reporte de Inventario de Bodega</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info">
        @if(isset($filters) && count(array_filter($filters)) > 0)
            <p><strong>Filtros aplicados:</strong></p>
            @if(isset($filters['search']) && $filters['search'])
                <p>Búsqueda: {{ $filters['search'] }}</p>
            @endif
            @if(isset($filters['tipo_producto']) && $filters['tipo_producto'])
                <p>Tipo de Producto: {{ $filters['tipo_producto'] }}</p>
            @endif
            @if(isset($filters['stock_bajo']) && $filters['stock_bajo'])
                <p>Filtro: Stock Bajo</p>
            @endif
            @if(isset($filters['proximos_vencer']) && $filters['proximos_vencer'])
                <p>Filtro: Próximos a Vencer</p>
            @endif
            @if(isset($filters['vencidos']) && $filters['vencidos'])
                <p>Filtro: Vencidos</p>
            @endif
        @else
            <p><strong>Período:</strong> Todos los productos</p>
        @endif
    </div>

    @if(isset($estadisticas))
    <div class="stats">
        <div class="stat-box">
            <h4>{{ number_format($estadisticas['total_productos'] ?? 0) }}</h4>
            <p>Total Productos</p>
        </div>
        <div class="stat-box">
            <h4>{{ number_format($estadisticas['stock_bajo'] ?? 0) }}</h4>
            <p>Stock Bajo</p>
        </div>
        <div class="stat-box">
            <h4>{{ number_format($estadisticas['proximos_vencer'] ?? 0) }}</h4>
            <p>Próximos a Vencer</p>
        </div>
        <div class="stat-box">
            <h4>${{ number_format($estadisticas['valor_total_stock'] ?? 0, 2) }}</h4>
            <p>Valor Total Stock</p>
        </div>
    </div>
    @endif

    @if($productos->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Precio Unit.</th>
                <th>Valor Total</th>
                <th>Proveedor</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto->codigo }}</td>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->tipo_producto }}</td>
                <td>{{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}</td>
                <td>{{ number_format($producto->stock_minimo, 2) }} {{ $producto->unidad_medida }}</td>
                <td>${{ number_format($producto->precio_unitario, 2) }}</td>
                <td><strong>${{ number_format($producto->valor_total_stock, 2) }}</strong></td>
                <td>{{ $producto->proveedor ?? 'N/A' }}</td>
                <td>
                    @if($producto->activo)
                        Activo
                    @else
                        Inactivo
                    @endif
                    @if($producto->tieneStockBajo())
                        | Stock Bajo
                    @endif
                    @if($producto->estaVencido())
                        | Vencido
                    @elseif($producto->estaProximoAVencer())
                        | Próximo a vencer
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 20px;">
        <p>No hay productos en inventario para mostrar con los filtros seleccionados.</p>
    </div>
    @endif

    <div class="footer">
        <p>Sistema de Producción Ganadera (S.P.G) - Reporte generado automáticamente</p>
    </div>
</body>
</html>

