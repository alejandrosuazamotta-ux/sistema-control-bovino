# 🚀 PLAN DE IMPLEMENTACIÓN DEFINITIVO - FASES 10-15 SISTEMA S.P.G

**Fecha**: 11 de Diciembre de 2025  
**Estado**: ⏳ **EN PROGRESO**  
**Regla Principal**: ✅ **NO MODIFICAR código existente, solo EXTENDER**

---

## 🛑 REGLAS OBLIGATORIAS

1. ✅ **NO MODIFICAR** ningún archivo existente salvo que se indique explícitamente
2. ✅ **Solo EXTENDER, ADICIONAR o MEJORAR** funcionalidades
3. ✅ **Compatibilidad total** con código existente
4. ✅ **Migraciones nuevas** (nunca editar migraciones antiguas)
5. ✅ **Services y Repositories nuevos** cuando aplique
6. ✅ **Sin romper** rutas, vistas ni validaciones existentes

---

## 📊 ESTADO ACTUAL VERIFICADO

### **✅ Lo que YA EXISTE y FUNCIONA**

#### **FASE 10 (Pruebas Sanitarias) - 95% COMPLETA**
- ✅ Migración con campos de pruebas sanitarias en `salud`
- ✅ Model Salud con scopes y accessors
- ✅ SaludService con lógica de procesamiento
- ✅ Integración con ProduccionLecheraService (validación de restricción)
- ✅ Alertas automáticas
- ✅ Inhabilitación de vacas
- ⚠️ **FALTA**: Campo `excluida_por_sanidad` en `produccion_lechera`
- ⚠️ **FALTA**: Marcar producción como excluida cuando hay mastitis positiva

#### **FASE 11 (Importación Excel) - 100% COMPLETA**
- ✅ 12 Import classes implementadas
- ✅ Previsualización antes de importar
- ✅ Validación de datos
- ⚠️ **FALTA**: Jobs asincrónicos (opcional, mejora)

#### **FASE 12 (Gráficas) - 70% COMPLETA**
- ✅ Dashboard principal (6 gráficas)
- ✅ Producción Lechera (3 gráficas)
- ✅ Salud (3 gráficas según código)
- ⚠️ **FALTA VERIFICAR**: Registros Reproductivos, Crías, Mortalidad, Potreros

#### **FASE 13 (Rotación Potreros) - 80% COMPLETA**
- ✅ Campos básicos: fecha_salida, dias_estancia, dias_descanso, ugg_total
- ⚠️ **FALTA**: carga_ugg, aforo_kg, peso_ingreso, mantenimiento

#### **FASE 14 (Inventario Bodega) - 100% COMPLETA**
- ✅ CRUD completo
- ✅ Movimientos (entradas/salidas/ajustes)
- ✅ Alertas de stock bajo
- ✅ Alertas de vencimiento

#### **FASE 15 (Testing) - 5% COMPLETA**
- ✅ PHPUnit instalado
- ❌ **FALTA**: Tests unitarios, integración, feature

---

## 🎯 PLAN DE IMPLEMENTACIÓN POR FASE

### **FASE 10: PRUEBAS SANITARIAS - COMPLETAR (5% faltante)**

#### **Tarea 1: Agregar campo `excluida_por_sanidad` a produccion_lechera**
- [ ] Crear migración nueva: `add_excluida_por_sanidad_to_produccion_lechera_table.php`
- [ ] Agregar campo `excluida_por_sanidad` (boolean, default false)
- [ ] Actualizar Model ProduccionLechera (agregar a fillable y casts)
- [ ] Agregar scope `sinSanidad()` (similar a `sinRetiro()`)

#### **Tarea 2: Extender ProduccionLecheraService**
- [ ] Agregar método `marcarExcluidaPorSanidad($produccionId, $motivo)`
- [ ] Integrar con SaludService para marcar automáticamente cuando hay mastitis positiva
- [ ] NO modificar métodos existentes, solo agregar nuevos

#### **Tarea 3: Extender SaludService**
- [ ] Agregar método `marcarProduccionesExcluidas($vacaId, $fechaDesde)`
- [ ] Llamar automáticamente cuando se detecta mastitis positiva

**Tiempo Estimado**: 1 día

---

### **FASE 11: IMPORTACIÓN EXCEL - COMPLETAR (Jobs opcionales)**

#### **Tarea 1: Crear Jobs para importación asíncrona**
- [ ] Crear Job: `ProcessExcelImportJob.php`
- [ ] Agregar método en controllers para usar Job (opcional)
- [ ] Mantener importación síncrona como opción

#### **Tarea 2: Plantillas descargables**
- [ ] Crear método `downloadTemplate()` en cada controller
- [ ] Generar plantillas Excel vacías con headers
- [ ] Agregar botón "Descargar Plantilla" en vistas de importación

