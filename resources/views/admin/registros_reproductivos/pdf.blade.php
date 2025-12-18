<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Registros Reproductivos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #FFC107;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #FFC107;
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
            background-color: #FFC107;
            color: #000;
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
            color: #FFC107;
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
        <h1><i class="fas fa-heart"></i> Reporte de Registros Reproductivos</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info">
        <p><strong>Período:</strong> 
            {{ $filters['fecha_inicio'] ?? now()->startOfMonth()->format('d/m/Y') }} 
            - 
            {{ $filters['fecha_fin'] ?? now()->format('d/m/Y') }}
        </p>
        @if($filters['tipo_evento'] ?? null)
            <p><strong>Tipo Evento:</strong> {{ $filters['tipo_evento'] }}</p>
        @endif
        @if($filters['id_vaca'] ?? null)
            <p><strong>Vaca:</strong> {{ $filters['id_vaca'] }}</p>
        @endif
    </div>

    <div class="stats">
        <div class="stat-box">
            <h4>{{ $estadisticas['total_registros'] ?? 0 }}</h4>
            <p>Total Registros</p>
        </div>
        <div class="stat-box">
            <h4>{{ $estadisticas['palpaciones_preñadas'] ?? 0 }}</h4>
            <p>Palpaciones Preñadas</p>
        </div>
        <div class="stat-box">
            <h4>{{ $estadisticas['vacas_proximas_parto'] ?? 0 }}</h4>
            <p>Próximas al Parto</p>
        </div>
        <div class="stat-box">
            <h4>{{ number_format($estadisticas['promedio_dias_abiertos'] ?? 0, 0) }}</h4>
            <p>Promedio Días Abiertos</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Vaca</th>
                <th>Tipo Evento</th>
                <th>Fecha Evento</th>
                <th>Resultado</th>
                <th>Fecha Probable Parto</th>
                <th>Días Abiertos</th>
                <th>Personal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $registro)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $registro->vaca->codigo ?? 'N/A' }}</td>
                    <td>{{ $registro->tipo_evento }}</td>
                    <td>{{ $registro->fecha_evento->format('d/m/Y') }}</td>
                    <td>{{ $registro->resultado_palpacion ?? 'N/A' }}</td>
                    <td>{{ $registro->fecha_probable_parto ? $registro->fecha_probable_parto->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $registro->dias_abiertos ?? 'N/A' }}</td>
                    <td>{{ $registro->personal->nombre ?? 'N/A' }}</td>
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

