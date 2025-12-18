@extends('layouts.master')

@section('title', 'Gestión de Crías')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-baby"></i> Gestión de Crías
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Crías</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Mensajes con SweetAlert2 -->
        @include('components.sweet-alert')

        <!-- Alertas -->
        @if(isset($criasProximasDestete) && $criasProximasDestete->count() > 0)
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle"></i> Crías Próximas al Destete (50-70 días): {{ $criasProximasDestete->count() }}</h5>
            <ul class="mb-0">
                @foreach($criasProximasDestete->take(5) as $cria)
                    <li>{{ $cria->vacaMadre ? $cria->vacaMadre->codigo : 'Sin madre' }} - {{ $cria->nombre_cria ?? 'Sin nombre' }} - Edad: {{ $cria->edadDias }} días</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Tarjetas de resumen -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticas['total_crias'] }}</h3>
                        <p>Total de Crías</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-baby"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-list"></i> Registradas
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $estadisticas['crias_este_mes'] }}</h3>
                        <p>Crías Este Mes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-chart-line"></i> Nacimientos
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $estadisticas['crias_destetadas'] }}</h3>
                        <p>Crías Destetadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-baby-carriage"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-check"></i> Completado
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['promedio_peso'], 2) }}</h3>
                        <p>Promedio Peso (kg)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-weight"></i>
                    </div>
                    <div class="small-box-footer">
                        <i class="fas fa-calculator"></i> Al nacer
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Registros de Crías
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.crias.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Cría
                    </a>
                    <a href="{{ route('admin.crias.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.crias.export.excel', request()->query()) }}">
                                <i class="fas fa-file-excel text-success"></i> Exportar a Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.crias.export.pdf', request()->query()) }}">
                                <i class="fas fa-file-pdf text-danger"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros de búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.crias.index') }}" class="form-inline">
                            <div class="input-group mr-2">
                                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, SINIGAN o código de vaca..." 
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="input-group mr-2">
                                <select name="sexo" class="form-control">
                                    <option value="">Todos los sexos</option>
                                    <option value="Macho" {{ request('sexo') == 'Macho' ? 'selected' : '' }}>Macho</option>
                                    <option value="Hembra" {{ request('sexo') == 'Hembra' ? 'selected' : '' }}>Hembra</option>
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="concepcion" class="form-control">
                                    <option value="">Todos los métodos</option>
                                    <option value="IA" {{ request('concepcion') == 'IA' ? 'selected' : '' }}>IA</option>
                                    <option value="Monta Natural" {{ request('concepcion') == 'Monta Natural' ? 'selected' : '' }}>Monta Natural</option>
                                    <option value="Transferencia Embrionaria" {{ request('concepcion') == 'Transferencia Embrionaria' ? 'selected' : '' }}>Transferencia Embrionaria</option>
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <select name="estado_destete" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="No destetada" {{ request('estado_destete') == 'No destetada' ? 'selected' : '' }}>No destetada</option>
                                    <option value="Destetada" {{ request('estado_destete') == 'Destetada' ? 'selected' : '' }}>Destetada</option>
                                </select>
                            </div>
                            <div class="input-group mr-2">
                                <input type="date" name="fecha_inicio" class="form-control" 
                                       placeholder="Fecha inicio" value="{{ request('fecha_inicio') }}">
                            </div>
                            <div class="input-group mr-2">
                                <input type="date" name="fecha_fin" class="form-control" 
                                       placeholder="Fecha fin" value="{{ request('fecha_fin') }}">
                            </div>
                            <div class="input-group mr-2">
                                <select name="id_vaca_madre" class="form-control">
                                    <option value="">Todas las vacas</option>
                                    @foreach($vacas as $vaca)
                                        <option value="{{ $vaca->id_vaca }}" 
                                                {{ request('id_vaca_madre') == $vaca->id_vaca ? 'selected' : '' }}>
                                            {{ $vaca->codigo }} - {{ $vaca->raza }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Filtrar</button>
                            <a href="{{ route('admin.crias.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
                        </form>
                    </div>
                </div>

                <!-- Tabla de registros -->
                @if($crias->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="3%">#</th>
                                    <th width="12%">Vaca Madre</th>
                                    <th width="10%">Nombre</th>
                                    <th width="8%">Sexo</th>
                                    <th width="10%">Fecha Nacimiento</th>
                                    <th width="8%">Edad</th>
                                    <th width="8%">Peso (kg)</th>
                                    <th width="10%">Concepción</th>
                                    <th width="10%">Estado Destete</th>
                                    <th width="11%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($crias as $index => $cria)
                                    @php
                                        $edadDias = $cria->fecha_nacimiento->diffInDays(now());
                                        $edadMeses = $cria->fecha_nacimiento->diffInMonths(now());
                                    @endphp
                                    <tr>
                                        <td>{{ $crias->firstItem() + $loop->index }}</td>
                                        <td>
                                            @if($cria->vacaMadre)
                                                <strong>{{ $cria->vacaMadre->codigo }}</strong><br>
                                                <small class="text-muted">{{ $cria->vacaMadre->raza ?? 'No especificada' }}</small>
                                            @else
                                                <span class="text-muted">Sin madre asignada</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cria->nombre_cria)
                                                <strong>{{ $cria->nombre_cria }}</strong>
                                            @else
                                                <span class="text-muted">Sin nombre</span>
                                            @endif
                                            @if($cria->sinigan)
                                                <br><small class="badge badge-info">SINIGAN: {{ $cria->sinigan }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cria->sexo)
                                                <span class="badge badge-{{ $cria->sexo == 'Macho' ? 'primary' : 'danger' }}">
                                                    {{ $cria->sexo }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar"></i> 
                                            {{ $cria->fecha_nacimiento->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            @if($edadMeses > 0)
                                                <span class="badge badge-info">{{ $edadMeses }} mes(es)</span>
                                            @else
                                                <span class="badge badge-warning">{{ $edadDias }} día(s)</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">
                                                <i class="fas fa-weight"></i> 
                                                {{ number_format($cria->peso, 2) }} kg
                                            </span>
                                        </td>
                                        <td>
                                            @if($cria->concepcion)
                                                <span class="badge badge-secondary">{{ $cria->concepcion }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cria->estado_destete == 'Destetada')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Destetada
                                                </span>
                                                @if($cria->fecha_destete)
                                                    <br><small>{{ $cria->fecha_destete->format('d/m/Y') }}</small>
                                                @endif
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-baby"></i> No destetada
                                                </span>
                                                @if($cria->estaProximaAlDestete())
                                                    <br><small class="badge badge-danger">Próxima</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.crias.show', $cria->id_cria) }}" 
                                                   class="btn btn-info btn-sm" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.crias.edit', $cria->id_cria) }}" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        onclick="confirmarEliminacion({{ $cria->id_cria }})"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $crias->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-baby fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay registros de crías</h4>
                        <p class="text-muted">No se encontraron registros de crías con los filtros aplicados.</p>
                        <a href="{{ route('admin.crias.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Registro
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Gráficas ApexCharts -->
        @if(isset($datosGraficas))
        <div class="row mb-3">
            <!-- Gráfica: Nacimientos por Mes -->
            @if(isset($datosGraficas['nacimientos_por_mes']) && count($datosGraficas['nacimientos_por_mes']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Nacimientos por Mes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartNacimientosPorMes" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Crías por Sexo -->
            @if(isset($datosGraficas['por_sexo']) && count($datosGraficas['por_sexo']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Distribución por Sexo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPorSexo" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gráfica: Crías por Concepción -->
            @if(isset($datosGraficas['por_concepcion']) && count($datosGraficas['por_concepcion']['labels']) > 0)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Método de Concepción
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPorConcepcion" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para las gráficas
    const datosNacimientosPorMes = @json($datosGraficas['nacimientos_por_mes'] ?? ['labels' => [], 'data' => []]);
    const datosPorSexo = @json($datosGraficas['por_sexo'] ?? ['labels' => [], 'data' => []]);
    const datosPorConcepcion = @json($datosGraficas['por_concepcion'] ?? ['labels' => [], 'data' => []]);

    // Gráfica: Nacimientos por Mes (Línea)
    if (datosNacimientosPorMes.labels.length > 0) {
        const chartNacimientosPorMes = new ApexCharts(document.querySelector("#chartNacimientosPorMes"), {
            series: [{
                name: 'Nacimientos',
                data: datosNacimientosPorMes.data
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: true },
                zoom: { enabled: false }
            },
            colors: ['#17A2B8'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: datosNacimientosPorMes.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' nacimientos' } }
            }
        });
        chartNacimientosPorMes.render();
    }

    // Gráfica: Por Sexo (Donut)
    if (datosPorSexo.labels.length > 0) {
        const chartPorSexo = new ApexCharts(document.querySelector("#chartPorSexo"), {
            series: datosPorSexo.data,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: datosPorSexo.labels,
            colors: ['#007BFF', '#DC3545'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' crías' } }
            }
        });
        chartPorSexo.render();
    }

    // Gráfica: Por Concepción (Barras)
    if (datosPorConcepcion.labels.length > 0) {
        const chartPorConcepcion = new ApexCharts(document.querySelector("#chartPorConcepcion"), {
            series: [{
                name: 'Cantidad',
                data: datosPorConcepcion.data
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: true }
            },
            colors: ['#28A745'],
            xaxis: {
                categories: datosPorConcepcion.labels
            },
            yaxis: {
                title: { text: 'Cantidad' }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false
                }
            },
            tooltip: {
                y: { formatter: function(val) { return val + ' crías' } }
            }
        });
        chartPorConcepcion.render();
    }
});

function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará el registro de cría. Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.crias.index") }}/' + id;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
