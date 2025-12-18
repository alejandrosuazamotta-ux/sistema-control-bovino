@extends('layouts.master')

@section('title', 'Detalle de Mortalidad')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-skull"></i> Detalle de Mortalidad
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.mortalidad.index') }}">Mortalidad</a></li>
                    <li class="breadcrumb-item active">Detalle</li>
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
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información del Registro
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.mortalidad.edit', $mortalidad->id_mortalidad) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">ID Registro</th>
                                    <td>#{{ $mortalidad->id_mortalidad }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo de Animal</th>
                                    <td>
                                        @if($mortalidad->esVaca())
                                            <span class="badge badge-primary">
                                                <i class="fas fa-cow"></i> Vaca
                                            </span>
                                        @else
                                            <span class="badge badge-info">
                                                <i class="fas fa-baby"></i> Cría
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Animal</th>
                                    <td>
                                        @if($mortalidad->animal)
                                            @if($mortalidad->esVaca())
                                                <strong>{{ $mortalidad->animal->codigo }}</strong>
                                                @if($mortalidad->animal->raza)
                                                    - {{ $mortalidad->animal->raza }}
                                                @endif
                                            @else
                                                <strong>{{ $mortalidad->animal->nombre_cria ?? 'Cría #' . $mortalidad->animal_id }}</strong>
                                                @if($mortalidad->animal->vacaMadre)
                                                    <br><small>Madre: {{ $mortalidad->animal->vacaMadre->codigo }}</small>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-muted">Animal no encontrado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha de Muerte</th>
                                    <td>
                                        <i class="fas fa-calendar"></i> {{ $mortalidad->fecha->format('d/m/Y') }}
                                        @if($mortalidad->hora)
                                            <br><small><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($mortalidad->hora)->format('H:i') }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Clasificación</th>
                                    <td>
                                        <span class="badge badge-secondary">{{ $mortalidad->clasificacion }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Peso (kg)</th>
                                    <td>
                                        @if($mortalidad->peso)
                                            <strong>{{ number_format($mortalidad->peso, 2) }} kg</strong>
                                        @else
                                            <span class="text-muted">No registrado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Causa de Muerte</th>
                                    <td>{{ $mortalidad->causa }}</td>
                                </tr>
                                @if($mortalidad->acta)
                                <tr>
                                    <th>Acta o Documento</th>
                                    <td>{{ $mortalidad->acta }}</td>
                                </tr>
                                @endif
                                @if($mortalidad->acta_path)
                                <tr>
                                    <th>Evidencia (Acta PDF/Imagen)</th>
                                    <td>
                                        <a href="{{ asset('storage/' . $mortalidad->acta_path) }}" 
                                           class="btn btn-info btn-sm" target="_blank">
                                            <i class="fas fa-download"></i> Ver Evidencia
                                        </a>
                                        @if(str_ends_with(strtolower($mortalidad->acta_path), '.pdf'))
                                            <span class="badge badge-danger">PDF</span>
                                        @else
                                            <span class="badge badge-info">Imagen</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @if($mortalidad->observaciones)
                                <tr>
                                    <th>Observaciones</th>
                                    <td>{{ $mortalidad->observaciones }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Fecha de Registro</th>
                                    <td>
                                        <i class="fas fa-calendar-alt"></i> {{ $mortalidad->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Última Actualización</th>
                                    <td>
                                        <i class="fas fa-clock"></i> {{ $mortalidad->updated_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.mortalidad.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('admin.mortalidad.edit', $mortalidad->id_mortalidad) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('admin.mortalidad.destroy', $mortalidad->id_mortalidad) }}" 
                              method="POST" class="d-inline" 
                              onsubmit="return confirm('¿Está seguro de eliminar este registro? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                @if($mortalidad->animal && $mortalidad->esVaca())
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cow"></i> Información de la Vaca
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Código:</strong> {{ $mortalidad->animal->codigo }}</p>
                            @if($mortalidad->animal->raza)
                                <p><strong>Raza:</strong> {{ $mortalidad->animal->raza }}</p>
                            @endif
                            @if($mortalidad->animal->fecha_nacimiento)
                                <p><strong>Fecha de Nacimiento:</strong> {{ $mortalidad->animal->fecha_nacimiento->format('d/m/Y') }}</p>
                            @endif
                            <p><strong>Estado de Salud:</strong> 
                                <span class="badge badge-danger">{{ $mortalidad->animal->estado_salud }}</span>
                            </p>
                        </div>
                    </div>
                @elseif($mortalidad->animal && $mortalidad->esCria())
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-baby"></i> Información de la Cría
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Nombre:</strong> {{ $mortalidad->animal->nombre_cria ?? 'Cría #' . $mortalidad->animal_id }}</p>
                            @if($mortalidad->animal->sexo)
                                <p><strong>Sexo:</strong> {{ $mortalidad->animal->sexo }}</p>
                            @endif
                            @if($mortalidad->animal->fecha_nacimiento)
                                <p><strong>Fecha de Nacimiento:</strong> {{ $mortalidad->animal->fecha_nacimiento->format('d/m/Y') }}</p>
                            @endif
                            @if($mortalidad->animal->vacaMadre)
                                <p><strong>Madre:</strong> {{ $mortalidad->animal->vacaMadre->codigo }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

