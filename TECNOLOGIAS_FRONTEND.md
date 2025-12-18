# 🎨 Tecnologías Frontend - Sistema S.P.G

Este documento detalla todas las tecnologías utilizadas en el frontend del Sistema de Producción Ganadera (S.P.G) y explica la función de cada una.

---

## 📦 Tecnologías Principales

### 1. **Vite 6.0.11**
**Función:** Herramienta de construcción y desarrollo frontend ultra-rápida.

**¿Qué hace?**
- Compila y procesa archivos CSS y JavaScript de forma extremadamente rápida
- Proporciona Hot Module Replacement (HMR) para recarga instantánea durante el desarrollo
- Optimiza y minifica los assets para producción
- Integra con Laravel mediante el plugin `laravel-vite-plugin`
- Procesa archivos desde `resources/css/app.css` y `resources/js/app.js`

**Uso en el proyecto:**
```javascript
// vite.config.js
plugins: [
    laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        refresh: true,
    }),
]
```

---

### 2. **TailwindCSS 3.4.0**
**Función:** Framework CSS utility-first para diseño rápido y consistente.

**¿Qué hace?**
- Proporciona clases utilitarias para estilizar componentes sin escribir CSS personalizado
- Permite diseño responsive con clases como `md:`, `lg:`, `xl:`
- Sistema de colores personalizado configurado para el tema SPG
- Utilidades para espaciado, tipografía, sombras, animaciones, etc.

**Configuración personalizada:**
- **Colores SPG:** Paleta de colores ganadera (verde primario, secundario, beige, etc.)
- **Fuentes:** Inter (sans-serif) y Poppins (display)
- **Sombras personalizadas:** `shadow-saas`, `shadow-saas-lg`, `shadow-saas-xl`
- **Animaciones:** fade-in, slide-in, scale-in, bounce-subtle, etc.

**Uso en el proyecto:**
```blade
<div class="bg-spg-primary text-white rounded-card shadow-saas-lg">
    <!-- Contenido -->
</div>
```

---

### 3. **Alpine.js 3.15.2**
**Función:** Framework JavaScript ligero para interactividad en el frontend.

**¿Qué hace?**
- Proporciona reactividad y manipulación del DOM sin necesidad de frameworks pesados
- Permite crear componentes interactivos directamente en HTML con atributos especiales
- Muy ligero (solo ~15KB minificado)
- Ideal para proyectos que necesitan interactividad sin la complejidad de React/Vue

**Características principales:**
- `x-data`: Define el estado del componente
- `x-show`: Muestra/oculta elementos
- `x-if`: Renderizado condicional
- `x-for`: Iteración sobre arrays
- `@click`, `@submit`: Event listeners
- `x-cloak`: Oculta elementos hasta que Alpine se inicialice

**Uso en el proyecto:**
```html
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Contenido</div>
</div>
```

---

### 4. **ApexCharts 5.3.6**
**Función:** Biblioteca de gráficos interactivos y modernos.

**¿Qué hace?**
- Crea gráficos interactivos y responsivos (líneas, barras, donas, áreas, etc.)
- Animaciones suaves y transiciones
- Interactividad: zoom, pan, tooltips, leyendas
- Totalmente personalizable con temas y estilos
- Soporte para datos en tiempo real

**Uso en el proyecto:**
- Dashboard principal: gráficos de producción diaria, mensual, por potrero
- Módulos de análisis: gráficos de mortalidad, nacimientos, registros reproductivos
- Visualización de inventario: stock por tipo, movimientos, próximos a vencer

**Ejemplo de uso:**
```javascript
const chart = new ApexCharts(document.querySelector("#chart"), {
    series: [{ name: 'Producción', data: [30, 40, 35] }],
    chart: { type: 'line' },
    xaxis: { categories: ['Ene', 'Feb', 'Mar'] }
});
chart.render();
```

---

## 🔧 Plugins y Extensiones de TailwindCSS

### 5. **@tailwindcss/forms 0.5.7**
**Función:** Plugin que estiliza automáticamente los elementos de formulario.

**¿Qué hace?**
- Aplica estilos consistentes a inputs, selects, textareas, checkboxes, radios
- Elimina las diferencias de estilo entre navegadores
- Proporciona clases utilitarias para personalizar formularios
- Mejora la accesibilidad y UX de los formularios

**Uso en el proyecto:**
```blade
<input type="text" class="form-input rounded-md border-spg-soft">
```

---

### 6. **@tailwindcss/typography 0.5.10**
**Función:** Plugin para estilizar contenido tipográfico (artículos, blogs, contenido markdown).

