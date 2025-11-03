<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BitacoraTrait;  // ← AGREGAR ESTA LÍNEA

class Empleado extends Model
{
    use HasFactory;
    use BitacoraTrait;  // ← AGREGAR ESTA LÍNEA

    // COMENTAMOS estas propiedades ya que NO usamos base de datos local
    // protected $table = 'empleado';
    // protected $primaryKey = 'Cod_Empleado';
    // public $timestamps = false;

    // En su lugar, definimos que este modelo es para uso interno de Laravel
    // pero los datos vienen de la API externa
    protected $table = null; // No usa tabla de Laravel
    public $timestamps = false; // No usa timestamps

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'Cod_Empleado',
        'Cod_Persona',
        'Nombre',        // Agregamos estos campos para mostrar en las vistas
        'Apellido',      // ya que vendrán de la API
        'Rol',
        'Fecha_Contratacion',
        'Salario',
        'Disponibilidad',
        'Correo',        // Agregamos campo para email
        'DNI',           // Agregamos campo para DNI
        'Genero',        // Agregamos campo para género
        'Fecha_Nacimiento' // Agregamos campo para fecha de nacimiento
    ];

    /**
     *  NUEVO: Método estático para crear instancias desde datos de API
     */
    public static function fromApiData(array $data): self
    {
        $empleado = new self();
        
        // Asignar datos principales del empleado
        $empleado->Cod_Empleado = $data['Cod_Empleado'] ?? $data['cod_empleado'] ?? null;
        $empleado->Cod_Persona = $data['Cod_Persona'] ?? $data['cod_persona'] ?? null;
        $empleado->Rol = $data['Rol'] ?? $data['rol'] ?? null;
        $empleado->Fecha_Contratacion = $data['Fecha_Contratacion'] ?? $data['fecha_contratacion'] ?? null;
        $empleado->Salario = $data['Salario'] ?? $data['salario'] ?? null;
        $empleado->Disponibilidad = $data['Disponibilidad'] ?? $data['disponibilidad'] ?? 'Activo';
        
        // Si los datos vienen con información de persona anidada
        if (isset($data['Persona']) && is_array($data['Persona'])) {
            $persona = $data['Persona'];
            $empleado->Nombre = $persona['Nombre'] ?? $persona['nombre'] ?? null;
            $empleado->Apellido = $persona['Apellido'] ?? $persona['apellido'] ?? null;
            $empleado->DNI = $persona['DNI'] ?? $persona['dni'] ?? null;
            $empleado->Genero = $persona['Genero'] ?? $persona['genero'] ?? null;
            $empleado->Fecha_Nacimiento = $persona['Fecha_Nacimiento'] ?? $persona['fecha_nacimiento'] ?? null;
        } else {
            // Si los datos vienen planos (directamente en el array principal)
            $empleado->Nombre = $data['Nombre'] ?? $data['nombre'] ?? null;
            $empleado->Apellido = $data['Apellido'] ?? $data['apellido'] ?? null;
            $empleado->DNI = $data['DNI'] ?? $data['dni'] ?? null;
            $empleado->Genero = $data['Genero'] ?? $data['genero'] ?? null;
            $empleado->Fecha_Nacimiento = $data['Fecha_Nacimiento'] ?? $data['fecha_nacimiento'] ?? null;
        }
        
        // Asignar correo (puede venir de diferentes fuentes)
        $empleado->Correo = $data['Correo'] ?? $data['correo'] ?? $data['Email'] ?? $data['email'] ?? null;
        
        return $empleado;
    }

    /**
     * Método para simular la búsqueda de empleados
     * En realidad los datos vendrán del ApiService
     */
    public static function getFromApi()
    {
        // Este método es solo para mantener compatibilidad
        // La lógica real está en el ApiService
        return collect([]);
    }

    /**
     * Relación con Persona (para mantener estructura si otras partes del código la usan)
     * Pero en realidad los datos vienen de la API
     */
    public function persona()
    {
        // Retornamos una relación vacía o simulada
        return new class {
            public $Nombre = '';
            public $Apellido = '';
            public $DNI = '';
            public $Correo = '';
            public $Genero = '';
            public $Fecha_Nacimiento = '';
        };
    }
<<<<<<< Updated upstream
=======

    /**
     * Métodos de acceso para compatibilidad
     */
    public function getNombreCompletoAttribute()
    {
        return trim(($this->Nombre ?? '') . ' ' . ($this->Apellido ?? ''));
    }

    public function getEmailAttribute()
    {
        return $this->Correo ?? '';
    }

    /**
     *  NUEVO: Método para obtener el ID para políticas de autorización
     */
    public function getKey()
    {
        return $this->Cod_Empleado;
    }

    /**
     *  NUEVO: Método para indicar qué campo se usa como clave primaria
     */
    public function getKeyName()
    {
        return 'Cod_Empleado';
    }

    /**
     * Indicar que este modelo no persiste en BD local
     */
    public function save(array $options = [])
    {
        // No guardar en BD local - los datos se guardan via API
        return false;
    }

    public function delete()
    {
        // No eliminar de BD local - se elimina via API
        return false;
    }

    /**
     *  NUEVO: Método para verificar si el empleado existe
     * Útil para políticas de autorización
     */
    public function exists()
    {
        return !empty($this->Cod_Empleado);
    }
>>>>>>> Stashed changes
}