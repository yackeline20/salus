<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacturaStoreRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // NOTA IMPORTANTE: Estas reglas asumen que el formulario de Blade o JS enviará
        // los detalles como arrays anidados: items_productos y items_tratamientos.

        return [
            // --- 1. Encabezado de la Factura ---
            'Cod_Cliente' => [
                'required',
                'integer',
                // Rule::exists('cliente', 'Cod_Cliente'), // Se comenta: la verificación de FK se delega a la API de Node.js.
            ],
            'Fecha_Factura' => ['nullable', 'date'],
            // El Total_Factura debe ser requerido.
            'Total_Factura' => ['required', 'numeric', 'min:0'],
            'Metodo_Pago'   => ['required', 'string', 'max:50'],
            'Estado_Pago'   => ['required', 'string', Rule::in(['PENDIENTE', 'PAGADA', 'ANULADA'])],
            'Descuento_Aplicado' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // --- 2. Validación de Ítems de Producto ---
            // items_productos es requerido si items_tratamientos es nulo/vacío, y viceversa.
            'items_productos' => [
                'nullable',
                'array',
                Rule::requiredIf(empty($this->input('items_tratamientos'))),
                Rule::when(empty($this->input('items_tratamientos')), ['min:1'])
            ],

            // Reglas para cada item de producto
            'items_productos.*.Cod_Producto' => [
                'required',
                'integer',
                // Rule::exists('producto', 'Cod_Producto'), // Se comenta: la verificación de FK se delega a la API de Node.js.
            ],
            'items_productos.*.Cantidad_Vendida' => [
                'required',
                'integer',
                'min:1',
            ],
            'items_productos.*.Precio_Unitario_Venta' => ['required', 'numeric', 'min:0'],
            'items_productos.*.Subtotal_Producto'     => ['required', 'numeric', 'min:0'],

            // --- 3. Validación de Ítems de Tratamiento ---
            'items_tratamientos' => [
                'nullable',
                'array',
                Rule::requiredIf(empty($this->input('items_productos'))),
                Rule::when(empty($this->input('items_productos')), ['min:1'])
            ],

            // Reglas para cada item de tratamiento
            'items_tratamientos.*.Cod_Tratamiento' => [
                'required',
                'integer',
                // Rule::exists('tratamiento', 'Cod_Tratamiento'), // Se comenta: la verificación de FK se delega a la API de Node.js.
            ],
            'items_tratamientos.*.Precio_Unitario_Venta' => ['required', 'numeric', 'min:0'],
            'items_tratamientos.*.Subtotal_Tratamiento'  => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Personaliza los mensajes de error de validación.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'Cod_Cliente.exists' => 'El código de cliente proporcionado no existe en la base de datos.',
            'Estado_Pago.in'     => 'El estado de pago debe ser PENDIENTE, PAGADA o ANULADA.',

            'items_productos.required' => 'La factura debe contener al menos un producto o un tratamiento.',
            'items_tratamientos.required' => 'La factura debe contener al menos un producto o un tratamiento.',
            'items_productos.min' => 'La factura debe contener al menos un producto.',
            'items_tratamientos.min' => 'La factura debe contener al menos un tratamiento.',

            'items_productos.*.Cod_Producto.exists' => 'Uno o más códigos de producto no existen.',
            'items_productos.*.Cantidad_Vendida.min' => 'La cantidad vendida de un producto debe ser al menos 1.',
            'items_tratamientos.*.Cod_Tratamiento.exists' => 'Uno o más códigos de tratamiento no existen.',

            // Mensajes genéricos
            'required' => 'El campo :attribute es obligatorio.',
            'numeric'  => 'El campo :attribute debe ser un número.',
            'integer'  => 'El campo :attribute debe ser un número entero.',
            'array'    => 'El campo :attribute debe ser un arreglo (lista de ítems).',
            'min'      => 'El campo :attribute debe tener un valor mínimo de :min.',
        ];
    }
}
