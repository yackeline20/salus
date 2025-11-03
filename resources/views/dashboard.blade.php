@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
{{-- Header personalizado --}}
@stop

@section('content')
<div class="container-fluid p-0">
    <!-- Header Welcome -->
    <div class="welcome-header">
        <div class="welcome-content">
            <h1 class="text-xl">¡Hola, {{ $persona->nombre_completo }}!</h1>
            <p>Aquí tienes un resumen de las actividades del día en tu clínica estética.</p>
        </div>
        <div class="welcome-date">
            <span class="date">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mt-4">
        <!-- Citas Hoy -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card blue-border">
                <div class="stat-header">
                    <span class="stat-icon blue-bg">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <span class="stat-label">CITAS HOY</span>
                </div>
                <div class="stat-value">12</div>
                <div class="stat-change positive">
                    <span>+2 más que ayer</span>
                </div>
            </div>
        </div>

        <!-- Pacientes Activos -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card green-border">
                <div class="stat-header">
                    <span class="stat-icon green-bg">
                        <i class="fas fa-users"></i>
                    </span>
                    <span class="stat-label">PACIENTES ACTIVOS</span>
                </div>
                <div class="stat-value">247</div>
                <div class="stat-change positive">
                    <span>+15 este mes</span>
                </div>
            </div>
        </div>

        <!-- Ingresos Mensuales -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card yellow-border">
                <div class="stat-header">
                    <span class="stat-icon yellow-bg">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                    <span class="stat-label">INGRESOS MENSUALES</span>
                </div>
                <div class="stat-value">$45,280</div>
                <div class="stat-change positive">
                    <span>+8.5% vs mes anterior</span>
                </div>
            </div>
        </div>

        <!-- Tratamientos -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card red-border">
                <div class="stat-header">
                    <span class="stat-icon red-bg">
                        <i class="fas fa-syringe"></i>
                    </span>
                    <span class="stat-label">TRATAMIENTOS</span>
                </div>
                <div class="stat-value">89</div>
                <div class="stat-change positive">
                    <span>+12 esta semana</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y tablas adicionales -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">Resumen Semanal</h3>
                </div>
                <div class="card-body">
                    <canvas id="chartWeekly" style="height:300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- Próximas Citas -->
            <div class="card modern-card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Próximas Citas</h3>
                </div>
                <div class="card-body">
                    <div class="appointment-list">
                        <div class="appointment-item">
                            <div class="appointment-time">15:00</div>
                            <div class="appointment-info">
                                <div class="patient-name">María González</div>
                                <div class="treatment-type">Botox</div>
                            </div>
                        </div>
                        <div class="appointment-item">
                            <div class="appointment-time">16:30</div>
                            <div class="appointment-info">
                                <div class="patient-name">Ana Rodríguez</div>
                                <div class="treatment-type">Rellenos faciales</div>
                            </div>
                        </div>
                        <div class="appointment-item">
                            <div class="appointment-time">17:00</div>
                            <div class="appointment-info">
                                <div class="patient-name">Laura Martínez</div>
                                <div class="treatment-type">Consulta inicial</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ SECCIÓN DE SEGURIDAD 2FA - AGREGAR AQUÍ -->
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
        border-left-color: #5DADE2;
    }

    .stat-card.green-border {
        border-left-color: #52C41A;
    }

    .stat-card.yellow-border {
        border-left-color: #FFA500;
    }

    .stat-card.red-border {
        border-left-color: #FF6B6B;
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
        background: linear-gradient(135deg, #5DADE2, #3498DB);
    }

    .stat-icon.green-bg {
        background: linear-gradient(135deg, #52C41A, #73D13D);
    }

    .stat-icon.yellow-bg {
        background: linear-gradient(135deg, #FFA500, #FFB732);
    }

    .stat-icon.red-bg {
        background: linear-gradient(135deg, #FF6B6B, #FF8787);
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
        color: #5DADE2;
        margin-right: 20px;
        min-width: 50px;
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico mejorado
    const ctx = document.getElementById('chartWeekly');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Pacientes',
                    backgroundColor: 'rgba(93, 173, 226, 0.1)',
                    borderColor: '#5DADE2',
                    borderWidth: 3,
                    data: [12, 19, 15, 25, 22, 30, 28],
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#5DADE2',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#8C8C8C'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#8C8C8C'
                        }
                    }
                }
            }
        });
    }
</script>
@stop
