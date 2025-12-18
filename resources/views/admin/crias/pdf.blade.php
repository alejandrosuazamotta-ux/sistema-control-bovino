<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Crías</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #17A2B8;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #17A2B8;
            margin: 0;
        }
        .info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #17A2B8;
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
            color: #17A2B8;
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
        <h1><i class="fas fa-baby"></i> Reporte de Crías</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info">
        <p><strong>Período:</strong> 
            {{ $filters['fecha_inicio'] ?? 'Todos' }} 
            - 
            {{ $filters['fecha_fin'] ?? 'Todos' }}
        </p>
        @if($filters['sexo'] ?? null)
            <p><strong>Sexo:</strong> {{ $filters['sexo'] }}</p>
        @endif
        @if($filters['estado_destete'] ?? null)
            <p><strong>Estado Destete:</strong> {{ $filters['estado_destete'] }}</p>
        @endif
    </div>

    <div class="stats">
        <div class="stat-box">
            <h4>{{ $estadisticas['total_crias'] ?? 0 }}</h4>
            <p>Total Crías</p>
        </div>
        <div class="stat-box">
            <h4>{{ $estadisticas['crias_este_mes'] ?? 0 }}</h4>
            <p>Este Mes</p>
        </div>
        <div class="stat-box">
            <h4>{{ $estadisticas['crias_destetadas'] ?? 0 }}</h4>
            <p>Destetadas</p>
        </div>
        <div class="stat-box">
            <h4>{{ number_format($estadisticas['promedio_peso'] ?? 0, 2) }}</h4>
            <p>Promedio Peso (kg)</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Vaca Madre</th>
                <th>Nombre</th>
                <th>Sexo</th>
                <th>Fecha Nacimiento</th>
                <th>Peso (kg)</th>
                <th>Estado Destete</th>
                <th>Fecha Destete</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $cria)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $cria->vacaMadre->codigo ?? 'N/A' }}</td>
                    <td>{{ $cria->nombre_cria ?? 'N/A' }}</td>
                    <td>{{ $cria->sexo }}</td>
                    <td>{{ $cria->fecha_nacimiento->format('d/m/Y') }}</td>
                    <td>{{ $cria->peso ? number_format($cria->peso, 2) : 'N/A' }}</td>
                    <td>{{ $cria->estado_destete }}</td>
                    <td>{{ $cria->fecha_destete ? $cria->fecha_destete->format('d/m/Y') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No hay registros para el período seleccionado</td>
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

