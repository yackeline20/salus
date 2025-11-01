@extends('adminlte::page')

@section('title', 'Citas')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
    <div class="container-fluid p-0">
        <div class="welcome-header">
            <div class="welcome-content">
                <h1>Gestión de Citas</h1>
                <p>Administra tus citas de manera eficiente.</p>
            </div>
            <div class="welcome-date">
                <span class="date"></span>
                <span class="time"></span>
            </div>
        </div>

        <div class="appointment-card mb-4">
            <div class="appointment-header">
                <i class="fas fa-calendar-plus" style="color: #c9a876; font-size: 24px;"></i>
                <h2 id="formTitle">Agendar Nueva Cita</h2>
            </div>

            <form id="appointmentForm">
                @csrf
                
                <input type="hidden" id="editingAppointmentId">

                <div class="client-search-section" id="searchSection">
                    <h4><i class="fas fa-user-search mr-2"></i>Buscar Cliente Existente</h4>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="searchClientCode">Código de Cliente</label>
                                <input type="number" class="form-control custom-input" id="searchClientCode" placeholder="Ingrese código de cliente">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-salus btn-block" id="btnSearchClient">
                                <i class="fas fa-search mr-2"></i>Buscar Cliente
                            </button>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-link" id="btnNewClient">
                            <i class="fas fa-user-plus mr-1"></i>¿Cliente nuevo? Haga clic aquí para registrar
                        </button>
                    </div>
                </div>

                <hr class="my-4">

                <div id="clientDataSection" style="display: none;">
                    <h4 class="text-success"><i class="fas fa-check-circle mr-2"></i>Cliente Encontrado</h4>
                    
                    <input type="hidden" id="codCliente" name="codCliente">
                    <input type="hidden" id="codPersona" name="codPersona">

                    <div class="alert alert-success">
                        <strong><i class="fas fa-user mr-2"></i><span id="clientFullName"></span></strong>
                        <br>
                        <small><strong>DNI:</strong> <span id="displayDNI"></span></small> | 
                        <small><strong>Teléfono:</strong> <span id="displayPhone"></span></small> | 
                        <small><strong>Email:</strong> <span id="displayEmail"></span></small>
                    </div>

                    <button type="button" class="btn btn-secondary btn-sm" id="btnChangeClient">
                        <i class="fas fa-redo mr-1"></i>Buscar otro cliente
                    </button>
                </div>

                <div id="newClientForm" style="display: none;">
                    <h4 class="text-primary"><i class="fas fa-user-plus mr-2"></i>Registro de Nuevo Cliente</h4>
                    
                    <div class="card card-body bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newClientName">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control custom-input" id="newClientName" placeholder="Nombre del cliente">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newClientLastName">Apellido <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control custom-input" id="newClientLastName" placeholder="Apellido del cliente">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newClientDNI">DNI <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control custom-input" id="newClientDNI" placeholder="0801-1990-12345">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newClientBirthDate">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control custom-input" id="newClientBirthDate">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newClientGender">Género <span class="text-danger">*</span></label>
                                    <select class="form-control custom-input" id="newClientGender">
                                        <option value="">Seleccionar</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                        <option value="O">Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newClientPhone">Teléfono</label>
                                    <input type="text" class="form-control custom-input" id="newClientPhone" placeholder="9876-5432">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newClientEmail">Correo Electrónico</label>
                                    <input type="email" class="form-control custom-input" id="newClientEmail" placeholder="cliente@ejemplo.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newClientAddress">Dirección</label>
                                    <input type="text" class="form-control custom-input" id="newClientAddress" placeholder="Colonia, Calle, Número">
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-secondary" id="btnCancelNewClient">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-success" id="btnSaveNewClient">
                                <i class="fas fa-save mr-2"></i>Guardar Cliente
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div id="appointmentDataSection" style="display: none;">
                    <h4><i class="fas fa-calendar-check mr-2"></i>Datos de la Cita</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="service">Servicio <span class="text-danger">*</span></label>
                                <select class="form-control custom-input" id="service" name="service" required>
                                    <option value="">Seleccionar servicio</option>
                                    <option value="Botox">Botox</option>
                                    <option value="Rellenos Dérmicos">Rellenos Dérmicos</option>
                                    <option value="Limpieza Facial">Limpieza Facial</option>
                                    <option value="Peeling Químico">Peeling Químico</option>
                                    <option value="Tratamiento Láser">Tratamiento Láser</option>
                                    <option value="Mesoterapia">Mesoterapia</option>
                                    <option value="HydraFacial">HydraFacial</option>
                                    <option value="Microneedling">Microneedling</option>
                                    <option value="otro">Otro servicio (especificar)</option>
                                </select>
                                <input type="text" 
                                    id="customService" 
                                    class="form-control custom-input custom-service-input mt-2" 
                                    placeholder="Escriba el nombre del servicio">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Fecha <span class="text-danger">*</span></label>
                                <input type="date" class="form-control custom-input" id="date" name="date" min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time">Hora <span class="text-danger">*</span></label>
                                <select class="form-control custom-input" id="time" name="time" required>
                                    <option value="">Seleccionar hora</option>
                                    <option value="09:00">09:00 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="12:00">12:00 PM</option>
                                    <option value="13:00">01:00 PM</option>
                                    <option value="14:00">02:00 PM</option>
                                    <option value="15:00">03:00 PM</option>
                                    <option value="16:00">04:00 PM</option>
                                    <option value="17:00">05:00 PM</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duration">Duración Estimada</label>
                                <select class="form-control custom-input" id="duration" name="duration">
                                    <option value="1">1 hora</option>
                                    <option value="1.5">1 hora 30 min</option>
                                    <option value="2">2 horas</option>
                                    <option value="2.5">2 horas 30 min</option>
                                    <option value="3">3 horas</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="notes">Notas Adicionales / Observaciones</label>
                                <textarea class="form-control custom-input" id="notes" name="notes" rows="3" placeholder="Alguna observación especial sobre el tratamiento, alergias, o requerimientos del paciente..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="button" class="btn btn-secondary" id="btnResetForm">
                            <i class="fas fa-redo mr-2"></i>Reiniciar Formulario
                        </button>
                        <button type="submit" class="btn btn-salus" id="submitAppointmentBtn">
                            <i class="fas fa-calendar-check mr-2"></i>Agendar Cita
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="appointments-list">
            <div class="list-header">
                <h2><i class="fas fa-list mr-2"></i>Mis Citas</h2>
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

    <!-- ⬇️ MODAL GENÉRICO MEJORADO ⬇️ -->
    <div id="confirmModal" class="custom-modal-overlay">
        <div class="custom-modal">
            <div class="modal-icon" id="modalIcon"> 
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 id="modalTitle">Confirmar Acción</h3> 
            <p id="modalMessage">¿Está seguro?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="closeConfirmModal()">Cancelar</button>
                <button class="modal-btn modal-btn-confirm" id="confirmActionBtn">Sí, confirmar</button> 
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

        .client-search-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            border: 2px dashed #c9a876;
        }

        .client-search-section h4 {
            color: #2C3E50;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        #clientDataSection, #newClientForm, #appointmentDataSection {
            animation: fadeIn 0.4s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
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
        
        .custom-service-input { 
            display: none; 
        }
        
        .custom-service-input.show { 
            display: block; 
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
        
        .appointment-item:hover { 
            background: #f5efe6; 
            transform: translateX(5px); 
        }
        
        .appointment-info { 
            display: flex; 
            gap: 20px; 
            align-items: center; 
            flex: 1; 
        }
        
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
        
        .appointment-time .hour { 
            font-size: 16px; 
            font-weight: 600; 
            color: #2C3E50; 
        }
        
        .appointment-time .date { 
            font-size: 12px; 
            color: #6b7280; 
            margin-top: 2px; 
        }

        .appointment-details { 
            flex: 1; 
        }
        
        .appointment-details h4 { 
            color: #2C3E50; 
            font-size: 16px; 
            margin-bottom: 5px; 
        }
        
        .appointment-details p { 
            color: #6b7280; 
            font-size: 14px; 
            margin: 0; 
        }

        .appointment-controls { 
            display: flex; 
            gap: 8px; 
            align-items: center;
        }
        
        .status-badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 500; 
            min-width: 100px; 
            text-align: center; 
        }
        
        .status-programada {
            background: #e0f2fe; /* Azul claro */
            color: #0c54a8; /* Azul oscuro */
        }
        .status-confirmed {
            background: #dcfce7; /* Verde claro */
            color: #15803d; /* Verde oscuro */
        }
        .status-realizada {
            background: #f3f4f6; /* Gris claro */
            color: #4b5563; /* Gris oscuro */
        }
        .status-cancelled {
            background: #fee2e2; /* Rojo claro */
            color: #b91c1c; /* Rojo oscuro */
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

        /* ⬇️ ESTILOS PARA EL MODAL MEJORADO ⬇️ */
        .custom-modal-overlay { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0, 0, 0, 0.5); 
            z-index: 99999; 
            justify-content: center; 
            align-items: center; 
            backdrop-filter: blur(4px);
        }
        
        .custom-modal-overlay.show { 
            display: flex; 
        }
        
        .custom-modal { 
            background: white; 
            border-radius: 20px; 
            padding: 40px; 
            max-width: 450px; 
            width: 90%; 
            text-align: center; 
            animation: modalSlideIn 0.3s ease-out; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        @keyframes modalSlideIn {
            from { transform: translateY(20px) scale(0.95); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }

        .modal-icon { 
            width: 80px; 
            height: 80px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 20px; 
            transition: background-color 0.3s;
        }
        
        .modal-icon i { 
            font-size: 40px; 
            transition: color 0.3s;
        }

        /* Estilo Peligro (Eliminar) */
        .modal-icon.icon-danger {
            background: #fee2e2; /* Rojo claro */
        }
        .modal-icon.icon-danger i {
            color: #dc2626; /* Rojo oscuro */
        }
        
        /* Estilo Info (Resetear) */
        .modal-icon.icon-info {
            background: #e0f2fe; /* Azul claro */
        }
        .modal-icon.icon-info i {
            color: #0c54a8; /* Azul oscuro */
        }
        
        .custom-modal h3 { 
            color: #2C3E50; 
            font-size: 24px; 
            font-weight: 600; 
            margin-bottom: 15px; 
        }
        
        .custom-modal p { 
            color: #6b7280; 
            font-size: 16px; 
            margin-bottom: 30px; 
            line-height: 1.6;
        }

        .modal-buttons { 
            display: flex; 
            gap: 15px; 
            justify-content: center; 
        }
        
        .modal-btn { 
            padding: 12px 30px; 
            border-radius: 10px; 
            border: none; 
            font-size: 16px; 
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
        
        /* Estilo Botón Peligro (Eliminar) */
        .modal-btn-confirm.btn-danger { 
            background: #dc2626; 
            color: white; 
        }
        .modal-btn-confirm.btn-danger:hover { 
            background: #b91c1c; 
            transform: translateY(-2px); 
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); 
        }

        /* Estilo Botón Info (Resetear) */
        .modal-btn-confirm.btn-info { 
            background: #0c54a8; 
            color: white; 
        }
        .modal-btn-confirm.btn-info:hover { 
            background: #0a438a; 
            transform: translateY(-2px); 
            box-shadow: 0 4px 12px rgba(12, 84, 168, 0.3); 
        }

        @media (max-width: 768px) {
            .welcome-header { 
                flex-direction: column; 
                text-align: center; 
            }
            .list-header { 
                flex-direction: column; 
                gap: 15px; 
            }
            .appointment-info { 
                flex-direction: column; 
                align-items: flex-start; 
                gap: 15px; 
            }
        }
    </style>
@stop

@section('js')
    <script>
        let appointments = [];
        let currentFilter = 'todas';
        let currentClientCode = null;
        let confirmActionCallback = null; // Guardar la acción a ejecutar

        function mapGenderToFullText(genderCode) {
            switch (genderCode) {
                case 'M': return 'Masculino';
                case 'F': return 'Femenino';
                case 'O': return 'Otro';
                default: return '';
            }
        }

        async function findClient(codCliente) {
            if (!codCliente) {
                showNotification('Por favor ingrese un código de cliente', 'error');
                return false;
            }
            const btnSearch = document.getElementById('btnSearchClient');
            const originalBtnHtml = btnSearch.innerHTML;
            btnSearch.disabled = true;
            btnSearch.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Buscando...';

            try {
                const response = await fetch(`/api/citas/buscar-cliente?cod=${codCliente}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    currentClientCode = data.cliente.Cod_Cliente;
                    document.getElementById('codCliente').value = data.cliente.Cod_Cliente;
                    document.getElementById('codPersona').value = data.persona.Cod_Persona;
                    document.getElementById('clientFullName').textContent = `${data.persona.Nombre} ${data.persona.Apellido}`;
                    document.getElementById('displayDNI').textContent = data.persona.DNI || 'No registrado';
                    document.getElementById('displayPhone').textContent = data.telefonos.length > 0 ? data.telefonos[0].Numero : 'No registrado';
                    document.getElementById('displayEmail').textContent = data.correos.length > 0 ? data.correos[0].Correo : 'No registrado';
                    
                    document.getElementById('searchSection').style.display = 'none';
                    document.getElementById('clientDataSection').style.display = 'block';
                    document.getElementById('appointmentDataSection').style.display = 'block';
                    
                    showNotification(`✓ Cliente encontrado: ${data.persona.Nombre} ${data.persona.Apellido}`);
                    return true;
                } else {
                    showNotification('❌ Cliente no encontrado. Verifique el código o registre uno nuevo.', 'error');
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al buscar cliente', 'error');
                return false;
            } finally {
                btnSearch.disabled = false;
                btnSearch.innerHTML = originalBtnHtml;
            }
        }

        document.getElementById('btnSearchClient').addEventListener('click', async function() {
            const codCliente = document.getElementById('searchClientCode').value.trim();
            await findClient(codCliente);
        });

        document.getElementById('btnNewClient').addEventListener('click', function() {
            document.getElementById('searchSection').style.display = 'none';
            document.getElementById('newClientForm').style.display = 'block';
        });

        document.getElementById('btnSaveNewClient').addEventListener('click', async function() {
            const nombre = document.getElementById('newClientName').value.trim();
            const apellido = document.getElementById('newClientLastName').value.trim();
            const dni = document.getElementById('newClientDNI').value.trim();
            const fechaNacimiento = document.getElementById('newClientBirthDate').value;
            const generoCode = document.getElementById('newClientGender').value;
            const generoTexto = mapGenderToFullText(generoCode); 
            const telefono = document.getElementById('newClientPhone').value.trim();
            const correo = document.getElementById('newClientEmail').value.trim();
            const direccion = document.getElementById('newClientAddress').value.trim();

            if (!nombre || !apellido || !dni || !fechaNacimiento || !generoCode) {
                showNotification('⚠️ Complete todos los campos obligatorios (marcados con *)', 'error');
                return;
            }

            const btnSave = this;
            btnSave.disabled = true;
            btnSave.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';

            try {
                const response = await fetch('/api/citas/crear-cliente', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nombre: nombre, apellido: apellido, dni: dni, fechaNacimiento: fechaNacimiento,
                        genero: generoTexto, telefono: telefono, correo: correo, direccion: direccion
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    currentClientCode = result.cod_cliente;
                    document.getElementById('codCliente').value = result.cod_cliente;
                    document.getElementById('codPersona').value = result.cod_persona || '';
                    document.getElementById('clientFullName').textContent = `${nombre} ${apellido}`;
                    document.getElementById('displayDNI').textContent = dni;
                    document.getElementById('displayPhone').textContent = telefono || 'No registrado';
                    document.getElementById('displayEmail').textContent = correo || 'No registrado';
                    
                    document.getElementById('newClientForm').style.display = 'none';
                    document.getElementById('clientDataSection').style.display = 'block';
                    document.getElementById('appointmentDataSection').style.display = 'block';
                    
                    showNotification(`✓ Cliente registrado exitosamente: ${nombre} ${apellido} (Código: ${result.cod_cliente})`);
                    
                    // Limpiar formulario de nuevo cliente
                    document.getElementById('newClientName').value = '';
                    document.getElementById('newClientLastName').value = '';
                    document.getElementById('newClientDNI').value = '';
                    document.getElementById('newClientBirthDate').value = '';
                    document.getElementById('newClientGender').value = '';
                    document.getElementById('newClientPhone').value = '';
                    document.getElementById('newClientEmail').value = '';
                    document.getElementById('newClientAddress').value = '';

                } else {
                    showNotification('❌ Error al registrar cliente: ' + (result.error || 'Error desconocido'), 'error'); 
                }
            } catch (error) {
                console.error('Error completo:', error);
                showNotification('❌ Error de conexión: ' + error.message, 'error');
            } finally {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="fas fa-save mr-2"></i>Guardar Cliente';
            }
        });

        document.getElementById('btnCancelNewClient').addEventListener('click', function() {
            document.getElementById('newClientForm').style.display = 'none';
            document.getElementById('searchSection').style.display = 'block';
        });

        document.getElementById('btnChangeClient').addEventListener('click', function() {
            document.getElementById('clientDataSection').style.display = 'none';
            document.getElementById('appointmentDataSection').style.display = 'none';
            document.getElementById('searchSection').style.display = 'block';
            document.getElementById('searchClientCode').value = '';
            currentClientCode = null;
        });

        document.getElementById('btnResetForm').addEventListener('click', function() {
            openConfirmModal(
                'Confirmar Reinicio',
                '¿Está seguro de que desea reiniciar el formulario? Se perderán todos los datos ingresados.',
                'Sí, reiniciar',
                'info', 
                function() {
                    document.getElementById('appointmentForm').reset();
                    document.getElementById('clientDataSection').style.display = 'none';
                    document.getElementById('appointmentDataSection').style.display = 'none';
                    document.getElementById('newClientForm').style.display = 'none';
                    document.getElementById('searchSection').style.display = 'block';
                    document.getElementById('searchClientCode').value = '';
                    document.getElementById('customService').classList.remove('show');
                    currentClientCode = null;
                    resetFormToCreateMode();
                    closeConfirmModal();
                }
            );
        });

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

        document.getElementById('appointmentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const codCliente = document.getElementById('codCliente').value;
            if (!codCliente) {
                showNotification('⚠️ Debe seleccionar o registrar un cliente primero', 'error');
                return;
            }

            const serviceSelect = document.getElementById('service');
            const customService = document.getElementById('customService');
            let serviceValue = serviceSelect.value === 'otro' 
                ? customService.value 
                : serviceSelect.options[serviceSelect.selectedIndex].text;
            
            const horaInicio = document.getElementById('time').value;
            const duration = parseFloat(document.getElementById('duration').value);
            const [horas, minutos] = horaInicio.split(':');
            const horaFinHoras = parseInt(horas) + Math.floor(duration);
            const horaFinMinutos = parseInt(minutos) + ((duration % 1) * 60);
            
            const citaData = {
                codCliente: parseInt(codCliente),
                codEmpleado: 1, 
                fechaCita: document.getElementById('date').value,
                horaInicio: horaInicio + ':00',
                horaFin: `${String(horaFinHoras).padStart(2, '0')}:${String(horaFinMinutos).padStart(2, '0')}:00`,
                notasInternas: `Paciente: ${document.getElementById('clientFullName').textContent} - Servicio: ${serviceValue} - ${document.getElementById('notes').value}`.trim()
            };

            const editingId = document.getElementById('editingAppointmentId').value;
            const method = editingId ? 'PUT' : 'POST';
            const url = editingId ? `/api/citas/${editingId}` : '/api/citas'; 
            
            if (editingId) {
                const currentAppointment = appointments.find(a => a.id == editingId);
                citaData.estadoCita = currentAppointment.estadoCita; 
            } else {
                citaData.estadoCita = 'Programada';
            }

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(citaData)
                });

                if (response.ok) {
                    const message = editingId ? '✓ Cita actualizada exitosamente' : '✓ Cita agendada exitosamente';
                    showNotification(message);
                    document.getElementById('btnResetForm').dispatchEvent(new Event('click_internal'));
                    loadAppointments();
                } else {
                    const error = await response.json();
                    showNotification(`❌ Error: ${error.error || 'No se pudo guardar la cita'}`, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('❌ Error de conexión al guardar la cita', 'error');
            }
        });
        
        document.getElementById('btnResetForm').addEventListener('click_internal', function() {
            document.getElementById('appointmentForm').reset();
            document.getElementById('clientDataSection').style.display = 'none';
            document.getElementById('appointmentDataSection').style.display = 'none';
            document.getElementById('newClientForm').style.display = 'none';
            document.getElementById('searchSection').style.display = 'block';
            document.getElementById('searchClientCode').value = '';
            document.getElementById('customService').classList.remove('show');
            currentClientCode = null;
            resetFormToCreateMode();
        });

        function showNotification(message, type = 'success') {
            const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed; bottom: 20px; right: 20px; background: ${bgColor}; color: white;
                padding: 15px 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 100000; display: flex; align-items: center; gap: 10px;
                animation: slideInRight 0.3s ease-out; max-width: 400px;
            `;
            notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 4000);
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
            const dateOnly = fechaStr.split('T')[0];
            const parts = dateOnly.split('-');
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
            return fecha.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
        }

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
                        const notasPartes = (cita.Notas_Internas || '').split(' - ');
                        const patient = (notasPartes[0] || '').replace('Paciente: ', '') || 'Sin nombre';
                        const service = (notasPartes[1] || '').replace('Servicio: ', '') || 'Sin servicio';
                        const notes = notasPartes.slice(2).join(' - ') || ''; 

                        return {
                            id: cita.Cod_Cita,
                            codCita: cita.Cod_Cita,
                            codCliente: cita.Cod_Cliente,
                            codEmpleado: cita.Cod_Empleado,
                            fechaCita: cita.Fecha_Cita.split('T')[0], 
                            fechaObj: fechaObj,
                            horaInicio: cita.Hora_Inicio,
                            horaFin: cita.Hora_Fin,
                            estadoCita: cita.Estado_Cita,
                            patient: patient,
                            service: service,
                            notes: notes,
                            time: cita.Hora_Inicio ? formatTime12Hour(cita.Hora_Inicio.substring(0, 5)) : '',
                            dateText: getDateText(fechaObj),
                            status: mapearEstado(cita.Estado_Cita)
                        };
                    });
                    
                    // ⬇️ CORRECCIÓN DE ORDENAMIENTO ⬇️
                    // Ordena de la fecha más futura a la más pasada
                    appointments.sort((a, b) => {
                        const dateA = new Date(a.fechaObj);
                        const dateB = new Date(b.fechaObj);
                        
                        const [hA, mA] = a.horaInicio ? a.horaInicio.split(':') : [0,0];
                        const [hB, mB] = b.horaInicio ? b.horaInicio.split(':') : [0,0];
                        dateA.setHours(hA, mA);
                        dateB.setHours(hB, mB);

                        // b - a para orden descendente (más nuevo primero)
                        return dateB.getTime() - dateA.getTime(); 
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
            
            // El array 'appointments' ya está ordenado globalmente
            return appointments.filter(appointment => {
                if (!appointment.fechaObj) return false;
                const fechaCita = new Date(appointment.fechaObj);
                fechaCita.setHours(0, 0, 0, 0);
                switch(currentFilter) {
                    case 'hoy': return fechaCita.getTime() === hoy.getTime();
                    case 'proximas': return fechaCita.getTime() > hoy.getTime();
                    case 'pasadas': return fechaCita.getTime() < hoy.getTime();
                    default: return true;
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

            // ⬇️ SE ELIMINA EL .sort() DE AQUÍ. AHORA SE ORDENA EN loadAppointments() ⬇️
            
            container.innerHTML = filteredAppointments.map(appointment => `
                <div class="appointment-item" data-id="${appointment.id}">
                    <div class="appointment-info">
                        <div class="appointment-time">
                            <span class="hour">${appointment.time}</span>
                            <span class="date">${appointment.dateText}</span>
                        </div>
                        <div class="appointment-details">
                            <h4><i class="fas fa-user mr-2"></i>${appointment.patient}</h4>
                            <p><i class="fas fa-briefcase-medical mr-2"></i>${appointment.service}</p>
                        </div>
                    </div>
                    <div class="appointment-controls">
                        <span class="status-badge ${getStatusClass(appointment.status)}">${getStatusText(appointment.status)}</span>
                        
                        <!-- ⬇️ CORRECCIÓN: Añadido 'dropup' para que el menú salga hacia arriba ⬇️ -->
                        <div class="btn-group dropup">
                            <button class="action-btn" data-toggle="dropdown" title="Cambiar Estado" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); changeStatus(${appointment.id}, 'Programada');">Programada</a>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); changeStatus(${appointment.id}, 'Confirmada');">Confirmada</a>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); changeStatus(${appointment.id}, 'Realizada');">Realizada</a>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); changeStatus(${appointment.id}, 'Cancelada');">Cancelada</a>
                            </div>
                        </div>

                        <button class="action-btn" onclick="sendNotification(${appointment.id})" title="Enviar Notificación">
                            <i class="fas fa-bell"></i>
                        </button>

                        <button class="action-btn" onclick="editAppointment(${appointment.id})" title="Editar Cita">
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
            const classes = {
                'programada': 'status-programada', 'confirmed': 'status-confirmed',
                'realizada': 'status-realizada', 'cancelled': 'status-cancelled'
            };
            return classes[status] || 'status-programada';
        }

        function getStatusText(status) {
            const texts = {
                'programada': 'Programada', 'confirmed': 'Confirmada',
                'realizada': 'Realizada', 'cancelled': 'Cancelada'
            };
            return texts[status] || 'Programada';
        }

        async function changeStatus(id, newStatus) {
            const appointment = appointments.find(a => a.id === id);
            if (!appointment) return;
            showNotification(`🔄 Cambiando estado a ${newStatus}...`, 'info');

            try {
                const response = await fetch(`/api/citas/estado/${id}`, { 
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json', 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ estado: newStatus })
                });

                if (response.ok) {
                    showNotification(`✓ Estado actualizado a ${newStatus}`, 'success');
                    loadAppointments();
                } else {
                    const error = await response.json();
                    showNotification(`❌ Error al cambiar estado: ${error.error || 'Error desconocido'}`, 'error');
                }
            } catch (error) {
                console.error('Error al cambiar estado:', error);
                showNotification('❌ Error de conexión al cambiar estado', 'error');
            }
        }

        async function editAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (!appointment) return;
            
            showNotification('Cargando datos de la cita...', 'info');
            window.scrollTo({ top: 0, behavior: 'smooth' });

            const clientFound = await findClient(appointment.codCliente);
            
            if (clientFound) {
                document.getElementById('editingAppointmentId').value = appointment.id;
                document.getElementById('date').value = appointment.fechaCita; // Usar la fecha YYYY-MM-DD guardada
                document.getElementById('time').value = appointment.horaInicio.substring(0, 5);
                document.getElementById('notes').value = appointment.notes;

                const serviceSelect = document.getElementById('service');
                const customServiceInput = document.getElementById('customService');
                let optionFound = Array.from(serviceSelect.options).some(option => {
                    if (option.text === appointment.service) {
                        option.selected = true;
                        return true;
                    }
                    return false;
                });

                if (!optionFound && appointment.service) {
                    serviceSelect.value = 'otro';
                    customServiceInput.value = appointment.service;
                    customServiceInput.classList.add('show');
                } else {
                    customServiceInput.classList.remove('show');
                    customServiceInput.value = '';
                }
                
                document.getElementById('formTitle').textContent = 'Editar Cita';
                document.getElementById('submitAppointmentBtn').innerHTML = '<i class="fas fa-save mr-2"></i>Actualizar Cita';
            }
        }

        function resetFormToCreateMode() {
            document.getElementById('editingAppointmentId').value = '';
            document.getElementById('formTitle').textContent = 'Agendar Nueva Cita';
            document.getElementById('submitAppointmentBtn').innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Agendar Cita';
        }

        function sendNotification(id) {
            const appointment = appointments.find(a => a.id === id);
            if (!appointment) return;
            showNotification(`🔔 Enviando recordatorio para ${appointment.patient}...`, 'info');
            // Simulación
            setTimeout(() => {
                showNotification(`✓ Recordatorio (simulado) enviado para ${appointment.patient}`, 'success');
            }, 1500);
        }

        async function deleteAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (!appointment) return;

            openConfirmModal(
                'Confirmar Eliminación',
                `¿Está seguro de que desea eliminar la cita de <strong>${appointment.patient}</strong>?`,
                'Sí, eliminar',
                'danger', 
                async function() {
                    try {
                        const response = await fetch(`/api/citas/${appointment.codCita}`, { 
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            closeConfirmModal();
                            showNotification(`✓ Cita eliminada: ${appointment.patient}`);
                            loadAppointments();
                        } else {
                            const error = await response.json();
                            throw new Error(error.error || 'No se pudo eliminar la cita');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        closeConfirmModal();
                        showNotification(`❌ Error al eliminar la cita: ${error.message}`, 'error');
                    }
                }
            );
        }

        function openConfirmModal(title, message, confirmText, type = 'danger', callback) {
            const modal = document.getElementById('confirmModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('confirmActionBtn');
            const modalIcon = document.getElementById('modalIcon');

            modalTitle.textContent = title;
            modalMessage.innerHTML = message;
            confirmBtn.textContent = confirmText;
            
            modalIcon.className = 'modal-icon';
            confirmBtn.className = 'modal-btn modal-btn-confirm';

            if (type === 'danger') {
                modalIcon.classList.add('icon-danger');
                confirmBtn.classList.add('btn-danger');
            } else if (type === 'info') {
                modalIcon.classList.add('icon-info');
                confirmBtn.classList.add('btn-info');
                modalIcon.querySelector('i').className = 'fas fa-question-circle';
            }
            
            if(type !== 'info') {
                 modalIcon.querySelector('i').className = 'fas fa-exclamation-triangle';
            }

            confirmActionCallback = callback;
            modal.classList.add('show');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('show');
            confirmActionCallback = null;
        }

        document.getElementById('confirmActionBtn').addEventListener('click', () => {
            if (typeof confirmActionCallback === 'function') {
                confirmActionCallback();
            }
        });

        const filterTabs = document.querySelectorAll('.filter-tab');
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                renderAppointments();
            });
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