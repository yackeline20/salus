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
                <span class="date">{{ now()->format('l, d \d\e F Y') }}</span>
                <span class="time">{{ now()->format('H:i') }}</span>
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
                            <input type="text" class="form-control custom-input" id="employee_name" name="employee_name" placeholder="Nombre completo del empleado" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" class="form-control custom-input" id="email" name="email" placeholder="correo@ejemplo.com" required>
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
                                   placeholder="Escriba el nombre del departamento">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="position">Cargo</label>
                            <input type="text" class="form-control custom-input" id="position" name="position" placeholder="Ej: Enfermera, Recepcionista, etc." required>
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
                                <!-- Se llenará dinámicamente -->
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="commission_amount">Monto ($)</label>
                            <input type="number" class="form-control custom-input" id="commission_amount" name="commission_amount" placeholder="Ej: 500.00" step="0.01" required>
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
        /* Reset de algunos estilos de AdminLTE */
        .content-wrapper {
            background: #f8f9fa !important;
        }
        
        /* Ajuste para prevenir espacio en blanco al final */
        .content {
            padding-bottom: 20px !important;
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

        /* Stats Cards */
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

        .stats-content {
            flex: 1;
        }

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

        /* Tarjeta de empleado/formulario */
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

        /* Inputs personalizados */
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

        /* Botón Salus */
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

        /* Lista de empleados */
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

        /* Tabs de filtro */
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

        /* Items de empleado */
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

        .employee-item.editing {
            background: #f5efe6;
            border: 1px solid #c9a876;
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

        .employee-details {
            flex: 1;
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

        .employee-commission {
            text-align: center;
            padding: 10px 15px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            min-width: 120px;
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

        /* Controles de empleado */
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

        /* Botones de acción */
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

        /* Estado vacío */
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
        }
    </style>
@stop

@section('js')
    <script>
        // Datos de ejemplo de empleados
        let employees = [
            {
                id: 1,
                name: 'María Pérez',
                email: 'maria.perez@salus.com',
                department: 'administracion',
                position: 'Administradora',
                hire_date: '2023-01-15',
                salary: 30000,
                commissions: 2850,
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
                commissions: 1650,
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
                commissions: 980,
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
                commissions: 1200,
                status: 'active'
            }
        ];

        // Manejar departamento personalizado
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

        // Función para obtener iniciales
        function getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase();
        }

        // Función para formatear departamento
        function formatDepartment(dept) {
            const departments = {
                'administracion': 'Administración',
                'enfermeria': 'Enfermería', 
                'recepcion': 'Recepción',
                'limpieza': 'Limpieza'
            };
            return departments[dept] || dept;
        }

        // Función para obtener clase de estado
        function getStatusClass(status) {
            switch(status) {
                case 'active': return 'status-active';
                case 'inactive': return 'status-inactive';
                case 'vacation': return 'status-vacation';
                default: return 'status-active';
            }
        }

        // Función para obtener texto de estado
        function getStatusText(status) {
            switch(status) {
                case 'active': return 'Activo';
                case 'inactive': return 'Inactivo';
                case 'vacation': return 'Vacaciones';
                default: return 'Activo';
            }
        }

        // Renderizar empleados
        function renderEmployees(filter = 'todos') {
            const container = document.getElementById('employeesList');
            let filteredEmployees = employees;
            
            if (filter !== 'todos') {
                filteredEmployees = employees.filter(emp => emp.department === filter);
            }
            
            if (filteredEmployees.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No hay empleados${filter !== 'todos' ? ' en este departamento' : ''}</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filteredEmployees.map(employee => `
                <div class="employee-item" data-id="${employee.id}">
                    <div class="employee-info">
                        <div class="employee-avatar">
                            ${getInitials(employee.name)}
                        </div>
                        <div class="employee-details">
                            <h4>${employee.name}</h4>
                            <p><i class="fas fa-envelope mr-1"></i> ${employee.email}</p>
                            <p><i class="fas fa-building mr-1"></i> ${formatDepartment(employee.department)} • ${employee.position}</p>
                            <p><i class="fas fa-calendar mr-1"></i> Desde ${new Date(employee.hire_date).toLocaleDateString()}</p>
                        </div>
                        <div class="employee-commission">
                            <div class="commission-amount">$${employee.commissions.toLocaleString()}</div>
                            <div class="commission-label">Comisiones</div>
                        </div>
                    </div>
                    <div class="employee-controls">
                        <button class="status-badge ${getStatusClass(employee.status)}">
                            ${getStatusText(employee.status)}
                        </button>
                        <button class="action-btn commission-btn" onclick="addCommission(${employee.id})" title="Agregar Comisión">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="action-btn edit-btn" onclick="editEmployee(${employee.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteEmployee(${employee.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');

            // Actualizar select de comisiones
            updateCommissionSelect();
        }

        // Actualizar select de empleados en comisiones
        function updateCommissionSelect() {
            const select = document.getElementById('commission_employee');
            select.innerHTML = '<option value="">Seleccionar empleado</option>' +
                employees.map(emp => `<option value="${emp.id}">${emp.name}</option>`).join('');
        }

        // Agregar comisión
        function addCommission(employeeId) {
            const employee = employees.find(e => e.id === employeeId);
            if (employee) {
                document.getElementById('commission_employee').value = employeeId;
                document.getElementById('commission_amount').focus();
                
                // Scroll suave a la sección de comisiones
                document.querySelector('#commissionForm').closest('.appointment-card').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

        // Editar empleado
        function editEmployee(id) {
            const employee = employees.find(e => e.id === id);
            if (!employee) return;

            // Mostrar formulario de edición (simplificado para el ejemplo)
            const newName = prompt('Nuevo nombre:', employee.name);
            const newPosition = prompt('Nuevo cargo:', employee.position);
            
            if (newName && newPosition) {
                employee.name = newName;
                employee.position = newPosition;
                renderEmployees();
                
                showNotification('Empleado actualizado exitosamente', 'success');
            }
        }

        // Eliminar empleado
        function deleteEmployee(id) {
            // Usar SweetAlert2 si está disponible
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Está seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#c9a876',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        employees = employees.filter(e => e.id !== id);
                        renderEmployees();
                        updateCommissionSelect();
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'El empleado ha sido eliminado',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                if (confirm('¿Está seguro de que desea eliminar este empleado?')) {
                    employees = employees.filter(e => e.id !== id);
                    renderEmployees();
                    updateCommissionSelect();
                    showNotification('Empleado eliminado exitosamente', 'success');
                }
            }
        }

        // Función para mostrar notificaciones
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)'};
                color: white;
                padding: 15px 25px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 1000;
                animation: slideIn 0.3s ease-out;
                display: flex;
                align-items: center;
                gap: 10px;
                max-width: 350px;
            `;
            
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            // Animación CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
            if (!document.head.querySelector('style[data-notification]')) {
                style.setAttribute('data-notification', 'true');
                document.head.appendChild(style);
            }
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Manejar envío del formulario de empleado
        document.getElementById('employeeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const departmentSelect = document.getElementById('department');
            const customDepartment = document.getElementById('customDepartment');
            
            let departmentValue = departmentSelect.value === 'otro' 
                ? customDepartment.value 
                : departmentSelect.value;
            
            const newEmployee = {
                id: employees.length + 1,
                name: document.getElementById('employee_name').value,
                email: document.getElementById('email').value,
                department: departmentValue,
                position: document.getElementById('position').value,
                hire_date: document.getElementById('hire_date').value,
                salary: parseFloat(document.getElementById('salary').value),
                commissions: 0,
                status: 'active'
            };

            employees.push(newEmployee);
            renderEmployees();
            updateCommissionSelect();
            
            // Notificación de éxito
            showNotification('¡Empleado agregado exitosamente!', 'success');
            
            this.reset();
            customDepartment.classList.remove('show');
        });

        // Manejar envío del formulario de comisión
        document.getElementById('commissionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const employeeId = parseInt(document.getElementById('commission_employee').value);
            const amount = parseFloat(document.getElementById('commission_amount').value);
            const type = document.getElementById('commission_type').value;
            const description = document.getElementById('commission_description').value;
            
            const employee = employees.find(e => e.id === employeeId);
            if (employee) {
                employee.commissions += amount;
                renderEmployees();
                
                // Notificación de éxito
                showNotification(`Comisión de ${amount.toFixed(2)} agregada a ${employee.name}`, 'success');
                
                this.reset();
            }
        });

        // Manejar filtros
        const filterTabs = document.querySelectorAll('.filter-tab');
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                renderEmployees(filter);
            });
        });

        // Renderizar empleados iniciales
        renderEmployees();
        updateCommissionSelect();
    </script>
@stop