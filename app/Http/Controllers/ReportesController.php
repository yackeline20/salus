<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ArrayExport;

class ReportesController extends Controller
{
    protected $apiBaseUrl = "http://localhost:3000";

    public function index()
    {
        // Inicializamos TODAS las variables que la vista espera
        $citas = [];
        $financiero = [];
        $inventario = [];
        $compras = [];
        $tratamientos = [];

        $tipo = 'citas'; // Establece un tipo por defecto
        $fecha_inicio = now()->startOfMonth()->toDateString();
        $fecha_fin = now()->toDateString();

        // Nuevas variables para estadísticas (se deben cargar con la primera consulta, o se dejan en 0)
        $total_citas = 0;
        $ingresos_totales = 0;
        $productos_stock = 0;
        $total_tratamientos = 0;
     
        // Para las tarjetas, haremos una consulta inicial para el periodo por defecto.
        // Consulta inicial al API para datos de las tarjetas (simplificado para el ejemplo)
        try {
            // Reporte de Inventario no necesita fechas
            $inventario_data = Http::get("$this->apiBaseUrl/reporte/inventario")->json('data') ?? [];
         
            // Citas, Financiero y Tratamientos SÍ necesitan fechas
            $citas_data = Http::get("$this->apiBaseUrl/reporte/citas", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
            $financiero_data = Http::get("$this->apiBaseUrl/reporte/financiero", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
            $tratamientos_data = Http::get("$this->apiBaseUrl/reporte/tratamientos", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
         
            // Cálculo de estadísticas para las tarjetas (simplificado, asumiendo estructura de datos)
            $total_citas = count($citas_data);
            $ingresos_totales = array_sum(array_column($financiero_data, 'Total_Factura')); // Asumiendo que 'Total_Factura' es la columna
            $productos_stock = array_sum(array_column($inventario_data, 'Cantidad_En_Stock')); // Asumiendo que 'Cantidad_En_Stock' es la columna
            $total_tratamientos = count($tratamientos_data);

            // Por defecto, carga el reporte de citas al iniciar
            $citas = $citas_data;

        } catch (\Throwable $th) {
            // En caso de error de conexión a la API
            $error = "Error al conectar con el servicio de reportes: " . $th->getMessage();
            return view('reportes', compact(
                'citas', 'financiero', 'inventario', 'compras', 'tratamientos', 'tipo', 'fecha_inicio', 'fecha_fin',
                'total_citas', 'ingresos_totales', 'productos_stock', 'total_tratamientos', 'error'
            ));
        }

        // Enviamos todo a la vista
        return view('reportes', compact(
            'citas', 'financiero', 'inventario', 'compras', 'tratamientos',
            'tipo', 'fecha_inicio', 'fecha_fin',
            'total_citas', 'ingresos_totales', 'productos_stock', 'total_tratamientos'
        ));
    }

    // En ReportesController.php

    public function obtenerReporte(Request $request)
    {
        $tipo = $request->input('tipo');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        // Inicialización de variables de reportes
        $citas = [];
        $financiero = [];
        $inventario = [];
        $compras = [];
        $tratamientos = [];
        $data = []; // Almacena el reporte solicitado
    
        // El tipo debe ser válido para continuar
        if (!in_array($tipo, ['citas', 'financiero', 'inventario', 'compras', 'tratamientos'])) {
            return back()->with('error', "Tipo de reporte no válido");
        }

        // Lógica de conversión de fechas (mantener)
        if ($fecha_inicio && str_contains($fecha_inicio, '/')) {
            $fecha_inicio = implode('-', array_reverse(explode('/', $fecha_inicio)));
        }
        if ($fecha_fin && str_contains($fecha_fin, '/')) {
            $fecha_fin = implode('-', array_reverse(explode('/', $fecha_fin)));
        }

        // --- Obtener el reporte solicitado ---
        try {
            $params = ['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin];
            $url = "$this->apiBaseUrl/reporte/$tipo";

            if ($tipo === 'inventario') {
                $data = Http::get($url)->json('data') ?? [];
            } else {
                $data = Http::get($url, $params)->json('data') ?? [];
            }

            // Asigna los datos al array correcto
            switch ($tipo) {
                case 'citas': $citas = $data; break;
                case 'financiero': $financiero = $data; break;
                case 'inventario': $inventario = $data; break;
                case 'compras': $compras = $data; break;
                case 'tratamientos': $tratamientos = $data; break;
            }

            // --- Recalcular estadísticas para las tarjetas (Mismo código que en index) ---
            // Se puede optimizar para solo llamar los necesarios, pero por claridad se dejan así:
        
            $inventario_data = Http::get("$this->apiBaseUrl/reporte/inventario")->json('data') ?? [];
            $citas_data = Http::get("$this->apiBaseUrl/reporte/citas", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
            $financiero_data = Http::get("$this->apiBaseUrl/reporte/financiero", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
            $tratamientos_data = Http::get("$this->apiBaseUrl/reporte/tratamientos", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
        
            $total_citas = count($citas_data);
            $ingresos_totales = array_sum(array_column($financiero_data, 'Total_Factura'));
            $productos_stock = array_sum(array_column($inventario_data, 'Cantidad_En_Stock'));
            $total_tratamientos = count($tratamientos_data);

        } catch (\Throwable $th) {
            return back()->with('error', "Error al obtener el reporte: " . $th->getMessage());
        }
    
        // Devolvemos los datos y las variables de estadísticas a la vista
        return view('reportes', compact(
            'citas', 'financiero', 'inventario', 'compras', 'tratamientos',
            'tipo', 'fecha_inicio', 'fecha_fin',
            'total_citas', 'ingresos_totales', 'productos_stock', 'total_tratamientos'
        ));
    }



    public function exportar(Request $request)
    {
        $tipo = $request->query('tipo');
        $formato = $request->query('formato', 'excel');
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');

        switch ($tipo) {

            case 'citas':
                $data = Http::get("$this->apiBaseUrl/reporte/citas", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
                break;

            case 'financiero':
                $data = Http::get("$this->apiBaseUrl/reporte/financiero", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
                break;

            case 'inventario':
                $data = Http::get("$this->apiBaseUrl/reporte/inventario")->json('data') ?? [];
                break;

            case 'compras':
                $data = Http::get("$this->apiBaseUrl/reporte/compras", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
                break;

            case 'tratamientos':
                $data = Http::get("$this->apiBaseUrl/reporte/tratamientos", compact('fecha_inicio', 'fecha_fin'))->json('data') ?? [];
                break;

            default:
                return back()->with('error', "Tipo de reporte no válido");
        }

        // Exportar a Excel
        if ($formato === 'excel') {
            return Excel::download(new ArrayExport($data), "reporte_$tipo.xlsx");
        }

        // Exportar a PDF
        if ($formato === 'pdf') {
            // Asegúrate de que tienes la vista 'reportes.partials.pdf' creada
            $pdf = Pdf::loadView('reportes.partials.pdf', compact('data', 'tipo', 'fecha_inicio', 'fecha_fin'));
            return $pdf->download("reporte_$tipo.pdf");
        }

        // Si no se especifica ni excel ni pdf
        return back()->with('error', "Formato de exportación no válido.");
    }
}
