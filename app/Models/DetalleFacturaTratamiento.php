<?php
// En: app/Models/DetalleFacturaTratamiento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleFacturaTratamiento extends Model
{
    use HasFactory;

    protected $table = 'detalle_factura_tratamiento';
    // Asumiendo que el campo es 'Cod_Detalle_Ft' como se vio en la tabla
    protected $primaryKey = 'Cod_Detalle_Ft';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Factura',
        'Cod_Tratamiento',
        'Precio_Tratamiento_Venta',
        'Subtotal',
    ];

    // --- Relaciones ---

    /**
     * Relación: Pertenece a una Factura (MUCHOS a UNO)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'Cod_Factura', 'Cod_Factura');
    }

    /**
     * Relación: Pertenece a un Tratamiento (MUCHOS a UNO)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tratamiento(): BelongsTo
    {
        return $this->belongsTo(Tratamiento::class, 'Cod_Tratamiento', 'Cod_Tratamiento');
    }
}
