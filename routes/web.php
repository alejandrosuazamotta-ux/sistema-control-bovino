<?php
use App\Http\Controllers\Pasante\DashboardController as PasanteDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PersonalController;
use App\Http\Controllers\Admin\PotreroController;
use App\Http\Controllers\Admin\VacaController;
use App\Http\Controllers\Admin\CriaController;
use App\Http\Controllers\Admin\RegistroReproductivoController;
use App\Http\Controllers\Admin\ProduccionLecheraController;
use App\Http\Controllers\Admin\SaludController;
use App\Http\Controllers\Admin\AlimentacionController;
use App\Http\Controllers\Admin\AsignacionPotreroController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\MedicamentoController;
use App\Http\Controllers\Admin\UsoMedicamentoController;
use App\Http\Controllers\Admin\RetiroController;
use App\Http\Controllers\Admin\MortalidadController;
use App\Http\Controllers\Admin\AlertaController;
use App\Http\Controllers\Admin\InventarioBodegaController;
use App\Http\Controllers\Admin\PruebaSanitariaController;

Route::get('/', function () {
    return view('welcome');
});

// 🔹 Ruta única post-login (decide a dónde va según rol)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// 🔹 Rutas del Pasante
Route::prefix('pasante')->name('pasante.')->middleware(['auth', 'role:Pasante'])->group(function () {
    Route::get('/dashboard', [PasanteDashboardController::class, 'index'])->name('dashboard');
    
    // Actividades Pasantes
    Route::resource('actividades', \App\Http\Controllers\Pasante\ActividadPasanteController::class);
    
    // Tareas Pasantes
    Route::resource('tareas', \App\Http\Controllers\Pasante\TareaPasanteController::class);
    
    // Apoyo Ordeño Pasantes
    Route::resource('apoyo-ordeno', \App\Http\Controllers\Pasante\ApoyoOrdeñoPasanteController::class);
    
    // Apoyo Reproductivo Pasantes
    Route::resource('apoyo-reproductivo', \App\Http\Controllers\Pasante\ApoyoReproductivoPasanteController::class);
    
    // Rotación Potreros Pasantes
    Route::resource('rotacion-potreros', \App\Http\Controllers\Pasante\RotacionPotrerosPasanteController::class);
    
    // Producción Lechera (Solo lectura para Pasante)
    Route::get('produccion-lechera', [\App\Http\Controllers\Admin\ProduccionLecheraController::class, 'index'])->name('produccion-lechera.index');
    Route::get('produccion-lechera/{id}', [\App\Http\Controllers\Admin\ProduccionLecheraController::class, 'show'])->name('produccion-lechera.show');
    Route::get('produccion-lechera/dashboard', [\App\Http\Controllers\Pasante\ProduccionLecheraController::class, 'dashboard'])->name('produccion-lechera.dashboard');
    
    // Pruebas Sanitarias (Pasante puede crear y ver sus propias pruebas)
    Route::resource('pruebas-sanitarias', \App\Http\Controllers\Pasante\PruebaSanitariaController::class);
    Route::get('pruebas-sanitarias/{id}/descargar-evidencia', [\App\Http\Controllers\Pasante\PruebaSanitariaController::class, 'descargarEvidencia'])->name('pruebas-sanitarias.descargar-evidencia');
    
    // SEGURIDAD: Medicamentos y Uso de Medicamentos - SIN ACCESO PARA PASANTE
    // Según requerimiento, Pasante NO debe tener acceso a medicamentos, retiros, sanitarias (excepto crear pruebas sanitarias)
    // Route::get('medicamentos', ...); // ELIMINADO
    // Route::resource('uso-medicamentos', ...); // ELIMINADO
    
    // Mortalidad (Pasante puede crear y ver registros)
    Route::resource('mortalidad', \App\Http\Controllers\Pasante\MortalidadController::class);
});

