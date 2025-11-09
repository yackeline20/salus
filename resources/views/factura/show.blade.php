@extends('adminlte::page')

@php
    $facturaId = $factura['Cod_Factura'] ?? 'N/A';
    $totalFactura = $factura['Total_Factura'] ?? 0.00;
    $descuentoAplicado = $factura['Descuento_Aplicado'] ?? 0.00;

    // Calcular subtotal desde los detalles
    $subtotalCalculado = 0;
    if (!empty($detalles)) {
        foreach ($detalles as $detalle) {
            $subtotalCalculado += $detalle['Subtotal'] ?? 0;
        }
    }

    $impuestoTotal = 0.00;
    $nombreCliente = ($factura['Nombre'] ?? 'N/A') . ' ' . ($factura['Apellido'] ?? '');
    $identidadCliente = $factura['DNI'] ?? 'N/A';
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

@if ($apiError)
    <div class="alert alert-danger alert-dismissible">
        <h5><i class="icon fas fa-ban"></i> Error Crítico</h5>
        <p>Error: No se pudo obtener la información de la factura #{{ $facturaId }}.</p>
        <p><strong>Posibles causas:</strong></p>
        <ul>
            <li>El servidor Node.js no está corriendo (ejecuta: <code>node index.js</code>)</li>
            <li>El endpoint <code>/facturas/{{ $facturaId }}/completa</code> no existe o está mal ubicado</li>
            <li>El procedimiento almacenado <code>Sel_Factura_Completa</code> no existe en la base de datos</li>
        </ul>
        <p><a href="{{ route('factura.index') }}" class="btn btn-primary">Regresar al Listado</a></p>
    </div>
@else
    <div class="invoice p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-receipt"></i> Recibo de Pago.
                    <small class="float-right">Fecha: {{ \Carbon\Carbon::parse($factura['Fecha_Factura'] ?? date('Y-m-d'))->format('d/m/Y') }}</small>
                </h4>
            </div>
        </div>

        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <strong>Datos del Cliente</strong>
                <address>
                    <strong>{{ $nombreCliente }}</strong><br>
                    ID/DNI: {{ $identidadCliente }}<br>
                    Código Cliente: {{ $factura['Cod_Cliente'] ?? 'N/A' }}
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <strong>Detalles de Pago</strong>
                <address>
                    <strong>Método:</strong> {{ $factura['Metodo_Pago'] ?? 'N/A' }}<br>
                    <strong>Estado:</strong>
                    <span class="badge @if(($factura['Estado_Pago'] ?? '') === 'Pagada') badge-success @elseif(($factura['Estado_Pago'] ?? '') === 'Pendiente') badge-warning @else badge-danger @endif">
                        {{ $factura['Estado_Pago'] ?? 'N/A' }}
                    </span>
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <b>Factura #F-{{ $facturaId }}</b><br>
                <br>
                <b>Fecha Emisión:</b> {{ \Carbon\Carbon::parse($factura['Fecha_Factura'] ?? date('Y-m-d'))->format('d/m/Y') }}<br>
            </div>
        </div>
        <hr>

        <div class="row mt-3">
            <div class="col-6">
                <p class="lead">Información:</p>
                <div class="alert alert-info p-2">
                    <i class="fas fa-info-circle"></i>
                    Esta vista muestra solo la información de cabecera. Para ver la factura completa con todos los detalles, use el botón <strong>"Exportar como PDF"</strong>.
                </div>
            </div>
            <div class="col-6">
                <p class="lead">Resumen de Factura</p>

                <div class="table-responsive">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:50%">Subtotal:</th>
                            <td class="text-right">L. {{ number_format($subtotalCalculado, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Descuento Aplicado:</th>
                            <td class="text-right text-danger">- L. {{ number_format((float)$descuentoAplicado, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total a Pagar:</th>
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
                    <a href="{{ route('factura.export_pdf', $facturaId) }}" class="btn btn-info float-right" style="margin-right: 5px;" target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar como PDF
                    </a>
                @endif
                <a href="{{ route('factura.index') }}" class="btn btn-success float-right" style="margin-right: 5px;">
                    <i class="far fa-check-circle"></i> Terminar
                </a>
            </div>
        </div>
    </div>
@endif

@stop
