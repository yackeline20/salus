<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Importaciones necesarias para las relaciones y la lógica de permisos
use App\Models\Role;
use App\Models\Acceso;
use App\Models\Objeto;
use App\Models\Empleado;
use App\Models\Persona;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'Cod_Usuario';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Persona',
        'Cod_Rol',
        'Nombre_Usuario',
        'Password',
        'Indicador_Usuario_Activo',
        'Indicador_Insertado',
        'Usuario_Registro',
        'Fecha_Registro',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    // --- MÉTODOS CRÍTICOS DE AUTENTICACIÓN (Compatibilidad Laravel) ---

    public function getAuthIdentifier() { return $this->Cod_Usuario; }
    public function getAuthIdentifierName() { return 'Cod_Usuario'; }
    public function getAuthPassword() { return $this->Password; }
    public function getRememberToken() { return $this->remember_token; }
    public function setRememberToken($value) { $this->remember_token = $value; }
    public function getRememberTokenName() { return 'remember_token'; }

    // --- RELACIONES PARA EL MÓDULO DE SEGURIDAD Y DATOS ---

    /** Relación a Persona (datos personales) */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona');
    }

    /** Relación a Role (tipo de usuario) */
    public function rol()
    {
        return $this->belongsTo(Role::class, 'Cod_Rol', 'Cod_Rol');
    }

    /** Relación a Empleado (para staff como Esteticista o Recepcionista) */
    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'Cod_Persona', 'Cod_Persona');
    }

    // --- LÓGICA DEL MÓDULO DE SEGURIDAD (Permisos RBAC) ---

    /**
     * Verifica si el usuario tiene un rol específico (e.g., 'Administrador').
     */
    public function hasRole(string $roleName): bool
    {
        return $this->rol && $this->rol->Nombre_Rol === $roleName;
    }

    /**
     * Verifica si el usuario tiene un permiso específico (select, insert, update, delete)
     * para un Objeto (Citas, Inventario, etc.) consultando la tabla 'accesos'.
     */
    public function hasPermission(string $action, string $objectName): bool
    {
        // 1. Acceso total para el Administrador
        if ($this->hasRole('Administrador')) {
            return true;
        }

        if (!$this->rol) {
            return false;
        }

        // 2. Mapear la acción a la columna de la tabla 'accesos'
        $columnMap = [
            'select' => 'Permiso_Seleccionar', 'insert' => 'Permiso_Insertar',
            'update' => 'Permiso_Actualizar', 'delete' => 'Permiso_Eliminar',
        ];
        $column = $columnMap[strtolower($action)] ?? null;

        if (!$column) { return false; }

        // 3. Obtener el Cod_Objeto
        $objeto = Objeto::where('Nombre_Objeto', $objectName)->first();

        if (!$objeto) { return false; }

        // 4. Consultar la tabla 'accesos'
        return Acceso::where('Cod_Rol', $this->Cod_Rol)
                     ->where('Cod_Objeto', $objeto->Cod_Objeto)
                     ->where($column, 1) // Debe estar activo (1)
                     ->exists();
    }


    // --- SCOPE Y MÉTODOS DE COMPATIBILIDAD CON PERSONA (Modificados para datos reales) ---

    public function scopeActivos($query)
    {
        return $query->where('Indicador_Usuario_Activo', '1');
    }

    /** Devuelve la colección de correos de la Persona o una colección vacía. */
    public function correos()
    {
        return $this->persona ? $this->persona->correos : collect();
    }

    /** Devuelve la colección de teléfonos de la Persona o una colección vacía. */
    public function telefonos()
    {
        return $this->persona ? $this->persona->telefonos : collect();
    }

    /** Devuelve la colección de direcciones de la Persona o una colección vacía. */
    public function direcciones()
    {
        return $this->persona ? $this->persona->direcciones : collect();
    }

    /** Obtiene la dirección de correo principal de la Persona. */
    public function getCorreoPrincipal()
    {
        // Asumiendo que persona()->correos()->first() devuelve el objeto Correo
        return $this->persona && $this->persona->correos()->first()
               ? $this->persona->correos()->first()->Direccion_Correo
               : 'sin_correo@salus.com'; // Valor por defecto si no hay datos
    }

    /** Obtiene el número de teléfono principal de la Persona. */
    public function getTelefonoPrincipal()
    {
        return $this->persona && $this->persona->telefonos()->first()
               ? $this->persona->telefonos()->first()->Numero_Telefono
               : 'No Asignado';
    }

    /** Obtiene la dirección principal de la Persona. */
    public function getDireccionPrincipal()
    {
        return $this->persona && $this->persona->direcciones()->first()
               ? $this->persona->direcciones()->first()->Direccion_Completa
               : 'Sin Dirección';
    }

    /** Obtiene el Nombre Completo de la Persona. */
    public function getNombreCompleto()
    {
        return $this->persona
               ? $this->persona->Nombre . ' ' . $this->persona->Apellido
               : $this->Nombre_Usuario;
    }

    /** Alias para compatibilidad de nombre. */
    public function getNameAttribute()
    {
        return $this->Nombre_Usuario;
    }

    /** Obtiene el email para reset de contraseña (usa el correo principal). */
    public function getEmailForPasswordReset()
    {
        return $this->getCorreoPrincipal();
    }
}
