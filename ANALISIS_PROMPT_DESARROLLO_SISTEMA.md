# 🧠 ANÁLISIS COMPLETO DEL PROMPT - SISTEMA S.P.G

**Fecha de Análisis**: Diciembre 2025  
**Sistema**: SystemPG1 - Sistema de Producción Ganadera  
**Framework**: Laravel 12 + PHP 8.2 + MySQL 8

---

## 📋 RESUMEN EJECUTIVO

El prompt define un sistema ganadero completo con arquitectura limpia, lógica de negocio avanzada, gráficas analíticas, importación Excel y funcionalidades profesionales. El sistema actual está al **75%** y necesita extensión, refactorización y nuevos módulos.

---

## 🎯 OBJETIVOS PRINCIPALES DEL PROMPT

### 1. **Arquitectura Limpia** ✅
- ✅ Controllers delgados
- ✅ Form Requests para validación
- ✅ Services para lógica de negocio
- ✅ Repositories para consultas
- ✅ Jobs para importación
- ✅ Spatie Activitylog
- ✅ Spatie Backup
- ✅ DOMPDF
- ✅ ApexCharts
- ✅ maatwebsite/excel
- ✅ Tailwind + AdminLTE

### 2. **Módulos a Implementar/Refactorizar** (12 módulos)

| # | Módulo | Estado Actual | Acción Requerida | Prioridad |
|---|--------|---------------|------------------|-----------|
| 1 | **Vacas** | ✅ Refactorizado | ✅ Patrón oficial | - |
| 2 | **Crías/Nacimientos** | 🟡 CRUD básico | ⚠️ Extender + Refactorizar | 🔴 ALTA |
| 3 | **Producción Lechera** | 🟡 CRUD básico | ⚠️ Extender + Refactorizar | 🔴 ALTA |
| 4 | **Registros Reproductivos** | 🟡 CRUD básico | ⚠️ Extender + Refactorizar | 🔴 ALTA |
| 5 | **Uso de Medicamentos + Retiro** | ❌ No existe | ✅ Crear nuevo | 🔴 ALTA |
| 6 | **Inventario Bodega** | ❌ No existe | ✅ Crear nuevo | 🟡 MEDIA |
| 7 | **Mortalidad** | ❌ No existe | ✅ Crear nuevo | 🟡 MEDIA |
| 8 | **Rotación de Potreros** | 🟡 CRUD básico | ⚠️ Extender + Refactorizar | 🟡 MEDIA |
| 9 | **Pruebas Sanitarias** | 🟡 Parcial (Salud) | ⚠️ Extender + Refactorizar | 🔴 ALTA |

---

## 🔍 ANÁLISIS DETALLADO POR MÓDULO

### 📊 MÓDULO 1: Vacas ✅ (PATRÓN OFICIAL)

**Estado**: ✅ **Completamente refactorizado**

**Estructura actual**:
```
✅ app/Http/Requests/VacaStoreRequest.php
✅ app/Http/Requests/VacaUpdateRequest.php
✅ app/Repositories/VacaRepository.php
✅ app/Services/VacaService.php
✅ app/Http/Controllers/Admin/VacaController.php (refactorizado)
```

**Patrón a seguir**:
- Form Requests con validación completa
- Repository con eager loading
- Service con transacciones y logging
- Controller delgado (< 100 líneas)

**Falta**:
- ⚠️ Agregar campo `sexo` (enum: Macho, Hembra)
- ⚠️ Agregar campo `estado` (enum: Activa, Retirada, Muerta)

---

### 🍼 MÓDULO 2: Crías / Nacimientos 🟡

**Estado Actual**: CRUD básico existe

**Campos actuales**:
- `id_cria`, `id_vaca_madre`, `fecha_nacimiento`, `peso`, `estado_destete`, `observaciones`

**Campos a AGREGAR** (según prompt):
- ✅ `nombre_cria` (string, nullable)
- ✅ `sexo` (enum: Macho, Hembra) - **CRÍTICO**
- ✅ `fecha_tatuado` (date, nullable)
- ✅ `concepcion` (enum: IA, Monta Natural, Transferencia Embrionaria)
- ✅ `sinigan` (string, nullable) - código SINIGAN
- ✅ `fecha_destete` (date, nullable)

