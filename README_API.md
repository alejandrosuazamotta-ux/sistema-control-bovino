# Sistema de Gestión de Ganado - API Documentation

## Descripción General

Este sistema de gestión de ganado (SystemPG) proporciona una API REST completa para administrar todos los aspectos de una granja lechera, incluyendo personal, potreros, vacas, crías, registros reproductivos, producción lechera, salud, alimentación y asignación de potreros.

## Base URL

```
http://localhost:8000/api
```

## Endpoints Disponibles

### 1. Personal

**GET** `/personal` - Listar todo el personal
**POST** `/personal` - Crear nuevo personal
**GET** `/personal/{id}` - Obtener personal específico
**PUT** `/personal/{id}` - Actualizar personal
**DELETE** `/personal/{id}` - Eliminar personal

**Campos requeridos para crear/actualizar:**
```json
{
    "nombre": "string (máx 100 caracteres)",
    "rol": "Pasante|Supervisor|Otro",
    "fecha_contratacion": "date (opcional)"
}
```

### 2. Potreros

**GET** `/potreros` - Listar todos los potreros
**POST** `/potreros` - Crear nuevo potrero
**GET** `/potreros/{id}` - Obtener potrero específico
**PUT** `/potreros/{id}` - Actualizar potrero
**DELETE** `/potreros/{id}` - Eliminar potrero

**Campos requeridos para crear/actualizar:**
```json
{
    "nombre": "string (máx 50 caracteres)",
    "ubicacion": "string (máx 100 caracteres, opcional)",
    "capacidad": "integer (mín 1)"
}
```

### 3. Vacas

**GET** `/vacas` - Listar todas las vacas
**POST** `/vacas` - Crear nueva vaca
**GET** `/vacas/{id}` - Obtener vaca específica
**PUT** `/vacas/{id}` - Actualizar vaca
**DELETE** `/vacas/{id}` - Eliminar vaca

**Campos requeridos para crear/actualizar:**
```json
{
    "codigo": "string (máx 20 caracteres, único)",
    "fecha_nacimiento": "date (opcional)",
    "raza": "string (máx 50 caracteres, opcional)",
    "estado_salud": "Sana|En tratamiento|En observación",
    "estado_reproductivo": "Celo|Preñada|Lactancia|Descanso",
    "id_potrero": "integer (opcional, debe existir en potreros)"
}
```

### 4. Crías

**GET** `/crias` - Listar todas las crías
**POST** `/crias` - Crear nueva cría
**GET** `/crias/{id}` - Obtener cría específica
**PUT** `/crias/{id}` - Actualizar cría
**DELETE** `/crias/{id}` - Eliminar cría

**Campos requeridos para crear/actualizar:**
```json
{
    "id_vaca_madre": "integer (debe existir en vacas)",
    "fecha_nacimiento": "date",
    "peso": "decimal (opcional, mínimo 0)",
    "estado_destete": "No destetada|Destetada"
}
```

### 5. Registros Reproductivos

**GET** `/registros-reproductivos` - Listar todos los registros
**POST** `/registros-reproductivos` - Crear nuevo registro
**GET** `/registros-reproductivos/{id}` - Obtener registro específico
**PUT** `/registros-reproductivos/{id}` - Actualizar registro
**DELETE** `/registros-reproductivos/{id}` - Eliminar registro

**Campos requeridos para crear/actualizar:**
```json
{
    "id_vaca": "integer (debe existir en vacas)",
    "tipo_evento": "Inseminación|Parto|Celo|Días abiertos",
    "fecha_evento": "date",
    "observaciones": "string (opcional)",
    "id_personal": "integer (opcional, debe existir en personal)"
}
```

### 6. Producción Lechera

**GET** `/produccion-lechera` - Listar toda la producción
**POST** `/produccion-lechera` - Crear nuevo registro de producción
**GET** `/produccion-lechera/{id}` - Obtener registro específico
**PUT** `/produccion-lechera/{id}` - Actualizar registro
**DELETE** `/produccion-lechera/{id}` - Eliminar registro

**Campos requeridos para crear/actualizar:**
```json
{
    "id_vaca": "integer (debe existir en vacas)",
    "fecha": "date",
    "cantidad_leche": "decimal (mínimo 0)",
    "id_personal": "integer (opcional, debe existir en personal)"
}
```

### 7. Salud

**GET** `/salud` - Listar todos los registros de salud
**POST** `/salud` - Crear nuevo registro de salud
**GET** `/salud/{id}` - Obtener registro específico
**PUT** `/salud/{id}` - Actualizar registro
**DELETE** `/salud/{id}` - Eliminar registro

