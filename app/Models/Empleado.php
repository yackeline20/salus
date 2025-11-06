<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BitacoraTrait;

class Empleado extends Model
{
    use HasFactory;
    use BitacoraTrait;

    // ✅ DEFINE LA TABLA (aunque uses API, Laravel lo necesita)
    protected $table = 'empleado';
    
    // ✅ DEFINE LA CLAVE PRIMARIA
    protected $primaryKey = 'Cod_Empleado';
    
    public $timestamps = false;
    
    // ✅ IMPORTANTE: No incrementa automáticamente
    public $incrementing = false;

    protected $fillable = [
        'Cod_Empleado',
        'Cod_Persona',
        'Nombre',
        'Apellido',
        'Rol',
        'Fecha_Contratacion',
        'Salario',
        'Disponibilidad',
        'Correo',
        'DNI',
        'Genero',
        'Fecha_Nacimiento'
    ];

    /**
     * Método estático para crear instancias desde datos de API
     */
    public static function fromApiData(array $data): self
    {
        $empleado = new self();
        
        // ✅ CRÍTICO: Marcar como existente
        $empleado->exists = true;
        
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
            // Si los datos vienen planos
            $empleado->Nombre = $data['Nombre'] ?? $data['nombre'] ?? null;
            $empleado->Apellido = $data['Apellido'] ?? $data['apellido'] ?? null;
            $empleado->DNI = $data['DNI'] ?? $data['dni'] ?? null;
            $empleado->Genero = $data['Genero'] ?? $data['genero'] ?? null;
            $empleado->Fecha_Nacimiento = $data['Fecha_Nacimiento'] ?? $data['fecha_nacimiento'] ?? null;
        }
        
        // Asignar correo
        $empleado->Correo = $data['Correo'] ?? $data['correo'] ?? $data['Email'] ?? $data['email'] ?? null;
        
        return $empleado;
    }

    /**
     * Método para obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return trim(($this->Nombre ?? '') . ' ' . ($this->Apellido ?? ''));
    }

    /**
     * Getter para email
     */
    public function getEmailAttribute()
    {
        return $this->Correo ?? '';
    }

    /**
     * ✅ ELIMINADO: No sobrescribir getKey() - Laravel lo maneja automáticamente
     */

    /**
     * ✅ ELIMINADO: No sobrescribir getKeyName() - Ya está definido en $primaryKey
     */

    /**
     * ✅ ELIMINADO: No sobrescribir exists() - Es una propiedad, no un método
     */

    /**
     * Deshabilitar save() porque trabajamos con API
     */
    public function save(array $options = [])
    {
        // No hacer nada - modelo solo lectura desde API
        return true;
    }

    /**
     * Deshabilitar delete() porque trabajamos con API
     */
    public function delete()
    {
        // No hacer nada - la eliminación se hace vía API
        return true;
    }
}