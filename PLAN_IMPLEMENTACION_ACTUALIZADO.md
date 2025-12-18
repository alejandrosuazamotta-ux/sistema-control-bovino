# 🚀 PLAN DE IMPLEMENTACIÓN ACTUALIZADO - S.P.G SystemPG1

## 📊 RESUMEN EJECUTIVO

**Objetivo**: Convertir todos los Excel mencionados en módulos Laravel funcionales con formularios, gráficas, importación y lógica de negocio avanzada.

**Tecnologías Instaladas**:
- ✅ ApexCharts (gráficas modernas)
- ✅ Alpine.js (interactividad)
- ✅ maatwebsite/excel (importación Excel)
- ✅ spatie/laravel-activitylog (auditoría)
- ✅ spatie/laravel-backup (backups)
- ✅ barryvdh/laravel-dompdf (PDFs)

**Estado Actual**: Sistema base funcional, módulo Vacas refactorizado como ejemplo.

---

## 🎯 MAPEO EXCEL → MÓDULOS LARAVEL

### **Módulos Existentes (Extender)**

| Excel Original | Módulo Actual | Acción Requerida |
|----------------|---------------|------------------|
| REGISTRO DIARIO ORDEÑO 2024 | `produccion_lechera` | Agregar `turno`, `destino` |
| PBPS-PPB-F-009 Producción diaria | `produccion_lechera` | ✅ Ya existe |
| PBPS-PPB-F-041 Nacimientos | `crias` | Agregar `sexo`, relación con peso |
| REGISTRO DE PALPACIONES | `registros_reproductivos` | Agregar tipo "Palpación", calcular fecha parto |
| REGISTRO_DE_PRUEBA_DE_MASTITIS | `salud` (tipo "Prueba mastitis") | Extender con detalles, método, resultado |
| POTREROS | `potreros` | Agregar `area_ha`, `tipo_pasto` |
| PBPS-PPO-F-011 Rotación Potreros | `asignacion_potreros` | Mejorar con fechas de rotación |

### **Módulos Nuevos (Crear)**

| Excel Original | Nuevo Módulo | Prioridad | Complejidad |
|----------------|--------------|-----------|--------------|
| INVENTARIO DE BODEGA GENERAL | `inventario_insumos` | 🟡 Media | Baja |
| INVENTARIO DE MEDICAMENTOS | `medicamentos` | 🔴 Alta | Media |
| USO Y TRATAMIENTO DE MEDICAMENTOS | `uso_medicamentos` | 🔴 Alta | Alta |
| PBPS-PPO-F-008 Mortalidad/Muertes | `mortalidad` | 🟡 Media | Baja |
| FORMATO BRUCELOSIS/TUBERCULOSIS | `muestras_sanitarias` | 🟢 Baja | Media |
| PBPS-PPOR-F-004 Leche mes-animal | `produccion_mensual` | 🟡 Media | Baja (cálculo) |
| PESO TERNEROS | `pesos_crias` | 🟡 Media | Baja |

---

## 📅 CRONOGRAMA DETALLADO (5-6 Semanas)

### **FASE 1: Extensión de Módulos Existentes** (1 semana)

#### **Día 1-2: Producción Lechera**
- [ ] Migración: agregar `turno` (enum: AM/PM), `destino` (string)
- [ ] Actualizar Model `ProduccionLechera`
- [ ] Crear `ProduccionLecheraStoreRequest` y `UpdateRequest`
- [ ] Crear `ProduccionLecheraRepository` (consultas optimizadas)
- [ ] Crear `ProduccionLecheraService` (lógica de negocio)
- [ ] Refactorizar `ProduccionLecheraController` (Admin)
- [ ] Actualizar vistas (create, edit, index)

#### **Día 3-4: Registros Reproductivos**
- [ ] Migración: agregar `resultado_palpacion` (enum), `fecha_probable_parto` (date)
- [ ] Actualizar enum `tipo_evento` para incluir "Palpación"
- [ ] Actualizar Model `RegistroReproductivo`
- [ ] Crear Form Requests
- [ ] Crear Service con lógica:
  - Si palpación positiva → calcular `fecha_probable_parto = fecha_evento + 283 días`
  - Cambiar estado reproductivo a "Preñada"
  - Crear alerta 21 días antes del parto
- [ ] Refactorizar Controller