**¿Qué hace?**
- Proporciona clases `prose` para estilizar contenido de texto largo
- Aplica estilos consistentes a títulos, párrafos, listas, enlaces, etc.
- Mejora la legibilidad del contenido
- Personalizable con variantes de tamaño y color

**Uso en el proyecto:**
```blade
<article class="prose prose-lg max-w-none">
    <h1>Título</h1>
    <p>Contenido...</p>
</article>
```

---

### 7. **@tailwindcss/vite 4.0.0**
**Función:** Plugin de Vite para integración optimizada de TailwindCSS.

**¿Qué hace?**
- Integra TailwindCSS directamente con Vite
- Mejora el rendimiento de compilación
- Habilita purging automático de CSS no utilizado
- Optimiza el proceso de build

---

## 🛠️ Herramientas de Desarrollo

### 8. **PostCSS 8.4.32**
**Función:** Herramienta para transformar CSS con plugins de JavaScript.

**¿Qué hace?**
- Procesa CSS con plugins (Autoprefixer, TailwindCSS, etc.)
- Agrega prefijos de navegadores automáticamente
- Optimiza y transforma el CSS antes de enviarlo al navegador
- Esencial para el funcionamiento de TailwindCSS

---

### 9. **Autoprefixer 10.4.16**
**Función:** Plugin de PostCSS que agrega prefijos de navegadores automáticamente.

**¿Qué hace?**
- Agrega prefijos como `-webkit-`, `-moz-`, `-ms-` automáticamente
- Asegura compatibilidad con navegadores antiguos
- Basado en la base de datos Can I Use
- Reduce el trabajo manual de escribir prefijos

**Ejemplo:**
```css
/* Escribes: */
display: flex;

/* Autoprefixer genera: */
display: -webkit-box;
display: -ms-flexbox;
display: flex;
```

---

### 10. **Axios 1.7.4**
**Función:** Cliente HTTP basado en Promesas para hacer peticiones AJAX.

**¿Qué hace?**
- Realiza peticiones HTTP (GET, POST, PUT, DELETE) de forma sencilla
- Soporte para interceptores de request/response
- Manejo automático de errores
- Transformación automática de datos JSON
- Soporte para cancelación de peticiones

**Uso en el proyecto:**
```javascript
axios.get('/api/datos')
    .then(response => console.log(response.data))
    .catch(error => console.error(error));
```

---

### 11. **Laravel Vite Plugin 1.2.0**
**Función:** Plugin oficial de Laravel para integrar Vite con Blade.

**¿Qué hace?**
- Proporciona la directiva `@vite()` para incluir assets en Blade
- Maneja la inyección de assets en desarrollo y producción
- Soporta Hot Module Replacement (HMR) durante el desarrollo
- Genera los tags correctos para CSS y JS

**Uso en el proyecto:**
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

---

### 12. **Concurrently 9.0.1**
**Función:** Herramienta para ejecutar múltiples comandos en paralelo.

**¿Qué hace?**
- Permite ejecutar varios procesos simultáneamente
- Útil para desarrollo: ejecutar servidor Laravel, Vite, y otros procesos a la vez
- Colorea la salida de cada proceso para mejor visibilidad

**Uso en el proyecto:**
```json
"dev": "concurrently \"php artisan serve\" \"npm run dev\""
```

---

## 🎨 Recursos Externos (CDN)

### 13. **Font Awesome 6.4.0**
**Función:** Biblioteca de iconos vectoriales más popular del mundo.

**¿Qué hace?**
- Proporciona miles de iconos escalables y personalizables
- Iconos en formato de fuente (ligeros y escalables)
- Clases CSS simples para usar iconos: `<i class="fas fa-user"></i>`
- Variantes: Solid (fas), Regular (far), Light (fal), Brands (fab)

**Uso en el proyecto:**
```blade
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<i class="fas fa-cow"></i>
<i class="fas fa-chart-line"></i>
<i class="fas fa-user"></i>
```

---

### 14. **Google Fonts (Inter & Poppins)**
**Función:** Servicio de fuentes web de Google.

**¿Qué hace?**
- Proporciona fuentes tipográficas de alta calidad
- Carga optimizada de fuentes
- Varios pesos disponibles (300, 400, 500, 600, 700, 800)

**Fuentes utilizadas:**
- **Inter:** Fuente sans-serif principal para el cuerpo del texto
- **Poppins:** Fuente display para títulos y encabezados

**Uso en el proyecto:**
```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap');
```

---

## 🎯 Tecnologías de Laravel Frontend

### 15. **Blade Templates**
**Función:** Motor de plantillas de Laravel.

