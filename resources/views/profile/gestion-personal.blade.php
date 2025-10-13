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
                <span class="date" id="currentDate">{{ now()->format('l, d \d\e F Y') }}</span>
                <span class="time" id="currentTime">{{ now()->format('H:i') }}</span>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon bg-blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Empleados Activos</div>
                        <div class="stats-number">12</div>
                        <div class="stats-change positive">+2 más que ayer</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon bg-green">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Comisiones Mensuales</div>
                        <div class="stats-number">$15,280</div>
                        <div class="stats-change positive">+8.5% vs mes anterior</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon bg-yellow">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Promedio por Empleado</div>
                        <div class="stats-number">$1,273</div>
                        <div class="stats-change positive">+5.2% esta semana</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon bg-red">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Nuevos este Mes</div>
                        <div class="stats-number">3</div>
                        <div class="stats-change positive">+1 esta semana</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Nuevo Empleado -->
        <div class="appointment-card mb-4">
            <div class="appointment-header">
                <i class="fas fa-user-plus" style="color: #c9a876; font-size: 24px;"></i>
                <h2>Agregar Nuevo Empleado</h2>
            </div>

            <form id="employeeForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee_name">Nombre Completo</label>
                            <input type="text" 
                                   class="form-control custom-input" 
                                   id="employee_name" 
                                   name="employee_name" 
                                   placeholder="Nombre completo del empleado" 
                                   maxlength="100"
                                   pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]+"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" 
                                   class="form-control custom-input" 
                                   id="email" 
                                   name="email" 
                                   placeholder="correo@ejemplo.com" 
                                   maxlength="255"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="department">Departamento</label>
                            <select class="form-control custom-input" id="department" name="department" required>
                                <option value="">Seleccionar departamento</option>
                                <option value="administracion">Administración</option>
                                <option value="enfermeria">Enfermería</option>
                                <option value="recepcion">Recepción</option>
                                <option value="limpieza">Limpieza</option>
                                <option value="otro">Otro (especificar)</option>
                            </select>
                            <input type="text" 
                                   id="customDepartment" 
                                   class="form-control custom-input custom-department-input mt-2" 
                                   placeholder="Escriba el nombre del departamento"
                                   maxlength="50"
                                   pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]+">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="position">Cargo</label>
                            <input type="text" 
                                   class="form-control custom-input" 
                                   id="position" 
                                   name="position" 
                                   placeholder="Ej: Enfermera, Recepcionista, etc." 
                                   maxlength="80"
                                   pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.'\-()\/]+"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hire_date">Fecha de Contratación</label>
                            <input type="date" class="form-control custom-input" id="hire_date" name="hire_date" max="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="salary">Salario Base</label>
                            <input type="number" class="form-control custom-input" id="salary" name="salary" placeholder="Ej: 25000" step="0.01" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="notes">Notas Adicionales</label>
                            <textarea class="form-control custom-input" id="notes" name="notes" rows="3" placeholder="Información adicional sobre el empleado..."></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-salus">
                    <i class="fas fa-user-check mr-2"></i>
                    Agregar Empleado
                </button>
            </form>
        </div>

        <!-- Lista de Empleados -->
        <div class="appointments-list">
            <div class="list-header">
                <h2>Personal Activo</h2>
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="todos">Todos</button>
                    <button class="filter-tab" data-filter="administracion">Administración</button>
                    <button class="filter-tab" data-filter="enfermeria">Enfermería</button>
                    <button class="filter-tab" data-filter="recepcion">Recepción</button>
                    <button class="filter-tab" data-filter="limpieza">Limpieza</button>
                </div>
            </div>

            <div id="employeesList">
                <!-- Los empleados se mostrarán aquí dinámicamente -->
            </div>
        </div>

        <!-- Sección de Comisiones Rápidas -->
        <div class="appointment-card mt-4">
            <div class="appointment-header">
                <i class="fas fa-money-bill-wave" style="color: #c9a876; font-size: 24px;"></i>
                <h2>Registrar Comisión Rápida</h2>
            </div>

            <form id="commissionForm">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="commission_employee">Empleado</label>
                            <select class="form-control custom-input" id="commission_employee" name="commission_employee" required>
                                <option value="">Seleccionar empleado</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="commission_amount">Monto ($)</label>
                            <input type="number" class="form-control custom-input" id="commission_amount" name="commission_amount" placeholder="Ej: 500.00" step="0.01" min="0.01" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="commission_type">Tipo de Comisión</label>
                            <select class="form-control custom-input" id="commission_type" name="commission_type" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="venta">Venta</option>
                                <option value="bono">Bono por desempeño</option>
                                <option value="referido">Referido</option>
                                <option value="meta">Cumplimiento de meta</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="commission_description">Descripción</label>
                            <textarea class="form-control custom-input" id="commission_description" name="commission_description" rows="2" placeholder="Ej: Comisión por venta de tratamiento de botox..."></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-salus">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Registrar Comisión
                </button>
            </form>
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
        }

        .welcome-date .time {
            display: block;
            font-size: 24px;
            font-weight: 600;
            margin-top: 5px;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stats-icon.bg-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .stats-icon.bg-green { background: linear-gradient(135deg, #10b981, #059669); }
        .stats-icon.bg-yellow { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stats-icon.bg-red { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .stats-content { flex: 1; }

        .stats-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .stats-change {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 20px;
            display: inline-block;
        }

        .stats-change.positive {
            background: #d1fae5;
            color: #065f46;
        }

        .stats-change.negative {
            background: #fee2e2;
            color: #991b1b;
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
        }

        .custom-department-input {
            display: none;
        }

        .custom-department-input.show {
            display: block;
        }

        .custom-input.is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
        }
        
        .custom-input.is-valid {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1) !important;
        }
        
        .field-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            animation: slideDown 0.3s ease-out;
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
            align-items: center;
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
            align-items: center;
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
        }

        .employee-details { flex: 1; }

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

        .employee-commission {
            text-align: center;
            padding: 10px 15px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            min-width: 120px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .employee-commission:hover {
            background: #f0fdf4;
            border-color: #10b981;
        }

        .commission-amount {
            font-size: 18px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 2px;
        }

        .commission-label {
            font-size: 12px;
            color: #6b7280;
        }

        .employee-controls {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
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

        .status-vacation {
            background: #fff3cd;
            color: #856404;
        }

        .action-btn {
            padding: 8px 12px;
            background: transparent;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            cursor: pointer;
            color: #6b7280;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: #f5efe6;
            border-color: #c9a876;
        }

        .action-btn.commission-btn {
            background: #c9a876;
            color: white;
            border-color: #c9a876;
        }

        .action-btn.edit-btn {
            color: #3b82f6;
            border-color: #3b82f6;
        }

        .action-btn.delete-btn {
            color: #ef4444;
            border-color: #ef4444;
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

        .edit-form-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
        }

        .edit-form-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
        }

        .edit-form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .edit-form-title {
            font-size: 22px;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-edit-form {
            background: none;
            border: none;
            font-size: 24px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-edit-form:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .commissions-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
        }

        .commissions-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 900px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
        }

        .commissions-header {
            display: flex

justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .commissions-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-commissions {
            background: none;
            border: none;
            font-size: 28px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-commissions:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .total-commissions {
            background: linear-gradient(135deg, #c9a876, #d4b896);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .total-amount {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .total-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .commissions-list {
            margin-top: 20px;
        }

        .commission-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .commission-item:hover {
            background: #f5efe6;
            transform: translateX(5px);
        }

        .commission-header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .commission-amount-display {
            font-size: 24px;
            font-weight: 700;
            color: #059669;
        }

        .commission-date {
            color: #6b7280;
            font-size: 14px;
        }

        .commission-details {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .commission-type-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: #dbeafe;
            color: #1e40af;
        }

        .commission-description {
            color: #6b7280;
            font-size: 14px;
            font-style: italic;
        }

        .commission-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-edit-commission,
        .btn-delete-commission {
            padding: 6px 12px;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }

        .btn-edit-commission {
            background: #3b82f6;
        }

        .btn-edit-commission:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .btn-delete-commission {
            background: #ef4444;
        }

        .btn-delete-commission:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .no-commissions {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .no-commissions i {
            font-size: 60px;
            opacity: 0.3;
            margin-bottom: 15px;
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

            .list-header {
                flex-direction: column;
                gap: 15px;
            }

            .filter-tabs {
                width: 100%;
                justify-content: center;
            }

            .employee-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .employee-controls {
                flex-direction: column;
                width: 100%;
            }

            .stats-card {
                margin-bottom: 15px;
            }

            .commission-header-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
@stop

@section('js')
    <script>
        // ========== DATOS GLOBALES ==========
        let employees = [
            {
                id: 1,
                name: 'María Pérez',
                email: 'maria.perez@salus.com',
                department: 'administracion',
                position: 'Administradora',
                hire_date: '2023-01-15',
                salary: 30000,
                commissions: [
                    {id: 1, amount: 850, type: 'venta', description: 'Venta de tratamiento facial', date: '2024-01-15'},
                    {id: 2, amount: 1200, type: 'bono', description: 'Bono por desempeño mensual', date: '2024-01-28'},
                    {id: 3, amount: 800, type: 'meta', description: 'Cumplimiento meta trimestral', date: '2024-02-05'}
                ],
                status: 'active'
            },
            {
                id: 2,
                name: 'Ana García',
                email: 'ana.garcia@salus.com',
                department: 'enfermeria',
                position: 'Enfermera Senior',
                hire_date: '2022-06-10',
                salary: 28000,
                commissions: [
                    {id: 1, amount: 650, type: 'venta', description: 'Venta de tratamiento corporal', date: '2024-01-20'},
                    {id: 2, amount: 1000, type: 'referido', description: 'Referido de 2 clientes nuevos', date: '2024-02-01'}
                ],
                status: 'active'
            },
            {
                id: 3,
                name: 'Carlos Rodríguez',
                email: 'carlos.rodriguez@salus.com',
                department: 'recepcion',
                position: 'Recepcionista',
                hire_date: '2023-03-20',
                salary: 22000,
                commissions: [
                    {id: 1, amount: 480, type: 'bono', description: 'Bono por excelente atención', date: '2024-01-25'},
                    {id: 2, amount: 500, type: 'meta', description: 'Meta de satisfacción cliente', date: '2024-02-10'}
                ],
                status: 'vacation'
            },
            {
                id: 4,
                name: 'Laura Martínez',
                email: 'laura.martinez@salus.com',
                department: 'enfermeria',
                position: 'Enfermera',
                hire_date: '2023-07-01',
                salary: 25000,
                commissions: [
                    {id: 1, amount: 1200, type: 'venta', description: 'Venta de paquete premium', date: '2024-02-03'}
                ],
                status: 'active'
            }
        ];

        let commissionIdCounter = 10;

        // ========== UTILIDADES ==========
        const updateDateTime = () => {
            const now = new Date();
            const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            
            let dateString = now.toLocaleDateString('es-HN', optionsDate);
            dateString = dateString.charAt(0).toUpperCase() + dateString.slice(1);
            
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            hours = hours % 12 || 12;
            const timeString = `${hours.toString().padStart(2, '0')}:${minutes} ${now.getHours() >= 12 ? 'PM' : 'AM'}`;
            
            document.getElementById('currentDate').textContent = dateString;
            document.getElementById('currentTime').textContent = timeString;
        };

        const getInitials = name => name.split(' ').map(n => n[0]).join('').toUpperCase();

        const formatDepartment = dept => {
            const departments = {
                'administracion': 'Administración',
                'enfermeria': 'Enfermería', 
                'recepcion': 'Recepción',
                'limpieza': 'Limpieza'
            };
            return departments[dept] || dept;
        };

        const getStatusClass = status => `status-${status}`;

        const getStatusText = status => {
            const statuses = {
                'active': 'Activo',
                'inactive': 'Inactivo',
                'vacation': 'Vacaciones'
            };
            return statuses[status] || 'Activo';
        };

        const getTotalCommissions = employee => employee.commissions.reduce((sum, comm) => sum + comm.amount, 0);

        const formatCommissionType = type => {
            const types = {
                'venta': 'Venta',
                'bono': 'Bono',
                'referido': 'Referido',
                'meta': 'Meta',
                'otro': 'Otro'
            };
            return types[type] || type;
        };

        // ========== VALIDACIONES ==========
        const validateName = input => /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]+$/.test(input.trim());
        const validateEmail = email => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email.trim());
        const validatePosition = input => /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.'\-()\/]+$/.test(input.trim());
        const validateLength = (input, min = 2, max = 100) => {
            const len = input.trim().length;
            return len >= min && len <= max;
        };

        const sanitizeInput = (input, type) => {
            const patterns = {
                'name': /[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]/g,
                'email': /[^a-zA-Z0-9._%+-@]/g,
                'position': /[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.'\-()\/]/g
            };
            return input.replace(patterns[type] || /.^/, '');
        };

        const showFieldError = (fieldId, message) => {
            const field = document.getElementById(fieldId);
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) existingError.remove();
            
            field.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
            field.parentNode.appendChild(errorDiv);
        };

        const removeFieldError = fieldId => {
            const field = document.getElementById(fieldId);
            const errorDiv = field.parentNode.querySelector('.field-error');
            if (errorDiv) errorDiv.remove();
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        };

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

        // ========== RENDERIZADO ==========
        const renderEmployees = (filter = 'todos') => {
            const container = document.getElementById('employeesList');
            let filteredEmployees = filter === 'todos' ? employees : employees.filter(emp => emp.department === filter);
            
            if (filteredEmployees.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No hay empleados${filter !== 'todos' ? ' en este departamento' : ''}</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filteredEmployees.map(employee => {
                const totalComm = getTotalCommissions(employee);
                return `
                    <div class="employee-item" data-id="${employee.id}">
                        <div class="employee-info">
                            <div class="employee-avatar">${getInitials(employee.name)}</div>
                            <div class="employee-details">
                                <h4>${employee.name}</h4>
                                <p><i class="fas fa-envelope mr-1"></i> ${employee.email}</p>
                                <p><i class="fas fa-building mr-1"></i> ${formatDepartment(employee.department)} • ${employee.position}</p>
                                <p><i class="fas fa-calendar mr-1"></i> Desde ${new Date(employee.hire_date).toLocaleDateString()}</p>
                            </div>
                            <div class="employee-commission" onclick="viewCommissions(${employee.id})" title="Ver todas las comisiones">
                                <div class="commission-amount">$${totalComm.toLocaleString()}</div>
                                <div class="commission-label">Comisiones</div>
                            </div>
                        </div>
                        <div class="employee-controls">
                            <button class="status-badge ${getStatusClass(employee.status)}">${getStatusText(employee.status)}</button>
                            <button class="action-btn commission-btn" onclick="addCommission(${employee.id})" title="Agregar Comisión">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="action-btn" onclick="viewCommissions(${employee.id})" title="Ver Comisiones" style="color: #10b981; border-color: #10b981;">
                                <i class="fas fa-list-alt"></i>
                            </button>
                            <button class="action-btn edit-btn" onclick="editEmployee(${employee.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="deleteEmployee(${employee.id})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            updateCommissionSelect();
        };

        const updateCommissionSelect = () => {
            const select = document.getElementById('commission_employee');
            select.innerHTML = '<option value="">Seleccionar empleado</option>' +
                employees.map(emp => `<option value="${emp.id}">${emp.name}</option>`).join('');
        };

        // ========== COMISIONES ==========
        function viewCommissions(employeeId) {
            const employee = employees.find(e => e.id === employeeId);
            if (!employee) return;

            const total = getTotalCommissions(employee);
            const modal = document.createElement('div');
            modal.className = 'commissions-modal';
            
            modal.innerHTML = `
                <div class="commissions-container">
                    <div class="commissions-header">
                        <div class="commissions-title">
                            <i class="fas fa-money-bill-wave" style="color: #c9a876;"></i>
                            Comisiones de ${employee.name}
                        </div>
                        <button class="close-commissions"><i class="fas fa-times"></i></button>
                    </div>
                    
                    <div class="total-commissions">
                        <div class="total-amount">$${total.toLocaleString('es-HN', {minimumFractionDigits: 2})}</div>
                        <div class="total-label">Total de Comisiones</div>
                    </div>
                    
                    <div class="commissions-list">
                        ${employee.commissions.length > 0 ? employee.commissions.map(comm => `
                            <div class="commission-item">
                                <div class="commission-header-info">
                                    <div class="commission-amount-display">$${comm.amount.toLocaleString('es-HN', {minimumFractionDigits: 2})}</div>
                                    <div class="commission-date">
                                        <i class="fas fa-calendar mr-1"></i>
                                        ${new Date(comm.date).toLocaleDateString('es-HN')}
                                    </div>
                                </div>
                                <div class="commission-details">
                                    <span class="commission-type-badge">${formatCommissionType(comm.type)}</span>
                                </div>
                                ${comm.description ? `<div class="commission-description">${comm.description}</div>` : ''}
                                <div class="commission-actions">
                                    <button class="btn-edit-commission" onclick="editCommission(${employeeId}, ${comm.id})">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </button>
                                    <button class="btn-delete-commission" onclick="deleteCommission(${employeeId}, ${comm.id})">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        `).join('') : `
                            <div class="no-commissions">
                                <i class="fas fa-inbox"></i>
                                <p>No hay comisiones registradas para este empleado</p>
                            </div>
                        `}
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            modal.querySelector('.close-commissions').addEventListener('click', () => modal.remove());
            modal.addEventListener('click', e => { if (e.target === modal) modal.remove(); });
        }

        function editCommission(employeeId, commissionId) {
            const employee = employees.find(e => e.id === employeeId);
            if (!employee) return;
            
            const commission = employee.commissions.find(c => c.id === commissionId);
            if (!commission) return;

            const editModal = document.createElement('div');
            editModal.className = 'edit-form-overlay';
            
            editModal.innerHTML = `
                <div class="edit-form-container">
                    <div class="edit-form-header">
                        <div class="edit-form-title">
                            <i class="fas fa-edit" style="color: #c9a876;"></i>
                            Editar Comisión
                        </div>
                        <button class="close-edit-form"><i class="fas fa-times"></i></button>
                    </div>
                    
                    <form id="editCommissionForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Monto ($)</label>
                                    <input type="number" class="form-control custom-input" id="edit_comm_amount" value="${commission.amount}" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="date" class="form-control custom-input" id="edit_comm_date" value="${commission.date}" max="${new Date().toISOString().split('T')[0]}" required>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Tipo de Comisión</label>
                                    <select class="form-control custom-input" id="edit_comm_type" required>
                                        <option value="venta" ${commission.type === 'venta' ? 'selected' : ''}>Venta</option>
                                        <option value="bono" ${commission.type === 'bono' ? 'selected' : ''}>Bono por desempeño</option>
                                        <option value="referido" ${commission.type === 'referido' ? 'selected' : ''}>Referido</option>
                                        <option value="meta" ${commission.type === 'meta' ? 'selected' : ''}>Cumplimiento de meta</option>
                                        <option value="otro" ${commission.type === 'otro' ? 'selected' : ''}>Otro</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea class="form-control custom-input" id="edit_comm_description" rows="3" maxlength="255">${commission.description || ''}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary cancel-edit-btn">Cancelar</button>
                            <button type="submit" class="btn btn-salus">
                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            `;
            
            document.body.appendChild(editModal);
            
            // Botones de cierre
            const closeBtn = editModal.querySelector('.close-edit-form');
            const cancelBtn = editModal.querySelector('.cancel-edit-btn');
            
            closeBtn.addEventListener('click', () => editModal.remove());
            cancelBtn.addEventListener('click', () => editModal.remove());
            
            // Formulario
            const form = editModal.querySelector('#editCommissionForm');
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const newAmount = parseFloat(document.getElementById('edit_comm_amount').value);
                if (newAmount <= 0) {
                    showNotification('Error', 'El monto debe ser mayor a 0', 'error');
                    return;
                }
                
                commission.amount = newAmount;
                commission.date = document.getElementById('edit_comm_date').value;
                commission.type = document.getElementById('edit_comm_type').value;
                commission.description = document.getElementById('edit_comm_description').value;
                
                editModal.remove();
                const commissionsModal = document.querySelector('.commissions-modal');
                if (commissionsModal) commissionsModal.remove();
                
                renderEmployees();
                
                setTimeout(() => {
                    viewCommissions(employeeId);
                    showNotification('Comisión actualizada', 'La comisión se ha actualizado exitosamente', 'success');
                }, 100);
            });
            
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) editModal.remove();
            });
        }

        function deleteCommission(employeeId, commissionId) {
            const employee = employees.find(e => e.id === employeeId);
            if (!employee) return;
            
            const commission = employee.commissions.find(c => c.id === commissionId);
            if (!commission) return;

            const modalOverlay = document.createElement('div');
            modalOverlay.className = 'modal-overlay';
            
            modalOverlay.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="modal-title">Confirmar Eliminación</div>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro de que desea eliminar esta comisión de <strong>$${commission.amount.toFixed(2)}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button class="modal-btn modal-btn-cancel">Cancelar</button>
                        <button class="modal-btn modal-btn-confirm">Sí, eliminar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modalOverlay);
            
            // Botón cancelar
            const cancelBtn = modalOverlay.querySelector('.modal-btn-cancel');
            cancelBtn.addEventListener('click', () => modalOverlay.remove());
            
            // Botón confirmar
            const confirmBtn = modalOverlay.querySelector('.modal-btn-confirm');
            confirmBtn.addEventListener('click', () => {
                // Eliminar la comisión
                employee.commissions = employee.commissions.filter(c => c.id !== commissionId);
                
                // Cerrar modal de confirmación
                modalOverlay.remove();
                
                // Cerrar modal de comisiones si existe
                const commissionsModal = document.querySelector('.commissions-modal');
                if (commissionsModal) {
                    commissionsModal.remove();
                }
                
                // Actualizar vista
                renderEmployees();
                
                // Reabrir modal de comisiones actualizado
                setTimeout(() => {
                    viewCommissions(employeeId);
                    showNotification('Comisión eliminada', 'La comisión se ha eliminado del sistema', 'success');
                }, 100);
            });
            
            // Cerrar al hacer clic fuera
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) modalOverlay.remove();
            });
        }

        const addCommission = employeeId => {
            const employee = employees.find(e => e.id === employeeId);
            if (employee) {
                document.getElementById('commission_employee').value = employeeId;
                document.getElementById('commission_amount').focus();
                document.querySelector('#commissionForm').closest('.appointment-card').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        };

        // ========== EMPLEADOS ==========
        function editEmployee(id) {
            const employee = employees.find(e => e.id === id);
            if (!employee) return;

            const editOverlay = document.createElement('div');
            editOverlay.className = 'edit-form-overlay';
            
            editOverlay.innerHTML = `
                <div class="edit-form-container">
                    <div class="edit-form-header">
                        <div class="edit-form-title">
                            <i class="fas fa-user-edit" style="color: #c9a876;"></i>
                            Editar Empleado
                        </div>
                        <button class="close-edit-form">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="editEmployeeForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre Completo</label>
                                    <input type="text" class="form-control custom-input" id="edit_name" value="${employee.name}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Correo Electrónico</label>
                                    <input type="email" class="form-control custom-input" id="edit_email" value="${employee.email}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Departamento</label>
                                    <select class="form-control custom-input" id="edit_department" required>
                                        <option value="administracion" ${employee.department === 'administracion' ? 'selected' : ''}>Administración</option>
                                        <option value="enfermeria" ${employee.department === 'enfermeria' ? 'selected' : ''}>Enfermería</option>
                                        <option value="recepcion" ${employee.department === 'recepcion' ? 'selected' : ''}>Recepción</option>
                                        <option value="limpieza" ${employee.department === 'limpieza' ? 'selected' : ''}>Limpieza</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cargo</label>
                                    <input type="text" class="form-control custom-input" id="edit_position" value="${employee.position}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Salario Base</label>
                                    <input type="number" class="form-control custom-input" id="edit_salary" value="${employee.salary}" step="0.01" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estado</label>

<select class="form-control custom-input" id="edit_status" required>
                                        <option value="active" ${employee.status === 'active' ? 'selected' : ''}>Activo</option>
                                        <option value="inactive" ${employee.status === 'inactive' ? 'selected' : ''}>Inactivo</option>
                                        <option value="vacation" ${employee.status === 'vacation' ? 'selected' : ''}>Vacaciones</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary cancel-edit-employee">Cancelar</button>
                            <button type="submit" class="btn btn-salus">
                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            `;
            
            document.body.appendChild(editOverlay);
            
            // Botones de cierre
            const closeBtn = editOverlay.querySelector('.close-edit-form');
            const cancelBtn = editOverlay.querySelector('.cancel-edit-employee');
            
            closeBtn.addEventListener('click', () => editOverlay.remove());
            cancelBtn.addEventListener('click', () => editOverlay.remove());
            
            // Formulario
            const form = editOverlay.querySelector('#editEmployeeForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                employee.name = document.getElementById('edit_name').value;
                employee.email = document.getElementById('edit_email').value;
                employee.department = document.getElementById('edit_department').value;
                employee.position = document.getElementById('edit_position').value;
                employee.salary = parseFloat(document.getElementById('edit_salary').value);
                employee.status = document.getElementById('edit_status').value;
                
                renderEmployees();
                updateCommissionSelect();
                editOverlay.remove();
                showNotification('Empleado actualizado', 'Los datos se han actualizado exitosamente', 'success');
            });
            
            editOverlay.addEventListener('click', e => { 
                if (e.target === editOverlay) editOverlay.remove(); 
            });
        }

        function deleteEmployee(id) {
            const employee = employees.find(e => e.id === id);
            if (!employee) return;
            
            const modalOverlay = document.createElement('div');
            modalOverlay.className = 'modal-overlay';
            
            modalOverlay.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="modal-title">Confirmar Eliminación</div>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro de que desea eliminar a <strong>${employee.name}</strong> del sistema?
                    </div>
                    <div class="modal-footer">
                        <button class="modal-btn modal-btn-cancel">Cancelar</button>
                        <button class="modal-btn modal-btn-confirm">Sí, eliminar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modalOverlay);
            
            // Botón cancelar
            const cancelBtn = modalOverlay.querySelector('.modal-btn-cancel');
            cancelBtn.addEventListener('click', () => modalOverlay.remove());
            
            // Botón confirmar
            const confirmBtn = modalOverlay.querySelector('.modal-btn-confirm');
            confirmBtn.addEventListener('click', function() {
                employees = employees.filter(e => e.id !== id);
                renderEmployees();
                updateCommissionSelect();
                modalOverlay.remove();
                showNotification('Empleado eliminado', `${employee.name} ha sido eliminado del sistema`, 'success');
            });
            
            modalOverlay.addEventListener('click', e => { 
                if (e.target === modalOverlay) modalOverlay.remove(); 
            });
        }

        // ========== INICIALIZACIÓN Y EVENT LISTENERS ==========
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000);

            // Validaciones en tiempo real
            const fields = [
                { id: 'employee_name', type: 'name', validate: validateName, min: 2, max: 100 },
                { id: 'email', type: 'email', validate: validateEmail },
                { id: 'position', type: 'position', validate: validatePosition, min: 2, max: 80 },
                { id: 'customDepartment', type: 'name', validate: validateName, min: 2, max: 50 }
            ];

            fields.forEach(field => {
                const element = document.getElementById(field.id);
                if (element) {
                    element.addEventListener('input', function() {
                        const sanitized = sanitizeInput(this.value, field.type);
                        if (this.value !== sanitized) this.value = sanitized;
                        
                        if (this.value.trim()) {
                            if (!field.validate(this.value)) {
                                showFieldError(field.id, `Formato inválido`);
                            } else if (field.min && !validateLength(this.value, field.min, field.max)) {
                                showFieldError(field.id, `Debe tener entre ${field.min} y ${field.max} caracteres`);
                            } else {
                                removeFieldError(field.id);
                            }
                        } else {
                            removeFieldError(field.id);
                        }
                    });
                }
            });

            // Departamento personalizado
            document.getElementById('department').addEventListener('change', function() {
                const customInput = document.getElementById('customDepartment');
                if (this.value === 'otro') {
                    customInput.classList.add('show');
                    customInput.required = true;
                } else {
                    customInput.classList.remove('show');
                    customInput.required = false;
                    customInput.value = '';
                }
            });

            // Formulario de empleado
            document.getElementById('employeeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const name = document.getElementById('employee_name').value.trim();
                const email = document.getElementById('email').value.trim();
                const position = document.getElementById('position').value.trim();
                const customDeptField = document.getElementById('customDepartment');
                
                let isValid = true;

                if (!name || !validateName(name) || !validateLength(name, 2, 100)) {
                    showFieldError('employee_name', 'Nombre inválido');
                    isValid = false;
                }
                
                if (!email || !validateEmail(email)) {
                    showFieldError('email', 'Email inválido');
                    isValid = false;
                }
                
                if (!position || !validatePosition(position) || !validateLength(position, 2, 80)) {
                    showFieldError('position', 'Cargo inválido');
                    isValid = false;
                }
                
                if (customDeptField.classList.contains('show')) {
                    const customDept = customDeptField.value.trim();
                    if (!customDept || !validateName(customDept) || !validateLength(customDept, 2, 50)) {
                        showFieldError('customDepartment', 'Departamento inválido');
                        isValid = false;
                    }
                }
                
                if (!isValid) {
                    showNotification('Error', 'Corrija los errores en el formulario', 'error');
                    return;
                }
                
                const departmentSelect = document.getElementById('department');
                const departmentValue = departmentSelect.value === 'otro' 
                    ? customDeptField.value 
                    : departmentSelect.value;
                
                const newEmployee = {
                    id: employees.length + 1,
                    name,
                    email,
                    department: departmentValue,
                    position,
                    hire_date: document.getElementById('hire_date').value,
                    salary: parseFloat(document.getElementById('salary').value),
                    commissions: [],
                    status: 'active'
                };

                employees.push(newEmployee);
                renderEmployees();
                updateCommissionSelect();
                showNotification('Empleado agregado', 'El empleado se ha registrado exitosamente', 'success');
                
                this.reset();
                customDeptField.classList.remove('show');
                document.querySelectorAll('.field-error').forEach(e => e.remove());
                document.querySelectorAll('.is-valid, .is-invalid').forEach(f => {
                    f.classList.remove('is-valid', 'is-invalid');
                });
            });

            // Formulario de comisión
            document.getElementById('commissionForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const employeeId = parseInt(document.getElementById('commission_employee').value);
                const amount = parseFloat(document.getElementById('commission_amount').value);
                const type = document.getElementById('commission_type').value;
                const description = document.getElementById('commission_description').value;
                
                const employee = employees.find(e => e.id === employeeId);
                if (employee) {
                    const newCommission = {
                        id: ++commissionIdCounter,
                        amount,
                        type,
                        description,
                        date: new Date().toISOString().split('T')[0]
                    };
                    
                    employee.commissions.push(newCommission);
                    renderEmployees();
                    showNotification('Comisión registrada', `Se agregó $${amount.toFixed(2)} a ${employee.name}`, 'success');
                    this.reset();
                }
            });

            // Filtros
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    renderEmployees(this.dataset.filter);
                });
            });

            // Renderizado inicial
            renderEmployees();
            updateCommissionSelect();
        });
    </script>
@stop