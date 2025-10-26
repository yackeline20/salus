<?php
// En: app/Http/Controllers/FacturaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;

class FacturaController extends Controller
{
    public function __construct()
    {
        // 🟢 SOLUCIÓN: Usamos authorizeResource para proteger todo el controlador
        // Esto automáticamente llama a FacturaPolicy::viewAny para el index,
        // create para create/store, y update/delete para los métodos que manejan modelos.
        $this->authorizeResource(Factura::class, 'factura');
    }

    /**
     * Display a listing of the resource. (Leer/Seleccionar)
     * (Protegido por FacturaPolicy::viewAny)
     */
    public function index()
    {
        // Ya no necesitamos $this->authorize('viewAny', Factura::class);

        // ... Lógica para obtener y listar facturas ...
        $facturas = Factura::all(); // Ejemplo
        return view('factura.index', compact('facturas'));
    }

    /**
     * Show the form for creating a new resource. (Crear/Insertar)
     * (Protegido por FacturaPolicy::create)
     */
    public function create()
    {
        // Ya no necesitamos $this->authorize('create', Factura::class);
        return view('factura.create');
    }

    /**
     * Store a newly created resource in storage. (Crear/Insertar)
     * (Protegido por FacturaPolicy::create)
     */
    public function store(Request $request)
    {
        // Ya no necesitamos $this->authorize('create', Factura::class);
        // ... Lógica para validar y guardar la nueva factura ...
        return redirect()->route('factura.index')->with('success', 'Factura creada.');
    }

    /**
     * Display the specified resource. (Leer/Seleccionar detalle)
     * Si la ruta usa inyección de modelo: public function show(Factura $factura)
     * (Protegido por FacturaPolicy::view)
     */
    public function show(Factura $factura)
    {
        // Ya no necesitamos $this->authorize('view', $factura);
        return view('factura.show', compact('factura'));
    }

    /**
     * Show the form for editing the specified resource. (Actualizar)
     * (Protegido por FacturaPolicy::update)
     */
    public function edit(Factura $factura)
    {
        // Ya no necesitamos $this->authorize('update', $factura);
        return view('factura.edit', compact('factura'));
    }

    /**
     * Update the specified resource in storage. (Actualizar)
     * (Protegido por FacturaPolicy::update)
     */
    public function update(Request $request, Factura $factura)
    {
        // Ya no necesitamos $this->authorize('update', $factura);
        // ... Lógica para validar y actualizar la factura ...
        return redirect()->route('factura.index')->with('success', 'Factura actualizada.');
    }

    /**
     * Remove the specified resource from storage. (Eliminar)
     * (Protegido por FacturaPolicy::delete)
     */
    public function destroy(Factura $factura)
    {
        // Ya no necesitamos $this->authorize('delete', $factura);
        // ... Lógica para eliminar la factura ...
        return redirect()->route('factura.index')->with('success', 'Factura eliminada.');
    }
}
