# 🚀 PLAN DE IMPLEMENTACIÓN - FASES 10-15 SISTEMA S.P.G

**Fecha**: 11 de Diciembre de 2025  
**Estado**: ⏳ **EN PROGRESO**  
**Regla Principal**: ✅ **NO MODIFICAR código existente, solo EXTENDER**

---

## 📋 RESUMEN EJECUTIVO

Este documento detalla el plan de implementación para completar las FASES 10-15 del sistema S.P.G, siguiendo estrictamente la regla de **NO MODIFICAR** código existente, solo **EXTENDER** y **MEJORAR**.

---

## ✅ ESTADO ACTUAL DE CADA FASE

### **FASE 10: Pruebas Sanitarias** ⚠️ 80% COMPLETA

**Lo que YA existe**:
- ✅ Migración con campos de pruebas sanitarias
- ✅ Model Salud con scopes y accessors
- ✅ SaludService con lógica de procesamiento
- ✅ Integración con ProduccionLecheraService (validación de restricción)
- ✅ Alertas automáticas
- ✅ Inhabilitación de vacas

**Lo que FALTA**:
- ⚠️ Marcar producción como `excluida_por_sanidad` cuando hay mastitis positiva
- ⚠️ Campo `excluida_por_sanidad` en tabla `produccion_lechera` (nueva migración)
- ⚠️ Vista de gráficas de pruebas sanitarias
- ⚠️ Endpoint API para gráficas

**Acciones Requeridas**:
1. Crear migración nueva para agregar `excluida_por_sanidad` a `produccion_lechera`
2. Extender ProduccionLecheraService para marcar exclusión por sanidad
3. Agregar gráficas ApexCharts en módulo Salud
4. Crear endpoint API para datos de gráficas

---

### **FASE 11: Importación Excel Completa** ⚠️ 85% COMPLETA

**Lo que YA existe**:
- ✅ 12 Import classes implementadas:
  1. AlimentacionImport ✅
  2. AsignacionPotreroImport ✅
  3. CriaImport ✅
  4. InventarioBodegaImport ✅
  5. MedicamentoImport ✅
  6. MortalidadImport ✅
  7. PotreroImport ✅
  8. ProduccionLecheraImport ✅
  9. RegistroReproductivoImport ✅
  10. RetiroImport ✅
  11. SaludImport ✅
  12. UsoMedicamentoImport ✅

**Lo que FALTA**:
- ⚠️ Jobs asincrónicos para importación masiva (algunos módulos)
- ⚠️ Plantillas descargables por módulo
- ⚠️ Mejorar validación de estructura Excel

**Acciones Requeridas**:
1. Crear Jobs para importación asíncrona (opcional, mejora)
2. Agregar método para descargar plantillas Excel
3. Mejorar validación de estructura

---

### **FASE 12: Gráficas ApexCharts por Módulo** ⚠️ 60% COMPLETA

**Lo que YA existe**:
- ✅ Dashboard principal (6 gráficas)
- ✅ Producción Lechera (3 gráficas)
- ✅ Registros Reproductivos (3 gráficas) - según análisis
- ✅ Crías (gráficas) - según análisis
- ✅ Mortalidad (gráficas) - según análisis

**Lo que FALTA**:
- ⚠️ Verificar gráficas en Registros Reproductivos
- ⚠️ Verificar gráficas en Crías
- ⚠️ Verificar gráficas en Mortalidad
- ⚠️ Gráficas en Potreros
- ⚠️ Gráficas en Salud/Pruebas Sanitarias
- ⚠️ Endpoints API para todas las gráficas

**Acciones Requeridas**:
1. Verificar qué gráficas existen realmente
2. Completar gráficas faltantes
3. Crear endpoints API para datos
4. Agregar gráficas en vistas

---

### **FASE 13: Rotación de Potreros (Extensión)** ⚠️ 70% COMPLETA

**Lo que YA existe**:
- ✅ Model AsignacionPotrero con campos básicos
- ✅ Cálculo automático de dias_estancia
- ✅ Cálculo automático de UGG
- ✅ Scopes para activas/finalizadas

**Lo que FALTA**:
- ⚠️ Campo `carga_ugg` (UGG por hectárea)
- ⚠️ Campo `aforo_kg` (aforo en kg por hectárea)
- ⚠️ Campo `peso_ingreso` (peso de la vaca al ingresar)
- ⚠️ Campo `mantenimiento` (boolean)
- ⚠️ Cálculo de `dias_descanso` entre rotaciones
- ⚠️ Gráficas de uso y descanso

**Acciones Requeridas**:
1. Crear migración nueva para agregar campos faltantes
2. Extender Model con nuevos campos
3. Extender Service con lógica de cálculo
4. Agregar gráficas de rotación

---

### **FASE 14: Inventario de Bodega** ✅ 100% COMPLETA