**Campos requeridos para crear/actualizar:**
```json
{
    "id_vaca": "integer (debe existir en vacas)",
    "tipo_registro": "Vacunación|Tratamiento|Prueba mastitis|Otro",
    "fecha": "date",
    "descripcion": "string (opcional)",
    "resultado_prueba": "string (máx 50 caracteres, opcional)",
    "id_personal": "integer (opcional, debe existir en personal)"
}
```

### 8. Alimentación

**GET** `/alimentacion` - Listar todos los registros de alimentación
**POST** `/alimentacion` - Crear nuevo registro de alimentación
**GET** `/alimentacion/{id}` - Obtener registro específico
**PUT** `/alimentacion/{id}` - Actualizar registro
**DELETE** `/alimentacion/{id}` - Eliminar registro

**Campos requeridos para crear/actualizar:**
```json
{
    "id_vaca": "integer (debe existir en vacas)",
    "fecha": "date",
    "tipo_alimento": "Ensilaje|Pasto|Concentrado|Subproducto|Otro",
    "cantidad": "decimal (opcional, mínimo 0)",
    "observaciones": "string (opcional)",
    "id_personal": "integer (opcional, debe existir en personal)"
}
```

### 9. Asignación de Potreros

**GET** `/asignacion-potreros` - Listar todas las asignaciones
**POST** `/asignacion-potreros` - Crear nueva asignación
**GET** `/asignacion-potreros/{id}` - Obtener asignación específica
**PUT** `/asignacion-potreros/{id}` - Actualizar asignación
**DELETE** `/asignacion-potreros/{id}` - Eliminar asignación

**Campos requeridos para crear/actualizar:**
```json
{
    "id_potrero": "integer (debe existir en potreros)",
    "id_vaca": "integer (debe existir en vacas)",
    "fecha_asignacion": "date"
}
```

### 10. Reportes

**GET** `/reportes/produccion-diaria` - Reporte de producción diaria
**GET** `/reportes/estado-reproductivo` - Reporte de estado reproductivo
**GET** `/reportes/salud-general` - Reporte de salud general

## Respuestas de la API

Todas las respuestas siguen el siguiente formato:

### Respuesta exitosa:
```json
{
    "success": true,
    "message": "Mensaje descriptivo",
    "data": {
        // Datos del recurso
    }
}
```

### Respuesta de error:
```json
{
    "success": false,
    "message": "Mensaje de error",
    "errors": {
        // Detalles de validación si aplica
    }
}
```

## Códigos de Estado HTTP

- **200** - OK (Operación exitosa)
- **201** - Created (Recurso creado exitosamente)
- **400** - Bad Request (Datos inválidos)
- **404** - Not Found (Recurso no encontrado)
- **422** - Unprocessable Entity (Errores de validación)
- **500** - Internal Server Error (Error del servidor)

## Ejemplos de Uso

### Crear una vaca:
```bash
curl -X POST http://localhost:8000/api/vacas \
  -H "Content-Type: application/json" \
  -d '{
    "codigo": "VACA001",
    "fecha_nacimiento": "2020-01-15",
    "raza": "Holstein",
    "estado_salud": "Sana",
    "estado_reproductivo": "Descanso"
  }'
```

### Obtener todas las vacas:
```bash
curl -X GET http://localhost:8000/api/vacas
```

### Actualizar una vaca:
```bash
curl -X PUT http://localhost:8000/api/vacas/1 \
  -H "Content-Type: application/json" \
  -d '{
    "estado_reproductivo": "Celo"
  }'
```

## Relaciones entre Entidades

- **Vaca** pertenece a un **Potrero**
- **Vaca** tiene muchas **Crías**
- **Vaca** tiene muchos **Registros Reproductivos**
- **Vaca** tiene muchos registros de **Producción Lechera**
- **Vaca** tiene muchos registros de **Salud**
- **Vaca** tiene muchos registros de **Alimentación**
- **Personal** puede estar asociado a registros de **Producción Lechera**, **Salud**, **Alimentación** y **Registros Reproductivos**

## Notas Importantes

1. Todas las fechas deben estar en formato `YYYY-MM-DD`
2. Los IDs de las claves foráneas deben existir en sus respectivas tablas
3. Los campos enum tienen valores específicos permitidos
4. Los campos decimales aceptan números con hasta 2 decimales
5. Todos los endpoints devuelven respuestas en formato JSON 