**¿Qué hace?**
- Permite crear vistas reutilizables con sintaxis simple
- Componentes Blade para código reutilizable
- Directivas especiales: `@if`, `@foreach`, `@vite`, `@auth`, etc.
- Herencia de layouts con `@extends` y `@section`

**Uso en el proyecto:**
```blade
@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>{{ $title }}</h1>
    </div>
@endsection
```

---

### 16. **Livewire 3.6.4** (Backend pero afecta Frontend)
**Función:** Framework full-stack de Laravel para crear interfaces dinámicas.

**¿Qué hace?**
- Permite crear componentes interactivos sin escribir JavaScript
- Actualización del DOM automática mediante AJAX
- Comunicación bidireccional entre frontend y backend
- Validación en tiempo real sin recargar la página

---

## 📐 Estilos y Componentes Personalizados

### 17. **CSS Personalizado (app.css)**
**Función:** Estilos y componentes personalizados del proyecto.

**Componentes definidos:**
- **Animaciones:** fade-in, slide-in, scale-in, bounce-subtle
- **Efectos:** btn-ripple (efecto de ondas en botones)
- **Gradientes animados:** `gradient-animated` para fondos dinámicos
- **Skeleton loaders:** Para estados de carga
- **Glassmorphism:** Efecto de vidrio esmerilado
- **Utilidades:** text-shadow, backdrop-blur-saas

**Ejemplo:**
```css
.gradient-animated {
    background: linear-gradient(-45deg, #1F713E, #4BAE4F, #89C65B, #E8D9C3);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
}
```

---

## 🎨 Paleta de Colores SPG

**Colores personalizados definidos en Tailwind:**

```javascript
spg: {
    primary: '#1F713E',    // Verde oscuro principal
    secondary: '#4BAE4F',  // Verde secundario
    soft: '#89C65B',       // Verde suave
    brown: '#B48A61',      // Marrón
    beige: '#E8D9C3',      // Beige
    sun: '#EBC365',        // Amarillo sol
    deepblue: '#2D4059',   // Azul oscuro
}
```

---

## 📊 Resumen de Funciones por Categoría

### **Construcción y Desarrollo:**
- **Vite:** Compilación y desarrollo rápido
- **PostCSS:** Procesamiento de CSS
- **Autoprefixer:** Compatibilidad de navegadores
- **Laravel Vite Plugin:** Integración Laravel-Vite

### **Estilos y Diseño:**
- **TailwindCSS:** Framework CSS utility-first
- **@tailwindcss/forms:** Estilos de formularios
- **@tailwindcss/typography:** Estilos tipográficos
- **CSS Personalizado:** Componentes y animaciones custom

### **Interactividad:**
- **Alpine.js:** Reactividad y manipulación DOM
- **Axios:** Peticiones HTTP/AJAX

### **Visualización de Datos:**
- **ApexCharts:** Gráficos interactivos

### **Recursos Externos:**
- **Font Awesome:** Iconos
- **Google Fonts:** Tipografías (Inter & Poppins)

### **Templates:**
- **Blade:** Motor de plantillas Laravel
- **Livewire:** Componentes interactivos full-stack

---

## 🚀 Flujo de Trabajo Frontend

1. **Desarrollo:**
   - Escribes código en `resources/css/app.css` y `resources/js/app.js`
   - Vite detecta cambios y recompila automáticamente
   - Hot Module Replacement actualiza el navegador sin recargar

2. **Compilación:**
   - TailwindCSS procesa las clases utilitarias
   - PostCSS y Autoprefixer optimizan el CSS
   - Vite genera archivos optimizados en `public/build/`

3. **Producción:**
   - Assets minificados y optimizados
   - CSS purgado (solo clases utilizadas)
   - JavaScript optimizado y tree-shaken

---

## 📝 Comandos Útiles

```bash
# Desarrollo (con HMR)
npm run dev

# Compilación para producción
npm run build

# Desarrollo completo (Laravel + Vite + Queue + Logs)
composer dev
```

---

## 🔗 Versiones Actuales

| Tecnología | Versión | Tipo |
|------------|---------|------|
| Vite | 6.0.11 | Build Tool |
| TailwindCSS | 3.4.0 | CSS Framework |
| Alpine.js | 3.15.2 | JS Framework |
| ApexCharts | 5.3.6 | Chart Library |
| Font Awesome | 6.4.0 | Icon Library |
| PostCSS | 8.4.32 | CSS Processor |
| Autoprefixer | 10.4.16 | CSS Plugin |
| Axios | 1.7.4 | HTTP Client |

---

*Documento generado para el Sistema de Producción Ganadera (S.P.G)*

