<?php
// En: app/Providers/AuthServiceProvider.php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// 1. IMPORTA los Modelos que serán protegidos (de app/Models/)
use App\Models\Cita;
use App\Models\Factura;
use App\Models\Product;    // Módulo Inventario
use App\Models\Cliente;
use App\Models\Tratamiento; // Módulo Gestión de Servicios (basado en tabla 'tratamiento')
use App\Models\Empleado;   // Módulo Gestión de Personal (basado en tabla 'empleado')
use App\Models\Reporte;    // Módulo Reportes (Modelo Placeholder)

// 2. IMPORTA las Clases de Policies que vas a crear (de app/Policies/)
use App\Policies\CitaPolicy;
use App\Policies\FacturaPolicy;
use App\Policies\InventarioPolicy; // Protege Product
use App\Policies\ClientePolicy;
use App\Policies\ServicioPolicy;   // Protege Tratamiento
use App\Policies\PersonalPolicy;   // Protege Empleado
use App\Policies\ReportePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de Modelo a Policy (Objeto a su Protector).
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Mapeo: [Modelo/Objeto] => [Policy]
        Factura::class      => FacturaPolicy::class,
        Cita::class         => CitaPolicy::class,
        Product::class      => InventarioPolicy::class,
        Cliente::class      => ClientePolicy::class,
        Tratamiento::class  => ServicioPolicy::class,
        Empleado::class     => PersonalPolicy::class,
        Reporte::class      => ReportePolicy::class,
    ];

    /**
     * Registra cualquier servicio de autenticación / autorización.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ----------------------------------------------------
        // 🛡️ DEFINICIÓN DE GATES ESPECIALES (Para Dashboard y Reportes)
        // ----------------------------------------------------

        /**
         * GATE: Acceso a la PÁGINA PRINCIPAL (Dashboard).
         * Usado en DashboardController.php
         */
        Gate::define('access-dashboard', function ($user) {
            // Verifica el permiso de lectura (select) sobre el objeto 'Dashboard'
            // NOTA: 'Dashboard' debe existir en tu tabla 'objetos'.
            return $user->hasPermission('select', 'Dashboard');
        });

        /**
         * GATE: Acceso al MÓDULO DE REPORTES.
         * Usado en ReportesController.php
         */
        Gate::define('view-reports', function ($user) {
            // Verifica el permiso de lectura (select) sobre el objeto 'Reportes'
            // NOTA: 'Reportes' debe existir en tu tabla 'objetos'.
            return $user->hasPermission('select', 'Reportes');
        });
    }
}
