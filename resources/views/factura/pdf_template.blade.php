<!DOCTYPE html>
<html>
<head>
    <title>Factura #F-{{ str_pad($factura['Cod_Factura'] ?? '0', 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            width: 50%;
            padding: 5px;
            vertical-align: top;
            border: none;
        }
        .info-table .client-info {
            text-align: right;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            color: #333;
        }
        .totals-wrapper {
            width: 100%;
            overflow: hidden;
        }
        .totals {
            width: 40%;
            margin-top: 20px;
            margin-left: auto;
            margin-right: 0;
        }
        .totals table {
             width: 100%;
             border-collapse: collapse;
        }
        .totals td {
            padding: 5px 0;
            border: none;
        }
        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-size: 10px;
            display: inline-block;
        }
        .paid {
            background-color: #28a745;
        }
        .pending {
            background-color: #ffc107;
            color: #333;
        }
        .cancelled {
            background-color: #dc3545;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>RECIBO DE VENTA</h1>
        <p>Clínica Estética SALUS</p>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <strong>Factura No:</strong> #F-{{ str_pad($factura['Cod_Factura'] ?? 'N/A', 4, '0', STR_PAD_LEFT) }}<br>
                <strong>Fecha:</strong> {{ date('d/m/Y', strtotime($factura['Fecha_Factura'] ?? now())) }}<br>
                <strong>Método de Pago:</strong> {{ $factura['Metodo_Pago'] ?? 'N/A' }}
            </td>
            <td class="client-info">
                <strong>Cliente:</strong> {{ $factura['Nombre'] ?? 'N/A' }} {{ $factura['Apellido'] ?? '' }}<br>
                <strong>DNI:</strong> {{ $factura['DNI'] ?? 'N/A' }}<br>
                <strong>Código:</strong> {{ $factura['Cod_Cliente'] ?? 'N/A' }}
            </td>
        </tr>
    </table>

    @if (!empty($detalles))
        <h3>Detalles de la Venta</h3>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%">Tipo</th>
                    <th>Descripción</th>
                    <th style="width: 10%" class="text-right">Cant.</th>
                    <th style="width: 15%" class="text-right">P. Unit.</th>
                    <th style="width: 15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalles as $detalle)
                    @php
                        // Determinar si es producto o tratamiento
                        $isProduct = isset($detalle['Cod_Producto']);
                        $type = $isProduct ? 'Producto' : 'Tratamiento';

                        if ($isProduct) {
                            $description = $detalle['Nombre_Producto'] ?? 'Producto';
                            $quantity = $detalle['Cantidad'] ?? 1;
                            $unitPrice = $detalle['Precio_Unitario'] ?? 0;
                            $totalLinea = $detalle['Subtotal'] ?? 0;
                        } else {
                            $description = $detalle['Nombre_Tratamiento'] ?? 'Tratamiento';
                            $quantity = 1;
                            $unitPrice = $detalle['Costo'] ?? 0;
                            $totalLinea = $detalle['Subtotal'] ?? 0;
                        }
                    @endphp
                <tr>
                    <td>{{ $type }}</td>
                    <td>{{ $description }}</td>
                    <td class="text-right">{{ $quantity }}</td>
                    <td class="text-right">L. {{ number_format($unitPrice, 2) }}</td>
                    <td class="text-right">L. {{ number_format($totalLinea, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No se encontraron detalles para esta factura.</p>
    @endif

    @php
        // Calcular subtotal
        $subtotalCalculado = 0;
        foreach ($detalles as $detalle) {
            $subtotalCalculado += $detalle['Subtotal'] ?? 0;
        }

        $descuento = $factura['Descuento_Aplicado'] ?? 0;
        $total = $factura['Total_Factura'] ?? 0;
    @endphp

    <div class="totals-wrapper">
        <div class="totals">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">L. {{ number_format($subtotalCalculado, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Descuento:</strong></td>
                    <td class="text-right" style="color: #dc3545;">(L. {{ number_format($descuento, 2) }})</td>
                </tr>
                <tr style="border-top: 2px solid #333;">
                    <td><strong style="font-size: 12px;">TOTAL A PAGAR:</strong></td>
                    <td class="text-right"><strong style="font-size: 12px; color: #28a745;">L. {{ number_format($total, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-right">
                        @php
                            $estadoPago = $factura['Estado_Pago'] ?? 'Pendiente';
                            $class = ($estadoPago == 'Pagada') ? 'paid' : (($estadoPago == 'Anulada') ? 'cancelled' : 'pending');
                        @endphp
                        <span class="status {{ $class }}">{{ $estadoPago }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="text-align: center; margin-top: 50px; border-top: 1px dashed #ccc; padding-top: 10px;">
        <p>¡Gracias por su preferencia!</p>
    </div>
</div>

</body>
</html>
