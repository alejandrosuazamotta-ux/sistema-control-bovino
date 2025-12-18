@extends('layouts.master')

@section('title', 'Detalles de Uso de Medicamento')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-eye"></i> Detalles de Uso</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.uso-medicamentos.index') }}">Uso de Medicamentos</a></li>
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
                        <div class="card-tools">
                            <a href="{{ route('admin.uso-medicamentos.edit', $uso->id_uso) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Medicamento:</strong></td>
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
                        <a href="{{ route('admin.retiros.show', $uso->retiro->id_retiro) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Ver Retiro
                        </a>
                    </div>
                </div>
                @elseif($uso->medicamento->periodo_retiro_dias > 0)
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información</h3>
                    </div>
                    <div class="card-body">
                        <p>Este medicamento requiere {{ $uso->medicamento->periodo_retiro_dias }} días de retiro, pero no se ha generado un retiro automático.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.uso-medicamentos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