**Acciones requeridas**:
1. ⚠️ Crear migración para agregar campos
2. ⚠️ Actualizar Model `Cria`
3. ⚠️ Crear `CriaStoreRequest` y `CriaUpdateRequest`
4. ⚠️ Crear `CriaRepository`
5. ⚠️ Crear `CriaService`
6. ⚠️ Refactorizar `CriaController`
7. ⚠️ Actualizar vistas

**Lógica de negocio**:
- Calcular `estado_destete` automáticamente basado en `fecha_destete`

---

### 🥛 MÓDULO 3: Producción Lechera 🔴 (PRIORIDAD 1)

**Estado Actual**: CRUD básico existe

**Campos actuales**:
- `id_produccion`, `id_vaca`, `fecha`, `cantidad_leche`, `id_personal`, `observaciones`

**Campos a AGREGAR** (según prompt):
- ✅ `turno` (enum: AM, PM) - **CRÍTICO**
- ✅ `destino` (enum: Agroindustria, Lechero, Particular, Consumo)
- ✅ `valor_unidad` (decimal, nullable)
- ✅ `valor_total` (decimal, nullable, calculado)
- ✅ `excluida_por_retiro` (boolean, default false)

**Índice compuesto requerido**:
- `(id_vaca, fecha, turno)` para evitar duplicados

**Reglas de negocio**:
1. ⚠️ **Evitar duplicados** por (vaca, fecha, turno)
2. ⚠️ **Excluir vacas bajo retiro** automáticamente
3. ⚠️ **Soporte turnos AM/PM**
4. ⚠️ **Destino del producto** obligatorio
5. ⚠️ **Calcular valor_total** = cantidad_leche * valor_unidad

**Acciones requeridas**:
1. ⚠️ Crear migración para agregar campos + índice
2. ⚠️ Actualizar Model `ProduccionLechera`
3. ⚠️ Crear `ProduccionLecheraStoreRequest` y `UpdateRequest`
4. ⚠️ Crear `ProduccionLecheraRepository`
5. ⚠️ Crear `ProduccionLecheraService` con lógica de duplicados y retiro
6. ⚠️ Refactorizar `ProduccionLecheraController`
7. ⚠️ Actualizar vistas (agregar selectores de turno y destino)

---

### 🐄 MÓDULO 4: Registros Reproductivos 🔴 (PRIORIDAD 2)

**Estado Actual**: CRUD básico existe

**Campos actuales**:
- `id_registro`, `id_vaca`, `tipo_evento`, `fecha_evento`, `observaciones`

**Campos a AGREGAR** (según prompt):
- ✅ `resultado_palpacion` (enum: Vacia, Preñada) - cuando tipo_evento = 'Palpación'
- ✅ `tiempo_gestacion_dias` (integer, nullable)
- ✅ `fecha_probable_parto` (date, nullable, **CALCULADO**: fecha_palpacion + 283 días)
- ✅ `especialista` (string, nullable)
- ✅ `fecha_parto_anterior` (date, nullable) - para calcular días abiertos

**Lógica de negocio avanzada**:
1. ⚠️ **Fecha probable parto** = fecha_palpación + 283 días
2. ⚠️ **Días abiertos** = fecha_palpación – fecha_parto_anterior
3. ⚠️ **Alerta de preparto** (21 días antes)
4. ⚠️ **Alerta de celo** (cada 21 días)
5. ⚠️ **Intervalos parto–parto** (calcular automáticamente)

**Acciones requeridas**:
1. ⚠️ Crear migración para agregar campos
2. ⚠️ Actualizar enum `tipo_evento` para incluir "Palpación"
3. ⚠️ Actualizar Model `RegistroReproductivo`
4. ⚠️ Crear Form Requests
5. ⚠️ Crear `RegistroReproductivoRepository`
6. ⚠️ Crear `RegistroReproductivoService` con cálculos automáticos
7. ⚠️ Refactorizar Controller
8. ⚠️ Crear sistema de alertas (Jobs o eventos)

