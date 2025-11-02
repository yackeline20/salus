<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ArrayExport;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $baseUrl = 'http://localhost:3000/reporte';

        // Fechas por defecto o las que se envíen desde el formulario
        $fecha_inicio = $request->input('fecha_inicio', now()->startOfMonth()->toDateString());
        $fecha_fin = $request->input('fecha_fin', now()->toDateString());

        try {
            $citas = Http::get("$baseUrl/citas", compact('fecha_inicio', 'fecha_fin'))->json();
            $financiero = Http::get("$baseUrl/financiero", compact('fecha_inicio', 'fecha_fin'))->json();
            $inventario = Http::get("$baseUrl/inventario")->json();
            $compras = Http::get("$baseUrl/compras", compact('fecha_inicio', 'fecha_fin'))->json();
            $tratamientos = Http::get("$baseUrl/tratamientos", compact('fecha_inicio', 'fecha_fin'))->json();
        } catch (\Exception $e) {
            return back()->with('error', 'Error al conectar con el servidor de reportes.');
        }

        return view('reportes', compact(
            'citas',
            'financiero',
            'inventario',
            'compras',
            'tratamientos',
            'fecha_inicio',
            'fecha_fin'
        ));
    }

    // Exportar datos a Excel o PDF
    public function export(Request $request)
    {
        $tipo = $request->query('tipo');
        $formato = $request->query('formato', 'excel');
        $baseUrl = 'http://localhost:3000/reporte';

        // Fechas
        $fecha_inicio = $request->input('fecha_inicio', now()->startOfMonth()->toDateString());
        $fecha_fin = $request->input('fecha_fin', now()->toDateString());

        // Llamada API según tipo de reporte
        $endpoint = match ($tipo) {
            'citas' => "$baseUrl/citas",
            'financiero' => "$baseUrl/financiero",
            'inventario' => "$baseUrl/inventario",
            'compras' => "$baseUrl/compras",
            'tratamientos' => "$baseUrl/tratamientos",
            default => null
        };

        if (!$endpoint) {
            return back()->with('error', 'Tipo de reporte no válido.');
        }

        $data = Http::get($endpoint, compact('fecha_inicio', 'fecha_fin'))->json();

        // Exportar según formato
        if ($formato === 'excel') {
            return Excel::download(new \App\Exports\ArrayExport($data), "reporte_$tipo.xlsx");
        } else {
            $pdf = Pdf::loadView('reportes.partials.pdf', compact('data', 'tipo'));
            return $pdf->download("reporte_$tipo.pdf");
        }
    }
}
