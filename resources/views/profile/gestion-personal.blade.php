@extends('adminlte::page')

@section('title', 'Gestión de Personal')

@section('content_header')
    {{-- Header personalizado --}}
@stop

@section('content')
    <div class="container-fluid p-0">
        <!-- Header Welcome -->
        <div class="welcome-header">
            <div class="welcome-content">
                <h1>Gestión de Personal</h1>
                <p>Administra tu equipo de trabajo y las comisiones del personal de manera eficiente.</p>
            </div>
            <div class="welcome-date">
                <span class="date" id="currentDate">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                <span class="time" id="currentTime">{{ now()->format('H:i') }}</span>
            </div>
        </div>

        <!-- Alertas de éxito/error -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" style="border-radius: 15px; border-left: 5px solid #10b981;">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 15px; border-left: 5px solid #ef4444;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- Formulario de Nuevo Empleado -->
        <div class="appointment-card mb-4">
            <div class="appointment-header">
                <i class="fas fa-user-plus" style="color: #c9a876; font-size: 24px;"></i>
                <h2>Agregar Nuevo Empleado</h2>
            </div>

            <form action="{{ route('gestion-personal.store') }}" method="POST" id="empleadoForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control custom-input" name="nombre" placeholder="Nombre" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido</label>
                            <input type="text" class="form-control custom-input" name="apellido" placeholder="Apellido" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>DNI</label>
                            <input type="text" class="form-control custom-input" name="dni" placeholder="Número de identificación" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label>
                            <input type="date" class="form-control custom-input" name="fecha_nacimiento" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Género</label>
                            <select class="form-control custom-input" name="genero" required>
                                <option value="">Seleccionar...</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" class="form-control custom-input" name="email" placeholder="correo@salus.com" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Departamento / Cargo</label>
                            <select class="form-control custom-input" name="rol" required>
                                <option value="">Seleccionar departamento...</option>
                                <option value="Administración">Administración</option>
                                <option value="Enfermería">Enfermería</option>
                                <option value="Recepción">Recepción</option>
                                <option value="Limpieza">Limpieza</option>
                                <option value="Médico">Médico</option>
                                <option value="Esteticista">Esteticista</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Contratación</label>
                            <input type="date" class="form-control custom-input" name="fecha_contratacion" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Salario Base ($)</label>
                            <input type="number" step="0.01" class="form-control custom-input" name="salario" placeholder="Ej: 2500.00" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-salus">
                    <i class="fas fa-user-check mr-2"></i>
                    Registrar Empleado
                </button>
            </form>
        </div>

        <!-- Sección de Comisiones Rápidas -->
        <div class="appointment-card mb-4">
            <div class="appointment-header">
                <i class="fas fa-money-bill-wave" style="color: #c9a876; font-size: 24px;"></i>
                <h2>Registrar Comisión Rápida</h2>
            </div>

            <form action="{{ route('gestion-personal.comision.store') }}" method="POST" id="comisionForm">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Empleado</label>
                            <select class="form-control custom-input" name="cod_empleado" id="selectEmpleado" required>
                                <option value="">Cargando empleados...</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Monto ($)</label>
                            <input type="number" step="0.01" class="form-control custom-input" name="monto_comision" placeholder="Ej: 500.00" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de Comisión</label>
                            <input type="date" class="form-control custom-input" name="fecha_comision" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control custom-input" name="concepto_comision" rows="3" placeholder="Ej: Comisión por venta de tratamiento de botox..." required></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-salus">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Registrar Comisión
                </button>
            </form>
        </div>

        <!-- Lista de Empleados -->
        <div class="appointments-list">
            <div class="list-header">
                <h2>Personal Activo</h2>
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="todos">Todos</button>
                    <button class="filter-tab" data-filter="Administración">Administración</button>
                    <button class="filter-tab" data-filter="Enfermería">Enfermería</button>
                    <button class="filter-tab" data-filter="Recepción">Recepción</button>
                    <button class="filter-tab" data-filter="Limpieza">Limpieza</button>
                    <button class="filter-tab" data-filter="Médico">Médico</button>
                    <button class="filter-tab" data-filter="Esteticista">Esteticista</button>
                </div>
            </div>

            <div id="empleadosTableBody">
                @if(isset($empleados) && count($empleados) > 0)
                    @foreach($empleados as $empleado)
                        @php
                            $rol = $empleado['Rol'] ?? 'N/A';
                            $initials = '';
                            $nombre = ($empleado['Nombre'] ?? '') . ' ' . ($empleado['Apellido'] ?? '');
                            $words = explode(' ', trim($nombre));
                            foreach($words as $word) {
                                if (!empty($word)) {
                                    $initials .= strtoupper(substr($word, 0, 1));
                                }
                            }
                            
                            // Obtener comisiones del empleado
                            $comisiones = obtenerComisionesEmpleado($empleado['Cod_Empleado'] ?? 0);
                            $totalComisiones = collect($comisiones)->sum('Monto_Comision');
                            $salarioTotal = ($empleado['Salario'] ?? 0) + $totalComisiones;
                        @endphp
                        <div class="employee-item" data-department="{{ $rol }}">
                            <div class="employee-info">
                                <div class="employee-avatar">{{ $initials }}</div>
                                <div class="employee-details">
                                    <h4>{{ $nombre }}</h4>
                                    <p><i class="fas fa-envelope mr-1"></i> {{ $empleado['Correo'] ?? 'N/A' }}</p>
                                    <p><i class="fas fa-building mr-1"></i> {{ $rol }}</p>
                                    <p><i class="fas fa-calendar mr-1"></i> Desde {{ $empleado['Fecha_Contratacion'] ?? 'N/A' }}</p>
                                    
                                    <!-- Comisiones del empleado -->
                                    @if(count($comisiones) > 0)
                                        <div class="comisiones-list mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                <strong>Comisiones:</strong> 
                                                @foreach($comisiones as $comision)
                                                    <span class="badge badge-light mr-1">
                                                        ${{ number_format($comision['Monto_Comision'], 2) }} 
                                                        ({{ \Carbon\Carbon::parse($comision['Fecha_Comision'])->format('d/m/Y') }})
                                                    </span>
                                                @endforeach
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="employee-controls">
                                @php
                                    $estado = $empleado['Disponibilidad'] ?? 'Activo';
                                    $statusClass = $estado === 'Activo' ? 'status-active' : 'status-inactive';
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $estado }}</span>
                                
                                <!-- Información de Salario y Comisiones -->
                                <div class="employee-financial-info">
                                    <div class="employee-salary">
                                        <div class="salary-amount">${{ number_format($empleado['Salario'] ?? 0, 2) }}</div>
                                        <div class="salary-label">Salario Base</div>
                                    </div>
                                    
                                    @if($totalComisiones > 0)
                                        <div class="employee-comisiones">
                                            <div class="comision-amount text-success">+${{ number_format($totalComisiones, 2) }}</div>
                                            <div class="comision-label">Comisiones</div>
                                        </div>
                                        
                                        <div class="employee-total">
                                            <div class="total-amount text-primary">${{ number_format($salarioTotal, 2) }}</div>
                                            <div class="total-label">Total</div>
                                        </div>
                                    @endif
                                </div>
                                
                                <button class="action-btn view-comisiones-btn" onclick="verComisiones({{ $empleado['Cod_Empleado'] ?? 0 }}, '{{ $nombre }}')" title="Ver Comisiones">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                                <button class="action-btn edit-btn" onclick="editarEmpleado({{ $empleado['Cod_Empleado'] ?? 0 }})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete-btn" onclick="eliminarEmpleado({{ $empleado['Cod_Empleado'] ?? 0 }})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No hay empleados registrados o error al cargar datos</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .content-wrapper { background: #f8f9fa !important; }
        .content { padding-bottom: 20px !important; }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes notificationPop {
            0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0; }
            50% { transform: translate(-50%, -50%) scale(1.05); }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        }

        .welcome-header {
            background: linear-gradient(135deg, #c9a876 0%, #d4b896 100%);
            border-radius: 20px;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(201, 168, 118, 0.2);
            animation: fadeInDown 0.8s ease-out;
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
            text-transform: capitalize;
        }

        .welcome-date .time {
            display: block;
            font-size: 24px;
            font-weight: 600;
            margin-top: 5px;
        }

        .appointment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .appointment-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .appointment-header h2 {
            color: #2C3E50;
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .custom-input {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .custom-input:focus {
            border-color: #c9a876;
            box-shadow: 0 0 0 3px rgba(201, 168, 118, 0.1);
            outline: none;
        }

        .btn-salus {
            background: linear-gradient(135deg, #c9a876, #d4b896);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-salus:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(201, 168, 118, 0.3);
        }

        .appointments-list {
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            flex-wrap: wrap;
            gap: 15px;
        }

        .list-header h2 {
            color: #2C3E50;
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 16px;
            background: #f3f4f6;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            color: #6b7280;
            font-size: 14px;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #c9a876, #d4b896);
            color: white;
        }

        .filter-tab:hover:not(.active) {
            background: #e5e7eb;
        }

        .employee-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            background: #f8f9fa;
            transition: all 0.3s;
            border: 1px solid #e5e7eb;
        }

        .employee-item:hover {
            background: #f5efe6;
            transform: translateX(5px);
        }

        .employee-info {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex: 1;
        }

        .employee-avatar {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #c9a876, #d4b896);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
            flex-shrink: 0;
        }

        .employee-details { 
            flex: 1; 
            min-width: 0;
        }

        .employee-details h4 {
            color: #2C3E50;
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .employee-details p {
            color: #6b7280;
            font-size: 14px;
            margin: 2px 0;
        }

        .comisiones-list {
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            margin-top: 8px;
        }

        .comisiones-list .badge {
            font-size: 11px;
            padding: 4px 8px;
            margin-bottom: 2px;
        }

        .employee-controls {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-shrink: 0;
        }

        .employee-financial-info {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .employee-salary, .employee-comisiones, .employee-total {
            text-align: center;
            padding: 8px 12px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            min-width: 100px;
        }

        .salary-amount, .comision-amount, .total-amount {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .salary-amount { color: #059669; }
        .comision-amount { color: #10b981; }
        .total-amount { color: #3b82f6; }

        .salary-label, .comision-label, .total-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: default;
            min-width: 80px;
            text-align: center;
        }

        .status-active {
            background: #d4f4dd;
            color: #2d7a4e;
        }

        .status-inactive {
            background: #ffd6d6;
            color: #d32f2f;
        }

        .action-btn {
            padding: 8px 12px;
            background: transparent;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            cursor: pointer;
            color: #6b7280;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .action-btn:hover {
            background: #f5efe6;
            border-color: #c9a876;
        }

        .action-btn.view-comisiones-btn {
            color: #8b5cf6;
            border-color: #8b5cf6;
        }

        .action-btn.view-comisiones-btn:hover {
            background: #ede9fe;
        }

        .action-btn.edit-btn {
            color: #3b82f6;
            border-color: #3b82f6;
        }

        .action-btn.edit-btn:hover {
            background: #dbeafe;
        }

        .action-btn.delete-btn {
            color: #ef4444;
            border-color: #ef4444;
        }

        .action-btn.delete-btn:hover {
            background: #fee2e2;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 80px;
            opacity: 0.3;
            margin-bottom: 20px;
        }

        /* Modal de Comisiones */
        .comisiones-modal {
            max-width: 600px !important;
        }

        .comision-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 8px;
            background: #f8f9fa;
        }

        .comision-info {
            flex: 1;
        }

        .comision-concepto {
            font-weight: 500;
            color: #2C3E50;
            margin-bottom: 4px;
        }

        .comision-fecha {
            font-size: 12px;
            color: #6b7280;
        }

        .comision-monto {
            font-weight: 700;
            color: #059669;
            font-size: 16px;
        }

        .comision-total {
            background: linear-gradient(135deg, #c9a876, #d4b896);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-top: 15px;
        }

        .comision-total-amount {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .comision-total-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
        }

        .modal-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .modal-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        .modal-body {
            color: #6b7280;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .modal-btn {
            padding: 10px 24px;
            border-radius: 10px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .modal-btn-cancel {
            background: #f3f4f6;
            color: #6b7280;
        }

        .modal-btn-cancel:hover {
            background: #e5e7eb;
        }

        .modal-btn-confirm {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .modal-btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .notification-toast {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 300px;
            animation: notificationPop 0.4s ease-out;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .notification-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .notification-error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .notification-content { flex: 1; }

        .notification-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .notification-message {
            color: #6b7280;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .welcome-header {
                flex-direction: column;
                text-align: center;
            }

            .welcome-date {
                margin-top: 20px;
                text-align: center;
            }

            .employee-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .employee-controls {
                flex-wrap: wrap;
                width: 100%;
                justify-content: flex-start;
                margin-top: 15px;
            }

            .employee-financial-info {
                flex-wrap: wrap;
                gap: 5px;
            }

            .employee-salary, .employee-comisiones, .employee-total {
                min-width: 80px;
                padding: 6px 8px;
            }

            .filter-tabs {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ========== ACTUALIZAR FECHA Y HORA ==========
    const updateDateTime = () => {
        const now = new Date();
        const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        
        let dateString = now.toLocaleDateString('es-HN', optionsDate);
        dateString = dateString.charAt(0).toUpperCase() + dateString.slice(1);
        
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const timeString = `${hours.toString().padStart(2, '0')}:${minutes}`;
        
        document.getElementById('currentDate').textContent = dateString;
        document.getElementById('currentTime').textContent = timeString;
    };

    // ========== NOTIFICACIONES ==========
    const showNotification = (title, message, type = 'success') => {
        const existing = document.querySelector('.notification-toast');
        if (existing) existing.remove();
        
        const notification = document.createElement('div');
        notification.className = 'notification-toast';
        notification.innerHTML = `
            <div class="notification-icon notification-${type}">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
        `;
        
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.animation = 'notificationPop 0.4s ease-out reverse';
            setTimeout(() => notification.remove(), 400);
        }, 3000);
    };

    // ========== CARGAR EMPLEADOS EN SELECT DE COMISIONES ==========
    function cargarEmpleadosParaComision() {
        const select = document.getElementById('selectEmpleado');
        
        fetch('{{ route("empleados.activos") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(empleados => {
                if (empleados.error || empleados.length === 0) {
                    select.innerHTML = '<option value="">No hay empleados disponibles</option>';
                    return;
                }

                let html = '<option value="">Seleccionar empleado...</option>';
                empleados.forEach(empleado => {
                    html += `<option value="${empleado.Cod_Empleado}">${empleado.Nombre_Completo} - ${empleado.Departamento}</option>`;
                });

                select.innerHTML = html;
            })
            .catch(error => {
                console.error('Error al cargar empleados:', error);
                select.innerHTML = '<option value="">Error al cargar empleados</option>';
                cargarSelectEmpleadosFallback();
            });
    }

    // Función de respaldo usando la ruta antigua
    function cargarSelectEmpleadosFallback() {
        const select = document.getElementById('selectEmpleado');
        
        fetch('{{ route("gestion-personal.empleados.ajax") }}')
            .then(response => response.json())
            .then(data => {
                if (data.error || data.length === 0) {
                    select.innerHTML = '<option value="">No hay empleados disponibles</option>';
                    return;
                }

                let html = '<option value="">Seleccionar empleado...</option>';
                data.forEach(empleado => {
                    if (empleado.Disponibilidad === 'Activo') {
                        html += `<option value="${empleado.Cod_Empleado}">${empleado.Nombre} ${empleado.Apellido} - ${empleado.Rol}</option>`;
                    }
                });

                select.innerHTML = html;
            })
            .catch(error => {
                select.innerHTML = '<option value="">Error al cargar empleados</option>';
                console.error('Error en fallback:', error);
            });
    }

    // ========== VER COMISIONES DEL EMPLEADO ==========
    function verComisiones(codEmpleado, nombreEmpleado) {
        fetch(`/api/comisiones/${codEmpleado}`)
            .then(response => response.json())
            .then(comisiones => {
                let comisionesHTML = '';
                let totalComisiones = 0;

                if (comisiones.length > 0) {
                    comisiones.forEach(comision => {
                        totalComisiones += parseFloat(comision.Monto_Comision);
                        const fecha = new Date(comision.Fecha_Comision).toLocaleDateString('es-HN');
                        comisionesHTML += `
                            <div class="comision-item">
                                <div class="comision-info">
                                    <div class="comision-concepto">${comision.Concepto_Comision}</div>
                                    <div class="comision-fecha">${fecha}</div>
                                </div>
                                <div class="comision-monto">$${parseFloat(comision.Monto_Comision).toFixed(2)}</div>
                            </div>
                        `;
                    });
                } else {
                    comisionesHTML = '<p class="text-center text-muted py-3">No hay comisiones registradas</p>';
                }

                Swal.fire({
                    title: `Comisiones de ${nombreEmpleado}`,
                    html: `
                        <div class="comisiones-list">
                            ${comisionesHTML}
                            ${comisiones.length > 0 ? `
                                <div class="comision-total">
                                    <div class="comision-total-amount">$${totalComisiones.toFixed(2)}</div>
                                    <div class="comision-total-label">Total en Comisiones</div>
                                </div>
                            ` : ''}
                        </div>
                    `,
                    width: 600,
                    customClass: {
                        popup: 'comisiones-modal'
                    },
                    showCloseButton: true,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Error al cargar comisiones:', error);
                showNotification('Error', 'No se pudieron cargar las comisiones', 'error');
            });
    }

    // ========== INICIALIZACIÓN ==========
    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        setInterval(updateDateTime, 60000);
        
        cargarEmpleadosParaComision();

        // Filtros de departamento
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                const items = document.querySelectorAll('.employee-item');
                
                items.forEach(item => {
                    if (filter === 'todos') {
                        item.style.display = '';
                    } else {
                        const department = item.dataset.department;
                        item.style.display = department === filter ? '' : 'none';
                    }
                });
            });
        });
    });

    function cargarEmpleados() {
        location.reload();
    }

    // ========== EDITAR EMPLEADO ==========
    function editarEmpleado(codEmpleado) {
        Swal.fire({
            title: 'Editar Empleado',
            text: 'Funcionalidad en desarrollo',
            icon: 'info',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#c9a876'
        });
    }

    // ========== ELIMINAR EMPLEADO ==========
    function eliminarEmpleado(codEmpleado) {
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'modal-overlay';
        
        modalOverlay.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="modal-title">¿Está seguro?</div>
                </div>
                <div class="modal-body">
                    Esta acción eliminará permanentemente el empleado del sistema. ¿Desea continuar?
                </div>
                <div class="modal-footer">
                    <button class="modal-btn modal-btn-cancel">Cancelar</button>
                    <button class="modal-btn modal-btn-confirm">Sí, eliminar</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modalOverlay);
        
        modalOverlay.querySelector('.modal-btn-cancel').addEventListener('click', () => {
            modalOverlay.remove();
        });
        
        modalOverlay.querySelector('.modal-btn-confirm').addEventListener('click', () => {
            fetch(`/gestion-personal/empleados/${codEmpleado}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                modalOverlay.remove();
                if (data.success) {
                    showNotification('¡Eliminado!', data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error', data.message, 'error');
                }
            })
            .catch(error => {
                modalOverlay.remove();
                showNotification('Error', 'Ocurrió un error al eliminar el empleado', 'error');
                console.error('Error:', error);
            });
        });
        
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) modalOverlay.remove();
        });
    }

    // ========== VALIDACIÓN DE FORMULARIOS ==========
    document.getElementById('empleadoForm').addEventListener('submit', function(e) {
        const fechaNacimiento = new Date(this.fecha_nacimiento.value);
        const fechaContratacion = new Date(this.fecha_contratacion.value);
        const hoy = new Date();
        
        if (fechaNacimiento >= fechaContratacion) {
            e.preventDefault();
            showNotification('Error', 'La fecha de nacimiento debe ser anterior a la fecha de contratación', 'error');
            return;
        }
        
        if (fechaContratacion > hoy) {
            e.preventDefault();
            showNotification('Error', 'La fecha de contratación no puede ser futura', 'error');
            return;
        }
    });

    document.getElementById('comisionForm').addEventListener('submit', function(e) {
        const fechaComision = new Date(this.fecha_comision.value);
        const hoy = new Date();
        
        if (fechaComision > hoy) {
            e.preventDefault();
            showNotification('Error', 'La fecha de comisión no puede ser futura', 'error');
        }
        
        const empleadoSelect = document.getElementById('selectEmpleado');
        if (!empleadoSelect.value) {
            e.preventDefault();
            showNotification('Error', 'Debe seleccionar un empleado', 'error');
            empleadoSelect.focus();
        }
    });
</script>
@stop

@php
function obtenerComisionesEmpleado($codEmpleado) {
    try {
        $apiService = new \App\Services\ApiService();
        $comisiones = $apiService->getComisiones();
        
        return collect($comisiones)->where('Cod_Empleado', $codEmpleado)->take(3)->all();
    } catch (\Exception $e) {
        return [];
    }
}
@endphp