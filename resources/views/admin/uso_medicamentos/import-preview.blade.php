@extends('layouts.master')

@section('title', 'Previsualizar Importación de Uso de Medicamentos')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-eye"></i> Previsualizar Importación
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.uso-medicamentos.index') }}">Uso de Medicamentos</a></li>
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

                        <form action="{{ route('admin.uso-medicamentos.process-import') }}" method="POST">
                            @csrf
                            <input type="hidden" name="archivo_temp" value="{{ $rutaTemporal }}">
                            <div class="mt-3">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check"></i> Confirmar e Importar
                                </button>
                                <a href="{{ route('admin.uso-medicamentos.import') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </form>
                        @else
                        <div class="alert alert-warning">
                            <p>No se encontraron datos en el archivo. Por favor, verifique el formato del archivo.</p>
                            <a href="{{ route('admin.uso-medicamentos.import') }}" class="btn btn-secondary">
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
@endsection

