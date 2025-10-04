@extends('adminlte::page')

@section('title', 'Citas')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
    <div class="container-fluid p-0">
        <!-- Header Welcome -->
        <div class="welcome-header">
            <div class="welcome-content">
                <h1>Citas</h1>
                <p>Administra tus citas manera eficiente.</p>
            </div>
            <div class="welcome-date">
                <span class="date"></span>
                <span class="time"></span>
            </div>
        </div>

        <!-- Formulario de Nueva Cita -->
        <div class="appointment-card mb-4">
            <div class="appointment-header">
                <i class="fas fa-calendar-plus" style="color: #c9a876; font-size: 24px;"></i>
                <h2>Agendar Nueva Cita</h2>
            </div>

            <form id="appointmentForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="patient">Nombre del Paciente</label>
                            <input type="text" class="form-control custom-input" id="patient" name="patient" placeholder="Nombre completo" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="service">Servicio</label>
                            <select class="form-control custom-input" id="service" name="service" required>
                                <option value="">Seleccionar servicio</option>
                                <option value="otro">Otro servicio (especificar)</option>
                                <option value="botox">Botox</option>
                                <option value="rellenos">Rellenos Dérmicos</option>
                                <option value="limpieza">Limpieza Facial</option>
                                <option value="peeling">Peeling Químico</option>
                                <option value="laser">Tratamiento Láser</option>
                                <option value="mesoterapia">Mesoterapia</option>
                            </select>
                            <input type="text" 
                                   id="customService" 
                                   class="form-control custom-input custom-service-input mt-2" 
                                   placeholder="Escriba el nombre del servicio">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Fecha</label>
                            <input type="date" class="form-control custom-input" id="date" name="date" min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="time">Hora</label>
                            <select class="form-control custom-input" id="time" name="time" required>
                                <option value="">Seleccionar hora</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                                <option value="17:00">05:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="notes">Notas Adicionales</label>
                            <textarea class="form-control custom-input" id="notes" name="notes" rows="3" placeholder="Alguna observación especial..."></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-salus">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Agendar Cita
                </button>
            </form>
        </div>

        <!-- Lista de Citas -->
        <div class="appointments-list">
            <div class="list-header">
                <h2>Mis Citas</h2>
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="todas">Todas</button>
                    <button class="filter-tab" data-filter="hoy">Hoy</button>
                    <button class="filter-tab" data-filter="proximas">Próximas</button>
                    <button class="filter-tab" data-filter="pasadas">Pasadas</button>
                </div>
            </div>

            <div id="appointmentsList"></div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div id="confirmModal" class="custom-modal-overlay">
        <div class="custom-modal">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Confirmar Eliminación</h3>
            <p id="modalMessage">¿Está seguro de que desea eliminar esta cita?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="closeConfirmModal()">Cancelar</button>
                <button class="modal-btn modal-btn-confirm" id="confirmDeleteBtn">Sí, eliminar</button>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .content-wrapper { 
            background: #f8f9fa !important;
        }
        
        .content { 
            padding-bottom: 20px !important;
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
        }

        .welcome-content h1 { color: white; font-size: 28px; font-weight: 600; margin-bottom: 8px; }
        .welcome-content p { color: rgba(255, 255, 255, 0.9); font-size: 16px; margin: 0; }
        .welcome-date { text-align: right; color: white; }
        .welcome-date .date { display: block; font-size: 14px; opacity: 0.9; }
        .welcome-date .time { display: block; font-size: 24px; font-weight: 600; margin-top: 5px; }

        .appointment-card { background: white; border-radius: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; }
        .appointment-header { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; }
        .appointment-header h2 { color: #2C3E50; font-size: 20px; font-weight: 600; margin: 0; }

        .custom-input { border-radius: 10px; border: 1px solid #e0e0e0; padding: 10px 15px; transition: all 0.3s; }
        .custom-input:focus { border-color: #c9a876; box-shadow: 0 0 0 3px rgba(201, 168, 118, 0.1); }
        .custom-service-input { display: none; }
        .custom-service-input.show { display: block; }

        .btn-salus {
            background: linear-gradient(135deg, #c9a876, #d4b896);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-salus:hover { color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(201, 168, 118, 0.3); }

        .appointments-list { 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); 
            padding: 30px;
        }
        .list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; }
        .list-header h2 { color: #2C3E50; font-size: 20px; font-weight: 600; margin: 0; }

        .filter-tabs { display: flex; gap: 8px; }
        .filter-tab { padding: 8px 16px; background: #f3f4f6; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s; color: #6b7280; font-size: 14px; }
        .filter-tab.active { background: linear-gradient(135deg, #c9a876, #d4b896); color: white; }
        .filter-tab:hover:not(.active) { background: #e5e7eb; }

        #appointmentsList {
            position: relative;
        }

        .appointment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            background: #f8f9fa;
            transition: all 0.3s;
            border: 1px solid #e5e7eb;
            position: relative;
            z-index: 1;
        }
        
        .appointment-item:has(.status-options.show) {
            z-index: 1000;
        }
        
        .appointment-item:hover { background: #f5efe6; transform: translateX(5px); }
        .appointment-item.editing { background: #f5efe6; border: 1px solid #c9a876; }

        .appointment-info { display: flex; gap: 20px; align-items: center; flex: 1; }
        .appointment-time {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 15px;
            background: white;
            border-radius: 10px;
            min-width: 80px;
            border: 1px solid #e5e7eb;
        }
        .appointment-time .hour { font-size: 16px; font-weight: 600; color: #2C3E50; }
        .appointment-time .date { font-size: 12px; color: #6b7280; margin-top: 2px; }

        .appointment-details { flex: 1; }
        .appointment-details h4 { color: #2C3E50; font-size: 16px; margin-bottom: 5px; }
        .appointment-details p { color: #6b7280; font-size: 14px; margin: 0; }
        .appointment-details input { width: 100%; padding: 8px 12px; border: 1px solid #c9a876; border-radius: 6px; margin-bottom: 5px; }

        .edit-date-time { display: flex; gap: 10px; margin-bottom: 10px; }
        .edit-date-time input { flex: 1; }

        .appointment-controls { 
            display: flex; 
            gap: 8px; 
            align-items: center;
            position: relative;
        }
        
        .status-dropdown { 
            position: relative;
        }
        
        .status-badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 500; 
            cursor: pointer; 
            transition: all 0.3s; 
            border: none; 
            min-width: 100px; 
            text-align: center; 
        }
        
        .status-programada {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-confirmed {
            background: #d4f4dd;
            color: #2d7a4e;
        }

        .status-realizada {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #ffd6d6;
            color: #d32f2f;
        }

        .status-options { 
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2); 
            display: none; 
            z-index: 99999; 
            min-width: 160px; 
            border: 1px solid #e5e7eb;
            padding: 4px 0;
        }
        
        .status-options.show { 
            display: block; 
        }
        
        .status-option { 
            padding: 10px 16px; 
            cursor: pointer; 
            transition: all 0.2s; 
            font-size: 14px; 
            display: flex; 
            align-items: center; 
            gap: 12px;
            color: #374151;
        }
        .status-option:hover { 
            background: #f3f4f6;
        }
        .status-option i { 
            width: 16px; 
            font-size: 14px;
        }

        .action-btn { padding: 8px 12px; background: transparent; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; color: #6b7280; transition: all 0.3s; }
        .action-btn:hover { background: #f5efe6; border-color: #c9a876; }
        .action-btn.save-btn { background: #10b981; color: white; border-color: #10b981; }
        .action-btn.cancel-btn { background: #ef4444; color: white; border-color: #ef4444; }
        .action-btn.delete-btn { color: #ef4444; border-color: #ef4444; }
        .action-btn.reminder-btn { background: #c9a876; color: white; border-color: #c9a876; }

        .empty-state { text-align: center; padding: 60px 20px; color: #6b7280; }
        .empty-state i { font-size: 80px; opacity: 0.3; margin-bottom: 20px; }

        .custom-modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 99999; justify-content: center; align-items: center; }
        .custom-modal-overlay.show { display: flex; }
        .custom-modal { background: white; border-radius: 20px; padding: 40px; max-width: 450px; width: 90%; text-align: center; animation: modalSlideIn 0.3s ease-out; }

        @keyframes modalSlideIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-icon { width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .modal-icon i { color: #dc2626; font-size: 40px; }
        .custom-modal h3 { color: #2C3E50; font-size: 24px; font-weight: 600; margin-bottom: 15px; }
        .custom-modal p { color: #6b7280; font-size: 16px; margin-bottom: 30px; }

        .modal-buttons { display: flex; gap: 15px; justify-content: center; }
        .modal-btn { padding: 12px 30px; border-radius: 10px; border: none; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s; }
        .modal-btn-cancel { background: #f3f4f6; color: #6b7280; }
        .modal-btn-cancel:hover { background: #e5e7eb; }
        .modal-btn-confirm { background: #dc2626; color: white; }
        .modal-btn-confirm:hover { background: #b91c1c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); }

        @media (max-width: 768px) {
            .welcome-header { flex-direction: column; text-align: center; }
            .welcome-date { margin-top: 20px; text-align: center; }
            .list-header { flex-direction: column; gap: 15px; }
            .filter-tabs { width: 100%; justify-content: center; }
            .appointment-info { flex-direction: column; align-items: flex-start; gap: 15px; }
            .appointment-controls { flex-direction: column; width: 100%; }
            .status-badge { width: 100%; }
            .edit-date-time { flex-direction: column; }
        }
    </style>
@stop

@section('js')
    <script>
        let appointments = [];
        let currentFilter = 'todas';

        document.getElementById('service').addEventListener('change', function() {
            const customInput = document.getElementById('customService');
            if (this.value === 'otro') {
                customInput.classList.add('show');
                customInput.required = true;
            } else {
                customInput.classList.remove('show');
                customInput.required = false;
                customInput.value = '';
            }
        });

        function showNotification(message, type = 'success') {
            const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: ${bgColor};
                color: white;
                padding: 15px 25px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 100000;
                display: flex;
                align-items: center;
                gap: 10px;
                animation: slideInRight 0.3s ease-out;
            `;
            notification.innerHTML = `<i class="fas fa-check-circle"></i>${message}`;
            
            const style = document.createElement('style');
            style.textContent = `@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
            document.head.appendChild(style);
            
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        function formatTime12Hour(time24) {
            if (!time24) return '';
            const [hours, minutes] = time24.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const hour12 = hour % 12 || 12;
            return `${hour12}:${minutes} ${ampm}`;
        }

        function parseMySQLDate(fechaStr) {
            if (!fechaStr) return null;
            const parts = fechaStr.split('-');
            return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        }

        function getDateText(fechaObj) {
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            
            const fecha = new Date(fechaObj);
            fecha.setHours(0, 0, 0, 0);
            
            const diff = fecha.getTime() - hoy.getTime();
            const daysDiff = Math.floor(diff / (1000 * 60 * 60 * 24));
            
            if (daysDiff === 0) return 'Hoy';
            if (daysDiff === 1) return 'Mañana';
            
            const opciones = { day: 'numeric', month: 'short' };
            return fecha.toLocaleDateString('es-ES', opciones);
        }

        document.getElementById('appointmentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const serviceSelect = document.getElementById('service');
            const customService = document.getElementById('customService');
            
            let serviceValue = serviceSelect.value === 'otro' 
                ? customService.value 
                : serviceSelect.options[serviceSelect.selectedIndex].text;
            
            const horaInicio = document.getElementById('time').value;
            const [horas, minutos] = horaInicio.split(':');
            const horaFin = (parseInt(horas) + 1).toString().padStart(2, '0');
            const horaFinCompleta = `${horaFin}:${minutos}:00`;
            
            const citaData = {
                codCliente: 105,
                codEmpleado: 1,
                fechaCita: document.getElementById('date').value,
                horaInicio: horaInicio + ':00',
                horaFin: horaFinCompleta,
                estadoCita: 'Programada',
                notasInternas: `Paciente: ${document.getElementById('patient').value} - Servicio: ${serviceValue} - ${document.getElementById('notes').value}`
            };

            try {
                const response = await fetch('/api/citas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(citaData)
                });

                if (response.ok) {
                    showNotification(`Cita agendada - ${document.getElementById('patient').value} registrado exitosamente`);
                    this.reset();
                    customService.classList.remove('show');
                    loadAppointments();
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al agendar la cita', 'error');
            }
        });

        async function loadAppointments() {
            try {
                const response = await fetch('/api/citas', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });

                if (response.ok) {
                    const citas = await response.json();
                    
                    appointments = citas.map(cita => {
                        const fechaObj = parseMySQLDate(cita.Fecha_Cita);
                        
                        return {
                            id: cita.Cod_Cita,
                            codCita: cita.Cod_Cita,
                            codCliente: cita.Cod_Cliente,
                            codEmpleado: cita.Cod_Empleado,
                            fechaCita: cita.Fecha_Cita,
                            fechaObj: fechaObj,
                            horaInicio: cita.Hora_Inicio,
                            horaFin: cita.Hora_Fin,
                            estadoCita: cita.Estado_Cita,
                            patient: cita.Notas_Internas ? cita.Notas_Internas.split(' - ')[0].replace('Paciente: ', '') : 'Sin nombre',
                            service: cita.Notas_Internas ? (cita.Notas_Internas.split(' - ')[1] || 'Sin servicio').replace('Servicio: ', '') : 'Sin servicio',
                            time: cita.Hora_Inicio ? formatTime12Hour(cita.Hora_Inicio.substring(0, 5)) : '',
                            dateText: getDateText(fechaObj),
                            status: mapearEstado(cita.Estado_Cita)
                        };
                    });

                    renderAppointments();
                }
            } catch (error) {
                console.error('Error al cargar citas:', error);
            }
        }

        function mapearEstado(estado) {
            if (!estado) return 'programada';
            const estadoLower = estado.toLowerCase();
            if (estadoLower.includes('program')) return 'programada';
            if (estadoLower.includes('confirm')) return 'confirmed';
            if (estadoLower.includes('realiz')) return 'realizada';
            if (estadoLower.includes('cancel')) return 'cancelled';
            return 'programada';
        }

        function filterAppointments() {
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            return appointments.filter(appointment => {
                if (!appointment.fechaObj) return false;
                
                const fechaCita = new Date(appointment.fechaObj);
                fechaCita.setHours(0, 0, 0, 0);

                switch(currentFilter) {
                    case 'hoy':
                        return fechaCita.getTime() === hoy.getTime();
                    case 'proximas':
                        return fechaCita.getTime() > hoy.getTime();
                    case 'pasadas':
                        return fechaCita.getTime() < hoy.getTime();
                    default:
                        return true;
                }
            });
        }

        function renderAppointments() {
            const container = document.getElementById('appointmentsList');
            const filteredAppointments = filterAppointments();
            
            if (filteredAppointments.length === 0) {
                container.innerHTML = `<div class="empty-state"><i class="fas fa-calendar-alt"></i><p>No hay citas en esta categoría</p></div>`;
                return;
            }

            filteredAppointments.sort((a, b) => {
                const fechaA = new Date(a.fechaObj);
                const fechaB = new Date(b.fechaObj);
                return fechaA - fechaB;
            });

            container.innerHTML = filteredAppointments.map(appointment => `
                <div class="appointment-item" data-id="${appointment.id}">
                    <div class="appointment-info">
                        <div class="appointment-time">
                            <span class="hour">${appointment.time}</span>
                            <span class="date">${appointment.dateText}</span>
                        </div>
                        <div class="appointment-details">
                            <h4>${appointment.patient}</h4>
                            <p>${appointment.service}</p>
                        </div>
                    </div>
                    <div class="appointment-controls">
                        <div class="status-dropdown">
                            <button class="status-badge ${getStatusClass(appointment.status)}" onclick="toggleStatusDropdown(${appointment.id})">
                                ${getStatusText(appointment.status)}
                            </button>
                            <div class="status-options" id="status-options-${appointment.id}">
                                <div class="status-option" onclick="changeStatus(${appointment.id}, 'programada')">
                                    <i class="fas fa-calendar-alt"></i> Programada
                                </div>
                                <div class="status-option" onclick="changeStatus(${appointment.id}, 'confirmed')">
                                    <i class="fas fa-check-circle"></i> Confirmada
                                </div>
                                <div class="status-option" onclick="changeStatus(${appointment.id}, 'realizada')">
                                    <i class="fas fa-check-double"></i> Realizada
                                </div>
                                <div class="status-option" onclick="changeStatus(${appointment.id}, 'cancelled')">
                                    <i class="fas fa-times-circle"></i> Cancelada
                                </div>
                            </div>
                        </div>
                        <button class="action-btn reminder-btn" onclick="sendReminder(${appointment.id})" title="Enviar Recordatorio">
                            <i class="fas fa-bell"></i>
                        </button>
                        <button class="action-btn edit-btn" onclick="editAppointment(${appointment.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteAppointment(${appointment.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function getStatusClass(status) {
            switch(status) {
                case 'programada': return 'status-programada';
                case 'confirmed': return 'status-confirmed';
                case 'realizada': return 'status-realizada';
                case 'cancelled': return 'status-cancelled';
                default: return 'status-programada';
            }
        }

        function getStatusText(status) {
            switch(status) {
                case 'programada': return 'Programada';
                case 'confirmed': return 'Confirmada';
                case 'realizada': return 'Realizada';
                case 'cancelled': return 'Cancelada';
                default: return 'Programada';
            }
        }

        function toggleStatusDropdown(id) {
            const dropdown = document.getElementById(`status-options-${id}`);
            document.querySelectorAll('.status-options').forEach(d => {
                if (d.id !== `status-options-${id}`) d.classList.remove('show');
            });
            dropdown.classList.toggle('show');
        }

        async function changeStatus(id, newStatus) {
            const appointment = appointments.find(a => a.id === id);
            if (!appointment) return;

            try {
                let estadoCita = 'Programada';
                if (newStatus === 'confirmed') estadoCita = 'Confirmada';
                if (newStatus === 'realizada') estadoCita = 'Realizada';
                if (newStatus === 'cancelled') estadoCita = 'Cancelada';
                
                const response = await fetch('/api/citas', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        Cod_Cita: appointment.codCita,
                        Cod_Cliente: appointment.codCliente,
                        Cod_Empleado: appointment.codEmpleado,
                        Fecha_Cita: appointment.fechaCita,
                        Hora_Inicio: appointment.horaInicio,
                        Hora_Fin: appointment.horaFin,
                        Estado_Cita: estadoCita,
                        Notas_Internas: `Paciente: ${appointment.patient} - Servicio: ${appointment.service}`
                    })
                });

                if (response.ok) {
                    document.querySelectorAll('.status-options').forEach(d => d.classList.remove('show'));
                    loadAppointments();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function editAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (!appointment) return;

            const item = document.querySelector(`[data-id="${id}"]`);
            if (item.classList.contains('editing')) {
                renderAppointments();
                return;
            }

            const time24 = appointment.horaInicio.substring(0, 5);

            item.classList.add('editing');
            item.querySelector('.appointment-details').innerHTML = `
                <input type="text" value="${appointment.patient}" id="edit-patient-${id}" placeholder="Nombre del paciente" />
                <input type="text" value="${appointment.service}" id="edit-service-${id}" placeholder="Servicio" />
                <div class="edit-date-time">
                    <input type="date" value="${appointment.fechaCita}" id="edit-date-${id}" />
                    <input type="time" value="${time24}" id="edit-time-${id}" />
                </div>
            `;

            item.querySelector('.appointment-controls').innerHTML = `
                <button class="action-btn save-btn" onclick="saveAppointment(${id})">Guardar</button>
                <button class="action-btn cancel-btn" onclick="renderAppointments()">Cancelar</button>
            `;
        }

        async function saveAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (!appointment) return;

            const newPatient = document.getElementById(`edit-patient-${id}`).value;
            const newService = document.getElementById(`edit-service-${id}`).value;
            const newDate = document.getElementById(`edit-date-${id}`).value;
            const newTime = document.getElementById(`edit-time-${id}`).value;

            const [horas, minutos] = newTime.split(':');
            const horaFin = (parseInt(horas) + 1).toString().padStart(2, '0');
            const horaFinCompleta = `${horaFin}:${minutos}:00`;

            try {
                const response = await fetch('/api/citas', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        Cod_Cita: appointment.codCita,
                        Cod_Cliente: appointment.codCliente,
                        Cod_Empleado: appointment.codEmpleado,
                        Fecha_Cita: newDate,
                        Hora_Inicio: newTime + ':00',
                        Hora_Fin: horaFinCompleta,
                        Estado_Cita: appointment.estadoCita,
                        Notas_Internas: `Paciente: ${newPatient} - Servicio: ${newService}`
                    })
                });

                if (response.ok) {
                    showNotification(`Cita actualizada - ${newPatient} modificado exitosamente`);
                    loadAppointments();
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al actualizar la cita', 'error');
            }
        }

        async function deleteAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (!appointment) return;

            const modal = document.getElementById('confirmModal');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            const modalMessage = document.getElementById('modalMessage');
            
            modalMessage.innerHTML = `¿Está seguro de que desea eliminar la cita de <strong>${appointment.patient}</strong>?`;
            modal.classList.add('show');

            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

            newConfirmBtn.onclick = async function() {
                try {
                    const response = await fetch(`/api/citas?cod=${appointment.codCita}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        closeConfirmModal();
                        showNotification(`Cita eliminada - ${appointment.patient} ha sido eliminado del sistema`);
                        loadAppointments();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    closeConfirmModal();
                    showNotification('Error al eliminar la cita', 'error');
                }
            };
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('show');
        }

        function sendReminder(id) {
            const appointment = appointments.find(a => a.id === id);
            if (appointment) {
                showNotification(`Recordatorio enviado a ${appointment.patient}`, 'info');
            }
        }

        const filterTabs = document.querySelectorAll('.filter-tab');
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                renderAppointments();
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.status-dropdown')) {
                document.querySelectorAll('.status-options').forEach(d => d.classList.remove('show'));
            }
        });

        function updateClock() {
            const now = new Date();
            const dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            
            const dateStr = `${dias[now.getDay()]}, ${now.getDate()} de ${meses[now.getMonth()]} ${now.getFullYear()}`;
            
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            const timeStr = `${hours}:${minutes} ${ampm}`;
            
            const dateElement = document.querySelector('.welcome-date .date');
            const timeElement = document.querySelector('.welcome-date .time');
            
            if (dateElement) dateElement.textContent = dateStr;
            if (timeElement) timeElement.textContent = timeStr;
        }

        updateClock();
        setInterval(updateClock, 1000);
        loadAppointments();
    </script>
@stop