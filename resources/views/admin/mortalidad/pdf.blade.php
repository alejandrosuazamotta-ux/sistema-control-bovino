<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Mortalidad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #DC3545;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #DC3545;
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
            background-color: #DC3545;
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
            color: #DC3545;
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
        <h1><i class="fas fa-skull"></i> Reporte de Mortalidad</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info">
        <p><strong>Período:</strong> 
            {{ $filters['fecha_inicio'] ?? now()->startOfMonth()->format('d/m/Y') }} 
            - 
            {{ $filters['fecha_fin'] ?? now()->format('d/m/Y') }}
        </p>
        @if($filters['animal_type'] ?? null)
            <p><strong>Tipo Animal:</strong> {{ $filters['animal_type'] }}</p>
        @endif
        @if($filters['clasificacion'] ?? null)
            <p><strong>Clasificación:</strong> {{ $filters['clasificacion'] }}</p>
        @endif
    </div>

    <div class="stats">
        <div class="stat-box">
            <h4>{{ $estadisticas['total_mortalidad'] ?? 0 }}</h4>
            <p>Total Muertes</p>
        </div>
        <div class="stat-box">
            <h4>{{ $estadisticas['mortalidad_este_mes'] ?? 0 }}</h4>
            <p>Este Mes</p>
        </div>
        <div class="stat-box">
            <h4>{{ $estadisticas['mortalidad_ultimos_30_dias'] ?? 0 }}</h4>
            <p>Últimos 30 Días</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tipo</th>
                <th>Código/Nombre</th>
                <th>Fecha</th>
                <th>Clasificación</th>
                <th>Peso (kg)</th>
                <th>Causa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $mortalidad)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mortalidad->esVaca() ? 'Vaca' : 'Cría' }}</td>
                    <td>
                        @if($mortalidad->animal)
                            @if($mortalidad->esVaca())
                                {{ $mortalidad->animal->codigo ?? 'N/A' }}
                            @else
                                {{ $mortalidad->animal->nombre_cria ?? $mortalidad->animal->sinigan ?? 'N/A' }}
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $mortalidad->fecha->format('d/m/Y') }}</td>
                    <td>{{ $mortalidad->clasificacion }}</td>
                    <td>{{ $mortalidad->peso ? number_format($mortalidad->peso, 2) : 'N/A' }}</td>
                    <td>{{ $mortalidad->causa }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No hay registros para el período seleccionado</td>
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