#### **Día 5: Potreros y Crías**
- [ ] Migración potreros: agregar `area_ha` (decimal), `tipo_pasto` (string)
- [ ] Migración crías: agregar `sexo` (enum: Macho/Hembra)
- [ ] Actualizar Models
- [ ] Mejorar asignación potreros con fechas de rotación

---

### **FASE 2: Nuevos Módulos Críticos** (1.5 semanas)

#### **Semana 2: Medicamentos y Uso**

**Día 1-2: Tabla Medicamentos**
- [ ] Crear migración `medicamentos`
- [ ] Crear Model `Medicamento`
- [ ] Crear Repository, Service, Form Requests
- [ ] Crear Controller Admin
- [ ] Crear vistas CRUD

**Día 3-5: Uso de Medicamentos**
- [ ] Crear migración `uso_medicamentos`
- [ ] Crear Model `UsoMedicamento`
- [ ] Crear Repository, Service con lógica:
  - Calcular `periodo_retiro_fin = fecha + periodo_retiro_leche`
  - Bloquear producción de leche durante retiro
  - Actualizar inventario (descontar medicamento)
- [ ] Crear Form Requests
- [ ] Crear Controller Admin
- [ ] Crear vistas CRUD
- [ ] Agregar alertas visuales en ficha de vaca

**Campos Críticos:**
```php
// medicamentos
- nombre, registro_ICA, principio_activo
- periodo_retiro_leche (días)
- periodo_retiro_carne (días)

// uso_medicamentos
- id_vaca, id_medicamento, fecha
- dosis, via, id_personal
- periodo_retiro_fin (calculado)
```

---

#### **Semana 2-3: Inventario y Mortalidad**

**Día 1-2: Inventario Bodega**
- [ ] Crear migración `inventario_insumos`
- [ ] Crear Model, Repository, Service
- [ ] Lógica: alertas de stock bajo, vencimiento próximo
- [ ] CRUD completo

**Día 3-4: Mortalidad**
- [ ] Crear migración `mortalidad`
- [ ] Crear Model, Repository, Service
- [ ] Estadísticas: tasa de mortalidad, causas
- [ ] CRUD completo

---

### **FASE 3: Importación Excel** (1 semana)

#### **Semana 3:**
- [ ] Crear Jobs para procesamiento asíncrono
- [ ] Crear Import classes:
  - `ProduccionLecheraImport`
  - `MedicamentosImport`
  - `CriaImport`
  - `MortalidadImport`
- [ ] Crear UI de importación (drag & drop)
- [ ] Validación y mapeo de columnas
- [ ] Reporte de errores
- [ ] Plantillas Excel para descargar

---

### **FASE 4: Gráficas y Visualización** (1 semana)

#### **Semana 4:**
- [ ] Crear endpoints API para datos:
  - `/api/dashboard/summary`
  - `/api/produccion/daily`
  - `/api/produccion/vaca/{id}`
  - `/api/produccion/potrero/{id}`
  - `/api/reproduccion/estadisticas`
  - `/api/mastitis/incidencia`
- [ ] Implementar gráficas en Dashboard:
  - Producción diaria (línea ApexCharts)
  - Producción por potrero (pastel)
  - Curva de lactancia (área)
  - Estado reproductivo (barras)
  - Incidencia mastitis (barras)
- [ ] Página de reportes con filtros
- [ ] Exportación PDF/Excel

---

### **FASE 5: Lógica de Negocio Avanzada** (1 semana)

#### **Semana 5:**
- [ ] Implementar alertas automáticas:
  - Celo cada 21 días
  - Parto 21 y 7 días antes
  - Stock bajo en inventario
  - Vencimiento de medicamentos
  - Periodo de retiro activo
- [ ] Cálculos automáticos:
  - Días abiertos
  - Tasa de preñez
  - Pico de producción
  - Curva de lactancia
- [ ] Sistema de notificaciones (email/UI)
- [ ] Jobs programados (cron) para cálculos diarios

---

## 🏗️ ESTRUCTURA DE ARCHIVOS A CREAR

### **Nuevas Migraciones:**
```
database/migrations/
├── YYYY_MM_DD_add_turno_destino_to_produccion_lechera.php
├── YYYY_MM_DD_add_campos_to_registros_reproductivos.php
├── YYYY_MM_DD_add_campos_to_potreros.php
├── YYYY_MM_DD_add_sexo_to_crias.php
├── YYYY_MM_DD_create_medicamentos_table.php
├── YYYY_MM_DD_create_uso_medicamentos_table.php
├── YYYY_MM_DD_create_inventario_insumos_table.php
├── YYYY_MM_DD_create_mortalidad_table.php
├── YYYY_MM_DD_create_muestras_sanitarias_table.php
├── YYYY_MM_DD_create_pesos_crias_table.php
└── YYYY_MM_DD_create_produccion_mensual_table.php
```

