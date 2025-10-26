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
use App\Models\Usuario;

// 2. IMPORTA las Clases de Policies que vas a crear (de app/Policies/)
use App\Policies\CitaPolicy;
use App\Policies\FacturaPolicy;
use App\Policies\InventarioPolicy;
use App\Policies\ClientePolicy;
use App\Policies\TratamientoPolicy; // 🟢 Usamos el nombre TratamientoPolicy
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
        Factura::class      => FacturaPolicy::class,
        Cita::class         => CitaPolicy::class,
        Product::class      => InventarioPolicy::class, // ✅ CORRECCIÓN: Usamos Product::class, el modelo importado
        Cliente::class      => ClientePolicy::class,
        Tratamiento::class  => TratamientoPolicy::class,
        Empleado::class     => PersonalPolicy::class,
        Reporte::class      => ReportePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gates existentes
        Gate::define('access-dashboard', function ($user) {
            return $user->hasPermission('select', 'Dashboard');
        });

        Gate::define('view-reports', function ($user) {
            return $user->hasPermission('select', 'Reportes');
        });
    }
}
