<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar y estructurar los datos de la factura
 * que llegan desde el formulario de Blade (JSON enviado por JS).
 *
 * NOTA: Asume que las tablas se llaman 'clliente', 'producto' y 'tratamiento'.
 */
class FacturaStoreRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     * En un sistema real, se verificaría aquí el permiso del usuario.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Asumimos autorización por defecto para este ejemplo.
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // --- REGLAS DE LA CABECERA (Tabla Factura) ---
            'Cod_Cliente' => ['required', 'integer', 'exists:cliente,Cod_Cliente'], // Verifica que exista en la tabla 'clliente'
            'Fecha_Factura' => ['required', 'date_format:Y-m-d'],
            'Total_Factura' => ['required', 'numeric', 'min:0.01'],
            'Metodo_Pago' => ['required', 'string', 'max:50'],
            'Estado_Pago' => ['required', 'string', 'max:50'],
            'Descuento_Aplicado' => ['required', 'numeric', 'min:0'],
            'Observacion' => ['nullable', 'string', 'max:500'],
            'Servicio_Medico' => ['nullable', 'string', 'max:100'],

            // --- REGLAS DE DETALLES DE PRODUCTO ---
            'detalles_producto' => ['nullable', 'array'],
            // Las claves dentro de los detalles deben ser el nombre de la columna en la BD
            'detalles_producto.*.Cod_Producto' => [
                'required_with:detalles_producto',
                'integer',
                'exists:producto,Cod_Producto' // Verifica que exista en la tabla 'producto'
            ],
            'detalles_producto.*.Cantidad' => [
                'required_with:detalles_producto.*.Cod_Producto',
                'integer',
                'min:1'
            ],
            'detalles_producto.*.Precio_Unitario' => [
                'required_with:detalles_producto.*.Cod_Producto',
                'numeric',
                'min:0.01'
            ],
            'detalles_producto.*.Total_Detalle' => [
                'required_with:detalles_producto.*.Cod_Producto',
                'numeric',
                'min:0.01'
            ],

            // --- REGLAS DE DETALLES DE TRATAMIENTO ---
            'detalles_tratamiento' => ['nullable', 'array'],
            // Las claves dentro de los detalles deben ser el nombre de la columna en la BD
            'detalles_tratamiento.*.Cod_Tratamiento' => [
                'required_with:detalles_tratamiento',
                'integer',
                'exists:tratamiento,Cod_Tratamiento' // Verifica que exista en la tabla 'tratamiento'
            ],
            'detalles_tratamiento.*.Sesiones' => [
                'required_with:detalles_tratamiento.*.Cod_Tratamiento',
                'integer',
                'min:1'
            ],
            'detalles_tratamiento.*.Costo' => [
                'required_with:detalles_tratamiento.*.Cod_Tratamiento',
                'numeric',
                'min:0.01'
            ],
            'detalles_tratamiento.*.Precio_Unitario' => [
                'required_with:detalles_tratamiento.*.Cod_Tratamiento',
                'numeric',
                'min:0.01'
            ],

            // --- Campos de Cálculo (CORREGIDOS a snake_case) ---
            // Estos nombres DEBEN COINCIDIR con los inputs ocultos del formulario.
            'sub_total_calculado' => ['required', 'numeric', 'min:0'],
            'isv_calculado' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Define los nombres de los atributos para mensajes de error amigables.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'Cod_Cliente' => 'Código de Cliente',
            'Fecha_Factura' => 'Fecha de la Factura',
            'Total_Factura' => 'Total a Pagar',
            'Metodo_Pago' => 'Método de Pago',
            'Estado_Pago' => 'Estado del Pago',
            'Descuento_Aplicado' => 'Descuento Aplicado',
            'detalles_producto' => 'Detalles de Productos',
            'detalles_tratamiento' => 'Detalles de Tratamientos',
            'detalles_producto.*.Cod_Producto' => 'Código de Producto',
            'detalles_tratamiento.*.Cod_Tratamiento' => 'Código de Tratamiento',
            'detalles_producto.*.Cantidad' => 'Cantidad de Producto',
            'detalles_tratamiento.*.Sesiones' => 'Cantidad de Sesiones',
            'detalles_tratamiento.*.Costo' => 'Costo del Tratamiento',

            // --- Atributos para los campos calculados ---
            'sub_total_calculado' => 'Subtotal Calculado',
            'isv_calculado' => 'ISV Calculado',
        ];
    }
}
