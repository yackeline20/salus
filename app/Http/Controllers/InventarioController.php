<?php
// En: app/Http/Controllers/InventarioController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class InventarioController extends Controller
{
    public function index()
    {
        // 🛡️ Autorizar la visualización del listado (viewAny)
        // Llama a InventarioPolicy::viewAny() para verificar el permiso 'select' en el objeto 'Inventario'.
        $this->authorize('viewAny', Product::class);

        // Aquí debes obtener los productos de tu base de datos o de donde sea que los estés obteniendo.
        $productos = [
            ['id' => 1, 'nombre' => 'Producto 1', 'precio' => 10.00],
            ['id' => 2, 'nombre' => 'Producto 2', 'precio' => 20.00],
        ]; // Esto es solo un ejemplo.

        // Luego, pasa la variable $productos a la vista.
        return view('inventario', compact('productos'));
    }
}
