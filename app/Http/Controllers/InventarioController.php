<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        // Aquí debes obtener los productos de tu base de datos o de donde sea que los estés obteniendo.
        $productos = [
            ['id' => 1, 'nombre' => 'Producto 1', 'precio' => 10.00],
            ['id' => 2, 'nombre' => 'Producto 2', 'precio' => 20.00],
        ]; // Esto es solo un ejemplo.

        // Luego, pasa la variable $productos a la vista.
        return view('inventario', compact('productos'));
    }
}