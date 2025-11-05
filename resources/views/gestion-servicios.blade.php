@extends('adminlte::page')

@section('title', 'Gestión de Servicios')

@section('content_header')
    <h1 class="d-flex align-items-center">
        <div class="page-icon mr-3">
            <i class="fas fa-clipboard-list"></i>
        </div>
        Gestión de Servicios
    </h1>
    <p class="text-muted">Administración de servicios y tratamientos del spa</p>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="openModal()">
                        <i class="fas fa-plus"></i>
                        Agregar Nuevo Servicio
                    </button>
                    <button class="btn btn-secondary ml-2" onclick="goBack()">
                        <i class="fas fa-arrow-left"></i>
                        Regresar
                    </button>
                </div>
                
                <div class="filter-bar d-flex align-items-center">
                    <div class="search-box mr-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Buscar servicio..." id="searchInput">
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-secondary btn-sm mr-2" onclick="cargarServicios()">
                        <i class="fas fa-sync-alt"></i>
                        Actualizar
                    </button>
                    
                    <button class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Loader -->
            <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando servicios...</p>
            </div>

            <!-- Mensaje de error -->
            <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>

            <!-- Mensaje sin datos -->
            <div id="noDataMessage" class="alert alert-info" style="display: none;">
                <i class="fas fa-info-circle"></i> No hay servicios registrados.
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>SERVICIO</th>
                            <th>DESCRIPCIÓN</th>
                            <th>PRECIO</th>
                            <th>DURACIÓN</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="servicesTableBody">
                        <!-- Los datos se cargarán dinámicamente aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para editar/agregar servicio -->
    <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Agregar Nuevo Servicio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="serviceForm">
                    <input type="hidden" id="serviceId" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre del servicio *</label>
                            <input type="text" class="form-control" id="serviceName" name="Nombre_Tratamiento" required maxlength="50">
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" id="serviceDescription" name="Descripcion" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Precio (L) *</label>
                            <input type="number" class="form-control" id="servicePrice" name="Precio_Estandar" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Duración (minutos) *</label>
                            <input type="number" class="form-control" id="serviceDuration" name="Duracion_Estimada_Min" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="submitButton">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .page-icon {
            width: 45px;
            height: 45px;
            background: #0d6efd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .table th {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .search-box {
            width: 250px;
        }

        .service-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .price {
            color: #28a745;
            font-weight: bold;
        }

        .duration {
            color: #6c757d;
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }
        
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-box {
                width: 100%;
            }
            
            .filter-bar {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .filter-bar .form-control,
            .filter-bar .btn {
                flex: 1;
                min-width: 120px;
            }
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variable para almacenar todos los servicios
        let todosLosServicios = [];

        // ============================================
        // CARGAR SERVICIOS AL INICIAR LA PÁGINA
        // ============================================
        $(document).ready(function() {
            cargarServicios();
        });

        // ============================================
        // FUNCIÓN PARA CARGAR SERVICIOS DESDE LA API
        // ============================================
        function cargarServicios() {
            // Mostrar spinner
            $('#loadingSpinner').show();
            $('#servicesTableBody').empty();
            $('#errorMessage').hide();
            $('#noDataMessage').hide();

            $.ajax({
                url: '{{ route("api.servicios.get") }}',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loadingSpinner').hide();
                    
                    if (response.success && response.data && response.data.length > 0) {
                        todosLosServicios = response.data;
                        renderizarServicios(response.data);
                    } else {
                        $('#noDataMessage').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingSpinner').hide();
                    console.error('Error al cargar servicios:', error);
                    
                    let errorMsg = 'Error al cargar los servicios.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    $('#errorMessage').text(errorMsg).show();
                }
            });
        }

        // ============================================
        // FUNCIÓN PARA RENDERIZAR SERVICIOS EN LA TABLA
        // ============================================
        function renderizarServicios(servicios) {
            const tbody = $('#servicesTableBody');
            tbody.empty();

            if (!servicios || servicios.length === 0) {
                $('#noDataMessage').show();
                return;
            }

            servicios.forEach(function(servicio) {
                const precio = parseFloat(servicio.Precio_Estandar).toFixed(2);
                const duracion = servicio.Duracion_Estimada_Min;
                const descripcion = servicio.Descripcion || 'Sin descripción';

                const row = `
                    <tr data-id="${servicio.Cod_Tratamiento}">
                        <td class="service-name">${servicio.Nombre_Tratamiento}</td>
                        <td>${descripcion}</td>
                        <td class="price">L ${precio}</td>
                        <td class="duration"><i class="far fa-clock"></i> ${duracion} min</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-action" onclick="editService(${servicio.Cod_Tratamiento})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteService(${servicio.Cod_Tratamiento}, '${servicio.Nombre_Tratamiento}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        // ============================================
        // FUNCIÓN PARA ABRIR EL MODAL (CREAR/EDITAR)
        // ============================================
        function openModal(isEdit = false) {
            const modal = $('#serviceModal');
            
            if (!isEdit) {
                $('#modalTitle').text('Agregar Nuevo Servicio');
                $('#serviceForm')[0].reset();
                $('#serviceId').val('');
            }
            
            modal.modal('show');
        }

        // ============================================
        // FUNCIÓN PARA EDITAR SERVICIO
        // ============================================
        function editService(id) {
            // Buscar el servicio en el array
            const servicio = todosLosServicios.find(s => s.Cod_Tratamiento == id);
            
            if (!servicio) {
                Swal.fire('Error', 'No se encontró el servicio', 'error');
                return;
            }

            // Llenar el formulario con los datos del servicio
            $('#modalTitle').text('Editar Servicio');
            $('#serviceId').val(servicio.Cod_Tratamiento);
            $('#serviceName').val(servicio.Nombre_Tratamiento);
            $('#serviceDescription').val(servicio.Descripcion || '');
            $('#servicePrice').val(servicio.Precio_Estandar);
            $('#serviceDuration').val(servicio.Duracion_Estimada_Min);
            
            openModal(true);
        }

        // ============================================
        // FUNCIÓN PARA ELIMINAR SERVICIO
        // ============================================
        function deleteService(id, nombre) {
            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea eliminar el servicio "${nombre}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Eliminando...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/api/servicios/${id}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    '¡Eliminado!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    cargarServicios(); // Recargar la tabla
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Error al eliminar el servicio';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        }

        // ============================================
        // FUNCIÓN PARA REGRESAR
        // ============================================
        function goBack() {
            window.history.back();
        }

        // ============================================
        // MANEJO DEL FORMULARIO (CREAR/ACTUALIZAR)
        // ============================================
        $('#serviceForm').on('submit', function(e) {
            e.preventDefault();
            
            const serviceId = $('#serviceId').val();
            const isEdit = serviceId !== '';
            
            const formData = {
                Nombre_Tratamiento: $('#serviceName').val(),
                Descripcion: $('#serviceDescription').val(),
                Precio_Estandar: $('#servicePrice').val(),
                Duracion_Estimada_Min: $('#serviceDuration').val()
            };

            // Deshabilitar el botón de submit
            $('#submitButton').prop('disabled', true);

            // Mostrar loading
            Swal.fire({
                title: isEdit ? 'Actualizando...' : 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const url = isEdit ? `/api/servicios/${serviceId}` : '{{ route("api.servicios.store") }}';
            const method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(formData),
                success: function(response) {
                    $('#submitButton').prop('disabled', false);
                    
                    if (response.success) {
                        Swal.fire(
                            '¡Éxito!',
                            response.message,
                            'success'
                        ).then(() => {
                            $('#serviceModal').modal('hide');
                            cargarServicios(); // Recargar la tabla
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    $('#submitButton').prop('disabled', false);
                    
                    let errorMsg = 'Error al guardar el servicio';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        });

        // ============================================
        // BÚSQUEDA EN TIEMPO REAL
        // ============================================
        $('#searchInput').on('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            if (searchTerm === '') {
                renderizarServicios(todosLosServicios);
            } else {
                const serviciosFiltrados = todosLosServicios.filter(function(servicio) {
                    const nombre = servicio.Nombre_Tratamiento.toLowerCase();
                    const descripcion = (servicio.Descripcion || '').toLowerCase();
                    
                    return nombre.includes(searchTerm) || descripcion.includes(searchTerm);
                });
                
                renderizarServicios(serviciosFiltrados);
            }
        });
    </script>
@stop