---

### 💉 MÓDULO 5: Uso de Medicamentos + Retiro 🔴 (PRIORIDAD 1)

**Estado Actual**: ❌ **NO EXISTE**

**Tablas requeridas**:
1. `medicamentos` (inventario de medicamentos)
2. `uso_medicamentos` (registro de uso)
3. `retiros` (registro de retiros de ordeño/producción)

**Estructura `medicamentos`**:
- `id_medicamento`, `nombre`, `tipo`, `principio_activo`, `via_administracion`, `dosis`, `periodo_retiro_dias`, `activo`

**Estructura `uso_medicamentos`**:
- `id_uso`, `id_medicamento`, `id_vaca`, `fecha_aplicacion`, `dosis_aplicada`, `id_personal`, `observaciones`

**Estructura `retiros`**:
- `id_retiro`, `id_vaca`, `id_uso_medicamento`, `fecha_inicio`, `fecha_fin`, `tipo_retiro` (enum: Ordeño, Producción), `activo`

**Lógica de negocio**:
1. ⚠️ **Tratamiento → añade retiro automático**
2. ⚠️ **Retiro → bloquea ordeño** (validar en ProduccionLecheraService)
3. ⚠️ **Retiro → bloquea producción** (validar en ProduccionLecheraService)
4. ⚠️ **Calcular fecha_fin** = fecha_inicio + periodo_retiro_dias del medicamento

**Acciones requeridas**:
1. ⚠️ Crear 3 migraciones (medicamentos, uso_medicamentos, retiros)
2. ⚠️ Crear 3 Models
3. ⚠️ Crear Form Requests para cada uno
4. ⚠️ Crear Repositories
5. ⚠️ Crear Services con lógica de retiro automático
6. ⚠️ Crear Controllers
7. ⚠️ Crear vistas CRUD
8. ⚠️ Integrar validación de retiro en ProduccionLecheraService

---

### 📦 MÓDULO 6: Inventario de Bodega 🟡

**Estado Actual**: ❌ **NO EXISTE**

**Tabla `inventario_bodega`**:
- `id_inventario`, `tipo` (enum: Medicamento, Alimento, Insumo, Otro), `nombre`, `cantidad`, `unidad_medida`, `fecha_vencimiento`, `proveedor`, `costo_unitario`, `activo`

**Acciones requeridas**:
1. ⚠️ Crear migración
2. ⚠️ Crear Model, Repository, Service, Requests, Controller
3. ⚠️ Crear vistas CRUD
4. ⚠️ Integrar con módulo Medicamentos

---

### 💀 MÓDULO 7: Mortalidad 🟡

**Estado Actual**: ❌ **NO EXISTE**

**Tabla `mortalidad`**:
- `id_mortalidad`, `fecha`, `hora` (time, nullable), `animal_type` (polimórfico: Vaca o Cria), `animal_id`, `clasificacion` (enum: Ternero, Novilla, Vaca, Toro), `peso` (decimal), `causa` (string), `acta` (string, nullable)

**Lógica de negocio**:
1. ⚠️ **Cambiar estado de la vaca a "Muerta" automáticamente**
2. ⚠️ **Relación polimórfica** con Vaca o Cria

**Acciones requeridas**:
1. ⚠️ Crear migración
2. ⚠️ Crear Model con relación polimórfica
3. ⚠️ Crear Service que actualice estado de vaca automáticamente
4. ⚠️ Crear Repository, Requests, Controller, vistas

---

### 🌱 MÓDULO 8: Rotación de Potreros 🟡

**Estado Actual**: CRUD básico en `asignacion_potreros`

**Campos actuales**:
- `id_asignacion`, `id_vaca`, `id_potrero`, `fecha_asignacion`, `observaciones`

**Campos a AGREGAR**:
- ✅ `fecha_salida` (date, nullable)
- ✅ `carga_ugg` (decimal, nullable) - Unidades Gran Ganado
- ✅ `peso_ingreso` (decimal, nullable)
- ✅ `aforo_kg` (decimal, nullable) - aforo en kg/ha
- ✅ `mantenimiento` (boolean, default false)

