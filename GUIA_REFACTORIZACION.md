# 🔧 GUÍA DE REFACTORIZACIÓN - S.P.G SystemPG1

## ✅ PROGRESO ACTUAL

### Fase B - Refactor Mínimo Obligatorio (EN PROGRESO)

#### ✅ Completado

1. **Paquetes Instalados**
   - ✅ `spatie/laravel-activitylog` - Para auditoría
   - ✅ `spatie/laravel-backup` - Para backups automáticos
   - ✅ `barryvdh/laravel-dompdf` - Para exportar PDFs

2. **Módulo Vacas - Refactorizado Completamente**
   - ✅ `VacaStoreRequest` - Form Request para creación
   - ✅ `VacaUpdateRequest` - Form Request para actualización
   - ✅ `VacaRepository` - Repositorio con consultas optimizadas
   - ✅ `VacaService` - Servicio con lógica de negocio
   - ✅ `VacaController` (Admin) - Refactorizado para usar Request + Service

#### 🚧 Pendiente

- [ ] Refactorizar módulo Producción Lechera
- [ ] Refactorizar módulo Crías
- [ ] Refactorizar módulo Personal
- [ ] Refactorizar módulo Potreros
- [ ] Refactorizar módulo Salud
- [ ] Refactorizar módulo Alimentación
- [ ] Refactorizar módulo Registros Reproductivos
- [ ] Refactorizar módulo Asignación Potreros

---

## 📋 PATRÓN A SEGUIR

### Estructura de Archivos

Para cada módulo (ejemplo: `ProduccionLechera`):

```
app/
├── Http/
│   ├── Requests/
│   │   ├── ProduccionLecheraStoreRequest.php
│   │   └── ProduccionLecheraUpdateRequest.php
│   └── Controllers/
│       └── Admin/
│           └── ProduccionLecheraController.php (refactorizado)
├── Services/
│   └── ProduccionLecheraService.php
└── Repositories/
    └── ProduccionLecheraRepository.php
```

### Pasos para Refactorizar un Módulo

#### Paso 1: Crear Form Requests

```bash
php artisan make:request [Modulo]StoreRequest
php artisan make:request [Modulo]UpdateRequest
```

**Ejemplo para ProduccionLechera:**
```bash
php artisan make:request ProduccionLecheraStoreRequest
php artisan make:request ProduccionLecheraUpdateRequest
```

#### Paso 2: Implementar Validaciones en Form Requests

Copiar las reglas de validación del controlador actual al Form Request correspondiente.

#### Paso 3: Crear Repository

Crear archivo en `app/Repositories/[Modulo]Repository.php` con:
- Métodos para consultas optimizadas
- Eager loading para evitar N+1
- Métodos de filtrado y búsqueda

#### Paso 4: Crear Service

Crear archivo en `app/Services/[Modulo]Service.php` con:
- Lógica de negocio
- Validaciones complejas
- Transacciones de base de datos
- Logging de actividades

#### Paso 5: Refactorizar Controller

Actualizar el controlador para:
- Usar Form Requests en lugar de validación manual
- Inyectar Service en el constructor
- Delegar lógica al Service
- Mantener solo coordinación HTTP

---

## 📝 EJEMPLO COMPLETO: Módulo Vacas (Ya Implementado)

### VacaStoreRequest.php
```php
public function rules(): array
{
    return [
        'codigo' => 'required|string|max:20|unique:vacas,codigo',
        'fecha_nacimiento' => 'nullable|date|before_or_equal:today',
        // ... más reglas
    ];
}
```

### VacaRepository.php
```php
public function paginateWithFilters(array $filters = [], int $perPage = 10)
{
    $query = Vaca::with('potrero'); // Eager loading
    
    // Filtros...
    
    return $query->paginate($perPage);
}
```