**Tiempo Estimado**: 1 día (opcional)

---

### **FASE 12: GRÁFICAS APEXCHARTS - COMPLETAR (30% faltante)**

#### **Tarea 1: Verificar y completar gráficas faltantes**
- [ ] Verificar Registros Reproductivos (agregar si faltan)
- [ ] Verificar Crías (agregar si faltan)
- [ ] Verificar Mortalidad (agregar si faltan)
- [ ] Agregar gráficas en Potreros
- [ ] Verificar gráficas en Salud (ya deberían existir)

#### **Tarea 2: Crear endpoints API para gráficas**
- [ ] Crear `Api/GraficasController.php`
- [ ] Endpoints para cada módulo
- [ ] Usar Repositories para datos

**Tiempo Estimado**: 2-3 días

---

### **FASE 13: ROTACIÓN POTREROS - EXTENDER (20% faltante)**

#### **Tarea 1: Agregar campos faltantes**
- [ ] Crear migración nueva: `add_advanced_rotacion_fields_to_asignacion_potreros_table.php`
- [ ] Agregar: carga_ugg, aforo_kg, peso_ingreso, mantenimiento
- [ ] Actualizar Model (agregar a fillable y casts)

#### **Tarea 2: Extender Service con cálculos**
- [ ] Agregar método `calcularCargaUGG($potreroId)`
- [ ] Agregar método `calcularDiasDescanso($potreroId)`
- [ ] Agregar método `obtenerHistorialRotacion($potreroId)`

#### **Tarea 3: Agregar gráficas de rotación**
- [ ] Gráfica de uso por potrero
- [ ] Gráfica de días de descanso
- [ ] Gráfica de carga UGG

**Tiempo Estimado**: 1-2 días

---

### **FASE 14: INVENTARIO BODEGA - VERIFICAR (ya completo)**

#### **Tarea 1: Verificar funcionalidades**
- [ ] Verificar que alertas de vencimiento funcionen
- [ ] Verificar que alertas de stock mínimo funcionen
- [ ] Verificar integración con UsoMedicamentos

**Tiempo Estimado**: 0.5 días (verificación)

---

### **FASE 15: TESTING Y HARDENING - IMPLEMENTAR (95% faltante)**

#### **Tarea 1: Tests Unitarios**
- [ ] Tests para Services (lógica de negocio)
- [ ] Tests para Repositories (consultas)
- [ ] Tests para Models (relaciones, scopes)

#### **Tarea 2: Tests de Integración**
- [ ] Tests de flujos completos
- [ ] Tests de importación Excel
- [ ] Tests de exportación

#### **Tarea 3: Tests Feature**
- [ ] Tests de endpoints principales
- [ ] Tests de autenticación/autorización
- [ ] Tests de validaciones

#### **Tarea 4: Hardening**
- [ ] Optimizar queries N+1
- [ ] Implementar caché de dashboards
- [ ] Completar Policies
- [ ] Configurar auditoría Spatie

**Tiempo Estimado**: 12-18 días

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

### **FASE 10: Pruebas Sanitarias**
- [ ] Migración `excluida_por_sanidad`
- [ ] Model actualizado
- [ ] Service extendido
- [ ] Integración con ProduccionLecheraService

### **FASE 11: Importación Excel**
- [ ] Jobs asincrónicos (opcional)
- [ ] Plantillas descargables

### **FASE 12: Gráficas**
- [ ] Gráficas en Registros Reproductivos
- [ ] Gráficas en Crías
- [ ] Gráficas en Mortalidad
- [ ] Gráficas en Potreros
- [ ] Endpoints API

### **FASE 13: Rotación Potreros**
- [ ] Campos avanzados
- [ ] Cálculos automáticos
- [ ] Gráficas de rotación

### **FASE 14: Inventario Bodega**
- [ ] Verificación completa

### **FASE 15: Testing**
- [ ] Tests unitarios
- [ ] Tests de integración
- [ ] Tests feature
- [ ] Hardening

---

## ⏱️ TIEMPO TOTAL ESTIMADO

- **FASE 10**: 1 día
- **FASE 11**: 1 día (opcional)
- **FASE 12**: 2-3 días
- **FASE 13**: 1-2 días
- **FASE 14**: 0.5 días
- **FASE 15**: 12-18 días

**Total**: 17-25 días (3-4 semanas)

---

## ✅ RESULTADO ESPERADO

Al finalizar estas fases:
- ✔ Sistema S.P.G 100% funcional
- ✔ Cobertura funcional completa
- ✔ Importación total de Excel
- ✔ Analítica completa
- ✔ Testing implementado
- ✔ Listo para producción

---

**Última Actualización**: 11 de Diciembre de 2025