**Cálculos requeridos**:
1. ⚠️ **Días estancia** = fecha_salida - fecha_asignacion
2. ⚠️ **Días descanso** = días entre rotaciones del mismo potrero
3. ⚠️ **UGG** = Unidades Gran Ganado (calcular según peso)
4. ⚠️ **Aforo** = kg de pasto disponible por hectárea

**Acciones requeridas**:
1. ⚠️ Crear migración para agregar campos
2. ⚠️ Actualizar Model
3. ⚠️ Crear Service con métodos de cálculo
4. ⚠️ Refactorizar siguiendo patrón Vacas

---

### 🧪 MÓDULO 9: Pruebas Sanitarias 🔴

**Estado Actual**: Existe módulo `salud` básico

**Campos actuales**:
- `id_salud`, `id_vaca`, `tipo_enfermedad`, `fecha`, `tratamiento`, `observaciones`

**Campos a AGREGAR**:
- ✅ `tipo_prueba` (enum: Mastitis, Brucelosis, Tuberculosis, Otra)
- ✅ `resultado` (enum: Positivo, Negativo, Pendiente)
- ✅ `fecha_resultado` (date, nullable)
- ✅ `restriccion_ordeño` (boolean, default false)
- ✅ `inhabilitada` (boolean, default false)

**Lógica de negocio**:
1. ⚠️ **Mastitis → alerta automática**
2. ⚠️ **Brucelosis/Tuberculosis → inhabilitar automáticamente**
3. ⚠️ **Restricción de ordeño** → validar en ProduccionLecheraService

**Acciones requeridas**:
1. ⚠️ Extender tabla `salud` con nuevos campos
2. ⚠️ Actualizar Model
3. ⚠️ Crear Service con lógica de alertas y restricciones
4. ⚠️ Refactorizar siguiendo patrón Vacas

---

## 🧮 REGLAS DE LÓGICA DE NEGOCIO AVANZADA

### 🔄 Reproductiva

| Regla | Fórmula | Implementación |
|-------|---------|----------------|
| **Fecha probable parto** | fecha_palpación + 283 días | Service: `calcularFechaProbableParto()` |
| **Días abiertos** | fecha_palpación – fecha_parto_anterior | Service: `calcularDiasAbiertos()` |
| **Alerta de preparto** | 21 días antes de fecha probable | Job/Event: `AlertaPrepartoJob` |
| **Alerta de celo** | Cada 21 días | Job/Event: `AlertaCeloJob` |

### 📊 Producción

| Métrica | Cálculo | Implementación |
|---------|---------|----------------|
| **Curva lactancia** | Producción por días en lactancia | Repository: `getCurvaLactancia($vacaId)` |
| **Pico producción** | Máximo de producción en lactancia | Repository: `getPicoProduccion($vacaId)` |
| **Promedio vaca** | Promedio diario de producción | Repository: `getPromedioVaca($vacaId)` |
| **Producción mensual** | Suma producción por mes | Repository: `getProduccionMensual($mes, $anio)` |
| **Producción por potrero** | Agrupar por potrero | Repository: `getProduccionPorPotrero($potreroId)` |

### 🏥 Sanitaria

| Regla | Acción | Implementación |
|-------|--------|----------------|
| **Tratamiento → retiro** | Crear retiro automático | Service: `crearRetiroAutomatico()` |
| **Retiro → bloquea ordeño** | Validar en producción | Service: `validarRetiroOrdeño()` |
| **Mastitis → alerta** | Notificar automáticamente | Event: `MastitisDetectadaEvent` |
| **Brucelosis/Tuberculosis → inhabilitar** | Cambiar estado vaca | Service: `inhabilitarVaca()` |

---

## 📊 GRÁFICAS OBLIGATORIAS (ApexCharts)

### Dashboard General

