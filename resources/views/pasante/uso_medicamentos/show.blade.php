@extends('layouts.master')

@section('title', 'Detalles de Uso de Medicamento')

@section('content')
@php
    // Protección: Esta vista no debería ser accesible para Pasante
    abort(404, 'Esta página no está disponible para tu rol.');
@endphp
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-eye"></i> Detalles de Uso</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pasante.uso-medicamentos.index') }}">Uso de Medicamentos</a></li>
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
                                <td width="30%"><strong>Medicamento:</strong></td>
                                <td>{{ $uso->medicamento->nombre }}</td>
                            </tr>
                            <tr>
                                <td><strong>Vaca:</strong></td>
                                <td>{{ $uso->vaca->codigo }} - {{ $uso->vaca->raza }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha de Aplicación:</strong></td>
                                <td>{{ $uso->fecha_aplicacion->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Dosis Aplicada:</strong></td>
                                <td>
                                    @if($uso->dosis_aplicada)
                                        {{ number_format($uso->dosis_aplicada, 2) }} {{ $uso->unidad_dosis ?? '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Personal:</strong></td>
                                <td>{{ $uso->personal->nombre ?? '-' }}</td>
                            </tr>
                            @if($uso->observaciones)
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td>{{ $uso->observaciones }}</td>
                            </tr>
                            @endif
                            @if($uso->evidencia_path)
                            <tr>
                                <td><strong>Evidencia:</strong></td>
                                <td>
                                    <a href="{{ asset('storage/' . $uso->evidencia_path) }}" 
                                       class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-download"></i> Ver Evidencia
                                    </a>
                                    @if(str_ends_with(strtolower($uso->evidencia_path), '.pdf'))
                                        <span class="badge badge-danger">PDF</span>
                                    @else
                                        <span class="badge badge-info">Imagen</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Registrado por:</strong></td>
                                <td>{{ $uso->usuario->name ?? 'Sistema' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                @if($uso->retiro)
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="card-title"><i class="fas fa-ban"></i> Retiro Asociado</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Tipo:</strong> {{ $uso->retiro->tipo_retiro }}</p>
                        <p><strong>Fecha Inicio:</strong> {{ $uso->retiro->fecha_inicio->format('d/m/Y') }}</p>
                        <p><strong>Fecha Fin:</strong> {{ $uso->retiro->fecha_fin->format('d/m/Y') }}</p>
                        <p><strong>Estado:</strong> 
                            @if($uso->retiro->estaActivo())
                                <span class="badge badge-danger">Activo</span>
                                <br><small>Días restantes: {{ $uso->retiro->diasRestantes() }}</small>
                            @else
                                <span class="badge badge-secondary">Finalizado</span>
                            @endif
                        </p>
                    </div>
                </div>
                @elseif($uso->medicamento->periodo_retiro_dias > 0)
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información</h3>
                    </div>
                    <div class="card-body">
                        <p>Este medicamento requiere un período de retiro de <strong>{{ $uso->medicamento->periodo_retiro_dias }} días</strong>, pero no se ha generado un retiro automático.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="{{ route('pasante.uso-medicamentos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

