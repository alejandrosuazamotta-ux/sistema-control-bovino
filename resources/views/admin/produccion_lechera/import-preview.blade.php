@extends('layouts.master')

@section('title', 'Previsualizar Importación')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Previsualizar Importación</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.produccion-lechera.index') }}">Producción Lechera</a></li>
                        <li class="breadcrumb-item active">Previsualizar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-eye"></i> Previsualización de Datos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <strong>Total de filas:</strong> {{ $totalRows }} | 
                                <strong>Mostrando:</strong> {{ count($preview) }} primeras filas
                            </div>

                            @if(count($preview) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            @foreach(array_keys($preview[0]) as $header)
                                                <th>{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($preview as $row)
                                            <tr>
                                                @foreach($row as $cell)
                                                    <td>{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <form action="{{ route('admin.produccion-lechera.process-import') }}" method="POST">
                                @csrf
                                <input type="hidden" name="archivo_temp" value="{{ $archivoTemp }}">
                                
                                @if($totalRows > 1000 || filesize(storage_path('app/' . $archivoTemp)) > 1048576)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Archivo grande detectado:</strong> Se recomienda procesar en segundo plano para evitar timeouts.
                                </div>
                                @endif
                                
                                <div class="form-check mt-3">
                                    <input type="checkbox" class="form-check-input" id="procesar_async" name="procesar_async" value="1" 
                                           {{ ($totalRows > 1000 || filesize(storage_path('app/' . $archivoTemp)) > 1048576) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="procesar_async">
                                        <i class="fas fa-clock"></i> Procesar en segundo plano (recomendado para archivos grandes)
                                        <br><small class="text-muted">Recibirá una notificación cuando se complete la importación</small>
                                    </label>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check"></i> Confirmar e Importar
                                    </button>
                                    <a href="{{ route('admin.produccion-lechera.import') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                            </form>
                            @else
                            <div class="alert alert-warning">
                                <p>No se encontraron datos en el archivo. Por favor, verifique el formato del archivo.</p>
                                <a href="{{ route('admin.produccion-lechera.import') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

