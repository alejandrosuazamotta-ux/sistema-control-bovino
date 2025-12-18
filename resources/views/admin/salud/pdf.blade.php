<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Salud y Pruebas Sanitarias</title>
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
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Salud y Pruebas Sanitarias</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info">
        @if(isset($filters) && count(array_filter($filters)) > 0)
            <p><strong>Filtros aplicados:</strong></p>
            <ul>
                @if(!empty($filters['search']))
                    <li>Búsqueda: {{ $filters['search'] }}</li>
                @endif
                @if(!empty($filters['tipo_registro']))
                    <li>Tipo de Registro: {{ $filters['tipo_registro'] }}</li>
                @endif
                @if(!empty($filters['tipo_prueba']))
                    <li>Tipo de Prueba: {{ $filters['tipo_prueba'] }}</li>
                @endif
                @if(!empty($filters['resultado']))
                    <li>Resultado: {{ $filters['resultado'] }}</li>
                @endif
                @if(!empty($filters['fecha_inicio']))
                    <li>Fecha Inicio: {{ $filters['fecha_inicio'] }}</li>
                @endif
                @if(!empty($filters['fecha_fin']))
                    <li>Fecha Fin: {{ $filters['fecha_fin'] }}</li>
                @endif
            </ul>
        @endif
    </div>

    @if(isset($estadisticas))
    <div class="stats">
        <div class="stat-box">
            <strong>Total Registros:</strong><br>
            {{ $estadisticas['total_registros'] ?? 0 }}
        </div>
        <div class="stat-box">
            <strong>Resultados Positivos:</strong><br>
            {{ $estadisticas['resultados_positivos'] ?? 0 }}
        </div>
        <div class="stat-box">
            <strong>Con Restricción:</strong><br>
            {{ $estadisticas['vacas_con_restriccion'] ?? 0 }}
        </div>
        <div class="stat-box">
            <strong>Inhabilitadas:</strong><br>
            {{ $estadisticas['vacas_inhabilitadas'] ?? 0 }}
        </div>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Vaca</th>
                <th>Tipo Registro</th>
                <th>Tipo Prueba</th>
                <th>Fecha</th>
                <th>Resultado</th>
                <th>Severidad</th>
                <th>Restricción</th>
                <th>Inhabilitada</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $registro)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $registro->vaca->codigo ?? 'N/A' }}</td>
                    <td>{{ $registro->tipo_registro }}</td>
                    <td>{{ $registro->tipo_prueba ?? 'N/A' }}</td>
                    <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                    <td>{{ $registro->resultado ?? 'N/A' }}</td>
                    <td>{{ $registro->severidad ?? 'N/A' }}</td>
                    <td>{{ $registro->restriccion_ordeño ? 'Sí' : 'No' }}</td>
                    <td>{{ $registro->inhabilitada ? 'Sí' : 'No' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No hay registros para mostrar</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

