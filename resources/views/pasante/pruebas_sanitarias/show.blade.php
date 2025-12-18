@extends('layouts.master')

@section('title', 'Detalles de Prueba Sanitaria')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-eye"></i> Detalles de Prueba Sanitaria
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pasante.pruebas-sanitarias.index') }}">Pruebas Sanitarias</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-vial"></i> Información de la Prueba
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong><i class="fas fa-cow"></i> Vaca:</strong></td>
                                <td>{{ $prueba->vaca->codigo ?? 'N/A' }} - {{ $prueba->vaca->raza ?? 'Sin raza' }}</td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-flask"></i> Tipo de Prueba:</strong></td>
                                <td>
                                    @if($prueba->tipo_prueba === 'Mastitis')
                                        <span class="badge badge-warning"><i class="fas fa-vial"></i> {{ $prueba->tipo_prueba }}</span>
                                    @elseif($prueba->tipo_prueba === 'Brucelosis')
                                        <span class="badge badge-danger"><i class="fas fa-biohazard"></i> {{ $prueba->tipo_prueba }}</span>
                                    @elseif($prueba->tipo_prueba === 'Tuberculosis')
                                        <span class="badge badge-danger"><i class="fas fa-lungs"></i> {{ $prueba->tipo_prueba }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-calendar-alt"></i> Fecha de Prueba:</strong></td>
                                <td>{{ $prueba->fecha_prueba->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-check-circle"></i> Resultado:</strong></td>
                                <td>
                                    @if($prueba->resultado === 'Positivo')
                                        <span class="badge badge-danger">{{ $prueba->resultado }}</span>
                                    @elseif($prueba->resultado === 'Negativo')
                                        <span class="badge badge-success">{{ $prueba->resultado }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $prueba->resultado }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($prueba->fecha_resultado)
                            <tr>
                                <td><strong><i class="fas fa-calendar-check"></i> Fecha Resultado:</strong></td>
                                <td>{{ $prueba->fecha_resultado->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            @if($prueba->severidad)
                            <tr>
                                <td><strong><i class="fas fa-exclamation-triangle"></i> Severidad:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $prueba->severidad === 'Severa' ? 'danger' : ($prueba->severidad === 'Moderada' ? 'warning' : 'info') }}">
                                        {{ $prueba->severidad }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong><i class="fas fa-ban"></i> Restricción Ordeño:</strong></td>
                                <td>
                                    @if($prueba->restriccion_ordeño)
                                        <span class="badge badge-danger"><i class="fas fa-ban"></i> Sí</span>
                                    @else
                                        <span class="badge badge-success">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-times-circle"></i> Inhabilitada:</strong></td>
                                <td>
                                    @if($prueba->inhabilitada)
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Sí</span>
                                    @else
                                        <span class="badge badge-success">No</span>
                                    @endif
                                </td>
                            </tr>
                            @if($prueba->acta)
                            <tr>
                                <td><strong><i class="fas fa-file-alt"></i> Número de Acta:</strong></td>
                                <td>{{ $prueba->acta }}</td>
                            </tr>
                            @endif
                            @if($prueba->responsable_prueba)
                            <tr>
                                <td><strong><i class="fas fa-user-md"></i> Responsable:</strong></td>
                                <td>{{ $prueba->responsable_prueba }}</td>
                            </tr>
                            @endif
                            @if($prueba->tratamiento_sugerido)
                            <tr>
                                <td><strong><i class="fas fa-pills"></i> Tratamiento Sugerido:</strong></td>
                                <td>{{ $prueba->tratamiento_sugerido }}</td>
                            </tr>
                            @endif
                            @if($prueba->observaciones)
                            <tr>
                                <td><strong><i class="fas fa-sticky-note"></i> Observaciones:</strong></td>
                                <td>{{ $prueba->observaciones }}</td>
                            </tr>
                            @endif
                            @if($prueba->personal)
                            <tr>
                                <td><strong><i class="fas fa-user"></i> Personal:</strong></td>
                                <td>{{ $prueba->personal->nombre }}</td>
                            </tr>
                            @endif
                            @if($prueba->evidencia_path)
                            <tr>
                                <td><strong><i class="fas fa-file"></i> Evidencia:</strong></td>
                                <td>
                                    <a href="{{ asset('storage/' . $prueba->evidencia_path) }}" 
                                       class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-download"></i> Ver Evidencia
                                    </a>
                                    @if(str_ends_with(strtolower($prueba->evidencia_path), '.pdf'))
                                        <span class="badge badge-danger">PDF</span>
                                    @else
                                        <span class="badge badge-info">Imagen</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong><i class="fas fa-clock"></i> Fecha Creación:</strong></td>
                                <td>{{ $prueba->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-edit"></i> Última Actualización:</strong></td>
                                <td>{{ $prueba->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información de la Vaca
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($prueba->vaca)
                            <p><strong>Código:</strong> {{ $prueba->vaca->codigo }}</p>
                            <p><strong>Raza:</strong> {{ $prueba->vaca->raza ?? 'N/A' }}</p>
                            <p><strong>Estado Salud:</strong> 
                                <span class="badge badge-{{ $prueba->vaca->estado_salud === 'Sana' ? 'success' : 'danger' }}">
                                    {{ $prueba->vaca->estado_salud }}
                                </span>
                            </p>
                            <p><strong>Estado Reproductivo:</strong> 
                                <span class="badge badge-info">{{ $prueba->vaca->estado_reproductivo }}</span>
                            </p>
                        @else
                            <p class="text-muted">Vaca no encontrada</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <a href="{{ route('pasante.pruebas-sanitarias.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

