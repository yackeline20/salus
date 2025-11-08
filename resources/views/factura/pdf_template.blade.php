<!DOCTYPE html>
<html>
<head>
    {{-- 🛑 IMPORTANTE: USAR DejaVu Sans para que Dompdf soporte caracteres especiales y acentos 🛑 --}}
    <title>Recibo de Factura #F-{{ str_pad($factura['Cod_Factura'] ?? '0', 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Estilos básicos para el PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px; /* Reducido un poco para ahorrar espacio en el PDF */
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

        /* Diseño de tabla para la cabecera (Cliente/Factura Info) */
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

        /* Tabla de Detalles */
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

        /* Bloque de totales (Alineación derecha) */
        .totals-wrapper {
            width: 100%;
            overflow: hidden;
        }
        .totals {
            width: 40%;
            margin-top: 20px;
            margin-left: auto; /* Alinea el bloque a la derecha */
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
        <p>Clínica Estética | Su Dirección Aquí</p>
    </div>

    {{-- Estructura de tabla para información de Factura y Cliente --}}
    <table class="info-table">
        <tr>
            {{-- Columna Izquierda: Info de Factura --}}
            <td>
                <strong>Factura No:</strong> #F-{{ str_pad($factura['Cod_Factura'] ?? 'N/A', 4, '0', STR_PAD_LEFT) }}<br>
                <strong>Fecha de Emisión:</strong> {{ date('d/m/Y', strtotime($factura['Fecha_Factura'] ?? now())) }}<br>
                <strong>Método de Pago:</strong> {{ $factura['Metodo_Pago'] ?? 'N/A' }}
            </td>
            {{-- Columna Derecha: Info de Cliente/Vendedor --}}
            <td class="client-info">
                {{-- Usamos los campos de Persona (Nombre, Apellido, DNI) obtenidos por fetchClientInfo --}}
                <strong>Cliente:</strong> {{ $factura['Nombre'] ?? 'N/A' }} {{ $factura['Apellido'] ?? '' }}<br>
                <strong>DNI:</strong> {{ $factura['DNI'] ?? 'N/A' }}<br>
                <strong>Vendedor:</strong> {{ $factura['Vendedor'] ?? 'N/A' }}
            </td>
        </tr>
    </table>

    {{-- Detalles de los Productos/Servicios --}}
    @if (!empty($detalles))
        <h3>Detalles del Consumo</h3>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%">Tipo</th>
                    <th>Descripción</th>
                    <th style="width: 10%" class="text-right">Cant.</th>
                    <th style="width: 15%" class="text-right">Precio Unit.</th>
                    <th style="width: 15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalles as $detalle)
                    @php
                        $isProduct = isset($detalle['Cod_Producto']);
                        $type = $isProduct ? 'Producto' : 'Tratamiento';

                        if ($isProduct) {
                            $description = $detalle['Nombre_Producto'] ?? 'Producto Desconocido';
                            $quantity = $detalle['Cantidad'] ?? 1;
                            $unitPrice = $detalle['Precio_Unitario'] ?? 0;
                            $totalDetalle = $unitPrice * $quantity;
                        } else {
                            // Concatenamos la descripción del tratamiento
                            $description = ($detalle['Nombre_Tratamiento'] ?? 'Tratamiento Desconocido') .
                                ($detalle['Descripcion'] ? ' (' . $detalle['Descripcion'] . ')' : '');
                            $quantity = 1; // Un tratamiento es una unidad
                            $unitPrice = $detalle['Costo'] ?? 0;
                            $totalDetalle = $unitPrice;
                        }
                    @endphp
                <tr>
                    <td>{{ $type }}</td>
                    <td>{{ $description }}</td>
                    <td class="text-right">{{ $quantity }}</td>
                    {{-- Usamos Lempiras (L.) --}}
                    <td class="text-right">L. {{ number_format($unitPrice, 2) }}</td>
                    {{-- Usamos Lempiras (L.) --}}
                    <td class="text-right">L. {{ number_format($totalDetalle, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No se encontraron detalles de servicios/productos para esta factura.</p>
    @endif

    {{-- Totales --}}
    <div class="totals-wrapper">
        <div class="totals">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    {{-- Usamos Lempiras (L.) --}}
                    <td class="text-right">L. {{ number_format($factura['Subtotal'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Descuento aplicado:</strong></td>
                    {{-- Usamos Lempiras (L.) --}}
                    <td class="text-right" style="color: #dc3545;">(L. {{ number_format($factura['Descuento_Aplicado'] ?? 0, 2) }})</td>
                </tr>
                <tr>
                    {{-- Impuesto --}}
                    <td><strong>Impuestos (IVA/ITBMS):</strong></td>
                    {{-- Usamos Lempiras (L.) --}}
                    <td class="text-right">L. {{ number_format($factura['Impuesto_Total'] ?? 0, 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #333;">
                    <td><strong style="font-size: 12px;">TOTAL A PAGAR:</strong></td>
                    {{-- Usamos Lempiras (L.) --}}
                    <td class="text-right"><strong style="font-size: 12px; color: #28a745;">L. {{ number_format($factura['Total_Factura'] ?? 0, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-right">
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
    </div>

    <div style="text-align: center; margin-top: 50px; border-top: 1px dashed #ccc; padding-top: 10px;">
        <p>¡Gracias por su preferencia!</p>
    </div>
</div>

</body>
</html>



