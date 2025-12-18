Actúa como:
Diseñador UI/UX Senior + Frontend Engineer experto en Laravel 12, TailwindCSS, AdminLTE, Blade Components, Alpine.js y diseño de interfaces para sistemas de agricultura/ganadería.

Quiero transformar el frontend actual (welcome, login, register, dashboard y módulos) que viene por defecto en Laravel, en una interfaz moderna, profesional, coherente con un sistema ganadero, usando el siguiente logo:

🎯 OBJETIVO GENERAL DEL FRONTEND

Crear una identidad visual completa para el sistema S.P.G – Sistema de Producción Ganadera, con un frontend que:

Sea moderno

Sea mínimo, limpio y profesional

Evite la sobrecarga de colores y contrastes

Use colores ganaderos suaves y elegantes

Integre Tailwind + AdminLTE correctamente

Muestre iconografía adecuada

Use cards, tablas y formularios modernos

Sea coherente entre todos los módulos

Use ApexCharts en dashboards con diseño uniforme

Se adapte al theme del logo

El sistema debe verse como una plataforma SaaS ganadera premium.

🟩 1. PALETA DE COLORES OFICIAL (Basada en el logo S.P.G)

Verde Primario (logo vaca):
#1F713E

Verde Medio (pasto):
#4BAE4F

Verde Suave (pasto claro):
#89C65B

Marrón Tierra:
#B48A61

Beige Suelo claro:
#E8D9C3

Amarillo Sol:
#EBC365

Azul Neutro (interfaz profesional):
#2D4059

Reglas:

Pantallas claras → usar beige suave + verde suave

Botones → verde primario

Sombras → gris azulado suave

Iconos → azul neutro o verde primario

Evitar saturaciones de amarillo (solo toques de luz)

🧩 2. ICONOGRAFÍA (FontAwesome 6)

Usar estos íconos obligatoriamente:

Módulo	Icono FA
Vacas	fa-cow
Crías	fa-baby
Producción Leche	fa-glass-water
Medicamentos	fa-syringe
Salud / Mastitis	fa-vial
Pruebas sanitarias	fa-microscope
Brucelosis	fa-biohazard
Tuberculosis	fa-lungs
Reproducción	fa-seedling
Potreros	fa-leaf
Rotación	fa-arrows-rotate
Bodega	fa-boxes-stacked
Mortalidad	fa-skull-crossbones
Dashboard	fa-chart-line
Alertas	fa-triangle-exclamation
Configuración	fa-gear
Usuarios	fa-users
🏛 3. ESTILO GLOBAL DE LA INTERFAZ
✔ Diseño general:

Layout de AdminLTE pero simplificado y modernizado con Tailwind

Barra lateral con verde primario + degradado suave

Header blanco con borde inferior verde suave

Cards con bordes redondeados 10–14px

Sombra suave tipo SaaS

Inputs estilo Tailwind con borde verde claro

Tablas con filas alternas en beige muy suave

Gráficas ApexCharts integradas en cards visuales

✔ Espaciado:

Padding global: px-6 py-4

Cards: p-4 md:p-6

Tablas: p-4 con rounded-xl

✔ Tipografías:

Fuente principal: "Inter"

Secundaria para títulos: "Poppins"

Tamaños:

Title: text-2xl font-semibold

Card title: text-lg font-medium

Labels form: text-sm font-medium text-gray-600

🖥 4. PÁGINAS A REDISEÑAR (OBLIGATORIO)
🔹 4.1 Welcome Page

Fondo degradado verde → beige

Logo S.P.G centrado

Slogan: "Sistema de Producción Ganadera — Control Total, Decisiones Inteligentes"

Botones grandes Login / Register

Footer minimalista

🔹 4.2 Login

Card centrada estilo SaaS

Logo pequeño arriba

Título: "Acceso al Sistema S.P.G"

Inputs con íconos

Botón verde primario grande

🔹 4.3 Register

Campos mínimos

Misma estética que login

🔹 4.4 Dashboard principal

Debe mostrar:

Gráfica Producción Últimos 30 días

Gráfica Estados reproductivos

Gráfica Vacas por estado

Cards:

Total Vacas

Vacas en ordeño

Vacas preñadas

Vacas enfermas

Producción hoy

Promedio por vaca

Cards en verde, azul y beige suave.

📊 5. DISEÑO PARA MÓDULOS CRUD

Cada módulo debe tener:

Encabezado:

Icono FA correspondiente

Nombre del módulo

Breadcrumb estilo Tailwind

Tabla:

DataTables con:

Borde redondeado

Hover con verde claro

Encabezados verde oscuro

Formulario:

Grid 2 columnas

Inputs con borde verde suave

Selects estilizados con Tailwind

Botón verde primario grande

Validación visual

Vista Show:

Card con información

Badge por estado

Gráficas internas si aplica

📦 6. IMPLEMENTACIÓN TÉCNICA PARA CURSOR

Cursor debe generar:

✔ Blade Components reutilizables:

<x-card>

<x-table>

<x-form.input>

<x-form.select>

<x-badge>

<x-chart> (ApexCharts)

✔ Archivos:

resources/views/layouts/app.blade.php

resources/views/welcome.blade.php

resources/views/auth/login.blade.php

resources/views/auth/register.blade.php

resources/views/dashboard.blade.php

Componentes en /resources/views/components

✔ Tailwind Config:

Agregar colores SPG:

extend: {
  colors: {
    spg: {
      primary: '#1F713E',
      secondary: '#4BAE4F',
      soft: '#89C65B',
      brown: '#B48A61',
      beige: '#E8D9C3',
      sun: '#EBC365',
      deepblue: '#2D4059'
    }
  }
}