### VacaService.php
```php
public function create(array $data): Vaca
{
    return DB::transaction(function () use ($data) {
        // Validar capacidad de potrero
        if (isset($data['id_potrero'])) {
            $capacityCheck = $this->repository->checkPotreroCapacity(...);
            if (!$capacityCheck['available']) {
                throw new \Exception(...);
            }
        }
        
        return $this->repository->create($data);
    });
}
```

### VacaController.php (Refactorizado)
```php
public function store(VacaStoreRequest $request)
{
    try {
        $vaca = $this->vacaService->create($request->validated());
        return redirect()->route('admin.vacas.index')
            ->with('success', 'Vaca registrada exitosamente.');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error: ' . $e->getMessage())
            ->withInput();
    }
}
```

---

## 🎯 PRÓXIMOS MÓDULOS A REFACTORIZAR

### Prioridad Alta (Esta Semana)

1. **ProduccionLechera** - Módulo crítico con lógica compleja
2. **Personal** - Ya usa transacciones, necesita Form Requests
3. **Potreros** - Validaciones de capacidad importantes

### Prioridad Media (Próxima Semana)

4. **Crías**
5. **Salud**
6. **Alimentación**
7. **Registros Reproductivos**
8. **Asignación Potreros**

---

## 🔍 CHECKLIST DE REFACTORIZACIÓN

Para cada módulo, verificar:

- [ ] Form Requests creados (Store y Update)
- [ ] Validaciones movidas a Form Requests
- [ ] Repository creado con métodos optimizados
- [ ] Service creado con lógica de negocio
- [ ] Controller refactorizado (usa Request + Service)
- [ ] Consultas optimizadas (eager loading, withCount)
- [ ] Transacciones donde sea necesario
- [ ] Logging de actividades importantes
- [ ] Manejo de errores consistente
- [ ] Tests básicos (opcional por ahora)

---

## 📊 BENEFICIOS OBTENIDOS

### Antes (VacaController)
- ❌ 182 líneas de código
- ❌ Validaciones duplicadas
- ❌ Lógica mezclada
- ❌ Consultas N+1 potenciales
- ❌ Difícil de testear

### Después (Refactorizado)
- ✅ ~80 líneas en Controller (reducción 56%)
- ✅ Validaciones centralizadas en Form Requests
- ✅ Lógica separada en Service
- ✅ Consultas optimizadas en Repository
- ✅ Fácil de testear (Service y Repository)

---

## 🚀 COMANDOS ÚTILES

### Crear estructura base para un módulo

```bash
# 1. Crear Form Requests
php artisan make:request [Modulo]StoreRequest
php artisan make:request [Modulo]UpdateRequest

# 2. Crear directorios (si no existen)
mkdir -p app/Services app/Repositories

# 3. Crear archivos manualmente:
# - app/Services/[Modulo]Service.php
# - app/Repositories/[Modulo]Repository.php
```

### Verificar que todo funciona

```bash
# Verificar sintaxis
php artisan route:list

# Verificar que no hay errores
php artisan config:clear
php artisan cache:clear
```

---

## 📚 RECURSOS Y REFERENCIAS

### Documentación Laravel
- [Form Requests](https://laravel.com/docs/12.x/validation#form-request-validation)
- [Service Container](https://laravel.com/docs/12.x/container)
- [Database Transactions](https://laravel.com/docs/12.x/database#database-transactions)

### Patrones de Diseño
- Repository Pattern
- Service Layer Pattern
- Dependency Injection

---

## ⚠️ NOTAS IMPORTANTES

1. **No romper funcionalidad existente**: Refactorizar módulo por módulo y probar cada uno
2. **Mantener compatibilidad**: Las vistas y rutas no deben cambiar
3. **Logging**: Agregar logs importantes en Services para auditoría
4. **Transacciones**: Usar en operaciones que afectan múltiples tablas
5. **Eager Loading**: Siempre usar `with()` para relaciones en listados

---

**Última actualización**: Diciembre 2025  
**Estado**: En progreso - Módulo Vacas completado

