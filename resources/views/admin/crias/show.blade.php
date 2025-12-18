@extends('layouts.master')

@section('title', 'Detalles de Cría')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-eye"></i> Detalles de Cría
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crias.index') }}">Crías</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <!-- Información principal -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Información de la Cría
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.crias.edit', $cria->id_cria) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" 
                                    class="btn btn-danger btn-sm" 
                                    onclick="confirmarEliminacion({{ $cria->id_cria }})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong><i class="fas fa-cow"></i> Vaca Madre:</strong></td>
                                        <td>{{ $cria->vacaMadre->codigo }} - {{ $cria->vacaMadre->raza }}</td>
                                    </tr>
                                    @if($cria->nombre_cria)
                                    <tr>
                                        <td><strong><i class="fas fa-tag"></i> Nombre:</strong></td>
                                        <td>{{ $cria->nombre_cria }}</td>
                                    </tr>
                                    @endif
                                    @if($cria->sexo)
                                    <tr>
                                        <td><strong><i class="fas fa-venus-mars"></i> Sexo:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $cria->sexo == 'Macho' ? 'primary' : 'danger' }}">
                                                {{ $cria->sexo }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong><i class="fas fa-calendar"></i> Fecha Nacimiento:</strong></td>
                                        <td>{{ $cria->fecha_nacimiento->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-weight"></i> Peso al Nacer:</strong></td>
                                        <td>
                                            <span class="badge badge-primary">
                                                {{ number_format($cria->peso, 2) }} kg
                                            </span>
                                        </td>
                                    </tr>
                                    @if($cria->fecha_tatuado)
                                    <tr>
                                        <td><strong><i class="fas fa-stamp"></i> Fecha Tatuado:</strong></td>
                                        <td>{{ $cria->fecha_tatuado->format('d/m/Y') }}</td>
                                    </tr>
                                    @endif
                                    @if($cria->concepcion)
                                    <tr>
                                        <td><strong><i class="fas fa-dna"></i> Concepción:</strong></td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $cria->concepcion }}</span>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($cria->sinigan)
                                    <tr>
                                        <td><strong><i class="fas fa-barcode"></i> SINIGAN:</strong></td>
                                        <td><span class="badge badge-info">{{ $cria->sinigan }}</span></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong><i class="fas fa-baby-carriage"></i> Estado Destete:</strong></td>
                                        <td>
                                            @if($cria->estado_destete == 'Destetada')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Destetada
                                                </span>
                                                @if($cria->fecha_destete)
                                                    <br><small>Fecha: {{ $cria->fecha_destete->format('d/m/Y') }}</small>
                                                @endif
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-baby"></i> No destetada
                                                </span>
                                                @if($cria->estaProximaAlDestete())
                                                    <br><small class="badge badge-danger">Próxima al destete</small>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong><i class="fas fa-clock"></i> Creado:</strong></td>
                                        <td>{{ $cria->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-edit"></i> Actualizado:</strong></td>
                                        <td>{{ $cria->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-calendar-alt"></i> Edad Actual:</strong></td>
                                        <td>
                                            @if($estadisticasCria['edad_meses'] > 0)
                                                <span class="badge badge-info">{{ $estadisticasCria['edad_meses'] }} mes(es)</span>
                                            @else
                                                <span class="badge badge-warning">{{ $estadisticasCria['edad_dias'] }} día(s)</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-heart"></i> Estado Salud:</strong></td>
                                        <td>
                                            @if($estadisticasCria['edad_dias'] < 30)
                                                <span class="badge badge-danger">Neonato</span>
                                            @elseif($estadisticasCria['edad_dias'] < 90)
                                                <span class="badge badge-warning">Lactante</span>
                                            @else
                                                <span class="badge badge-success">Joven</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($cria->observaciones)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-sticky-note"></i> Observaciones:</h5>
                                    <div class="alert alert-info">
                                        {{ $cria->observaciones }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estadísticas de la cría -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Estadísticas de la Cría
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Edad en Días</span>
                                <span class="info-box-number">{{ $estadisticasCria['edad_dias'] }}</span>
                            </div>
                        </div>

                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Edad en Meses</span>
                                <span class="info-box-number">{{ $estadisticasCria['edad_meses'] }}</span>
                            </div>
                        </div>

                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Hermanos</span>
                                <span class="info-box-number">{{ $estadisticasCria['hermanos'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de la vaca madre -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cow"></i> Información de la Vaca Madre
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Código:</strong> {{ $cria->vacaMadre->codigo }}</p>
                        <p><strong>Raza:</strong> {{ $cria->vacaMadre->raza }}</p>
                        <p><strong>Estado de Salud:</strong> 
                            <span class="badge badge-{{ $cria->vacaMadre->estado_salud == 'Sana' ? 'success' : ($cria->vacaMadre->estado_salud == 'En tratamiento' ? 'warning' : 'danger') }}">
                                {{ $cria->vacaMadre->estado_salud }}
                            </span>
                        </p>
                        <p><strong>Estado Reproductivo:</strong> 
                            <span class="badge badge-{{ $cria->vacaMadre->estado_reproductivo == 'Lactancia' ? 'success' : 'info' }}">
                                {{ $cria->vacaMadre->estado_reproductivo }}
                            </span>
                        </p>
                        @if($cria->vacaMadre->fecha_nacimiento)
                            <p><strong>Fecha de Nacimiento:</strong> {{ $cria->vacaMadre->fecha_nacimiento->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <a href="{{ route('admin.crias.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a la Lista
                        </a>
                        <a href="{{ route('admin.crias.edit', $cria->id_cria) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Cría
                        </a>
                        <a href="{{ route('admin.vacas.show', $cria->vacaMadre->id_vaca) }}" class="btn btn-info">
                            <i class="fas fa-cow"></i> Ver Vaca Madre
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar este registro de cría?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmarEliminacion(id) {
    $('#deleteForm').attr('action', '{{ route("admin.crias.index") }}/' + id);
    $('#deleteModal').modal('show');
}
</script>
@endpush
@endsection
