<!-- resources/views/inventario.blade.php -->

@extends('adminlte::page')

@section('title', 'Inventario')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-boxes text-primary"></i> Inventario</h1>
            <p class="text-muted mb-0">Gestión de productos y suministros médicos</p>
        </div>
        <button class="btn btn-primary">
            <i class="fas fa-plus"></i> Agregar Nuevo Producto
        </button>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <!-- Filtros y búsqueda -->
            <div class="p-3 border-bottom bg-light">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Buscar producto...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control">
                            <option value="">Todas las categorías</option>
                            <option value="peeling">Peeling</option>
                            <option value="depilacion">Depilación</option>
                            <option value="anestesia">Anestesia</option>
                            <option value="radiofrecuencia">Radiofrecuencia</option>
                            <option value="limpieza-facial">Limpieza facial</option>
                            <option value="post-tratamiento">Post-tratamiento</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control">
                            <option value="">Todos los proveedores</option>
                            <option value="cosmeceutlab">CosmeceutLab</option>
                            <option value="estetikpro">EstetikPro</option>
                            <option value="medicare">MediCare Plus</option>
                            <option value="bioskin">BioSkin Health</option>
                            <option value="dermskin">DermSkin S.A</option>
                            <option value="naturaderm">NaturaDerm</option>
                            <option value="purederma">PureDerma</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-success btn-block">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de inventario -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 200px;">PRODUCTO</th>
                            <th style="width: 120px;">CATEGORÍA</th>
                            <th style="width: 80px;">PRECIO UNITARIO</th>
                            <th style="width: 60px;">STOCK ACTUAL</th>
                            <th style="width: 60px;">STOCK MÍNIMO</th>
                            <th style="width: 100px;">FECHA DE INGRESO</th>
                            <th style="width: 100px;">FECHA DE VENCIMIENTO</th>
                            <th style="width: 120px;">PROVEEDOR</th>
                            <th style="width: 80px;">LOTE</th>
                            <th style="width: 80px;">USO POR CITA</th>
                            <th style="width: 150px;">OBSERVACIONES</th>
                            <th style="width: 80px;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold">Ácido glicólico 10%</td>
                            <td><span class="badge badge-info">Peeling</span></td>
                            <td class="text-success font-weight-bold">$25.00</td>
                            <td><span class="badge badge-warning">8</span></td>
                            <td>3</td>
                            <td class="text-muted">2025-06-25</td>
                            <td class="text-muted">2026-06-25</td>
                            <td>CosmeceutLab</td>
                            <td class="text-muted">282345</td>
                            <td class="text-muted">2 ml</td>
                            <td class="text-muted">Usar con moderación</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Cera caliente 1kg</td>
                            <td><span class="badge badge-secondary">Depilación</span></td>
                            <td class="text-success font-weight-bold">$20.00</td>
                            <td><span class="badge badge-warning">12</span></td>
                            <td>5</td>
                            <td class="text-muted">2025-07-01</td>
                            <td class="text-muted">2026-01-01</td>
                            <td>EstetikPro</td>
                            <td class="text-muted">282346</td>
                            <td class="text-muted">100 g</td>
                            <td class="text-muted">-</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Crema anestésica tópica</td>
                            <td><span class="badge badge-danger">Anestesia</span></td>
                            <td class="text-success font-weight-bold">$35.00</td>
                            <td><span class="badge badge-warning">6</span></td>
                            <td>3</td>
                            <td class="text-muted">2025-06-30</td>
                            <td class="text-muted">2026-06-30</td>
                            <td>MediCare Plus</td>
                            <td class="text-muted">282346</td>
                            <td class="text-muted">5 ml</td>
                            <td class="text-muted">Aplicar 20 min antes</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Gel conductor RF</td>
                            <td><span class="badge badge-warning">Radiofrecuencia</span></td>
                            <td class="text-success font-weight-bold">$10.00</td>
                            <td><span class="badge badge-success">18</span></td>
                            <td>6</td>
                            <td class="text-muted">2025-07-15</td>
                            <td class="text-muted">2026-07-15</td>
                            <td>BioSkin Health</td>
                            <td class="text-muted">282348</td>
                            <td class="text-muted">10 ml</td>
                            <td class="text-muted">-</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Gel limpiador facial</td>
                            <td><span class="badge badge-primary">Limpieza facial</span></td>
                            <td class="text-success font-weight-bold">$15.00</td>
                            <td><span class="badge badge-success">30</span></td>
                            <td>10</td>
                            <td class="text-muted">2025-07-10</td>
                            <td class="text-muted">2026-07-10</td>
                            <td>DermSkin S.A</td>
                            <td class="text-muted">282347</td>
                            <td class="text-muted">5 ml</td>
                            <td class="text-muted">Recordar agitar antes de usar</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Mascarilla calmante</td>
                            <td><span class="badge badge-success">Post-tratamiento</span></td>
                            <td class="text-success font-weight-bold">$8.00</td>
                            <td><span class="badge badge-success">40</span></td>
                            <td>15</td>
                            <td class="text-muted">2025-07-18</td>
                            <td class="text-muted">2026-01-18</td>
                            <td>NaturaDerm</td>
                            <td class="text-muted">282348</td>
                            <td class="text-muted">1 unidad</td>
                            <td class="text-muted">Ideal para pieles sensibles</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Protector solar SPF 50</td>
                            <td><span class="badge badge-success">Post-tratamiento</span></td>
                            <td class="text-success font-weight-bold">$18.00</td>
                            <td><span class="badge badge-success">25</span></td>
                            <td>8</td>
                            <td class="text-muted">2025-07-08</td>
                            <td class="text-danger font-weight-bold">2027-07-08</td>
                            <td>DermSkin S.A</td>
                            <td class="text-muted">282347</td>
                            <td class="text-muted">3 ml</td>
                            <td class="text-muted">Aplicar post-tratamiento</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Suero ácido hialurónico</td>
                            <td><span class="badge badge-success">Post-tratamiento</span></td>
                            <td class="text-success font-weight-bold">$12.00</td>
                            <td><span class="badge badge-success">20</span></td>
                            <td>5</td>
                            <td class="text-muted">2025-07-12</td>
                            <td class="text-muted">2026-07-12</td>
                            <td>PureDerma</td>
                            <td class="text-muted">282347</td>
                            <td class="text-muted">2 ml</td>
                            <td class="text-muted">Mantener refrigerado</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Paginación -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Mostrando 8 de 8 productos
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Anterior</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Resumen de estadísticas -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Productos</span>
                    <span class="info-box-number">8</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stock Bajo</span>
                    <span class="info-box-number">3</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valor Total</span>
                    <span class="info-box-number">$3,267</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Próximos a Vencer</span>
                    <span class="info-box-number">1</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table th {
            background-color: #343a40;
            color: white;
            font-size: 0.85rem;
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            border: none;
        }
        
        .table td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.4em 0.6em;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
        
        .info-box {
            border-radius: 0.5rem;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
        }
        
        .thead-dark th {
            background-color: #495057 !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            $('[title]').tooltip();
            
            // Funcionalidad de búsqueda en tiempo real
            $('input[placeholder="Buscar producto..."]').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            
            // Filtros
            $('select').on('change', function() {
                // Aquí puedes agregar la lógica de filtrado
                console.log('Filtro aplicado:', $(this).val());
            });
        });
    </script>
@stop