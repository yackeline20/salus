<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Recupera un usuario por sus credenciales.
     * Sobrescribimos este método para buscar por correo en la tabla 'correo'.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return null;
        }

        // Si las credenciales contienen 'email', usamos nuestro scope personalizado
        if (isset($credentials['email'])) {
            $query = $this->newModelQuery();

            // Usa el scope whereEmail del modelo Usuario
            // Esto busca en: usuarios -> persona -> correos (tabla 'correo')
            $query->whereEmail($credentials['email']);

            return $query->first();
        }

        // Para otras credenciales, usamos el comportamiento por defecto
        return parent::retrieveByCredentials($credentials);
    }
}
