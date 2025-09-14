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
                    
                    <select class="form-control form-control-sm mr-2" id="categoryFilter" style="width: 150px;">
                        <option value="">Todas las categorías</option>
                        <option value="facial">Tratamientos Faciales</option>
                        <option value="aesthetic">Servicios Estéticos</option>
                        <option value="natural">Terapias Naturales</option>
                        <option value="medical">Medicina Estética</option>
                    </select>
                    
                    <select class="form-control form-control-sm mr-2" style="width: 120px;">
                        <option value="">Todos los estados</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                    
                    <button class="btn btn-outline-secondary btn-sm mr-2">
                        <i class="fas fa-filter"></i>
                        Filtrar
                    </button>
                    
                    <button class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>SERVICIO</th>
                            <th>CATEGORÍA</th>
                            <th>PRECIO</th>
                            <th>DURACIÓN</th>
                            <th>ESTADO</th>
                            <th>ÚLTIMA ACTUALIZACIÓN</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="servicesTableBody">
                        <tr>
                            <td class="service-name font-weight-bold">Mesoterapia capilar</td>
                            <td><span class="badge bg-orange">Facial</span></td>
                            <td class="price text-success font-weight-bold">L 2,500.00</td>
                            <td class="duration text-muted"><i class="far fa-clock"></i> 60 min</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>2025-06-25</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editService('Mesoterapia capilar')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteService('Mesoterapia capilar')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="service-name font-weight-bold">Aclaramiento facial</td>
                            <td><span class="badge bg-info">Estético</span></td>
                            <td class="price text-success font-weight-bold">L 1,800.00</td>
                            <td class="duration text-muted"><i class="far fa-clock"></i> 45 min</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>2025-06-30</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editService('Aclaramiento facial')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteService('Aclaramiento facial')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="service-name font-weight-bold">Peeling Facial</td>
                            <td><span class="badge bg-orange">Facial</span></td>
                            <td class="price text-success font-weight-bold">L 2,200.00</td>
                            <td class="duration text-muted"><i class="far fa-clock"></i> 50 min</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>2025-07-01</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editService('Peeling Facial')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteService('Peeling Facial')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="service-name font-weight-bold">Limpieza facial simple</td>
                            <td><span class="badge bg-orange">Facial</span></td>
                            <td class="price text-success font-weight-bold">L 800.00</td>
                            <td class="duration text-muted"><i class="far fa-clock"></i> 30 min</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>2025-06-28</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editService('Limpieza facial simple')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteService('Limpieza facial simple')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="service-name font-weight-bold">Masaje relajante</td>
                            <td><span class="badge bg-success">Natural</span></td>
                            <td class="price text-success font-weight-bold">L 1,500.00</td>
                            <td class="duration text-muted"><i class="far fa-clock"></i> 90 min</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>2025-07-02</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editService('Masaje relajante')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteService('Masaje relajante')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="service-name font-weight-bold">Botox</td>
                            <td><span class="badge bg-danger">Médico</span></td>
                            <td class="price text-success font-weight-bold">L 5,000.00</td>
                            <td class="duration text-muted"><i class="far fa-clock"></i> 30 min</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>2025-06-20</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editService('Botox')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteService('Botox')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="service-name font-weight-bold">Depilación láser</td>
                            <td><span class="badge bg-info">Estético</span></td>
                            <td class="price text-success font-weight-bold">L 3,000.00</td>
                            <td class="duration text-muted"><i class="far fa-clock"></i> 40 min</td>
                            <td><span class="badge bg-danger">Inactivo</span></td>
                            <td>2025-05-15</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editService('Depilación láser')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteService('Depilación láser')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
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
                    <h5 class="modal-title" id="modalTitle">Editar Servicio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="serviceForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre del servicio</label>
                            <input type="text" class="form-control" id="serviceName" required>
                        </div>
                        <div class="form-group">
                            <label>Categoría</label>
                            <select class="form-control" id="serviceCategory" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="facial">Tratamientos Faciales</option>
                                <option value="aesthetic">Servicios Estéticos</option>
                                <option value="natural">Terapias Naturales</option>
                                <option value="medical">Medicina Estética</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Precio (L)</label>
                            <input type="number" class="form-control" id="servicePrice" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Duración (minutos)</label>
                            <input type="number" class="form-control" id="serviceDuration" required>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" id="serviceDescription" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Estado</label>
                            <select class="form-control" id="serviceStatus">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        
        .bg-orange {
            background-color: #f0ad7e !important;
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
    </style>
@stop

@section('js')
    <script>
        // Función para abrir el modal
        function openModal(isEdit = false) {
            const modal = $('#serviceModal');
            
            if (!isEdit) {
                $('#modalTitle').text('Agregar Nuevo Servicio');
                $('#serviceForm')[0].reset();
            }
            
            modal.modal('show');
        }

        // Función para editar servicio
        function editService(serviceName) {
            openModal(true);
            $('#modalTitle').text('Editar Servicio');
            $('#serviceName').val(serviceName);

            // Simulación de datos
            if (serviceName === 'Mesoterapia capilar') {
                $('#serviceCategory').val('facial');
                $('#servicePrice').val('2500');
                $('#serviceDuration').val('60');
                $('#serviceDescription').val('Tratamiento para fortalecimiento y regeneración capilar, vitaminas y nutrientes');
                $('#serviceStatus').val('active');
            }
        }

        // Función para eliminar servicio
        function deleteService(serviceName) {
            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea eliminar el servicio "${serviceName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        '¡Eliminado!',
                        `El servicio "${serviceName}" ha sido eliminado.`,
                        'success'
                    ).then(() => {
                        // Aquí iría la lógica AJAX para eliminar
                        location.reload();
                    });
                }
            });
        }

        // Función para regresar
        function goBack() {
            window.history.back();
        }

        // Manejo del formulario
        $('#serviceForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: $('#serviceName').val(),
                category: $('#serviceCategory').val(),
                price: $('#servicePrice').val(),
                duration: $('#serviceDuration').val(),
                description: $('#serviceDescription').val(),
                status: $('#serviceStatus').val()
            };

            console.log('Datos del servicio:', formData);
            
            Swal.fire(
                '¡Guardado!',
                'Servicio guardado correctamente',
                'success'
            ).then(() => {
                $('#serviceModal').modal('hide');
                location.reload();
            });
        });

        // Búsqueda en tiempo real
        $('#searchInput').on('input', function() {
            const searchTerm = this.value.toLowerCase();
            $('#servicesTableBody tr').each(function() {
                const serviceName = $(this).find('.service-name').text().toLowerCase();
                const category = $(this).find('.badge').text().toLowerCase();
                
                if (serviceName.includes(searchTerm) || category.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Filtro por categoría
        $('#categoryFilter').on('change', function() {
            const filterValue = this.value;
            $('#servicesTableBody tr').each(function() {
                if (!filterValue) {
                    $(this).show();
                } else {
                    const badgeClass = $(this).find('.badge').attr('class');
                    const hasCategory = 
                        (filterValue === 'facial' && badgeClass.includes('bg-orange')) ||
                        (filterValue === 'aesthetic' && badgeClass.includes('bg-info')) ||
                        (filterValue === 'natural' && badgeClass.includes('bg-success')) ||
                        (filterValue === 'medical' && badgeClass.includes('bg-danger'));
                    
                    $(this).toggle(hasCategory);
                }
            });
        });
    </script>
@stop