<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bitácora del Sistema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        h1 {
            text-align: center;
            color: #17a2b8;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .info {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #343a40;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            background-color: #17a2b8;
            color: white;
            font-size: 8px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>🕐 Bitácora del Sistema - Clínica SALUS</h1>
    <div class="info">
        <strong>Fecha de generación:</strong> {{ date('d/m/Y H:i:s') }}<br>
        <strong>Usuario:</strong> {{ auth()->user()->Nombre_Usuario ?? 'Sistema' }}<br>
        @if(request('fecha_inicial') || request('fecha_final'))
            <strong>Período:</strong> 
            {{ request('fecha_inicial') ? date('d/m/Y', strtotime(request('fecha_inicial'))) : 'Inicio' }} - 
            {{ request('fecha_final') ? date('d/m/Y', strtotime(request('fecha_final'))) : 'Actual' }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Fecha y Hora</th>
                <th style="width: 12%;">Usuario</th>
                <th style="width: 28%;">Acción</th>
                <th style="width: 10%;">Módulo</th>
                <th style="width: 25%;">Observaciones</th>
                <th style="width: 10%;">IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $registro)
            <tr>
                <td>{{ \Carbon\Carbon::parse($registro->Fecha_Registro)->format('d/m/Y H:i') }}</td>
                <td><strong>{{ $registro->Nombre_Usuario }}</strong></td>
                <td>{{ $registro->Accion }}</td>
                <td>
                    @if($registro->Modulo)
                        <span class="badge">{{ $registro->Modulo }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $registro->Observaciones ?? '-' }}</td>
                <td>{{ $registro->IP_Address ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    No hay registros en la bitácora
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <strong>Total de registros:</strong> {{ $registros->count() }}<br>
        Documento generado automáticamente por el Sistema de Gestión Clínica SALUS
    </div>
</body>
</html>