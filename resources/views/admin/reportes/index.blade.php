@extends('layouts.master')

@section('title', 'Reportes')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-chart-bar"></i> Reportes
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Reporte de Producción -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-glass-water"></i> Reporte de Producción
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Análisis completo de producción lechera con gráficas y estadísticas detalladas.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Producción diaria y mensual</li>
                            <li><i class="fas fa-check text-success"></i> Producción por turno y destino</li>
                            <li><i class="fas fa-check text-success"></i> Top vacas productoras</li>
                            <li><i class="fas fa-check text-success"></i> Gráficas ApexCharts</li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.reportes.produccion') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-eye"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte Reproductivo -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-heart"></i> Reporte Reproductivo
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Análisis de eventos reproductivos, partos, inseminaciones y tasa de preñez.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Eventos por tipo</li>
                            <li><i class="fas fa-check text-success"></i> Palpaciones y resultados</li>
                            <li><i class="fas fa-check text-success"></i> Partos por mes</li>
                            <li><i class="fas fa-check text-success"></i> Tasa de preñez</li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.reportes.reproductivo') }}" class="btn btn-success btn-block">
                            <i class="fas fa-eye"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte Sanitario -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-virus"></i> Reporte Sanitario
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Análisis de registros sanitarios, pruebas y restricciones de ordeño.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Registros por tipo</li>
                            <li><i class="fas fa-check text-success"></i> Pruebas sanitarias</li>
                            <li><i class="fas fa-check text-success"></i> Mastitis por severidad</li>
                            <li><i class="fas fa-check text-success"></i> Vacas con restricción</li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.reportes.sanitario') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-eye"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte de Mortalidad -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-skull"></i> Reporte de Mortalidad
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Análisis de mortalidad por tipo, clasificación y causa.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Muertes por tipo de animal</li>
                            <li><i class="fas fa-check text-success"></i> Muertes por clasificación</li>
                            <li><i class="fas fa-check text-success"></i> Muertes por causa</li>
                            <li><i class="fas fa-check text-success"></i> Tendencias mensuales</li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.reportes.mortalidad') }}" class="btn btn-danger btn-block">
                            <i class="fas fa-eye"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte de Medicamentos -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-pills"></i> Reporte de Medicamentos
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Análisis de uso de medicamentos, stock y vencimientos.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Usos por medicamento</li>
                            <li><i class="fas fa-check text-success"></i> Usos por tipo</li>
                            <li><i class="fas fa-check text-success"></i> Stock bajo</li>
                            <li><i class="fas fa-check text-success"></i> Próximos a vencer</li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.reportes.medicamentos') }}" class="btn btn-info btn-block">
                            <i class="fas fa-eye"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

