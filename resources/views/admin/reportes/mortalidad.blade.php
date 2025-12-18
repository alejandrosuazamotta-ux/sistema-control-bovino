@extends('layouts.master')

@section('title', 'Reporte de Mortalidad')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-skull"></i> Reporte de Mortalidad
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Mortalidad</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filtros -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Filtros
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes.mortalidad') }}" class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
                                   value="{{ $fechaInicio }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
                                   value="{{ $fechaFin }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                @can('export', \App\Models\Reporte::class)
                                <a href="{{ route('admin.reportes.mortalidad.export.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </a>
                                <a href="{{ route('admin.reportes.mortalidad.export.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" 
                                   class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resumen -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-skull"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Muertes</span>
                        <span class="info-box-number">{{ $datos['total_muertes'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> Muertes por Tipo de Animal
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="muertesPorTipoChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> Muertes por Clasificación
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="muertesPorClasificacionChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Muertes por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="muertesPorMesChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de causas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Top 10 Causas de Muerte
                </h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Causa</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datos['muertes_por_causa'] ?? [] as $causa)
                        <tr>
                            <td>{{ $causa->causa ?? 'N/A' }}</td>
                            <td>{{ $causa->total ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">No hay datos disponibles</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Muertes por Tipo
    @if(isset($datos['graficas']['muertes_por_tipo']))
    new Chart(document.getElementById('muertesPorTipoChart'), {
        type: 'pie',
        data: {
            labels: @json($datos['graficas']['muertes_por_tipo']['labels'] ?? []),
            datasets: [{
                data: @json($datos['graficas']['muertes_por_tipo']['data'] ?? []),
                backgroundColor: ['#dc3545', '#ffc107']
            }]
        }
    });
    @endif

    // Muertes por Clasificación
    @if(isset($datos['graficas']['muertes_por_clasificacion']))
    new Chart(document.getElementById('muertesPorClasificacionChart'), {
        type: 'pie',
        data: {
            labels: @json($datos['graficas']['muertes_por_clasificacion']['labels'] ?? []),
            datasets: [{
                data: @json($datos['graficas']['muertes_por_clasificacion']['data'] ?? []),
                backgroundColor: ['#dc3545', '#fd7e14', '#ffc107']
            }]
        }
    });
    @endif

    // Muertes por Mes
    @if(isset($datos['graficas']['muertes_por_mes']))
    new Chart(document.getElementById('muertesPorMesChart'), {
        type: 'bar',
        data: {
            labels: @json($datos['graficas']['muertes_por_mes']['labels'] ?? []),
            datasets: [{
                label: 'Muertes',
                data: @json($datos['graficas']['muertes_por_mes']['data'] ?? []),
                backgroundColor: '#dc3545'
            }]
        }
    });
    @endif
</script>
@endpush
@endsection

