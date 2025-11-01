<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class FacturaStoreRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     * En una API, se recomienda usar Policies para la autorización.
     * Si no usas Policies aquí, asegúrate de autorizar en el controlador.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Esto asume que la autorización se manejará en el controlador
        // (por FacturaController::authorizeResource) o en middleware.
        // Si no tienes policies definidas aún, debe ser 'true'.
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // --- Encabezado de la Factura ---
            'Cod_Cliente' => [
                'required',
                'integer',
                // Asegura que el Cod_Cliente exista en la tabla cliente
                Rule::exists('cliente', 'Cod_Cliente'),
            ],
            // Usamos Date_Factura en el controlador, pero asumiremos Fecha_Factura si se envía
            'Fecha_Factura' => ['nullable', 'date'],
            'Total_Factura' => ['required', 'numeric', 'min:0'],
            'Metodo_Pago'   => ['required', 'string', 'max:50'],
            'Estado_Pago'   => ['required', 'string', Rule::in(['PENDIENTE', 'PAGADA', 'ANULADA'])],
            'Descuento_Aplicado' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // --- Validación de Ítems de Producto ---
            'items_productos' => ['array'],
            // Reglas para cada item dentro del arreglo items_productos
            'items_productos.*.Cod_Producto' => [
                'required',
                'integer',
                Rule::exists('producto', 'Cod_Producto'),
            ],
            'items_productos.*.Cantidad_Vendida' => [
                'required',
                'integer',
                'min:1',
                // Validación para asegurar que haya suficiente stock
                // (La lógica del stock se maneja en el controlador, no aquí)
            ],
            'items_productos.*.Precio_Unitario_Venta' => ['required', 'numeric', 'min:0'],
            'items_productos.*.Subtotal_Producto'     => ['required', 'numeric', 'min:0'],

            // --- Validación de Ítems de Tratamiento ---
            'items_tratamientos' => ['array'],
            // Reglas para cada item dentro del arreglo items_tratamientos
            'items_tratamientos.*.Cod_Tratamiento' => [
                'required',
                'integer',
                Rule::exists('tratamiento', 'Cod_Tratamiento'),
            ],
            'items_tratamientos.*.Precio_Unitario_Venta' => ['required', 'numeric', 'min:0'],
            'items_tratamientos.*.Subtotal_Tratamiento'  => ['required', 'numeric', 'min:0'],
            // Nota: Los tratamientos no necesitan Cantidad_Vendida si se venden por unidad de servicio
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

            'items_productos.*.Cod_Producto.exists' => 'Uno o más códigos de producto no existen.',
            'items_productos.*.Cantidad_Vendida.min' => 'La cantidad vendida de un producto debe ser al menos 1.',

            'items_tratamientos.*.Cod_Tratamiento.exists' => 'Uno o más códigos de tratamiento no existen.',

            // Mensajes genéricos para campos requeridos
            'required' => 'El campo :attribute es obligatorio.',
            'numeric'  => 'El campo :attribute debe ser un número.',
            'integer'  => 'El campo :attribute debe ser un número entero.',
            'array'    => 'El campo :attribute debe ser un arreglo (lista de ítems).',
        ];
    }
}