// 🔹 Rutas del Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Recursos básicos
    Route::resource('personal', PersonalController::class);
    Route::resource('potreros', PotreroController::class);
    Route::get('potreros/importar', [PotreroController::class, 'importForm'])->name('potreros.import');
    Route::get('potreros/download-template', [PotreroController::class, 'downloadTemplate'])->name('potreros.download-template');
    Route::post('potreros/preview-import', [PotreroController::class, 'previewImport'])->name('potreros.preview-import');
    Route::post('potreros/process-import', [PotreroController::class, 'processImport'])->name('potreros.process-import');
    Route::get('potreros/export/excel', [PotreroController::class, 'exportExcel'])->name('potreros.export.excel');
    Route::get('potreros/export/pdf', [PotreroController::class, 'exportPdf'])->name('potreros.export.pdf');
    Route::resource('vacas', VacaController::class);
    
    // Producción Lechera
    Route::resource('produccion-lechera', ProduccionLecheraController::class);
    Route::get('produccion-lechera/importar', [ProduccionLecheraController::class, 'importForm'])->name('produccion-lechera.import');
    Route::get('produccion-lechera/download-template', [ProduccionLecheraController::class, 'downloadTemplate'])->name('produccion-lechera.download-template');
    Route::post('produccion-lechera/preview-import', [ProduccionLecheraController::class, 'previewImport'])->name('produccion-lechera.preview-import');
    Route::post('produccion-lechera/process-import', [ProduccionLecheraController::class, 'processImport'])->name('produccion-lechera.process-import');
    Route::get('produccion-lechera/export/excel', [ProduccionLecheraController::class, 'exportExcel'])->name('produccion-lechera.export.excel');
    Route::get('produccion-lechera/export/pdf', [ProduccionLecheraController::class, 'exportPdf'])->name('produccion-lechera.export.pdf');
    
    // Medicamentos y Retiros
    Route::resource('medicamentos', MedicamentoController::class);
    Route::get('medicamentos/importar', [MedicamentoController::class, 'importForm'])->name('medicamentos.import');
    Route::get('medicamentos/download-template', [MedicamentoController::class, 'downloadTemplate'])->name('medicamentos.download-template');
    Route::post('medicamentos/preview-import', [MedicamentoController::class, 'previewImport'])->name('medicamentos.preview-import');
    Route::post('medicamentos/process-import', [MedicamentoController::class, 'processImport'])->name('medicamentos.process-import');
    Route::get('medicamentos/export/excel', [MedicamentoController::class, 'exportExcel'])->name('medicamentos.export.excel');
    Route::get('medicamentos/export/pdf', [MedicamentoController::class, 'exportPdf'])->name('medicamentos.export.pdf');
    
    Route::resource('uso-medicamentos', UsoMedicamentoController::class);
    Route::get('uso-medicamentos/importar', [UsoMedicamentoController::class, 'importForm'])->name('uso-medicamentos.import');
    Route::get('uso-medicamentos/download-template', [UsoMedicamentoController::class, 'downloadTemplate'])->name('uso-medicamentos.download-template');
    Route::post('uso-medicamentos/preview-import', [UsoMedicamentoController::class, 'previewImport'])->name('uso-medicamentos.preview-import');
    Route::post('uso-medicamentos/process-import', [UsoMedicamentoController::class, 'processImport'])->name('uso-medicamentos.process-import');
    Route::get('uso-medicamentos/export/excel', [UsoMedicamentoController::class, 'exportExcel'])->name('uso-medicamentos.export.excel');
    Route::get('uso-medicamentos/export/pdf', [UsoMedicamentoController::class, 'exportPdf'])->name('uso-medicamentos.export.pdf');
    
    Route::resource('retiros', RetiroController::class);
    Route::get('retiros/importar', [RetiroController::class, 'importForm'])->name('retiros.import');
    Route::get('retiros/download-template', [RetiroController::class, 'downloadTemplate'])->name('retiros.download-template');
    Route::post('retiros/preview-import', [RetiroController::class, 'previewImport'])->name('retiros.preview-import');
    Route::post('retiros/process-import', [RetiroController::class, 'processImport'])->name('retiros.process-import');
    Route::get('retiros/export/excel', [RetiroController::class, 'exportExcel'])->name('retiros.export.excel');
    Route::get('retiros/export/pdf', [RetiroController::class, 'exportPdf'])->name('retiros.export.pdf');
    
    // Inventario Bodega
    Route::resource('inventario-bodega', InventarioBodegaController::class);
    Route::get('inventario-bodega/importar', [InventarioBodegaController::class, 'importForm'])->name('inventario-bodega.import');
    Route::get('inventario-bodega/download-template', [InventarioBodegaController::class, 'downloadTemplate'])->name('inventario-bodega.download-template');
    Route::post('inventario-bodega/preview-import', [InventarioBodegaController::class, 'previewImport'])->name('inventario-bodega.preview-import');
    Route::post('inventario-bodega/process-import', [InventarioBodegaController::class, 'processImport'])->name('inventario-bodega.process-import');
    Route::get('inventario-bodega/export/excel', [InventarioBodegaController::class, 'exportExcel'])->name('inventario-bodega.export.excel');
    Route::get('inventario-bodega/export/pdf', [InventarioBodegaController::class, 'exportPdf'])->name('inventario-bodega.export.pdf');
    Route::post('inventario-bodega/{id}/entrada', [InventarioBodegaController::class, 'registrarEntrada'])->name('inventario-bodega.entrada');
    Route::post('inventario-bodega/{id}/salida', [InventarioBodegaController::class, 'registrarSalida'])->name('inventario-bodega.salida');
    Route::post('inventario-bodega/{id}/ajuste', [InventarioBodegaController::class, 'registrarAjuste'])->name('inventario-bodega.ajuste');
    
    // Salud y Alimentación
    Route::resource('salud', SaludController::class);
    Route::get('salud/importar', [SaludController::class, 'importForm'])->name('salud.import');
    Route::get('salud/download-template', [SaludController::class, 'downloadTemplate'])->name('salud.download-template');
    Route::post('salud/preview-import', [SaludController::class, 'previewImport'])->name('salud.preview-import');
    Route::post('salud/process-import', [SaludController::class, 'processImport'])->name('salud.process-import');
    Route::get('salud/export/excel', [SaludController::class, 'exportExcel'])->name('salud.export.excel');
    Route::get('salud/export/pdf', [SaludController::class, 'exportPdf'])->name('salud.export.pdf');
    
    // Pruebas Sanitarias
    Route::resource('pruebas-sanitarias', PruebaSanitariaController::class);
    Route::get('pruebas-sanitarias/importar', [PruebaSanitariaController::class, 'importForm'])->name('pruebas-sanitarias.import');
    Route::get('pruebas-sanitarias/download-template', [PruebaSanitariaController::class, 'downloadTemplate'])->name('pruebas-sanitarias.download-template');
    Route::post('pruebas-sanitarias/preview-import', [PruebaSanitariaController::class, 'previewImport'])->name('pruebas-sanitarias.preview-import');
    Route::post('pruebas-sanitarias/process-import', [PruebaSanitariaController::class, 'processImport'])->name('pruebas-sanitarias.process-import');
    Route::get('pruebas-sanitarias/export/excel', [PruebaSanitariaController::class, 'exportExcel'])->name('pruebas-sanitarias.export.excel');
    Route::get('pruebas-sanitarias/export/pdf', [PruebaSanitariaController::class, 'exportPdf'])->name('pruebas-sanitarias.export.pdf');
    Route::post('pruebas-sanitarias/{id}/cerrar', [PruebaSanitariaController::class, 'cerrar'])->name('pruebas-sanitarias.cerrar');
    Route::get('pruebas-sanitarias/{id}/descargar-evidencia', [PruebaSanitariaController::class, 'descargarEvidencia'])->name('pruebas-sanitarias.descargar-evidencia');
    Route::resource('alimentacion', AlimentacionController::class);
    Route::get('alimentacion/importar', [AlimentacionController::class, 'importForm'])->name('alimentacion.import');
    Route::get('alimentacion/download-template', [AlimentacionController::class, 'downloadTemplate'])->name('alimentacion.download-template');
    Route::post('alimentacion/preview-import', [AlimentacionController::class, 'previewImport'])->name('alimentacion.preview-import');
    Route::post('alimentacion/process-import', [AlimentacionController::class, 'processImport'])->name('alimentacion.process-import');
    Route::get('alimentacion/export/excel', [AlimentacionController::class, 'exportExcel'])->name('alimentacion.export.excel');
    Route::get('alimentacion/export/pdf', [AlimentacionController::class, 'exportPdf'])->name('alimentacion.export.pdf');
    
    Route::resource('asignacion-potreros', AsignacionPotreroController::class);
    Route::get('asignacion-potreros/importar', [AsignacionPotreroController::class, 'importForm'])->name('asignacion-potreros.import');
    Route::get('asignacion-potreros/download-template', [AsignacionPotreroController::class, 'downloadTemplate'])->name('asignacion-potreros.download-template');
    Route::post('asignacion-potreros/preview-import', [AsignacionPotreroController::class, 'previewImport'])->name('asignacion-potreros.preview-import');
    Route::post('asignacion-potreros/process-import', [AsignacionPotreroController::class, 'processImport'])->name('asignacion-potreros.process-import');
    Route::get('asignacion-potreros/export/excel', [AsignacionPotreroController::class, 'exportExcel'])->name('asignacion-potreros.export.excel');
    Route::get('asignacion-potreros/export/pdf', [AsignacionPotreroController::class, 'exportPdf'])->name('asignacion-potreros.export.pdf');
    
    // Crías
    Route::resource('crias', CriaController::class);
    Route::get('crias/importar', [CriaController::class, 'importForm'])->name('crias.import');
    Route::get('crias/download-template', [CriaController::class, 'downloadTemplate'])->name('crias.download-template');
    Route::post('crias/preview-import', [CriaController::class, 'previewImport'])->name('crias.preview-import');
    Route::post('crias/process-import', [CriaController::class, 'processImport'])->name('crias.process-import');
    Route::get('crias/export/excel', [CriaController::class, 'exportExcel'])->name('crias.export.excel');
    Route::get('crias/export/pdf', [CriaController::class, 'exportPdf'])->name('crias.export.pdf');
    
    // Mortalidad
    Route::resource('mortalidad', MortalidadController::class);
    Route::get('mortalidad/importar', [MortalidadController::class, 'importForm'])->name('mortalidad.import');
    Route::get('mortalidad/download-template', [MortalidadController::class, 'downloadTemplate'])->name('mortalidad.download-template');
    Route::post('mortalidad/preview-import', [MortalidadController::class, 'previewImport'])->name('mortalidad.preview-import');
    Route::post('mortalidad/process-import', [MortalidadController::class, 'processImport'])->name('mortalidad.process-import');
    Route::get('mortalidad/export/excel', [MortalidadController::class, 'exportExcel'])->name('mortalidad.export.excel');
    Route::get('mortalidad/export/pdf', [MortalidadController::class, 'exportPdf'])->name('mortalidad.export.pdf');
    
    // Registros Reproductivos
    Route::resource('registros-reproductivos', RegistroReproductivoController::class);
    Route::get('registros-reproductivos/importar', [RegistroReproductivoController::class, 'importForm'])->name('registros-reproductivos.import');
    Route::get('registros-reproductivos/download-template', [RegistroReproductivoController::class, 'downloadTemplate'])->name('registros-reproductivos.download-template');
    Route::post('registros-reproductivos/preview-import', [RegistroReproductivoController::class, 'previewImport'])->name('registros-reproductivos.preview-import');
    Route::post('registros-reproductivos/process-import', [RegistroReproductivoController::class, 'processImport'])->name('registros-reproductivos.process-import');
    Route::get('registros-reproductivos/export/excel', [RegistroReproductivoController::class, 'exportExcel'])->name('registros-reproductivos.export.excel');
    Route::get('registros-reproductivos/export/pdf', [RegistroReproductivoController::class, 'exportPdf'])->name('registros-reproductivos.export.pdf');

        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/', [ReporteController::class, 'index'])->name('index');
            Route::get('/produccion', [ReporteController::class, 'produccion'])->name('produccion');
            Route::get('/reproductivo', [ReporteController::class, 'reproductivo'])->name('reproductivo');
            Route::get('/sanitario', [ReporteController::class, 'sanitario'])->name('sanitario');
            Route::get('/mortalidad', [ReporteController::class, 'mortalidad'])->name('mortalidad');
            Route::get('/medicamentos', [ReporteController::class, 'medicamentos'])->name('medicamentos');
            
            // Exportaciones (solo admin)
            Route::get('/produccion/export/excel', [ReporteController::class, 'exportProduccionExcel'])->name('produccion.export.excel');
            Route::get('/produccion/export/pdf', [ReporteController::class, 'exportProduccionPdf'])->name('produccion.export.pdf');
            Route::get('/reproductivo/export/excel', [ReporteController::class, 'exportReproductivoExcel'])->name('reproductivo.export.excel');
            Route::get('/reproductivo/export/pdf', [ReporteController::class, 'exportReproductivoPdf'])->name('reproductivo.export.pdf');
            Route::get('/sanitario/export/excel', [ReporteController::class, 'exportSanitarioExcel'])->name('sanitario.export.excel');
            Route::get('/sanitario/export/pdf', [ReporteController::class, 'exportSanitarioPdf'])->name('sanitario.export.pdf');
            Route::get('/mortalidad/export/excel', [ReporteController::class, 'exportMortalidadExcel'])->name('mortalidad.export.excel');
            Route::get('/mortalidad/export/pdf', [ReporteController::class, 'exportMortalidadPdf'])->name('mortalidad.export.pdf');
            Route::get('/medicamentos/export/excel', [ReporteController::class, 'exportMedicamentosExcel'])->name('medicamentos.export.excel');
            Route::get('/medicamentos/export/pdf', [ReporteController::class, 'exportMedicamentosPdf'])->name('medicamentos.export.pdf');
        });

        // Alertas y Notificaciones
        Route::prefix('alertas')->name('alertas.')->group(function () {
            Route::get('/', [AlertaController::class, 'index'])->name('index');
            Route::get('/no-leidas', [AlertaController::class, 'noLeidas'])->name('no-leidas');
            Route::post('/{id}/marcar-leida', [AlertaController::class, 'marcarLeida'])->name('marcar-leida');
            Route::post('/{id}/marcar-atendida', [AlertaController::class, 'marcarAtendida'])->name('marcar-atendida');
            Route::post('/marcar-todas-leidas', [AlertaController::class, 'marcarTodasLeidas'])->name('marcar-todas-leidas');
            Route::post('/generar', [AlertaController::class, 'generar'])->name('generar');
        });
});
