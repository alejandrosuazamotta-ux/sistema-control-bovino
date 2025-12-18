@extends('layouts.master')

@section('title', 'Reporte de Reproducción')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-heart mr-2"></i>
                    Reporte de Reproducción
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reporte de Reproducción</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info"></i> Reporte de Reproducción</h5>
            <p>Este reporte está en desarrollo. Próximamente mostrará estadísticas detalladas sobre los registros reproductivos del ganado.</p>
        </div>
    </div>
</section>
@endsection

