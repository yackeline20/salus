<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Role;
use App\Models\Acceso;
use App\Models\Objeto;
use App\Models\Persona;
use App\Models\Correo;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'Cod_Usuario';

    // IMPORTANTE: Definimos la columna de ID para serialización de autenticación
    protected $authIdentifierName = 'Cod_Usuario';

    // IMPORTANTE: Definimos el nombre real de la columna de la contraseña
    protected $passwordColumn = 'Password';

    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'Cod_Persona',
        'Cod_Rol',
        'Nombre_Usuario',
        'Password',
        'google2fa_secret',
        'google2fa_enabled',
        'Indicador_Usuario_Activo',
        'Indicador_Insertado',
        'Usuario_Registro',
        'Fecha_Registro',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    // --- MÉTODOS CRÍTICOS DE AUTENTICACIÓN (Compatibilidad de Sesión) ---

    // 1. Obtiene el nombre de la columna que almacena el ID (la clave primaria)
    public function getAuthIdentifierName()
    {
        return $this->authIdentifierName;
    }

    // 2. Obtiene el valor del ID. Simplificado para evitar problemas con el array de atributos.
    public function getAuthIdentifier()
    {
        // Devolvemos el valor del primaryKey directamente.
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    // 3. Obtiene el nombre de la columna de la contraseña
    public function getAuthPasswordName()
    {
        return $this->passwordColumn;
    }

    // 4. Obtiene el valor de la contraseña. Simplificado.
    public function getAuthPassword()
    {
        // Devolvemos el valor de la contraseña directamente.
        return $this->getAttribute($this->getAuthPasswordName());
    }

    // --- ACCESORES Y RELACIONES (Se mantienen como estaban) ---

    public function persona() { return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona'); }
    public function rol() { return $this->belongsTo(Role::class, 'Cod_Rol', 'Cod_Rol'); }

    public function getEmailAttribute()
    {
        return $this->getCorreoPrincipal();
    }

    public function getCorreoPrincipal()
    {
        if ($this->persona) {
            $correo = $this->persona->correos()->first();
            return $correo ? $correo->Correo : null;
        }
        return null;
    }

    public function getNombreCompleto()
    {
        return $this->persona
            ? trim($this->persona->Nombre . ' ' . $this->persona->Apellido)
            : $this->Nombre_Usuario;
    }

    // --- LÓGICA DEL MÓDULO DE SEGURIDAD (Permisos RBAC) ---

    /**
     * Verifica si el usuario tiene el rol especificado.
     * @param string $role Nombre del rol (ej: 'admin', 'recepcionista').
     * @return bool
     */
    public function hasRole($role): bool
    {
        // La CitaPolicy busca 'admin' o 'Administrador'
        if ($role === 'admin' || $role === 'Administrador') {
            return $this->Cod_Rol === 1; // Asumiendo Cod_Rol=1 es Administrador
        }

        // Puedes agregar otros roles si es necesario para lógica futura
        if ($role === 'recepcionista') {
            return $this->Cod_Rol === 2; // Asumiendo Cod_Rol=2 es Recepcionista
        }

        return false;
    }

    /**
     * Atajo para isAdmin, ya que muchos middlewares lo usan.
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->Cod_Rol === 1;
    }

    /**
     * Verifica si el rol del usuario tiene un permiso específico sobre un objeto.
     * @param string $action Tipo de permiso (select, insert, update, delete).
     * @param string $objectName Nombre del objeto (módulo) a revisar (ej: 'Citas').
     * @return bool
     */
    public function hasPermission(string $action, string $objectName): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->Cod_Rol) {
            return false;
        }

        $columnMap = [
            'select' => 'Permiso_Seleccionar', 'insert' => 'Permiso_Insertar',
            'update' => 'Permiso_Actualizar', 'delete' => 'Permiso_Eliminar',
        ];
        $column = $columnMap[strtolower($action)] ?? null;

        if (!$column) { return false; }

        // Usamos la clase Objeto importada
        $objeto = Objeto::where('Nombre_Objeto', $objectName)->first();

        if (!$objeto) { return false; }

        // Usamos la clase Acceso importada
        return Acceso::where('Cod_Rol', $this->Cod_Rol)
                     ->where('Cod_Objeto', $objeto->Cod_Objeto)
                     ->where($column, 1)
                     ->exists();
    }
}
