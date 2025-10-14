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
use App\Models\Correo; // Asegúrate de tener este modelo para la relación con Correo

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
        'Password', // Usar el nombre de la columna real
        'remember_token',
    ];

    // El campo que Laravel usa por defecto para el login es 'email',
    // lo sobreescribimos con un Accessor.
    protected $appends = ['email'];


    // --- MÉTODOS CRÍTICOS DE AUTENTICACIÓN (Compatibilidad Laravel) ---

    public function getAuthIdentifier() { return $this->Cod_Usuario; }
    public function getAuthIdentifierName() { return 'Cod_Usuario'; }
    public function getAuthPassword() { return $this->Password; }
    public function getRememberToken() { return $this->remember_token; }
    public function setRememberToken($value) { $this->remember_token = $value; }
    public function getRememberTokenName() { return 'remember_token'; }

    /**
     * ACCESOR VIRTUAL: Permite que Laravel use 'email' como campo de login
     * consultando el correo principal de la tabla 'correo'.
     */
    public function getEmailAttribute()
    {
        return $this->getCorreoPrincipal();
    }

    /**
     * ACCESOR DE LA VISTA: Define el método para obtener el nombre completo
     * y corregir el error "Call to undefined method".
     */
    public function getNombreCompleto()
    {
        // Retorna el nombre de usuario, ya que es el dato disponible en la tabla 'usuarios'.
        return $this->Nombre_Usuario;
    }


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

    // ... Otras relaciones (empleado)

    // --- LÓGICA DEL MÓDULO DE SEGURIDAD (Permisos RBAC) ---

    /**
     * Verifica si el usuario tiene un rol específico (e.g., 'Administrador').
     */
    public function hasRole(string $roleName): bool
    {
        // Se asegura de que la comparación sea insensible a mayúsculas/minúsculas
        return $this->rol && strtolower($this->rol->Nombre_Rol) === strtolower($roleName);
    }

    /**
     * Alias para verificar si el usuario es Administrador (usa Cod_Rol = 1).
     * Esto reemplaza a la propiedad antigua '$user->es_administrador'.
     */
    public function isAdmin(): bool
    {
        return $this->Cod_Rol === 1;
    }

    /**
     * Verifica si el usuario tiene un permiso específico (select, insert, update, delete)
     * para un Objeto (Citas, Inventario, etc.) consultando la tabla 'accesos'.
     */
    public function hasPermission(string $action, string $objectName): bool
    {
        // 1. Acceso total para el Administrador
        if ($this->isAdmin()) { // Usamos el nuevo alias
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

    // ... (El resto de los métodos se mantienen iguales)

    /** Devuelve la colección de correos de la Persona o una colección vacía. */
    public function correos()
    {
        // Asumiendo que Persona tiene la relación hasMany(Correo)
        return $this->persona ? $this->persona->correos() : null;
    }

    // Si tu modelo necesita la función getCorreoPrincipal, debe ser añadida aquí
    // ya que es llamada por getEmailAttribute().
    public function getCorreoPrincipal()
    {
        // Busca el correo con Cod_Persona. Requiere que la relación 'persona' esté cargada.
        if ($this->persona) {
            // Asume que la relación 'correos' devuelve una colección de correos.
            // Si quieres el primer correo, puedes usar:
            $correo = $this->persona->correos()->first();
            return $correo ? $correo->Correo : null;
        }
        return null; // No hay persona o relación cargada
    }


    // Nota: Es crucial que en tu modelo 'Persona.php' exista la relación 'hasMany' a 'Correo.php'.
}
