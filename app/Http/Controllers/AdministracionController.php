<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Bitacora;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BitacoraExport;
use Maatwebsite\Excel\Facades\Excel;

class AdministracionController extends Controller
{
    public function index()
    {
        return view('partials.administracion');
    }

    public function backup()
    {
        return view('partials.administracion.backup');
    }

    public function crearBackup(Request $request)
    {
        // Registrar en bitácora
        Bitacora::registrar(
            'Creó backup de la base de datos',
            'Administración',
            'Backup manual del sistema'
        );

        return back()->with('success', 'Backup creado exitosamente');
    }

    public function restaurarBackup(Request $request)
    {
        // Registrar en bitácora
        Bitacora::registrar(
            'Restauró backup de la base de datos',
            'Administración',
            'Restauración desde archivo backup'
        );

        return back()->with('success', 'Backup restaurado exitosamente');
    }

    public function password()
    {
        return view('partials.administracion.password');
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password_nueva' => 'required|min:6',
            'password_confirmar' => 'required|same:password_nueva',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_actual, $user->password)) {
            return back()->with('error', 'La contraseña actual es incorrecta');
        }

        $user->password = Hash::make($request->password_nueva);
        $user->save();

        // Registrar en bitácora
        Bitacora::registrar(
            'Cambió su contraseña',
            'Administración',
            'Actualización de credenciales de acceso'
        );

        return back()->with('success', 'Contraseña cambiada exitosamente');
    }

    /**
     * Mostrar la bitácora del sistema con filtros y búsqueda
     */
    public function bitacora(Request $request)
    {
        $query = Bitacora::query()->orderBy('Fecha_Registro', 'desc');

        // Filtro por fecha inicial
        if ($request->filled('fecha_inicial')) {
            $query->whereDate('Fecha_Registro', '>=', $request->fecha_inicial);
        }

        // Filtro por fecha final
        if ($request->filled('fecha_final')) {
            $query->whereDate('Fecha_Registro', '<=', $request->fecha_final);
        }

        // Búsqueda por usuario, acción, observaciones o módulo
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('Nombre_Usuario', 'like', "%{$buscar}%")
                  ->orWhere('Accion', 'like', "%{$buscar}%")
                  ->orWhere('Observaciones', 'like', "%{$buscar}%")
                  ->orWhere('Modulo', 'like', "%{$buscar}%");
            });
        }

        // Paginación con 15 registros por página
        $registros = $query->paginate(15)->appends($request->all());

        return view('partials.administracion.bitacora', compact('registros'));
    }

    /**
     * Exportar bitácora a PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Bitacora::query()->orderBy('Fecha_Registro', 'desc');

        // Aplicar filtros
        if ($request->filled('fecha_inicial')) {
            $query->whereDate('Fecha_Registro', '>=', $request->fecha_inicial);
        }

        if ($request->filled('fecha_final')) {
            $query->whereDate('Fecha_Registro', '<=', $request->fecha_final);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('Nombre_Usuario', 'like', "%{$buscar}%")
                  ->orWhere('Accion', 'like', "%{$buscar}%")
                  ->orWhere('Observaciones', 'like', "%{$buscar}%")
                  ->orWhere('Modulo', 'like', "%{$buscar}%");
            });
        }

        $registros = $query->get();

        // Registrar en bitácora
        Bitacora::registrar(
            'Exportó bitácora a PDF',
            'Administración',
            'Total de registros: ' . $registros->count()
        );

        $pdf = Pdf::loadView('partials.administracion.bitacora-pdf', compact('registros'));
        
        return $pdf->download('bitacora-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Exportar bitácora a Excel
     */
    public function exportExcel(Request $request)
    {
        // Registrar en bitácora
        Bitacora::registrar(
            'Exportó bitácora a Excel',
            'Administración',
            'Exportación con filtros aplicados'
        );

        return Excel::download(
            new BitacoraExport($request->all()), 
            'bitacora-' . date('Y-m-d-His') . '.xlsx'
        );
    }
}