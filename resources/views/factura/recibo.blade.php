// resources/views/factura/recibo.blade.php

<!DOCTYPE html>
<html>
<head>
    <title>Recibo de Factura #F-{{ str_pad($factura['Cod_Factura'], 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Estilos básicos para el PDF */
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 0; padding: 0; }
        .container { width: 90%; margin: 20px auto; border: 1px solid #ccc; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #007bff; }
        .header h1 { color: #007bff; margin: 0; font-size: 24px; }
        .info-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; display: inline-block; width: 48%; vertical-align: top; }
        .right { float: right; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; color: #333; }
        .totals { width: 40%; float: right; margin-top: 20px; }
        .totals td { padding: 5px; }
        .status { font-weight: bold; padding: 5px 10px; border-radius: 4px; color: white; }
        .paid { background-color: #28a745; }
        .pending { background-color: #ffc107; }
        .cancelled { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>RECIBO DE VENTA</h1>
        <p>Clínica Estética | Su Dirección Aquí</p>
    </div>

    {{-- Información de Factura --}}
    <div style="clear: both;">
        <div class="info-box">
            <strong>Factura No:</strong> #F-{{ str_pad($factura['Cod_Factura'], 4, '0', STR_PAD_LEFT) }}<br>
            <strong>Fecha de Emisión:</strong> {{ date('d/m/Y', strtotime($factura['Fecha_Factura'] ?? now())) }}<br>
            <strong>Método de Pago:</strong> {{ $factura['Metodo_Pago'] ?? 'N/A' }}
        </div>
        <div class="info-box right">
            <strong>Cliente:</strong> {{ $factura['Cod_Cliente'] ?? 'Cliente No. ' . $factura['Cod_Cliente'] }}<br>
            {{-- Aquí podrías añadir Nombre del Cliente si tu API lo provee --}}
            <strong>Vendedor:</strong> {{ $factura['Vendedor'] ?? 'N/A' }}
        </div>
    </div>
    <div style="clear: both;"></div>

    {{-- Detalles de los Productos/Servicios (Usaremos los detalles de la factura si están disponibles) --}}
    @if (!empty($factura['Detalles_Factura']))
        <h3>Detalles del Consumo</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th style="width: 10%">Cant.</th>
                    <th style="width: 15%">Precio Unit.</th>
                    <th style="width: 15%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($factura['Detalles_Factura'] as $detalle)
                <tr>
                    <td>{{ $detalle['Nombre_Servicio'] ?? $detalle['Nombre_Producto'] ?? 'Item sin descripción' }}</td>
                    <td>{{ $detalle['Cantidad'] ?? 1 }}</td>
                    <td>${{ number_format($detalle['Precio_Unitario'] ?? 0, 2) }}</td>
                    <td>${{ number_format(($detalle['Total_Detalle'] ?? 0), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No se encontraron detalles de servicios/productos para esta factura.</p>
    @endif

    {{-- Totales --}}
    <div class="totals">
        <table style="width: 100%;">
            <tr>
                <td>**Subtotal:**</td>
                <td style="text-align: right;">${{ number_format($factura['Subtotal'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>**Descuento aplicado:**</td>
                <td style="text-align: right; color: #dc3545;">(${{ number_format($factura['Descuento_Aplicado'] ?? 0, 2) }})</td>
            </tr>
            <tr>
                <td>**Impuestos (IVA/ITBMS):**</td>
                <td style="text-align: right;">${{ number_format($factura['Impuesto_Total'] ?? 0, 2) }}</td>
            </tr>
            <tr style="border-top: 2px solid #333;">
                <td><strong style="font-size: 14px;">TOTAL A PAGAR:</strong></td>
                <td style="text-align: right;"><strong style="font-size: 14px; color: #28a745;">${{ number_format($factura['Total_Factura'] ?? 0, 2) }}</strong></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;">
                    @php
                        $statusClass = strtolower($factura['Estado_Pago'] ?? 'pending');
                        $statusText = $factura['Estado_Pago'] ?? 'Pendiente';
                        $class = ($statusClass == 'pagada') ? 'paid' : (($statusClass == 'anulada' || $statusClass == 'cancelada') ? 'cancelled' : 'pending');
                    @endphp
                    <span class="status {{ $class }}">{{ $statusText }}</span>
                </td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>

    <div style="text-align: center; margin-top: 50px; border-top: 1px dashed #ccc; padding-top: 10px;">
        <p>¡Gracias por su preferencia!</p>
    </div>
</div>

</body>
</html>
