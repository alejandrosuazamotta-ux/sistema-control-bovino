<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>S.P.G - Sistema de Producción Ganadera | @yield('title', 'Dashboard')</title>

    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AdminLTE (mantenido para funcionalidad) -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    @stack('styles')
    
    <style>
        /* Override AdminLTE con colores SPG y animaciones */
        .main-sidebar {
            background: linear-gradient(180deg, #1F713E 0%, #4BAE4F 100%) !important;
            transition: all 0.3s ease;
        }
        
        .nav-sidebar > .nav-item {
            transition: all 0.3s ease;
        }
        
        .nav-sidebar > .nav-item > .nav-link {
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 2px 8px;
        }
        
        .nav-sidebar > .nav-item > .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #fff !important;
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .nav-sidebar > .nav-item > .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
            transform: translateX(4px);
        }
        
        .nav-sidebar > .nav-item > .nav-link i {
            transition: transform 0.3s ease;
        }
        
        .nav-sidebar > .nav-item > .nav-link:hover i {
            transform: scale(1.1);
        }
        
        .main-header {
            background-color: #fff !important;
            border-bottom: 2px solid #89C65B !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .content-wrapper {
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
            min-height: calc(100vh - 57px);
        }
        
        .brand-link {
            transition: all 0.3s ease;
            padding: 1.5rem 1rem !important;
        }
        
        .brand-link:hover {
            transform: scale(1.05);
        }
        
        .brand-text {
            color: #fff !important;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            letter-spacing: 1px;
        }
        
        .dropdown-menu {
            animation: fadeInDown 0.3s ease;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        
        .dropdown-item {
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 2px 8px;
        }
        
        .dropdown-item:hover {
            background-color: #f0f9ff;
            transform: translateX(4px);
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Animación para elementos del menú */
        .nav-sidebar > .nav-item {
            animation: slideInLeft 0.5s ease forwards;
            opacity: 0;
        }
        
        .nav-sidebar > .nav-item:nth-child(1) { animation-delay: 0.1s; }
        .nav-sidebar > .nav-item:nth-child(2) { animation-delay: 0.15s; }
        .nav-sidebar > .nav-item:nth-child(3) { animation-delay: 0.2s; }
        .nav-sidebar > .nav-item:nth-child(4) { animation-delay: 0.25s; }
        .nav-sidebar > .nav-item:nth-child(5) { animation-delay: 0.3s; }
        .nav-sidebar > .nav-item:nth-child(6) { animation-delay: 0.35s; }
        .nav-sidebar > .nav-item:nth-child(7) { animation-delay: 0.4s; }
        .nav-sidebar > .nav-item:nth-child(8) { animation-delay: 0.45s; }
        .nav-sidebar > .nav-item:nth-child(9) { animation-delay: 0.5s; }
        .nav-sidebar > .nav-item:nth-child(10) { animation-delay: 0.55s; }
        .nav-sidebar > .nav-item:nth-child(11) { animation-delay: 0.6s; }
        .nav-sidebar > .nav-item:nth-child(12) { animation-delay: 0.65s; }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light animate-fade-in-down">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link transition-all duration-300 hover:bg-spg-soft/20 rounded-lg p-2" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars text-spg-primary"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link text-spg-deepblue font-medium transition-colors duration-300 hover:text-spg-primary">
                    <i class="fas fa-home mr-1"></i> Inicio
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            @auth
            <li class="nav-item dropdown">
                <a class="nav-link transition-all duration-300 hover:bg-spg-soft/20 rounded-lg px-3 py-2" data-toggle="dropdown" href="#">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-spg-primary to-spg-secondary rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="text-spg-deepblue font-medium">{{ Auth::user()->name ?? 'Usuario' }}</span>
                        <i class="fas fa-chevron-down text-spg-deepblue text-xs"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-saas-lg">
                    <a href="{{ route('profile.show') ?? '#' }}" class="dropdown-item">
                        <i class="fas fa-user text-spg-primary mr-2"></i> Perfil
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item text-red-600 hover:bg-red-50" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
            @endauth
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link animate-fade-in-down">
            <div class="brand-text-wrapper text-center">
                <div class="mb-2">
                    <i class="fas fa-cow text-white text-3xl animate-bounce-subtle"></i>
                </div>
                <div class="brand-text">S.P.G</div>
                <div class="text-white/80 text-xs mt-1 font-normal">Sistema de Producción Ganadera</div>
            </div>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    {{-- Menú para ADMIN --}}
                    @if(auth()->check() && auth()->user()->hasRole('Admin'))
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Personal -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Personal <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.personal.create') }}" class="nav-link">
                                        <i class="fas fa-user-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.personal.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Potreros -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-leaf"></i>
                                <p>Potreros <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.potreros.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.potreros.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Vacas -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cow"></i>
                                <p>Vacas <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.vacas.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.vacas.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Crías -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-baby"></i>
                                <p>Crías <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.crias.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.crias.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Registros Reproductivos -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-seedling"></i>
                                <p>Reproducción <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.registros-reproductivos.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.registros-reproductivos.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Producción Lechera -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-glass-water"></i>
                                <p>Producción <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.produccion-lechera.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.produccion-lechera.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Salud -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-vial"></i>
                                <p>Salud <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.salud.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.salud.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Pruebas Sanitarias -->
                        <li class="nav-item has-treeview {{ request()->routeIs('admin.pruebas-sanitarias.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('admin.pruebas-sanitarias.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-vial"></i>
                                <p>Pruebas Sanitarias <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.pruebas-sanitarias.create') }}" class="nav-link {{ request()->routeIs('admin.pruebas-sanitarias.create') ? 'active' : '' }}">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Nueva Prueba</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.pruebas-sanitarias.index') }}" class="nav-link {{ request()->routeIs('admin.pruebas-sanitarias.index') ? 'active' : '' }}">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.pruebas-sanitarias.import') }}" class="nav-link {{ request()->routeIs('admin.pruebas-sanitarias.import') ? 'active' : '' }}">
                                        <i class="fas fa-file-excel nav-icon"></i>
                                        <p>Importar Excel</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Medicamentos -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-syringe"></i>
                                <p>Medicamentos <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.medicamentos.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.medicamentos.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Mortalidad -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-skull-crossbones"></i>
                                <p>Mortalidad <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.mortalidad.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.mortalidad.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Listar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Alertas -->
                        <li class="nav-item">
                            <a href="{{ route('admin.alertas.index') }}" class="nav-link {{ request()->routeIs('admin.alertas.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-triangle-exclamation"></i>
                                <p>Alertas</p>
                            </a>
                        </li>

                        <!-- Reportes -->
                        <li class="nav-item has-treeview {{ request()->routeIs('admin.reportes.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Reportes <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.reportes.index') }}" class="nav-link {{ request()->routeIs('admin.reportes.index') ? 'active' : '' }}">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Índice</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.reportes.produccion') }}" class="nav-link {{ request()->routeIs('admin.reportes.produccion') ? 'active' : '' }}">
                                        <i class="fas fa-glass-water nav-icon"></i>
                                        <p>Producción</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.reportes.reproductivo') }}" class="nav-link {{ request()->routeIs('admin.reportes.reproductivo') ? 'active' : '' }}">
                                        <i class="fas fa-heart nav-icon"></i>
                                        <p>Reproductivo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.reportes.sanitario') }}" class="nav-link {{ request()->routeIs('admin.reportes.sanitario') ? 'active' : '' }}">
                                        <i class="fas fa-virus nav-icon"></i>
                                        <p>Sanitario</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.reportes.mortalidad') }}" class="nav-link {{ request()->routeIs('admin.reportes.mortalidad') ? 'active' : '' }}">
                                        <i class="fas fa-skull nav-icon"></i>
                                        <p>Mortalidad</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.reportes.medicamentos') }}" class="nav-link {{ request()->routeIs('admin.reportes.medicamentos') ? 'active' : '' }}">
                                        <i class="fas fa-pills nav-icon"></i>
                                        <p>Medicamentos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    {{-- Menú para PASANTE --}}
                    @elseif(auth()->check() && auth()->user()->hasRole('Pasante'))
                        <li class="nav-item">
                            <a href="{{ route('pasante.dashboard') }}" class="nav-link {{ request()->routeIs('pasante.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Actividades -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link {{ request()->routeIs('pasante.actividades.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Actividades <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('pasante.actividades.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Nueva Actividad</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pasante.actividades.index') }}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Mis Actividades</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Tareas -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link {{ request()->routeIs('pasante.tareas.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>Tareas <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('pasante.tareas.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Nueva Tarea</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pasante.tareas.index') }}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Mis Tareas</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Apoyo Ordeño -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link {{ request()->routeIs('pasante.apoyo-ordeno.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-glass-water"></i>
                                <p>Apoyo Ordeño <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('pasante.apoyo-ordeno.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar Apoyo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pasante.apoyo-ordeno.index') }}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Mis Registros</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Apoyo Reproductivo -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link {{ request()->routeIs('pasante.apoyo-reproductivo.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-seedling"></i>
                                <p>Apoyo Reproductivo <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('pasante.apoyo-reproductivo.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Registrar Apoyo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pasante.apoyo-reproductivo.index') }}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Mis Registros</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Rotación Potreros -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link {{ request()->routeIs('pasante.rotacion-potreros.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Rotación Potreros <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('pasante.rotacion-potreros.create') }}" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Nueva Rotación</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pasante.rotacion-potreros.index') }}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Mis Rotaciones</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Producción Lechera (Solo lectura) -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link {{ request()->routeIs('pasante.produccion-lechera.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-milk"></i>
                                <p>Producción Lechera <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('pasante.produccion-lechera.dashboard') }}" class="nav-link">
                                        <i class="fas fa-chart-pie nav-icon"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pasante.produccion-lechera.index') }}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>Ver Producción</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Pruebas Sanitarias -->
                        <li class="nav-item has-treeview {{ request()->routeIs('pasante.pruebas-sanitarias.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('pasante.pruebas-sanitarias.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-vial"></i>
                                <p>Pruebas Sanitarias <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('pasante.pruebas-sanitarias.create') }}" class="nav-link {{ request()->routeIs('pasante.pruebas-sanitarias.create') ? 'active' : '' }}">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Nueva Prueba</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pasante.pruebas-sanitarias.index') }}" class="nav-link {{ request()->routeIs('pasante.pruebas-sanitarias.index') ? 'active' : '' }}">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Ver Historial</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <!-- Mortalidad -->
                        <li class="nav-item has-treeview {{ request()->routeIs('pasante.mortalidad.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('pasante.mortalidad.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-skull-crossbones"></i>
                                <p>Mortalidad <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('pasante.mortalidad.create') }}" class="nav-link {{ request()->routeIs('pasante.mortalidad.create') ? 'active' : '' }}">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Nuevo Registro</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pasante.mortalidad.index') }}" class="nav-link {{ request()->routeIs('pasante.mortalidad.index') ? 'active' : '' }}">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Ver Registros</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endif
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper animate-fade-in">
        <div class="px-4 md:px-6 py-4">
            @yield('content')
        </div>
    </div>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark"></aside>

    <!-- jQuery -->
    <script src="{{ asset('AdminLTE-3.2.0/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.2.0/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>$.widget.bridge('uibutton', $.ui.button)</script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('AdminLTE-3.2.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('AdminLTE-3.2.0/dist/js/adminlte.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>
</html>
