@extends('adminlte::page')

{{-- Extraemos los campos que sí se guardan en la tabla Factura --}}
@php
    $facturaId = $factura['Cod_Factura'] ?? 'N/A';
    $totalFactura = $factura['Total_Factura'] ?? 0.00;
    $descuentoAplicado = $factura['Descuento_Aplicado'] ?? 0.00;
    $impuestoTotal = $factura['Impuesto_Total'] ?? 0.00;
    $subtotal = $factura['Subtotal'] ?? $totalFactura - $impuestoTotal + $descuentoAplicado;
    // Usamos $subtotal para manejar el caso de que la API no lo envíe explícitamente

    // Campos de cliente que el controlador DEBE haber consultado a través de Cod_Cliente y Persona
    $nombreCliente = ($factura['Nombre'] ?? 'N/A') . ' ' . ($factura['Apellido'] ?? '');
    $identidadCliente = $factura['DNI'] ?? 'N/A';
    // Teléfono y Email no se guardan directamente en Factura ni Persona según tu endpoint,
    // por lo que los marcamos como N/A o asumimos que vienen de una consulta adicional.
    $telefonoCliente = $factura['Telefono'] ?? 'N/A';
    $emailCliente = $factura['Email'] ?? 'N/A';
@endphp

@section('title', "Detalle de Factura #F-{$facturaId}")

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-invoice-dollar"></i> Factura Generada #F-{{ $facturaId }}
        </h1>
        <a href="{{ route('factura.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar al Listado
        </a>
    </div>
@stop

@section('content')

{{-- 1. Manejo de Error de Conexión o API --}}
@if ($apiError)
    <div class="alert alert-danger alert-dismissible">
        <h5><i class="icon fas fa-ban"></i> Error Crítico</h5>
        Error: No se pudo obtener la información de la factura #{{ $facturaId }}.
        <p>Por favor, regrese al listado e intente recargar.</p>
    </div>
@else
    {{-- 2. Bloque Principal de la Factura (Estilo Imprimible) --}}
    <div class="invoice p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-receipt"></i> Recibo de Pago.
                    <small class="float-right">Fecha: {{ \Carbon\Carbon::parse($factura['Fecha_Factura'] ?? date('Y-m-d'))->format('d/m/Y') }}</small>
                </h4>
            </div>
        </div>

        {{-- INFO DE CLIENTE, PAGO Y FACTURA --}}
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <strong>Datos del Cliente</strong>
                <address>
                    {{-- Usamos la información obtenida a través de la tabla Persona --}}
                    <strong>{{ $nombreCliente }}</strong><br>
                    ID/DNI: {{ $identidadCliente }}<br>
                    Teléfono: {{ $telefonoCliente }}<br>
                    Email: {{ $emailCliente }}
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <strong>Detalles de Pago</strong>
                <address>
                    <strong>Método:</strong> {{ $factura['Metodo_Pago'] ?? 'N/A' }}<br>
                    <strong>Estado:</strong> <span class="badge @if(($factura['Estado_Pago'] ?? '') === 'PAGADA') badge-success @elseif(($factura['Estado_Pago'] ?? '') === 'PENDIENTE') badge-warning @else badge-danger @endif">
                        {{ $factura['Estado_Pago'] ?? 'N/A' }}
                    </span><br>
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <b>Factura #F-{{ $facturaId }}</b><br>
                <br>
                <b>Fecha Emisión:</b> {{ \Carbon\Carbon::parse($factura['Fecha_Factura'] ?? date('Y-m-d'))->format('d/m/Y') }}<br>
                {{-- Cod_Cliente solo se muestra si es relevante, si no, se deja en N/A --}}
                <b>Cód. Cliente:</b> {{ $factura['Cod_Cliente'] ?? 'N/A' }}<br>
            </div>
        </div>
        <hr>

        {{-- 🛑 LA TABLA DE DETALLES SE HA ELIMINADO COMPLETAMENTE --}}

        <div class="row mt-3">
            <div class="col-6">
                <p class="lead">Información:</p>
                <div class="alert alert-warning p-2">
                    <i class="fas fa-info-circle"></i>
                    Esta vista solo muestra la información de cabecera guardada. Para ver la factura completa (con detalles), use el botón **"Exportar como PDF"**.
                </div>
            </div>
            <div class="col-6">
                <p class="lead">Resumen de Factura</p>

                <div class="table-responsive">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:50%">Subtotal (Guardado):</th>
                            <td class="text-right">L. {{ number_format((float)$subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Impuesto (Guardado):</th>
                            <td class="text-right">L. {{ number_format((float)$impuestoTotal, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Descuento Aplicado:</th>
                            <td class="text-right text-danger">- L. {{ number_format((float)$descuentoAplicado, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total a Pagar (Final):</th>
                            <td class="text-right">
                                <h3>L. {{ number_format((float)$totalFactura, 2) }}</h3>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="row no-print mt-3">
            <div class="col-12">
                <button type="button" class="btn btn-default" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir Factura
                </button>
                @if ($facturaId !== 'N/A')
                    {{-- 🟢 BOTÓN VERIFICADO: Llama al exportPdf y abre en nueva pestaña --}}
                    <a href="{{ route('factura.export_pdf', $facturaId) }}" class="btn btn-info float-right" style="margin-right: 5px;" target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar como PDF
                    </a>
                @endif
                <a href="{{ route('factura.index') }}" class="btn btn-success float-right" style="margin-right: 5px;">
                    <i class="far fa-check-circle"></i> Terminar
                </a>
                {{-- 🛑 BOTÓN ELIMINADO: Anteriormente aquí estaba el link a 'factura.recibo', ya no es necesario --}}
            </div>
        </div>
    </div>
@endif

@stop
