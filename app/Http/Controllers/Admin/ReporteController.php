<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReporteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReporteController extends Controller
{
    protected ReporteService $reporteService;

    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    /**
     * Mostrar índice de reportes
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function index()
    {
        // SEGURIDAD: Admin siempre tiene acceso, verificación suave
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewAny(auth()->user())) {
            abort(403, 'No tiene permisos para acceder a los reportes.');
        }
        return view('admin.reportes.index');
    }

    /**
     * Reporte de producción
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function produccion(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewProduccion(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteProduccion($fechaInicio, $fechaFin);

        return view('admin.reportes.produccion', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte reproductivo
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function reproductivo(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewReproductivo(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteReproductivo($fechaInicio, $fechaFin);

        return view('admin.reportes.reproductivo', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte sanitario
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function sanitario(Request $request)
    {
        // SEGURIDAD: Admin siempre tiene acceso, verificación suave
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewSanitario(auth()->user())) {
            abort(403, 'No tiene permisos para ver este reporte.');
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteSanitario($fechaInicio, $fechaFin);

        return view('admin.reportes.sanitario', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de mortalidad
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function mortalidad(Request $request)
    {
        // SEGURIDAD: Admin siempre tiene acceso, verificación suave
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewMortalidad(auth()->user())) {
            abort(403, 'No tiene permisos para ver este reporte.');
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteMortalidad($fechaInicio, $fechaFin);

        return view('admin.reportes.mortalidad', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de medicamentos
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function medicamentos(Request $request)
    {
        // SEGURIDAD: Admin siempre tiene acceso, verificación suave
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewMedicamentos(auth()->user())) {
            abort(403, 'No tiene permisos para ver este reporte.');
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteMedicamentos($fechaInicio, $fechaFin);

        return view('admin.reportes.medicamentos', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Exportar reporte de producción a Excel
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportProduccionExcel(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteProduccion($fechaInicio, $fechaFin);

        // Crear exportación Excel
        $export = new class($datos, $fechaInicio, $fechaFin) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            protected $datos;
            protected $fechaInicio;
            protected $fechaFin;

            public function __construct($datos, $fechaInicio, $fechaFin) {
                $this->datos = $datos;
                $this->fechaInicio = $fechaInicio;
                $this->fechaFin = $fechaFin;
            }

            public function array(): array {
                $rows = [];
                $rows[] = ['Total Producción (L)', number_format($this->datos['total_produccion'], 2)];
                $rows[] = ['Promedio Diario (L)', number_format($this->datos['promedio_diario'], 2)];
                $rows[] = ['Días con Producción', $this->datos['dias_con_produccion']];
                $rows[] = [];
                $rows[] = ['Fecha', 'Producción (L)'];
                foreach ($this->datos['produccion_diaria'] as $prod) {
                    $rows[] = [$prod->dia, number_format($prod->total_leche, 2)];
                }
                return $rows;
            }

            public function headings(): array {
                return ['Concepto', 'Valor'];
            }

            public function title(): string {
                return 'Reporte Producción';
            }
        };

        $nombreArchivo = 'reporte_produccion_' . $fechaInicio . '_' . $fechaFin . '.xlsx';
        return Excel::download($export, $nombreArchivo);
    }

    /**
     * Exportar reporte de producción a PDF
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportProduccionPdf(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteProduccion($fechaInicio, $fechaFin);

        $pdf = PDF::loadView('admin.reportes.pdf.produccion', compact('datos', 'fechaInicio', 'fechaFin'));
        $nombreArchivo = 'reporte_produccion_' . $fechaInicio . '_' . $fechaFin . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar reporte reproductivo a Excel
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportReproductivoExcel(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteReproductivo($fechaInicio, $fechaFin);

        $export = new class($datos, $fechaInicio, $fechaFin) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            protected $datos;
            protected $fechaInicio;
            protected $fechaFin;

            public function __construct($datos, $fechaInicio, $fechaFin) {
                $this->datos = $datos;
                $this->fechaInicio = $fechaInicio;
                $this->fechaFin = $fechaFin;
            }

            public function array(): array {
                $rows = [];
                $rows[] = ['Tasa de Preñez (%)', number_format($this->datos['tasa_preñez'], 2)];
                $rows[] = ['Total Inseminaciones', $this->datos['total_inseminaciones']];
                $rows[] = ['Total Palpaciones Positivas', $this->datos['total_palpaciones_positivas']];
                $rows[] = [];
                $rows[] = ['Tipo Evento', 'Total'];
                foreach ($this->datos['eventos_por_tipo'] as $evento) {
                    $rows[] = [$evento->tipo_evento, $evento->total];
                }
                return $rows;
            }

            public function headings(): array {
                return ['Concepto', 'Valor'];
            }

            public function title(): string {
                return 'Reporte Reproductivo';
            }
        };

        $nombreArchivo = 'reporte_reproductivo_' . $fechaInicio . '_' . $fechaFin . '.xlsx';
        return Excel::download($export, $nombreArchivo);
    }

    /**
     * Exportar reporte reproductivo a PDF
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportReproductivoPdf(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteReproductivo($fechaInicio, $fechaFin);

        $pdf = PDF::loadView('admin.reportes.pdf.reproductivo', compact('datos', 'fechaInicio', 'fechaFin'));
        $nombreArchivo = 'reporte_reproductivo_' . $fechaInicio . '_' . $fechaFin . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar reporte sanitario a Excel
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportSanitarioExcel(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteSanitario($fechaInicio, $fechaFin);

        $export = new class($datos, $fechaInicio, $fechaFin) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            protected $datos;
            protected $fechaInicio;
            protected $fechaFin;

            public function __construct($datos, $fechaInicio, $fechaFin) {
                $this->datos = $datos;
                $this->fechaInicio = $fechaInicio;
                $this->fechaFin = $fechaFin;
            }

            public function array(): array {
                $rows = [];
                $rows[] = ['Tipo Registro', 'Total'];
                foreach ($this->datos['registros_por_tipo'] as $registro) {
                    $rows[] = [$registro->tipo_registro, $registro->total];
                }
                $rows[] = [];
                $rows[] = ['Resultado Prueba', 'Total'];
                foreach ($this->datos['pruebas_por_resultado'] as $prueba) {
                    $rows[] = [$prueba->resultado, $prueba->total];
                }
                return $rows;
            }

            public function headings(): array {
                return ['Concepto', 'Valor'];
            }

            public function title(): string {
                return 'Reporte Sanitario';
            }
        };

        $nombreArchivo = 'reporte_sanitario_' . $fechaInicio . '_' . $fechaFin . '.xlsx';
        return Excel::download($export, $nombreArchivo);
    }

    /**
     * Exportar reporte sanitario a PDF
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportSanitarioPdf(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteSanitario($fechaInicio, $fechaFin);

        $pdf = PDF::loadView('admin.reportes.pdf.sanitario', compact('datos', 'fechaInicio', 'fechaFin'));
        $nombreArchivo = 'reporte_sanitario_' . $fechaInicio . '_' . $fechaFin . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar reporte de mortalidad a Excel
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportMortalidadExcel(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteMortalidad($fechaInicio, $fechaFin);

        $export = new class($datos, $fechaInicio, $fechaFin) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            protected $datos;
            protected $fechaInicio;
            protected $fechaFin;

            public function __construct($datos, $fechaInicio, $fechaFin) {
                $this->datos = $datos;
                $this->fechaInicio = $fechaInicio;
                $this->fechaFin = $fechaFin;
            }

            public function array(): array {
                $rows = [];
                $rows[] = ['Total Muertes', $this->datos['total_muertes']];
                $rows[] = [];
                $rows[] = ['Tipo Animal', 'Total'];
                foreach ($this->datos['muertes_por_tipo'] as $muerte) {
                    $tipo = $muerte->animal_type === 'App\Models\Vaca' ? 'Vaca' : 'Cría';
                    $rows[] = [$tipo, $muerte->total];
                }
                $rows[] = [];
                $rows[] = ['Causa', 'Total'];
                foreach ($this->datos['muertes_por_causa'] as $causa) {
                    $rows[] = [$causa->causa, $causa->total];
                }
                return $rows;
            }

            public function headings(): array {
                return ['Concepto', 'Valor'];
            }

            public function title(): string {
                return 'Reporte Mortalidad';
            }
        };

        $nombreArchivo = 'reporte_mortalidad_' . $fechaInicio . '_' . $fechaFin . '.xlsx';
        return Excel::download($export, $nombreArchivo);
    }

    /**
     * Exportar reporte de mortalidad a PDF
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportMortalidadPdf(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteMortalidad($fechaInicio, $fechaFin);

        $pdf = PDF::loadView('admin.reportes.pdf.mortalidad', compact('datos', 'fechaInicio', 'fechaFin'));
        $nombreArchivo = 'reporte_mortalidad_' . $fechaInicio . '_' . $fechaFin . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar reporte de medicamentos a Excel
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportMedicamentosExcel(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteMedicamentos($fechaInicio, $fechaFin);

        $export = new class($datos, $fechaInicio, $fechaFin) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            protected $datos;
            protected $fechaInicio;
            protected $fechaFin;

            public function __construct($datos, $fechaInicio, $fechaFin) {
                $this->datos = $datos;
                $this->fechaInicio = $fechaInicio;
                $this->fechaFin = $fechaFin;
            }

            public function array(): array {
                $rows = [];
                $rows[] = ['Medicamento', 'Total Usos', 'Total Dosis'];
                foreach ($this->datos['usos_por_medicamento'] as $uso) {
                    $rows[] = [
                        $uso->medicamento->nombre ?? 'N/A',
                        $uso->total_usos,
                        number_format($uso->total_dosis, 2)
                    ];
                }
                $rows[] = [];
                $rows[] = ['Tipo Medicamento', 'Total Usos'];
                foreach ($this->datos['usos_por_tipo'] as $tipo) {
                    $rows[] = [$tipo->tipo, $tipo->total_usos];
                }
                return $rows;
            }

            public function headings(): array {
                return ['Concepto', 'Valor 1', 'Valor 2'];
            }

            public function title(): string {
                return 'Reporte Medicamentos';
            }
        };

        $nombreArchivo = 'reporte_medicamentos_' . $fechaInicio . '_' . $fechaFin . '.xlsx';
        return Excel::download($export, $nombreArchivo);
    }

    /**
     * Exportar reporte de medicamentos a PDF
     * 
     * Autorización: Solo Admin y Supervisor pueden exportar
     */
    public function exportMedicamentosPdf(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->export(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteMedicamentos($fechaInicio, $fechaFin);

        $pdf = PDF::loadView('admin.reportes.pdf.medicamentos', compact('datos', 'fechaInicio', 'fechaFin'));
        $nombreArchivo = 'reporte_medicamentos_' . $fechaInicio . '_' . $fechaFin . '.pdf';
        return $pdf->download($nombreArchivo);
    }
}