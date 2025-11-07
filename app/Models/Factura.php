<?php

namespace App\Models;

use App\Traits\BitacoraTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    use HasFactory;
    use BitacoraTrait;

    // Configuración de la tabla
    protected $table = 'factura';
    protected $primaryKey = 'Cod_Factura'; // ✅ VITAL: Define la clave primaria
    public $timestamps = false; // Confirmado que no usa timestamps

    protected $fillable = [
        'Cod_Cliente',
        'Fecha_Factura',
        'Total_Factura',
        'Metodo_Pago',
        'Estado_Pago',
        'Descuento_Aplicado',
    ];

    /**
     * Define explícitamente el nombre de la clave para el Route Model Binding.
     * Esto asegura que Laravel use Cod_Factura en lugar del ID por defecto.
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'Cod_Factura';
    }

    // --- Relaciones ---

    public function cliente(): BelongsTo
    {
        // Factura pertenece a un Cliente
        return $this->belongsTo(Cliente::class, 'Cod_Cliente', 'Cod_Cliente');
    }

    public function detalleProductos(): HasMany
    {
        // Una Factura tiene muchos DetalleFacturaProducto
        return $this->hasMany(DetalleFacturaProducto::class, 'Cod_Factura', 'Cod_Factura');
    }

    public function detalleTratamientos(): HasMany
    {
        // Una Factura tiene muchos DetalleFacturaTratamiento
        return $this->hasMany(DetalleFacturaTratamiento::class, 'Cod_Factura', 'Cod_Factura');
    }
}