1. ✅ **Producción diaria** (línea) - últimos 30 días
2. ✅ **Producción mensual** (barras) - últimos 12 meses
3. ✅ **Vacas por estado** (donut) - Activa, Retirada, Muerta
4. ✅ **Producción por potrero** (pastel) - distribución
5. ✅ **Ranking vacas productivas** (barras horizontales) - top 10
6. ✅ **Estado reproductivo** (barras) - Celo, Preñada, Lactancia, Descanso

### Gráficas por Módulo

Cada módulo debe tener sus gráficas individuales en su vista `index` o `dashboard`.

**Implementación**:
- Crear componente Blade para cada gráfica
- Usar ApexCharts (ya instalado)
- Datos desde Repository/Service
- Colores según paleta definida

---

## 📥 IMPORTACIÓN EXCEL (maatwebsite/excel)

### Módulos con Importación

1. ✅ **Producción diaria** → `ProduccionLecheraImportJob`
2. ✅ **Nacimientos** → `CriaImportJob`
3. ✅ **Mortalidad** → `MortalidadImportJob`
4. ✅ **Potreros** → `PotreroImportJob`
5. ✅ **Palpaciones** → `RegistroReproductivoImportJob`
6. ✅ **Medicamentos y uso** → `MedicamentoImportJob`, `UsoMedicamentoImportJob`
7. ✅ **Prueba mastitis** → `SaludImportJob`
8. ✅ **Producción mes-animal** → `ProduccionMensualImportJob`
9. ✅ **Pesos terneros** → `CriaPesoImportJob`

### Estructura Requerida

Para cada importación:
1. ✅ **Import Class** (`app/Imports/{Modulo}Import.php`)
2. ✅ **FormRequest** para validar archivo (`{Modulo}ImportRequest.php`)
3. ✅ **Previsualización** (vista con preview antes de importar)
4. ✅ **Job async** (`app/Jobs/{Modulo}ImportJob.php`)
5. ✅ **Reporte de errores** (mostrar filas con errores)
6. ✅ **Exportación PDF/Excel** (exportar datos)

---

## 🎨 UI/UX REQUERIDA

### Colores

```css
Primario: #28A745 (verde)
Secundario: #0D6EFD (azul)
Éxito: #198754
Advertencia: #FFC107
Peligro: #DC3545
Info: #17A2B8
```

### Iconos FontAwesome 6

- `fa-cow` - Vacas
- `fa-baby` - Crías
- `fa-syringe` - Medicamentos
- `fa-vial` - Pruebas sanitarias
- `fa-leaf` - Potreros
- `fa-glass-water` - Producción lechera
- `fa-arrows-rotate` - Rotación
- `fa-chart-line` - Gráficas
- `fa-triangle-exclamation` - Alertas

---

## 🔐 SEGURIDAD Y PERMISOS

### Roles Requeridos

1. **Administrador** - Acceso total
2. **Encargado ganadería** - Gestión completa
3. **Ordeñador** - Solo producción lechera
4. **Zootecnista** - Registros reproductivos, salud
5. **Auxiliar** - Solo lectura

### Permisos por Módulo

Cada módulo debe tener:
- `{modulo}.create`
- `{modulo}.view`
- `{modulo}.update`
- `{modulo}.delete`
- `{modulo}.export`
- `{modulo}.import`

**Implementación**: Usar Spatie Laravel Permission (ya instalado)

---

## 🧪 TESTING OBLIGATORIO

### Unit Tests

1. ✅ `calcularFechaProbableParto()` - RegistroReproductivoService
2. ✅ `calcularDiasAbiertos()` - RegistroReproductivoService
3. ✅ `crearRetiroAutomatico()` - UsoMedicamentoService
4. ✅ `validarDuplicadoProduccion()` - ProduccionLecheraService

### Feature Tests

1. ✅ Importación Excel (todos los módulos)
2. ✅ Restricciones de ordeño (vaca en retiro)
3. ✅ Alertas automáticas (preparto, celo, mastitis)
4. ✅ Cambio de estado automático (mortalidad → Muerta)

---

