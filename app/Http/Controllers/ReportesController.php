<?php
// En: app/Http/Controllers/ReportesController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // 👈 NECESARIO: Importar la fachada Gate

class ReportesController extends Controller
{
    /**
     * Muestra la página principal de Reportes.
     */
    public function index()
    {
        // 🛡️ APLICACIÓN DE SEGURIDAD RBAC (Gate)
        // Llama al Gate 'view-reports' (o el nombre que hayas configurado)
        // Esto verifica si el rol del usuario tiene permiso 'select' en el objeto 'Reportes'.
        Gate::authorize('view-reports');

        return view('reportes');
    }
}
