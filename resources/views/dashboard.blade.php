@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
{{-- Header personalizado --}}
@stop

@section('content')
<div class="container-fluid p-0">
    <div class="welcome-header">
        <div class="welcome-content">
            <h1 class="text-xl">¡Hola, {{ $persona->nombre_completo }}!</h1>
            <p>Aquí tienes un resumen de las actividades del día en tu clínica estética.</p>
        </div>
        <div class="welcome-date">
            <span class="date">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}</span>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-6 col-md-12 mb-4"> {{-- Ocupa la mitad del ancho (lg-6) --}}
            <div class="stat-card blue-border">
                <div class="stat-header">
                    {{-- Fondo para Visión --}}
                    <span class="stat-icon blue-bg" style="background: linear-gradient(135deg, #1ABC9C, #16A085);">
                        <i class="fas fa-bullseye"></i> {{-- Ícono de objetivo/visión --}}
                    </span>
                    <span class="stat-label">NUESTRA VISIÓN ✨</span>
                </div>
                {{-- Título de la tarjeta --}}
                <div class="stat-value" style="font-size: 20px; font-weight: 600; color: #34495e; margin-bottom: 5px;">
                    ¿Qué queremos lograr?
                </div>
                {{-- Contenido de la Visión --}}
                <div class="stat-change" style="background: none; padding: 0; display: block;">
                    <p style="font-size: 15px; color: #555; line-height: 1.6; margin-top: 10px;">{{ $vision }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 mb-4"> {{-- Ocupa la otra mitad del ancho (lg-6) --}}
            <div class="stat-card green-border">
                <div class="stat-header">
                    {{-- Fondo para Misión --}}
                    <span class="stat-icon green-bg" style="background: linear-gradient(135deg, #9B59B6, #8E44AD);">
                        <i class="fas fa-hand-holding-heart"></i> {{-- Ícono de corazón/misión --}}
                    </span>
                    <span class="stat-label">NUESTRA MISIÓN 🌿</span>
                </div>
                {{-- Título de la tarjeta --}}
                <div class="stat-value" style="font-size: 20px; font-weight: 600; color: #34495e; margin-bottom: 5px;">
                    ¿Cuál es nuestro propósito?
                </div>
                {{-- Contenido de la Misión --}}
                <div class="stat-change" style="background: none; padding: 0; display: block;">
                    <p style="font-size: 15px; color: #555; line-height: 1.6; margin-top: 10px;">{{ $mision }}</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card modern-card" style="min-height: 500px;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-check"></i> Calendario de Citas</h3>
                </div>
                <div class="card-body">
                    {{-- Aquí se inicializará FullCalendar --}}
                    <div id="fullCalendar"></div> 
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card modern-card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Citas Próximas y Hoy</h3>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="appointment-list">

                        {{-- Verificar si hay citas y iterar sobre ellas --}}
                        @if (count($citasProximas) > 0)
                            @foreach ($citasProximas as $cita)
                                
                                {{-- 
                                    MODIFICACIÓN: 
                                    Extraemos los datos de 'Notas_Internas' y 'Hora_Inicio' 
                                    para que coincida con la lógica de la vista de Citas (citas.blade.php).
                                --}}
                                @php
                                    // 1. Parsear Notas_Internas (como en el JS de citas.blade.php)
                                    $notasPartes = explode(' - ', $cita['Notas_Internas'] ?? '');
                                    
                                    // 2. Obtener Paciente
                                    // str_replace para quitar el prefijo, trim para espacios, ?: para el valor por defecto
                                    $nombrePaciente = trim(str_replace('Paciente:', '', $notasPartes[0] ?? '')) ?: 'Paciente Desconocido';
                                    
                                    // 3. Obtener Servicio
                                    // Usamos $notasPartes[1] si existe, si no, 'Servicio no especificado'
                                    $tratamiento = trim(str_replace('Servicio:', '', $notasPartes[1] ?? '')) ?: 'Servicio no especificado';
                                    
                                    // 4. Obtener Hora (del campo Hora_Inicio, como en el JS)
                                    $horaCita = '00:00';
                                    if (!empty($cita['Hora_Inicio'])) {
                                        // Convertir 'HH:MM:SS' a un objeto DateTime para formatear
                                        $horaObj = \DateTime::createFromFormat('H:i:s', $cita['Hora_Inicio']);
                                        if ($horaObj) {
                                            $horaCita = $horaObj->format('H:i'); // e.g., "06:00" o "11:00"
                                        }
                                    }
                                    
                                    // 5. Obtener Fecha (del campo Fecha_Cita)
                                    $fechaObj = new \DateTime($cita['Fecha_Cita'] ?? 'now');
                                    $fechaMuestra = $fechaObj->format('d/M'); // e.g., "20/Nov"
                                @endphp

                                <div class="appointment-item">
                                    {{-- Usar las variables corregidas --}}
                                    <div class="appointment-time">{{ $horaCita }}</div>
                                    <div class="appointment-info">
                                        <div class="patient-name">{{ $nombrePaciente }}</div>
                                        <div class="treatment-type">{{ $tratamiento }}</div>
                                    </div>
                                    <span class="appointment-date">{{ $fechaMuestra }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted p-3">
                                <i class="fas fa-check-circle" style="color: #2ECC71;"></i>
                                ¡No hay citas programadas próximas!
                            </p>
                        @endif
                    </div>
                </div>
            </div>


            <div class="card modern-card security-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i> Seguridad
                    </h3>
                </div>
                <div class="card-body">
                    <div class="security-item">
                        <div class="security-info">
                            <h6 class="mb-1">Autenticación de Dos Factores</h6>
                            <p class="text-muted small mb-2">
                                @if(auth()->user()->google2fa_enabled)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Activa
                                    </span>
                                    <span class="d-block mt-1">Tu cuenta está protegida</span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Inactiva
                                    </span>
                                    <span class="d-block mt-1">Agrega seguridad extra</span>
                                @endif
                            </p>
                        </div>
                        <div class="security-action">
                            <a href="{{ route('2fa.setup') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-cog"></i> Configurar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@stop

@section('css')
{{-- Estilos para FullCalendar --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<style>
    /* Reset de algunos estilos de AdminLTE */
    .content-wrapper {
        background: #f8f9fa !important;
    }

    /* Header de bienvenida */
    .welcome-header {
        background: linear-gradient(135deg, #c9a876 0%, #d4b896 100%);
        border-radius: 20px;
        padding: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(201, 168, 118, 0.2);
    }

    .welcome-content h1 {
        color: white;
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .welcome-content p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 16px;
        margin: 0;
    }

    .welcome-date {
        text-align: right;
        color: white;
    }

    .welcome-date .date {
        display: block;
        font-size: 14px;
        opacity: 0.9;
    }

    .welcome-date .time {
        display: block;
        font-size: 24px;
        font-weight: 600;
        margin-top: 5px;
    }

    /* Tarjetas de estadísticas */
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        height: 100%;
        border-left: 4px solid;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-card.blue-border {
        border-left-color: #1ABC9C;
    }

    .stat-card.green-border {
        border-left-color: #9B59B6;
    }

    .stat-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 20px;
        color: white;
    }

    .stat-icon.blue-bg {
        background: linear-gradient(135deg, #1ABC9C, #16A085);
    }

    .stat-icon.green-bg {
        background: linear-gradient(135deg, #9B59B6, #8E44AD);
    }

    .stat-label {
        color: #8C8C8C;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 10px;
    }

    .stat-change {
        background: #E8F5E9;
        padding: 8px 15px;
        border-radius: 20px;
        display: inline-block;
    }

    .stat-change.positive {
        background: #E8F5E9;
        color: #52C41A;
    }

    .stat-change.negative {
        background: #FFEBEE;
        color: #F44336;
    }

    .stat-change span {
        font-size: 13px;
        font-weight: 500;
    }

    /* Cards modernos */
    .modern-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .modern-card .card-header {
        background: white;
        border-bottom: 1px solid #f0f0f0;
        border-radius: 20px 20px 0 0;
        padding: 20px 25px;
    }

    .modern-card .card-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #2C3E50;
    }

    /* Lista de citas */
    .appointment-list {
        padding: 10px 0;
    }

    .appointment-item {
        display: flex;
        align-items: center;
        padding: 15px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-radius: 12px;
        transition: background 0.3s ease;
    }

    .appointment-item:hover {
        background: #e9ecef;
    }

    .appointment-time {
        font-size: 16px;
        font-weight: 600;
        color: #5DADE2; /* Color azul para la hora */
        margin-right: 20px;
        min-width: 50px;
    }
    
    .appointment-info {
        flex-grow: 1; /* Ocupa el espacio disponible */
    }

    .patient-name {
        font-weight: 600;
        color: #2C3E50;
        margin-bottom: 4px;
    }

    .treatment-type {
        font-size: 13px;
        color: #8C8C8C;
    }

    .appointment-date {
        font-size: 14px;
        font-weight: 500;
        color: #8C8C8C;
        margin-left: 10px; /* Pequeño espacio a la derecha */
    }

    /* ✅ ESTILOS PARA LA TARJETA DE SEGURIDAD */
    .security-card {
        border-left: 4px solid #FF6B6B;
    }

    .security-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .security-info h6 {
        font-size: 15px;
        font-weight: 600;
        color: #2C3E50;
        margin-bottom: 5px;
    }

    .security-info .badge {
        font-size: 11px;
        padding: 5px 10px;
        font-weight: 600;
    }

    .security-info .badge-success {
        background: #52C41A;
    }

    .security-info .badge-warning {
        background: #FFA500;
    }

    .security-action .btn {
        padding: 8px 16px;
        font-size: 13px;
        border-radius: 8px;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .welcome-header {
            flex-direction: column;
            text-align: center;
        }

        .welcome-date {
            margin-top: 20px;
            text-align: center;
        }

        .stat-value {
            font-size: 28px;
        }

        .welcome-content h1 {
            font-size: 24px;
        }

        .security-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .security-action {
            margin-top: 15px;
            width: 100%;
        }

        .security-action .btn {
            width: 100%;
        }
    }
</style>
@stop

@section('js')
{{-- Scripts para FullCalendar --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('fullCalendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    list: 'Lista'
                },
                events: '/api/citas-calendario', // ⬅️ Asegúrate de que esta ruta API exista y devuelva las citas
                eventTimeFormat: { // Formato de hora en 12h
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                }
            });
            calendar.render();
        }

        // ❌ Se eliminó el script del gráfico (Chart.js)
        // Estaba causando un error porque no existía el 'chartWeekly'
    });
</script>
@stop