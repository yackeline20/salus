<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar y estructurar los datos de la factura
 * que llegan desde el formulario de Blade (JSON enviado por JS).
 *
 * NOTA: Asume que las tablas se llaman 'cliente', 'producto' y 'tratamiento'.
 * Corregida la referencia del cliente a 'cliente'.
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
            // CORREGIDO: Usando 'cliente'
            'Cod_Cliente' => ['required', 'integer', 'exists:cliente,Cod_Cliente'],
            'Fecha_Factura' => ['required', 'date_format:Y-m-d'],
            'Total_Factura' => ['required', 'numeric', 'min:0.01'],
            'Metodo_Pago' => ['required', 'string', 'max:50'],
            'Estado_Pago' => ['required', 'string', 'max:50'],
            'Descuento_Aplicado' => ['required', 'numeric', 'min:0'],
            'Observacion' => ['nullable', 'string', 'max:500'],
            'Servicio_Medico' => ['nullable', 'string', 'max:100'],

            // --- REGLAS DE DETALLES DE PRODUCTO ---
            'detalles_producto' => ['nullable', 'array'],
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
            'detalles_tratamiento.*.Cod_Tratamiento' => [
                'required_with:detalles_tratamiento',
                'integer',
                'exists:tratamiento,Cod_Tratamiento' // Verifica que exista en la tabla 'tratamiento'
            ],
            'detalles_tratamiento.*.Sesiones' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'detalles_tratamiento.*.Costo' => [
                'required_with:detalles_tratamiento.*.Cod_Tratamiento',
                'numeric',
                'min:0.01'
            ],
            'detalles_tratamiento.*.Precio_Unitario' => [
                'nullable',
                'numeric',
                'min:0.01'
            ],

            // --- Campos de Cálculo ---
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
            // Cabecera
            'Cod_Cliente' => 'el Código de Cliente',
            'Fecha_Factura' => 'la Fecha de la Factura',
            'Total_Factura' => 'el Total a Pagar',
            'Metodo_Pago' => 'el Método de Pago',
            'Estado_Pago' => 'el Estado del Pago',
            'Descuento_Aplicado' => 'el Descuento Aplicado',
            'Observacion' => 'la Observación',
            'Servicio_Medico' => 'el Servicio Médico',

            // Detalles de Producto
            'detalles_producto' => 'los Detalles de Productos',
            'detalles_producto.*.Cod_Producto' => 'el Código de Producto',
            'detalles_producto.*.Cantidad' => 'la Cantidad de Producto',
            'detalles_producto.*.Precio_Unitario' => 'el Precio Unitario del Producto',
            'detalles_producto.*.Total_Detalle' => 'el Subtotal del Detalle de Producto',

            // Detalles de Tratamiento
            'detalles_tratamiento' => 'los Detalles de Tratamientos',
            'detalles_tratamiento.*.Cod_Tratamiento' => 'el Código de Tratamiento',
            'detalles_tratamiento.*.Sesiones' => 'la Cantidad de Sesiones',
            'detalles_tratamiento.*.Costo' => 'el Costo del Tratamiento',
            'detalles_tratamiento.*.Precio_Unitario' => 'el Precio Unitario del Tratamiento',

            // Cálculos
            'sub_total_calculado' => 'el Subtotal Calculado',
            'isv_calculado' => 'el ISV Calculado',
        ];
    }

    /**
     * Obtiene los mensajes de validación personalizados para las reglas definidas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Mensajes generales de la cabecera
            'Cod_Cliente.exists' => 'El Código de Cliente no es válido o no existe en la tabla :cliente.',
            'Fecha_Factura.date_format' => 'El formato de la Fecha de la Factura debe ser AAAA-MM-DD.',
            'Total_Factura.min' => 'El Total a Pagar debe ser mayor a cero.',

            // Mensajes para los arreglos (detalles)
            'detalles_producto.array' => 'El campo de detalles de producto debe ser un arreglo.',
            'detalles_tratamiento.array' => 'El campo de detalles de tratamiento debe ser un arreglo.',

            // Mensajes específicos para los ítems de Producto
            'detalles_producto.*.Cod_Producto.required_with' => 'El Código de Producto es obligatorio si se especifica un detalle de producto.',
            'detalles_producto.*.Cod_Producto.exists' => 'El Código de Producto seleccionado no existe.',
            'detalles_producto.*.Cantidad.min' => 'La cantidad de producto debe ser al menos 1.',
            'detalles_producto.*.Precio_Unitario.min' => 'El precio unitario de producto debe ser al menos :min.',

            // Mensajes específicos para los ítems de Tratamiento
            'detalles_tratamiento.*.Cod_Tratamiento.required_with' => 'El Código de Tratamiento es obligatorio si se especifica un detalle de tratamiento.',
            'detalles_tratamiento.*.Cod_Tratamiento.exists' => 'El Código de Tratamiento seleccionado no existe.',
            'detalles_tratamiento.*.Sesiones.min' => 'La cantidad de sesiones debe ser al menos 1.',
            'detalles_tratamiento.*.Costo.min' => 'El costo del tratamiento debe ser al menos :min.',
            'detalles_tratamiento.*.Precio_Unitario.min' => 'El precio unitario del tratamiento debe ser al menos :min.',

            // Mensajes para campos calculados
            'required' => 'El campo :attribute es obligatorio.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'numeric' => 'El campo :attribute debe ser un número.',
        ];
    }
}