### **Nuevos Models:**
```
app/Models/
├── Medicamento.php
├── UsoMedicamento.php
├── InventarioInsumo.php
├── Mortalidad.php
├── MuestraSanitaria.php
├── PesoCria.php
└── ProduccionMensual.php
```

### **Nuevos Services:**
```
app/Services/
├── MedicamentoService.php
├── UsoMedicamentoService.php
├── InventarioService.php
├── MortalidadService.php
└── ProduccionLecheraService.php (refactorizar)
```

### **Nuevos Repositories:**
```
app/Repositories/
├── MedicamentoRepository.php
├── UsoMedicamentoRepository.php
├── InventarioRepository.php
├── MortalidadRepository.php
└── ProduccionLecheraRepository.php
```

### **Nuevos Imports:**
```
app/Imports/
├── ProduccionLecheraImport.php
├── MedicamentosImport.php
├── CriaImport.php
└── MortalidadImport.php
```

### **Nuevos Jobs:**
```
app/Jobs/
├── ImportProduccionLecheraJob.php
├── RecalcularProduccionMensualJob.php
└── CalcularAlertasJob.php
```

---

## 📊 GRÁFICAS PROPUESTAS (ApexCharts)

### **Dashboard Principal:**
1. **Producción Diaria** (últimos 30 días) - Línea
2. **Producción por Potrero** - Pastel
3. **Estado Reproductivo** - Barras horizontales
4. **Incidencia Mastitis** (últimos 6 meses) - Barras
5. **Vacas en Retiro** - Card con contador

### **Ficha de Vaca:**
1. **Curva de Lactancia** (días desde parto) - Área
2. **Producción Histórica** - Línea
3. **Historial de Salud** - Timeline
4. **Pesos de Crías** - Línea

### **Reportes:**
1. **Producción Mensual** - Barras agrupadas
2. **Tasa de Preñez** - Línea temporal
3. **Mortalidad por Causa** - Pastel
4. **Uso de Medicamentos** - Barras apiladas

---

## 🔧 REGLAS DE NEGOCIO CRÍTICAS

### **1. Periodo de Retiro de Medicamentos**
```php
// Al registrar uso_medicamento:
$periodoRetiroFin = $fecha->addDays($medicamento->periodo_retiro_leche);

// Bloquear producción:
- No contar litros de vaca en producción vendible
- Mostrar badge "En Retiro" en ficha
- Alertar cuando termine el retiro
```

### **2. Cálculo de Fecha de Parto**
```php
// Si palpación positiva:
$fechaParto = $fechaPalpacion->addDays(283);

// Alertas:
- 21 días antes: "Parto próximo"
- 7 días antes: "Parto inminente"
```

### **3. Producción Duplicada**
```php
// Evitar duplicados:
- Validar unique: (id_vaca, fecha, turno)
- Usar firstOrCreate en Service
```

### **4. Capacidad de Potrero**
```php
// Al asignar vaca:
- Verificar: ocupacion < capacidad
- Usar withCount para optimizar
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### **Por Módulo:**
- [ ] Migración creada y ejecutada
- [ ] Model con relaciones
- [ ] Form Requests (Store/Update)
- [ ] Repository con consultas optimizadas
- [ ] Service con lógica de negocio
- [ ] Controller Admin refactorizado
- [ ] Vistas CRUD (create, edit, index, show)
- [ ] Validaciones implementadas
- [ ] Tests básicos (opcional)

### **Global:**
- [ ] Endpoints API para gráficas
- [ ] Gráficas ApexCharts implementadas
- [ ] Importación Excel funcional
- [ ] Alertas automáticas
- [ ] Exportación PDF/Excel
- [ ] Documentación actualizada

---

## 🚀 PRÓXIMOS PASOS INMEDIATOS

1. **Empezar FASE 1 - Día 1**: Extender Producción Lechera
2. **Crear migración** para agregar `turno` y `destino`
3. **Refactorizar** siguiendo patrón de Vacas
4. **Continuar** con Registros Reproductivos

---

**Última actualización**: Diciembre 2025  
**Estado**: Listo para implementar - FASE 1

