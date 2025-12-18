<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.P.G - Sistema de Producción Ganadera</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen gradient-animated overflow-hidden">
    <!-- Partículas de fondo animadas -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-spg-soft/20 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute top-40 right-20 w-40 h-40 bg-spg-primary/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-1/4 w-36 h-36 bg-spg-beige/30 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
    </div>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-6 py-12 z-10">
        <!-- Logo y Título con animación -->
        <div class="text-center mb-12 animate-fade-in-up">
            <div class="mb-6 animate-scale-in" style="animation-delay: 0.2s;">
                <div class="inline-flex flex-col items-center justify-center">
                    @if(file_exists(public_path('images/logo.png')))
                        <div class="relative w-40 h-40 mb-3">
                            <div class="absolute inset-0 rounded-full border-4 border-white shadow-2xl"></div>
                            <img src="{{ asset('images/logo.png') }}" alt="S.P.G Logo" class="w-full h-full object-contain rounded-full p-2 animate-bounce-subtle">
                        </div>
                    @elseif(file_exists(public_path('logo.png')))
                        <div class="relative w-40 h-40 mb-3">
                            <div class="absolute inset-0 rounded-full border-4 border-white shadow-2xl"></div>
                            <img src="{{ asset('logo.png') }}" alt="S.P.G Logo" class="w-full h-full object-contain rounded-full p-2 animate-bounce-subtle">
                        </div>
                    @else
                        <!-- Logo SVG como fallback -->
                        <div class="flex flex-col items-center">
                            <svg viewBox="0 0 200 200" class="w-40 h-40 drop-shadow-2xl animate-bounce-subtle">
                                <!-- Fondo blanco dentro del círculo -->
                                <circle cx="100" cy="100" r="92" fill="white"/>
                                
                                <!-- Círculo exterior verde oscuro con borde grueso -->
                                <circle cx="100" cy="100" r="92" fill="none" stroke="#1F713E" stroke-width="8"/>
                                
                                <!-- Colinas/Campos - Capa inferior (verde oscuro rico) -->
                                <path d="M 15 165 Q 50 155, 100 155 T 185 165 L 185 192 L 15 192 Z" fill="#2D5F3F"/>
                                
                                <!-- Colinas/Campos - Capa media (beige/tan) -->
                                <path d="M 20 150 Q 50 140, 100 140 T 180 150 L 180 165 L 20 165 Z" fill="#D4A574"/>
                                
                                <!-- Colinas/Campos - Capa superior (verde claro vibrante, la más grande) -->
                                <path d="M 25 130 Q 50 120, 100 120 T 175 130 L 175 150 L 25 150 Z" fill="#89C65B"/>
                                
                                <!-- Sol semi-circular en cuadrante superior derecho -->
                                <path d="M 140 40 A 18 18 0 0 1 158 40 L 158 50 A 18 18 0 0 1 140 50 Z" fill="#D4A574"/>
                                
                                <!-- Rayos del sol -->
                                <line x1="140" y1="30" x2="140" y2="25" stroke="#D4A574" stroke-width="2" stroke-linecap="round"/>
                                <line x1="149" y1="35" x2="152" y2="32" stroke="#D4A574" stroke-width="2" stroke-linecap="round"/>
                                <line x1="158" y1="40" x2="163" y2="40" stroke="#D4A574" stroke-width="2" stroke-linecap="round"/>
                                <line x1="149" y1="45" x2="152" y2="48" stroke="#D4A574" stroke-width="2" stroke-linecap="round"/>
                                <line x1="140" y1="50" x2="140" y2="55" stroke="#D4A574" stroke-width="2" stroke-linecap="round"/>
                                <line x1="131" y1="45" x2="128" y2="48" stroke="#D4A574" stroke-width="2" stroke-linecap="round"/>
                                <line x1="122" y1="40" x2="117" y2="40" stroke="#D4A574" stroke-width="2" stroke-linecap="round"/>
                                <line x1="131" y1="35" x2="128" y2="32" stroke="#D4A574" stroke-width="2" stroke-linecap="round"/>
                                
                                <!-- Vaca estilizada en blanco con contorno verde oscuro, mirando hacia la derecha -->
                                <!-- Cuerpo de la vaca -->
                                <ellipse cx="85" cy="105" rx="22" ry="18" fill="white" stroke="#1F713E" stroke-width="2.5"/>
                                
                                <!-- Cabeza de la vaca -->
                                <ellipse cx="70" cy="100" rx="10" ry="8" fill="white" stroke="#1F713E" stroke-width="2.5"/>
                                
                                <!-- Ojo -->
                                <circle cx="72" cy="98" r="2.5" fill="#1F713E"/>
                                
                                <!-- Oreja -->
                                <ellipse cx="65" cy="95" rx="4" ry="6" fill="white" stroke="#1F713E" stroke-width="2"/>
                                
                                <!-- Patas -->
                                <path d="M 68 120 Q 65 125, 65 130" stroke="#1F713E" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                <path d="M 78 120 Q 75 125, 75 130" stroke="#1F713E" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                <path d="M 92 120 Q 95 125, 95 130" stroke="#1F713E" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                <path d="M 102 120 Q 105 125, 105 130" stroke="#1F713E" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                
                                <!-- Ubre visible -->
                                <ellipse cx="90" cy="118" rx="8" ry="6" fill="white" stroke="#1F713E" stroke-width="2"/>
                                
                                <!-- Gota de leche con gradiente (verde claro a beige/amarillento) -->
                                <defs>
                                    <linearGradient id="milkGradient3" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#89C65B;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#EBC365;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M 75 95 Q 75 88, 78 88 Q 81 88, 81 92 Q 81 96, 78 98 Q 75 100, 75 95" fill="url(#milkGradient3)" stroke="#1F713E" stroke-width="1.5"/>
                            </svg>
                            <!-- Texto S.P.G debajo del círculo -->
                            <span class="text-3xl font-bold text-white font-display mt-3 text-shadow-lg">S.P.G</span>
                        </div>
        @endif
                </div>
            </div>
            <h1 class="text-6xl md:text-7xl font-bold text-white font-display mb-4 text-shadow-lg animate-fade-in-up" style="animation-delay: 0.3s;">
                S.P.G
            </h1>
            <p class="text-2xl md:text-3xl text-white/95 font-semibold max-w-2xl mx-auto mb-2 animate-fade-in-up" style="animation-delay: 0.4s;">
                Sistema de Producción Ganadera
            </p>
            <p class="text-lg md:text-xl text-white/85 max-w-xl mx-auto animate-fade-in-up" style="animation-delay: 0.5s;">
                Control Total, Decisiones Inteligentes
            </p>
        </div>

        <!-- Botones de Acción con animación -->
        <div class="flex flex-col sm:flex-row gap-4 mb-12 animate-fade-in-up" style="animation-delay: 0.6s;">
            @if (Route::has('login'))
                <a href="{{ route('login') }}" 
                   class="group inline-flex items-center justify-center px-10 py-4 bg-white text-spg-primary rounded-xl font-semibold text-lg shadow-saas-xl hover:shadow-saas-lg hover:scale-105 transition-all duration-300 btn-ripple">
                    <i class="fas fa-sign-in-alt mr-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                    Iniciar Sesión
                </a>
            @endif

                        @if (Route::has('register'))
                <a href="{{ route('register') }}" 
                   class="group inline-flex items-center justify-center px-10 py-4 bg-spg-primary text-white rounded-xl font-semibold text-lg shadow-saas-xl hover:shadow-saas-lg hover:scale-105 transition-all duration-300 border-2 border-white/50 btn-ripple">
                    <i class="fas fa-user-plus mr-3 group-hover:rotate-12 transition-transform duration-300"></i>
                    Registrarse
                </a>
            @endif
        </div>

        <!-- Footer Minimalista -->
        <footer class="mt-auto text-center text-white/70 text-sm animate-fade-in" style="animation-delay: 0.8s;">
            <p>&copy; {{ date('Y') }} Sistema de Producción Ganadera. Todos los derechos reservados.</p>
        </footer>
    </div>
    </body>
</html>