## 📦 ESTRUCTURA DE ARCHIVOS REQUERIDA

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       ├── VacaController.php ✅
│   │       ├── CriaController.php ⚠️
│   │       ├── ProduccionLecheraController.php ⚠️
│   │       ├── RegistroReproductivoController.php ⚠️
│   │       ├── MedicamentoController.php ❌
│   │       ├── UsoMedicamentoController.php ❌
│   │       ├── RetiroController.php ❌
│   │       ├── InventarioBodegaController.php ❌
│   │       ├── MortalidadController.php ❌
│   │       ├── AsignacionPotreroController.php ⚠️
│   │       └── SaludController.php ⚠️
│   └── Requests/
│       ├── Vaca/ ✅
│       ├── Cria/ ⚠️
│       ├── ProduccionLechera/ ⚠️
│       ├── RegistroReproductivo/ ⚠️
│       ├── Medicamento/ ❌
│       ├── UsoMedicamento/ ❌
│       ├── Retiro/ ❌
│       ├── InventarioBodega/ ❌
│       ├── Mortalidad/ ❌
│       ├── AsignacionPotrero/ ⚠️
│       └── Salud/ ⚠️
├── Services/
│   ├── VacaService.php ✅
│   ├── CriaService.php ⚠️
│   ├── ProduccionLecheraService.php ⚠️
│   ├── RegistroReproductivoService.php ⚠️
│   ├── MedicamentoService.php ❌
│   ├── UsoMedicamentoService.php ❌
│   ├── RetiroService.php ❌
│   ├── InventarioBodegaService.php ❌
│   ├── MortalidadService.php ❌
│   ├── AsignacionPotreroService.php ⚠️
│   └── SaludService.php ⚠️
├── Repositories/
│   ├── VacaRepository.php ✅
│   ├── CriaRepository.php ⚠️
│   ├── ProduccionLecheraRepository.php ⚠️
│   ├── RegistroReproductivoRepository.php ⚠️
│   ├── MedicamentoRepository.php ❌
│   ├── UsoMedicamentoRepository.php ❌
│   ├── RetiroRepository.php ❌
│   ├── InventarioBodegaRepository.php ❌
│   ├── MortalidadRepository.php ❌
│   ├── AsignacionPotreroRepository.php ⚠️
│   └── SaludRepository.php ⚠️
├── Jobs/
│   ├── ProduccionLecheraImportJob.php ❌
│   ├── CriaImportJob.php ❌
│   ├── MortalidadImportJob.php ❌
│   └── ... (más Jobs)
└── Imports/
    ├── ProduccionLecheraImport.php ❌
    ├── CriaImport.php ❌
    └── ... (más Imports)

routes/
└── modules/
    ├── vacas.php ✅
    ├── crias.php ⚠️
    ├── produccion-lechera.php ⚠️
    └── ... (más módulos)

resources/
└── views/
    └── modules/
        ├── vacas/ ✅
        ├── crias/ ⚠️
        ├── produccion-lechera/ ⚠️
        └── ... (más módulos)
