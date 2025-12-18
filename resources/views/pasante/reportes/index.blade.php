@extends('layouts.master')

@section('title', 'Reportes')

@section('content')
@php
    // Protección: Los reportes para Pasante no están implementados
    header('Location: ' . route('pasante.dashboard'));
    exit;
@endphp
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
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Nota:</strong> Como pasante, puedes visualizar todos los reportes pero no puedes exportarlos.
        </div>

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
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('pasante.reportes.produccion') }}" class="btn btn-primary btn-block">
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
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('pasante.reportes.reproductivo') }}" class="btn btn-success btn-block">
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
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('pasante.reportes.sanitario') }}" class="btn btn-warning btn-block">
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
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('pasante.reportes.mortalidad') }}" class="btn btn-danger btn-block">
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
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('pasante.reportes.medicamentos') }}" class="btn btn-info btn-block">
                            <i class="fas fa-eye"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

