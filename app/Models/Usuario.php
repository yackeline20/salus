<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Role;
use App\Models\Acceso;
use App\Models\Objeto;
use App\Models\Persona;
use App\Models\Correo;

// Aseguramos que el modelo implemente la interfaz de restablecimiento
class Usuario extends Authenticatable implements CanResetPassword
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

    // ----------------------------------------------------------------------
    // 🟢 FUNCIONES CRÍTICAS PARA RESTABLECIMIENTO DE CONTRASEÑA 🟢
    // (Resuelve: Unknown column 'email' in 'where clause')
    // ----------------------------------------------------------------------

    /**
     * 1. OBLIGA a Laravel a buscar el usuario por el correo en la tabla 'correo'.
     * Esta función sobrescribe la consulta SQL fallida (SELECT * FROM `usuarios` WHERE `email` =...)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email El email ingresado en el formulario.
     */
    public function scopeWhereEmail(Builder $query, string $email): Builder
    {
        // Usa whereHas para buscar en la relación anidada: Usuario -> Persona -> Correos
        return $query->whereHas('persona.correos', function (Builder $query) use ($email) {
            // CRÍTICO: Buscamos la columna 'Correo' en la tabla 'correo'
            $query->where('Correo', $email);
        });
    }

    /**
     * 2. Obtiene el correo electrónico para el envío del enlace.
     * (Laravel llama a esta función para saber a dónde enviar el correo).
     */
    public function getEmailForPasswordReset(): string
    {
        // Usamos tu método existente que ya sabe cómo encontrar el correo principal
        return $this->getCorreoPrincipal() ?? '';
    }

    // ----------------------------------------------------------------------
    // --- MÉTODOS CRÍTICOS DE AUTENTICACIÓN (Compatibilidad de Sesión) ---

    // 1. Obtiene el nombre de la columna que almacena el ID (la clave primaria)
    public function getAuthIdentifierName()
    {
        return $this->authIdentifierName;
    }

    // 2. Obtiene el valor del ID.
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    // 3. Obtiene el nombre de la columna de la contraseña
    public function getAuthPasswordName()
    {
        return $this->passwordColumn;
    }

    // 4. Obtiene el valor de la contraseña.
    public function getAuthPassword()
    {
        return $this->getAttribute($this->getAuthPasswordName());
    }

    // --- ACCESORES Y RELACIONES ---

    // La relación persona()
    public function persona() { return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona'); }

    // La relación rol()
    public function rol() { return $this->belongsTo(Role::class, 'Cod_Rol', 'Cod_Rol'); }

    // ACCESOR: Permite acceder al correo como $usuario->email
    public function getEmailAttribute()
    {
        return $this->getCorreoPrincipal();
    }

    /**
     * Obtiene el Correo principal del usuario (Personal > Primero encontrado).
     * @return string|null
     */
    public function getCorreoPrincipal()
    {
        // El operador opcional (?) de PHP 8+ es más limpio que muchos if/else
        $correos = $this->persona->correos ?? null;

        if ($correos && $correos->isNotEmpty()) {
            // 1. Busca el correo con Tipo_correo = 'Personal'
            $personal = $correos->where('Tipo_correo', 'Personal')->first();

            // 2. Si existe el personal, lo devuelve. Si no, devuelve el Correo del primer elemento.
            return $personal?->Correo ?? $correos->first()->Correo ?? null;
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

    public function hasRole($role): bool
    {
        if ($role === 'admin' || $role === 'Administrador') { return $this->Cod_Rol === 1; }
        if ($role === 'recepcionista') { return $this->Cod_Rol === 2; }
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->Cod_Rol === 1;
    }

    public function hasPermission(string $action, string $objectName): bool
    {
        if ($this->isAdmin()) { return true; }
        if (!$this->Cod_Rol) { return false; }

        $columnMap = [
            'select' => 'Permiso_Seleccionar', 'insert' => 'Permiso_Insertar',
            'update' => 'Permiso_Actualizar', 'delete' => 'Permiso_Eliminar',
        ];
        $column = $columnMap[strtolower($action)] ?? null;

        if (!$column) { return false; }

        $objeto = Objeto::where('Nombre_Objeto', $objectName)->first();
        if (!$objeto) { return false; }

        return Acceso::where('Cod_Rol', $this->Cod_Rol)
                     ->where('Cod_Objeto', $objeto->Cod_Objeto)
                     ->where($column, 1)
                     ->exists();
    }
}

