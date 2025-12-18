@extends('layouts.master')

@section('title', 'Detalles de Registro Reproductivo')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-eye"></i> Detalles de Registro Reproductivo</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.registros-reproductivos.index') }}">Registros Reproductivos</a></li>
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
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información del Evento</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.registros-reproductivos.edit', $registro->id_registro) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Vaca:</strong></td>
                                <td>{{ $registro->vaca->codigo }} - {{ $registro->vaca->raza }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de Evento:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $registro->tipo_evento == 'Parto' ? 'success' : ($registro->tipo_evento == 'Palpación' ? 'info' : 'warning') }}">
                                        {{ $registro->tipo_evento }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Fecha del Evento:</strong></td>
                                <td>{{ $registro->fecha_evento->format('d/m/Y') }}</td>
                            </tr>
                            @if($registro->tipo_evento === 'Palpación')
                            <tr>
                                <td><strong>Resultado de Palpación:</strong></td>
                                <td>
                                    @if($registro->resultado_palpacion)
                                        <span class="badge badge-{{ $registro->resultado_palpacion == 'Preñada' ? 'success' : 'secondary' }}">
                                            {{ $registro->resultado_palpacion }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @if($registro->tiempo_gestacion_dias)
                            <tr>
                                <td><strong>Tiempo de Gestación:</strong></td>
                                <td>{{ $registro->tiempo_gestacion_dias }} días</td>
                            </tr>
                            @endif
                            @if($registro->fecha_probable_parto)
                            <tr>
                                <td><strong>Fecha Probable de Parto:</strong></td>
                                <td>
                                    {{ $registro->fecha_probable_parto->format('d/m/Y') }}
                                    @if($registro->estaProximoAlParto())
                                        <span class="badge badge-warning ml-2">Próximo ({{ now()->diffInDays($registro->fecha_probable_parto) }} días)</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @if($registro->especialista)
                            <tr>
                                <td><strong>Especialista:</strong></td>
                                <td>{{ $registro->especialista }}</td>
                            </tr>
                            @endif
                            @endif
                            @if($registro->dias_abiertos)
                            <tr>
                                <td><strong>Días Abiertos:</strong></td>
                                <td>{{ $registro->dias_abiertos }} días</td>
                            </tr>
                            @endif
                            @if($registro->personal)
                            <tr>
                                <td><strong>Personal Responsable:</strong></td>
                                <td>{{ $registro->personal->nombre }} - {{ $registro->personal->rol }}</td>
                            </tr>
                            @endif
                            @if($registro->observaciones)
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td>{{ $registro->observaciones }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                @if($registro->fecha_probable_parto && $registro->estaProximoAlParto())
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Alerta</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Vaca próxima al parto</strong></p>
                        <p>Fecha probable: {{ $registro->fecha_probable_parto->format('d/m/Y') }}</p>
                        <p>Días restantes: {{ now()->diffInDays($registro->fecha_probable_parto) }}</p>
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history"></i> Historial Reproductivo</h3>
                    </div>
                    <div class="card-body">
                        @if($historial && $historial->count() > 0)
                            <ul class="list-unstyled">
                                @foreach($historial->take(5) as $hist)
                                    <li class="mb-2">
                                        <strong>{{ $hist->fecha_evento->format('d/m/Y') }}</strong> - 
                                        {{ $hist->tipo_evento }}
                                        @if($hist->resultado_palpacion)
                                            ({{ $hist->resultado_palpacion }})
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No hay más registros</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.registros-reproductivos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.vacas.show', $registro->vaca->id_vaca) }}" class="btn btn-info">
                    <i class="fas fa-cow"></i> Ver Vaca
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

