@extends('layouts.master')

@section('title', 'Uso de Medicamentos - Mis Registros')

@section('content')
@php
    // Protección: Esta vista no debería ser accesible para Pasante
    // Las rutas fueron eliminadas por seguridad
    abort(404, 'Esta página no está disponible para tu rol.');
@endphp
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-pills"></i> Mis Registros de Uso de Medicamentos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('pasante.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Uso de Medicamentos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('components.sweet-alert')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Mis Registros</h3>
                <div class="card-tools">
                    <a href="{{ route('pasante.uso-medicamentos.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pasante.uso-medicamentos.index') }}" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar por código de vaca..." value="{{ request('search') }}">
                    <select name="id_vaca" class="form-control mr-2">
                        <option value="">Todas las vacas</option>
                        @foreach($vacas as $vaca)
                            <option value="{{ $vaca->id_vaca }}" {{ request('id_vaca') == $vaca->id_vaca ? 'selected' : '' }}>
                                {{ $vaca->codigo }}
                            </option>
                        @endforeach
                    </select>
                    <select name="id_medicamento" class="form-control mr-2">
                        <option value="">Todos los medicamentos</option>
                        @foreach($medicamentos as $medicamento)
                            <option value="{{ $medicamento->id_medicamento }}" {{ request('id_medicamento') == $medicamento->id_medicamento ? 'selected' : '' }}>
                                {{ $medicamento->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('pasante.uso-medicamentos.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                </form>

                @if($usos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Vaca</th>
                                    <th>Medicamento</th>
                                    <th>Fecha Aplicación</th>
                                    <th>Dosis</th>
                                    <th>Personal</th>
                                    <th>Retiro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usos as $uso)
                                    <tr>
                                        <td>{{ $usos->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $uso->vaca->codigo }}</strong><br>
                                            <small>{{ $uso->vaca->raza }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $uso->medicamento->nombre }}</strong><br>
                                            @if($uso->medicamento->periodo_retiro_dias > 0)
                                                <small class="badge badge-warning">{{ $uso->medicamento->periodo_retiro_dias }} días retiro</small>
                                            @endif
                                        </td>
                                        <td>{{ $uso->fecha_aplicacion->format('d/m/Y') }}</td>
                                        <td>
                                            @if($uso->dosis_aplicada)
                                                {{ number_format($uso->dosis_aplicada, 2) }} {{ $uso->unidad_dosis ?? '' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $uso->personal->nombre ?? '-' }}</td>
                                        <td>
                                            @if($uso->retiro)
                                                @if($uso->retiro->estaActivo())
                                                    <span class="badge badge-danger">
                                                        Activo hasta {{ $uso->retiro->fecha_fin->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">Finalizado</span>
                                                @endif
                                            @else
                                                <span class="badge badge-success">Sin retiro</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pasante.uso-medicamentos.show', $uso->id_uso) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $usos->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay registros de uso</h5>
                        <a href="{{ route('pasante.uso-medicamentos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Registrar Primer Uso
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

