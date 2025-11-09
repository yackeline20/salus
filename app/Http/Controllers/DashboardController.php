<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Persona;
use App\Models\Usuario;

class DashboardController extends Controller
{
    protected $apiUrl = 'http://localhost:3000';

    /**
     * Muestra la página principal (Dashboard).
     * El acceso ya está garantizado por el middleware 'auth' en web.php.
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar que el usuario esté autenticado (aunque el middleware 'auth' ya lo hace)
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesión inválida o expirada.');
        }

        // Detectar el tipo de usuario y preparar los datos
        if ($user instanceof Usuario) {
            // Es un Usuario (empleado/administrador)
            return $this->dashboardForUsuario($user);
        } elseif ($user instanceof Persona) {
            // Es una Persona (cliente/externo)
            return $this->dashboardForPersona($user);
        }

        // Fallback (no debería llegar aquí)
        return redirect()->route('login')->with('error', 'Tipo de usuario no reconocido');
    }

    /**
     * Obtiene las citas programadas para hoy del API REST.
     */
    private function fetchCitasHoy()
    {
        $citasHoy = [];
        $today = now()->format('Y-m-d'); // Fecha de hoy en formato YYYY-MM-DD

        try {
            // Llamada al endpoint de la API REST para obtener todas las citas
            // Dado que Sel_Cita(?) con NULL obtiene todas, llamamos sin el parámetro 'cod'.
            $response = Http::timeout(5)->get("{$this->apiUrl}/cita");

            if ($response->successful()) {
                $allCitas = $response->json();
                
                // El API REST de Node.js devuelve rows[0] que es un array de objetos
                if (is_array($allCitas)) {
                    // Filtrar las citas para obtener solo las programadas para hoy o el futuro.
                    $citasHoy = collect($allCitas)->filter(function ($cita) use ($today) {
                        // Asumimos que la cita tiene un campo 'Fecha_Cita' en formato YYYY-MM-DD
                        $fechaCita = date('Y-m-d', strtotime($cita['Fecha_Cita'] ?? ''));

                        // Retorna true si la cita es hoy, o en el futuro
                        return $fechaCita >= $today; 
                    })
                    // Opcional: Ordenar por hora si la hora viene en un campo aparte o dentro de la Fecha_Cita
                    ->sortBy('Fecha_Cita') 
                    ->take(5) // Tomar solo las 5 más próximas
                    ->values()
                    ->toArray();
                }

            } else {
                \Log::error("Error al conectar con API de Citas: {$response->status()}");
            }
        } catch (\Exception $e) {
            \Log::error("Excepción al llamar al API de Citas: " . $e->getMessage());
        }

        return $citasHoy;
    }

    /**
     * Dashboard para usuarios de la tabla 'usuarios'
     */
    private function dashboardForUsuario(Usuario $usuario)
    {
        // Obtener la data de las citas
        $citasProximas = $this->fetchCitasHoy();

        // Textos de Misión y Visión fijos
        $mision = 'En Clínica Estética Salus nos dedicamos a mejorar la salud, la belleza y el bienestar de nuestros pacientes mediante tratamientos especializados en escleroterapia, masajes terapéuticos y estética avanzada. Brindamos atención profesional, segura y personalizada, integrando tecnología moderna y un equipo altamente calificado comprometido con la excelencia en cada detalle.';
        $vision = 'Ser reconocidos como la clínica estética líder en la región, destacando por la calidad de nuestros tratamientos en escleroterapia, masajes y estética integral. Aspiramos a consolidar un modelo de atención que combine innovación, ética y resultados visibles, promoviendo el bienestar y la confianza de cada persona que elige Salus.';

        // La vista 'dashboard' recibirá la información del usuario
        return view('dashboard', [
            'persona' => $usuario->persona, // Asumiendo que existe la relación 'persona'
            'nombre_completo' => $usuario->Nombre_Usuario,
            'correo' => 'Usuario del sistema',
            'es_usuario' => true,
            // PASAMOS LAS VARIABLES A LA VISTA: [clave_en_vista] => $variable_php
            'mision' => $mision,
            'vision' => $vision,
            'citasProximas' => $citasProximas
        ]);
    }

    /**
     * Dashboard para usuarios de la tabla 'persona'
     */
    private function dashboardForPersona(Persona $persona)
    {
        // El cliente probablemente no necesita ver todas las citas
        $citasProximas = []; // Podríamos filtrar las citas solo para este cliente si tu API lo permite.
        
        // Obtener el correo principal de la persona
        // Asumiendo que getCorreoPrincipal() está definido en tu modelo Persona
        $correo = $persona->getCorreoPrincipal();
        
        // TEXTOS DE MISIÓN Y VISIÓN FIJOS
        $mision = 'En Clínica Estética Salus nos dedicamos a mejorar la salud, la belleza y el bienestar de nuestros pacientes mediante tratamientos especializados en escleroterapia, masajes terapéuticos y estética avanzada. Brindamos atención profesional, segura y personalizada, integrando tecnología moderna y un equipo altamente calificado comprometido con la excelencia en cada detalle.';
        $vision = 'Ser reconocidos como la clínica estética líder en la región, destacando por la calidad de nuestros tratamientos en escleroterapia, masajes y estética integral. Aspiramos a consolidar un modelo de atención que combine innovación, ética y resultados visibles, promoviendo el bienestar y la confianza de cada persona que elige Salus.';

        return view('dashboard', [
            'persona' => $persona,
            'nombre_completo' => $persona->getNombreCompleto(),
            'correo' => $correo ? $correo->Correo : 'Sin correo',
            'es_usuario' => false,
            // PASAMOS LAS VARIABLES A LA VISTA
            'mision' => $mision,
            'vision' => $vision
        ]);
    }
}