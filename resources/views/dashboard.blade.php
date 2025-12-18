@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <p>Bienvenido, {{ auth()->user()->name }}.</p>
                <p>Tu cuenta no tiene un rol asignado. Por favor, contacta al administrador para que te asigne un rol (Admin o Pasante) y puedas acceder a las funcionalidades del sistema.</p>
                
                <div class="mt-4">
                    <p><strong>Correo Electrónico:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Rol Asignado:</strong> 
                        @if(auth()->user()->roles->count() > 0)
                            <span class="badge bg-primary">{{ auth()->user()->roles->first()->name }}</span>
                        @else
                            <span class="badge bg-warning">Sin rol</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
