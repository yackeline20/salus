<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'tratamiento';

    /**
     * La clave primaria de la tabla
     */
    protected $primaryKey = 'Cod_Tratamiento';

    /**
     * Indica si el modelo debe gestionar timestamps (created_at, updated_at)
     * Como la tabla no tiene estos campos, lo deshabilitamos
     */
    public $timestamps = false;

    /**
     * Los atributos que son asignables en masa
     */
    protected $fillable = [
        'Nombre_Tratamiento',
        'Descripcion',
        'Precio_Estandar',
        'Duracion_Estimada_Min'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'Precio_Estandar' => 'decimal:2',
        'Duracion_Estimada_Min' => 'integer'
    ];

    /**
     * Relación con Composicion_Tratamiento
     * Un tratamiento puede tener muchas composiciones (productos asociados)
     */
    public function composiciones()
    {
        return $this->hasMany(ComposicionTratamiento::class, 'Cod_Tratamiento', 'Cod_Tratamiento');
    }

    /**
     * Relación con Detalle_Cita_Tratamiento
     * Un tratamiento puede estar en muchas citas
     */
    public function detallesCitas()
    {
        return $this->hasMany(DetalleCitaTratamiento::class, 'Cod_Tratamiento', 'Cod_Tratamiento');
    }

    /**
     * Scope para buscar tratamientos por nombre
     */
    public function scopeBuscarPorNombre($query, $nombre)
    {
        return $query->where('Nombre_Tratamiento', 'LIKE', "%{$nombre}%");
    }

    /**
     * Scope para obtener tratamientos activos (si implementas un campo de estado)
     */
    public function scopeActivos($query)
    {
        return $query; // Implementar lógica si hay campo de estado
    }

    /**
     * Accessor para formatear el precio con el símbolo de Lempira
     */
    public function getPrecioFormateadoAttribute()
    {
        return 'L ' . number_format($this->Precio_Estandar, 2);
    }

    /**
     * Accessor para obtener la duración formateada
     */
    public function getDuracionFormateadaAttribute()
    {
        return $this->Duracion_Estimada_Min . ' min';
    }
}