@extends('layouts.master')

@section('title', 'Detalles de Medicamento')

@section('content')
@php
    // Protección: Esta vista no debería ser accesible para Pasante
    abort(404, 'Esta página no está disponible para tu rol.');
@endphp
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-eye"></i> Detalles de Medicamento</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pasante.medicamentos.index') }}">Medicamentos</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Nombre:</strong></td>
                                <td>{{ $medicamento->nombre }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo:</strong></td>
                                <td>{{ $medicamento->tipo ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Principio Activo:</strong></td>
                                <td>{{ $medicamento->principio_activo ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Vía de Administración:</strong></td>
                                <td>{{ $medicamento->via_administracion ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Dosis:</strong></td>
                                <td>{{ $medicamento->dosis ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Período de Retiro:</strong></td>
                                <td>
                                    @if($medicamento->periodo_retiro_dias > 0)
                                        <span class="badge badge-warning">{{ $medicamento->periodo_retiro_dias }} días</span>
                                    @else
                                        <span class="badge badge-success">Sin retiro</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    @if($medicamento->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-secondary">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                            @if($medicamento->observaciones)
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td>{{ $medicamento->observaciones }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Estadísticas</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-syringe"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Usos Registrados</span>
                                <span class="info-box-number">{{ $medicamento->usos_medicamentos_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="{{ route('pasante.medicamentos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

