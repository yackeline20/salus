<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';
    protected $primaryKey = 'Cod_Persona';
    public $timestamps = false; // Deshabilitar timestamps

    protected $fillable = [
        'Nombre',
        'Apellido',
        'DNI',
        'Fecha_Nacimiento',
        'Genero',
    ];

    // --- Relaciones Eloquent ---

    /**
     * Relación UNO a UNO con Cliente.
     */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a UNO con Usuario.
     */
    public function usuario(): HasOne
    {

        return $this->hasOne(Usuario::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Correo.
     */
    public function correos(): HasMany
    {
        return $this->hasMany(Correo::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Telefono
     */
    public function telefonos(): HasMany
    {
        return $this->hasMany(Telefono::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Direccion
     */
    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'Cod_Persona', 'Cod_Persona');
    }


    // --- ACCESORES (Usar como $persona->nombre_completo) ---

    /**
     * Accesor para obtener el nombre completo (Forma idiomática de Laravel).
     * Se accede en la vista como: $persona->nombre_completo
     * @return string
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim($this->Nombre . ' ' . $this->Apellido);
    }

    // --- CORRECCIÓN DEL ERROR DE LA VISTA (getnombreCompleto()) ---

    /**
     * CORRECCIÓN DEL ERROR: Método simple para coincidir con la llamada de la vista.
     * La llamada en la vista es: $persona->getnombreCompleto()
     * Nota: Este método tiene la 'n' de nombre en minúscula, como se ve en la imagen de error.
     * Es mejor cambiar la vista para usar el Accesor (getNombreCompletoAttribute).
     * @return string
     */
    public function getnombreCompleto(): string
    {
        // Llamamos al Accesor real para mantener la lógica centralizada
        return $this->getNombreCompletoAttribute();
    }


    // --- Métodos Auxiliares/Funciones ---

    /**
     * Método auxiliar para obtener el modelo Correo principal (Personal o el primero).
     * @return \App\Models\Correo|null
     */
    public function getCorreoPrincipal()
    {
        // Busca el correo tipo 'Personal' o el primer correo disponible.
        return $this->correos()->where('Tipo_correo', 'Personal')->first() ??
               $this->correos()->first();
    }

    // --- Scopes ---

    /**
     * Scope para buscar personas por DNI.
     * Uso: Persona::byDni('123456')->first()
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $dni
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDni($query, $dni)
    {
        return $query->where('DNI', $dni);
    }

}
