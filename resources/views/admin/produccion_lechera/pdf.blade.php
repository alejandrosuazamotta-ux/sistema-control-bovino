<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Producción Lechera</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #28A745;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #28A745;
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
            background-color: #28A745;
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
            color: #28A745;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-milk"></i> Reporte de Producción Lechera</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info">
        <p><strong>Período:</strong> 
            {{ $filters['fecha_inicio'] ?? now()->startOfMonth()->format('d/m/Y') }} 
            - 
            {{ $filters['fecha_fin'] ?? now()->format('d/m/Y') }}
        </p>
        @if($filters['id_vaca'] ?? null)
            <p><strong>Vaca:</strong> {{ $filters['id_vaca'] }}</p>
        @endif
        @if($filters['turno'] ?? null)
            <p><strong>Turno:</strong> {{ $filters['turno'] }}</p>
        @endif
        @if($filters['destino'] ?? null)
            <p><strong>Destino:</strong> {{ $filters['destino'] }}</p>
        @endif
    </div>

    <div class="stats">
        <div class="stat-box">
            <h4>{{ number_format($estadisticas['total_produccion_mes'] ?? 0, 2) }}</h4>
            <p>Total Producción (L)</p>
        </div>
        <div class="stat-box">
            <h4>{{ number_format($estadisticas['promedio_diario'] ?? 0, 2) }}</h4>
            <p>Promedio Diario (L)</p>
        </div>
        <div class="stat-box">
            <h4>{{ $estadisticas['vacas_activas'] ?? 0 }}</h4>
            <p>Vacas en Lactancia</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Vaca</th>
                <th>Fecha</th>
                <th>Turno</th>
                <th>Cantidad (L)</th>
                <th>Destino</th>
                <th>Valor Unidad</th>
                <th>Valor Total</th>
                <th>Personal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $registro)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $registro->vaca->codigo ?? 'N/A' }}</td>
                    <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                    <td>{{ $registro->turno }}</td>
                    <td>{{ number_format($registro->cantidad_leche, 2) }}</td>
                    <td>{{ $registro->destino }}</td>
                    <td>{{ $registro->valor_unidad ? number_format($registro->valor_unidad, 2) : 'N/A' }}</td>
                    <td>{{ $registro->valor_total ? number_format($registro->valor_total, 2) : 'N/A' }}</td>
                    <td>{{ $registro->personal->nombre ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No hay registros para el período seleccionado</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión Ganadera - SystemPG1</p>
        <p>Página {PAGENO} de {nbpg}</p>
    </div>
</body>
</html>

