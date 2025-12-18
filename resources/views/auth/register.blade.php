<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema S.P.G</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-spg-beige via-white to-spg-beige/50 flex items-center justify-center px-6 py-12">
    <!-- Partículas de fondo suaves -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-32 h-32 bg-spg-soft/15 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-spg-beige/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-spg-primary/5 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
    </div>

    <div class="relative w-full max-w-md z-10">
        <!-- Logo y Título -->
        <div class="text-center mb-8 animate-fade-in-down">
            <div class="inline-flex flex-col items-center justify-center mb-6 animate-scale-in">
                @if(file_exists(public_path('images/logo.png')))
                    <div class="relative w-32 h-32 mb-2">
                        <div class="absolute inset-0 rounded-full border-4 border-spg-primary shadow-lg"></div>
                        <img src="{{ asset('images/logo.png') }}" alt="S.P.G Logo" class="w-full h-full object-contain rounded-full p-2 animate-bounce-subtle">
                    </div>
                    <span class="text-2xl font-bold text-spg-primary font-display">S.P.G</span>
                @elseif(file_exists(public_path('logo.png')))
                    <div class="relative w-32 h-32 mb-2">
                        <div class="absolute inset-0 rounded-full border-4 border-spg-primary shadow-lg"></div>
                        <img src="{{ asset('logo.png') }}" alt="S.P.G Logo" class="w-full h-full object-contain rounded-full p-2 animate-bounce-subtle">
                    </div>
                    <span class="text-2xl font-bold text-spg-primary font-display">S.P.G</span>
                @else
                    <!-- Logo SVG como fallback -->
                    <div class="flex flex-col items-center">
                        <svg viewBox="0 0 200 200" class="w-32 h-32 drop-shadow-lg animate-bounce-subtle">
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
                                <linearGradient id="milkGradient2" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:#89C65B;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#EBC365;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="M 75 95 Q 75 88, 78 88 Q 81 88, 81 92 Q 81 96, 78 98 Q 75 100, 75 95" fill="url(#milkGradient2)" stroke="#1F713E" stroke-width="1.5"/>
                        </svg>
                        <!-- Texto S.P.G debajo del círculo -->
                        <span class="text-2xl font-bold text-spg-primary font-display mt-2">S.P.G</span>
                    </div>
                @endif
            </div>
            <h1 class="text-3xl font-bold text-spg-deepblue font-display mb-2 animate-fade-in-down" style="animation-delay: 0.1s;">
                Crear Cuenta
            </h1>
            <p class="text-sm text-gray-600 animate-fade-in-down" style="animation-delay: 0.2s;">
                Sistema de Producción Ganadera
            </p>
        </div>

        <!-- Card de Registro con colores más agradables -->
        <div class="bg-white/90 backdrop-blur-md rounded-card shadow-saas-xl p-8 border border-spg-beige/30 animate-fade-in-up" style="animation-delay: 0.3s;">
            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <x-form.input 
                    label="Nombre Completo"
                    name="name"
                    type="text"
                    icon="fas fa-user"
                    :value="old('name')"
                    required
                    autofocus
                />

                <x-form.input 
                    label="Correo Electrónico"
                    name="email"
                    type="email"
                    icon="fas fa-envelope"
                    :value="old('email')"
                    required
                />

                <x-form.input 
                    label="Contraseña"
                    name="password"
                    type="password"
                    icon="fas fa-lock"
                    required
                />

                <x-form.input 
                    label="Confirmar Contraseña"
                    name="password_confirmation"
                    type="password"
                    icon="fas fa-lock"
                    required
                />

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="flex items-start">
                        <input id="terms" name="terms" type="checkbox" required
                               class="mt-1 rounded border-spg-soft text-spg-primary focus:ring-spg-primary/20 w-4 h-4 transition-colors duration-300">
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            Acepto los 
                            <a href="{{ route('terms.show') }}" target="_blank" class="text-spg-primary hover:text-spg-secondary font-medium transition-colors duration-300">
                                Términos de Servicio
                            </a>
                            y la 
                            <a href="{{ route('policy.show') }}" target="_blank" class="text-spg-primary hover:text-spg-secondary font-medium transition-colors duration-300">
                                Política de Privacidad
                            </a>
                        </label>
                    </div>
                @endif

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-spg-primary to-spg-secondary text-white py-3.5 px-4 rounded-xl font-semibold hover:from-spg-secondary hover:to-spg-soft focus:outline-none focus:ring-2 focus:ring-spg-primary/20 focus:ring-offset-2 transition-all duration-300 shadow-saas hover:shadow-saas-lg hover:scale-[1.02] btn-ripple">
                    <i class="fas fa-user-plus mr-2"></i>
                    Crear Cuenta
                </button>
            </form>

            <div class="mt-6 text-center pt-6 border-t border-spg-beige/30">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}" class="text-spg-primary hover:text-spg-secondary font-semibold transition-colors duration-300">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>

        <!-- Link a Welcome -->
        <div class="mt-6 text-center animate-fade-in" style="animation-delay: 0.5s;">
            <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-spg-primary transition-colors duration-300 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>
