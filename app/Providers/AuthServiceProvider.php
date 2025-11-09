<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// 1. IMPORTA los Modelos que serán protegidos (de app/Models/)
use App\Models\Cita;
use App\Models\Factura;
use App\Models\Product;
use App\Models\Cliente;
use App\Models\Tratamiento;
use App\Models\Empleado;
use App\Models\Reporte;
use App\Models\Usuario; // Asegúrese de que este modelo exista o esté bien ubicado

// 2. IMPORTA las Clases de Policies que vas a crear (de app/Policies/)
use App\Policies\CitaPolicy;
use App\Policies\FacturaPolicy;
use App\Policies\InventarioPolicy;
use App\Policies\ClientePolicy;
use App\Policies\TratamientoPolicy;
use App\Policies\PersonalPolicy;
use App\Policies\ReportePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de Modelo a Policy (Objeto a su Protector).
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // CRÍTICO: Registramos Factura::class con su política correspondiente.
        Factura::class          => FacturaPolicy::class,

        // El resto de políticas registradas:
        Cita::class             => CitaPolicy::class,
        Product::class          => InventarioPolicy::class,
        Cliente::class          => ClientePolicy::class,
        Tratamiento::class      => TratamientoPolicy::class,
        Empleado::class         => PersonalPolicy::class,
        Reporte::class          => ReportePolicy::class,
         Servicio::class => ServicioPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates existentes (se mantienen para permisos de alto nivel)
        Gate::define('access-dashboard', function ($user) {
            return $user->hasPermission('select', 'Dashboard');
        });

        Gate::define('view-reports', function ($user) {
            return $user->hasPermission('select', 'Reportes');
        });
    }
}