**Lo que YA existe**:
- ✅ Model InventarioBodega completo
- ✅ Model MovimientoInventario
- ✅ InventarioBodegaService con entradas/salidas/ajustes
- ✅ InventarioBodegaRepository
- ✅ Controller completo
- ✅ Vistas CRUD
- ✅ Importación/Exportación Excel
- ✅ Alertas de stock bajo y vencimiento

**Estado**: ✅ **COMPLETO - NO REQUIERE ACCIÓN**

---

### **FASE 15: Testing y Calidad Final** 🔴 5% COMPLETA

**Lo que YA existe**:
- ✅ PHPUnit 11.5.3 instalado
- ✅ 14 tests Feature (solo Jetstream por defecto)

**Lo que FALTA**:
- ❌ Tests unitarios para Services
- ❌ Tests unitarios para Repositories
- ❌ Tests de integración
- ❌ Tests Feature para módulos
- ❌ CI/CD

**Acciones Requeridas**:
1. Crear tests unitarios para lógica crítica
2. Crear tests de integración
3. Crear tests Feature
4. Configurar CI/CD

---

## 🎯 PLAN DE IMPLEMENTACIÓN DETALLADO

### **PRIORIDAD 1: FASE 10 - Completar Pruebas Sanitarias** 🔴

#### **Tarea 1.1: Agregar campo excluida_por_sanidad** (Nueva migración)

**Archivo**: `database/migrations/YYYY_MM_DD_HHMMSS_add_excluida_por_sanidad_to_produccion_lechera_table.php`

**Contenido**:
```php
Schema::table('produccion_lechera', function (Blueprint $table) {
    $table->boolean('excluida_por_sanidad')->default(false)->after('excluida_por_retiro');
});
```

#### **Tarea 1.2: Extender ProduccionLecheraService**

**NO MODIFICAR** el método `create()` existente.  
**CREAR** método nuevo `marcarExcluidaPorSanidad()` y llamarlo desde SaludService.

#### **Tarea 1.3: Extender SaludService**

**AGREGAR** lógica para marcar producción como excluida cuando hay mastitis positiva.

#### **Tarea 1.4: Agregar gráficas en Salud**

**CREAR** endpoint API y agregar gráficas ApexCharts en vista.

---

### **PRIORIDAD 2: FASE 12 - Completar Gráficas** 🟡

#### **Tarea 2.1: Verificar gráficas existentes**

Revisar qué módulos tienen gráficas y cuáles faltan.

#### **Tarea 2.2: Completar gráficas faltantes**

Crear métodos en Repositories y agregar gráficas en vistas.

---

### **PRIORIDAD 3: FASE 13 - Extender Rotación Potreros** 🟡

#### **Tarea 3.1: Crear migración nueva**

Agregar campos: `carga_ugg`, `aforo_kg`, `peso_ingreso`, `mantenimiento`.

#### **Tarea 3.2: Extender Model**

Agregar campos a `$fillable` y métodos de cálculo.

#### **Tarea 3.3: Extender Service**

Agregar lógica de cálculo de `dias_descanso` y `carga_ugg`.

---

### **PRIORIDAD 4: FASE 11 - Mejorar Importación** 🟢

#### **Tarea 4.1: Crear Jobs asincrónicos**

Para importaciones masivas.

#### **Tarea 4.2: Agregar plantillas descargables**

Método para descargar plantillas Excel por módulo.

---

### **PRIORIDAD 5: FASE 15 - Testing** 🔴

#### **Tarea 5.1: Tests unitarios críticos**

- Fecha probable de parto
- Retiro
- Bloqueo de ordeño

#### **Tarea 5.2: Tests de integración**

- Producción + retiro
- Producción + sanidad
- Importación Excel

---

## 📝 REGLAS DE IMPLEMENTACIÓN

### **✅ PERMITIDO**
- Crear nuevas migraciones
- Agregar métodos nuevos a Services/Repositories
- Crear nuevos archivos
- Extender modelos con nuevos campos
- Agregar nuevas rutas
- Crear nuevas vistas

### **❌ PROHIBIDO**
- Modificar migraciones existentes
- Eliminar código existente
- Reescribir métodos existentes
- Cambiar lógica existente
- Eliminar archivos
- Modificar rutas existentes sin necesidad

---

## 🚀 ORDEN DE IMPLEMENTACIÓN

1. **FASE 10** - Completar Pruebas Sanitarias (1-2 días)
2. **FASE 12** - Completar Gráficas (2-3 días)
3. **FASE 13** - Extender Rotación Potreros (1-2 días)
4. **FASE 11** - Mejorar Importación (1 día)
5. **FASE 15** - Testing (5-7 días)

**Total Estimado**: 10-15 días

---

**Última Actualización**: 11 de Diciembre de 2025