```

---

## 🚀 PLAN DE ACCIÓN PRIORIZADO

### FASE 1: Extensión de Módulos Existentes (Semana 1-2)

#### Día 1-2: Producción Lechera 🔴
- [ ] Migración: agregar turno, destino, valor_unidad, valor_total, excluida_por_retiro
- [ ] Actualizar Model
- [ ] Crear Form Requests
- [ ] Crear Repository
- [ ] Crear Service (lógica duplicados + retiro)
- [ ] Refactorizar Controller
- [ ] Actualizar vistas

#### Día 3-4: Registros Reproductivos 🔴
- [ ] Migración: agregar campos palpación
- [ ] Actualizar Model
- [ ] Crear Form Requests
- [ ] Crear Repository
- [ ] Crear Service (cálculos automáticos)
- [ ] Refactorizar Controller
- [ ] Crear Jobs para alertas

#### Día 5: Crías 🟡
- [ ] Migración: agregar sexo, nombre_cria, etc.
- [ ] Actualizar Model
- [ ] Crear Form Requests
- [ ] Crear Repository
- [ ] Crear Service
- [ ] Refactorizar Controller

### FASE 2: Nuevos Módulos Críticos (Semana 3-4)

#### Día 6-8: Medicamentos + Retiro 🔴
- [ ] Crear 3 migraciones
- [ ] Crear 3 Models
- [ ] Crear Form Requests
- [ ] Crear Repositories
- [ ] Crear Services (lógica retiro automático)
- [ ] Crear Controllers
- [ ] Crear vistas CRUD
- [ ] Integrar validación en ProduccionLecheraService

#### Día 9-10: Pruebas Sanitarias 🔴
- [ ] Extender migración salud
- [ ] Actualizar Model
- [ ] Crear Service con alertas
- [ ] Refactorizar Controller
- [ ] Integrar restricciones

#### Día 11: Mortalidad 🟡
- [ ] Crear migración
- [ ] Crear Model (polimórfico)
- [ ] Crear Service (cambiar estado automático)
- [ ] Crear Repository, Requests, Controller, vistas

### FASE 3: Módulos Secundarios (Semana 5)

#### Día 12-13: Rotación Potreros 🟡
- [ ] Migración: agregar campos
- [ ] Actualizar Model
- [ ] Crear Service con cálculos
- [ ] Refactorizar Controller

#### Día 14: Inventario Bodega 🟡
- [ ] Crear migración
- [ ] Crear Model, Repository, Service, Requests, Controller
- [ ] Crear vistas CRUD

### FASE 4: Funcionalidades Avanzadas (Semana 6-7)

#### Día 15-17: Gráficas ApexCharts
- [ ] Dashboard general (6 gráficas)
- [ ] Gráficas por módulo
- [ ] Componentes Blade reutilizables

#### Día 18-21: Importación Excel
- [ ] Crear Import classes (9 módulos)
- [ ] Crear Jobs async
- [ ] Crear vistas de previsualización
- [ ] Crear reportes de errores
- [ ] Crear exportadores PDF/Excel

#### Día 22-24: Testing
- [ ] Unit tests (4 tests)
- [ ] Feature tests (4 tests)
- [ ] Cobertura > 70%

### FASE 5: Refinamiento (Semana 8)

#### Día 25-28: Optimización y Documentación
- [ ] Optimizar consultas N+1
- [ ] Documentación Markdown completa
- [ ] Revisión de seguridad
- [ ] Ajustes de UI/UX

---

## ✅ CHECKLIST FINAL

### Arquitectura
- [ ] Todos los módulos refactorizados siguiendo patrón Vacas
- [ ] Controllers delgados (< 100 líneas)
- [ ] Form Requests para todos los módulos
- [ ] Services con lógica de negocio
- [ ] Repositories con eager loading
- [ ] Jobs para importación async

### Funcionalidad
- [ ] Todos los módulos implementados
- [ ] Lógica de negocio avanzada implementada
- [ ] Alertas automáticas funcionando
- [ ] Validaciones de retiro funcionando
- [ ] Cálculos automáticos funcionando

### UI/UX
- [ ] Gráficas ApexCharts implementadas
- [ ] Colores según paleta
- [ ] Iconos FontAwesome 6
- [ ] Vistas modernas y responsivas

### Seguridad
- [ ] Roles implementados
- [ ] Permisos por módulo
- [ ] Validaciones de seguridad

### Testing
- [ ] Unit tests completos
- [ ] Feature tests completos
- [ ] Cobertura > 70%

### Documentación
- [ ] Documentación Markdown completa
- [ ] PHPDoc en todos los métodos
- [ ] Guías de uso

---

## 🎯 CONCLUSIÓN

El prompt define un sistema ganadero profesional y completo. El estado actual (75%) requiere:

1. **Extensión** de 5 módulos existentes
2. **Creación** de 4 módulos nuevos
3. **Refactorización** de todos los módulos siguiendo patrón Vacas
4. **Implementación** de lógica de negocio avanzada
5. **Gráficas** analíticas con ApexCharts
6. **Importación** Excel completa
7. **Testing** exhaustivo

**Tiempo estimado total**: 8 semanas  
**Prioridad inmediata**: Producción Lechera (Día 1-2)

---

**¿Listo para comenzar con la FASE 1 - Día 1 (Producción Lechera)?**

