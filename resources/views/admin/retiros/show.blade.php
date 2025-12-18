@extends('layouts.master')

@section('title', 'Detalles de Retiro')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-eye"></i> Detalles de Retiro</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.retiros.index') }}">Retiros</a></li>
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
                            <a href="{{ route('admin.retiros.edit', $retiro->id_retiro) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Vaca:</strong></td>
                                <td>{{ $retiro->vaca->codigo }} - {{ $retiro->vaca->raza }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de Retiro:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $retiro->tipo_retiro == 'Ordeño' ? 'warning' : 'danger' }}">
                                        {{ $retiro->tipo_retiro }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Fecha Inicio:</strong></td>
                                <td>{{ $retiro->fecha_inicio->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha Fin:</strong></td>
                                <td>{{ $retiro->fecha_fin->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Duración:</strong></td>
                                <td>{{ $retiro->fecha_inicio->diffInDays($retiro->fecha_fin) }} días</td>
                            </tr>
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    @if($retiro->estaActivo())
                                        <span class="badge badge-danger">Activo</span>
                                        <br><small>Días restantes: {{ $retiro->diasRestantes() }}</small>
                                    @elseif($retiro->fecha_fin < now())
                                        <span class="badge badge-secondary">Finalizado</span>
                                    @else
                                        <span class="badge badge-warning">Programado</span>
                                    @endif
                                </td>
                            </tr>
                            @if($retiro->usoMedicamento)
                            <tr>
                                <td><strong>Uso de Medicamento:</strong></td>
                                <td>
                                    {{ $retiro->usoMedicamento->medicamento->nombre }}
                                    <a href="{{ route('admin.uso-medicamentos.show', $retiro->usoMedicamento->id_uso) }}" class="btn btn-sm btn-info ml-2">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if($retiro->observaciones)
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td>{{ $retiro->observaciones }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-{{ $retiro->estaActivo() ? 'danger' : 'secondary' }}">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Estado</h3>
                    </div>
                    <div class="card-body">
                        @if($retiro->estaActivo())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Retiro Activo</strong><br>
                                La vaca no puede registrar producción hasta el {{ $retiro->fecha_fin->format('d/m/Y') }}
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Retiro Finalizado</strong><br>
                                La vaca puede registrar producción normalmente
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.retiros.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.vacas.show', $retiro->vaca->id_vaca) }}" class="btn btn-info">
                    <i class="fas fa-cow"></i> Ver Vaca
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

