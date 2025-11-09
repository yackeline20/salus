@extends('adminlte::page')

@section('title', 'Sistema de Inventario')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-warehouse"></i> Sistema de Inventario</h1>
            <p class="text-muted">Gestión completa de productos, proveedores y relaciones</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="mostrarSeccion('productos')">
                <i class="fas fa-boxes"></i> Productos
            </button>
            <button class="btn btn-info" onclick="mostrarSeccion('proveedores')">
                <i class="fas fa-truck"></i> Proveedores
            </button>
            <button class="btn btn-success" onclick="mostrarSeccion('relaciones')">
                <i class="fas fa-link"></i> Relaciones
            </button>
        </div>
    </div>
@stop

@section('content')
    <!-- Alertas -->
    <div id="alertContainer"></div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="totalProductos">0</h3>
                    <p>Total Productos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="totalProveedores">0</h3>
                    <p>Proveedores Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="stockBajo">0</h3>
                    <p>Productos Stock Bajo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="proximosVencer">0</h3>
                    <p>Próximos a Vencer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN PRODUCTOS -->
    <div id="seccionProductos" class="seccion-inventario">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-boxes"></i> Gestión de Productos</h3>
                <div class="card-tools">
                    <button class="btn btn-tool btn-sm bg-white" onclick="abrirModalProducto()">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="buscarProducto" placeholder="Buscar producto...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filtroProveedor">
                            <option value="">Todos los proveedores</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filtroStock">
                            <option value="">Todo el stock</option>
                            <option value="bajo">Stock Bajo (<10)</option>
                            <option value="normal">Stock Normal</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success btn-block" onclick="exportarProductos()">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaProductos">
                        <thead class="bg-dark">
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Descripción</th>
                                <th>Precio Venta</th>
                                <th>Costo</th>
                                <th>Stock</th>
                                <th>Vencimiento</th>
                                <th>Proveedor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProductos">
                            <tr>
                                <td colspan="9" class="text-center">Cargando productos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN PROVEEDORES -->
    <div id="seccionProveedores" class="seccion-inventario" style="display:none;">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fas fa-truck"></i> Gestión de Proveedores</h3>
                <div class="card-tools">
                    <button class="btn btn-tool btn-sm bg-white" onclick="abrirModalProveedor()">
                        <i class="fas fa-plus"></i> Nuevo Proveedor
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="buscarProveedor" placeholder="Buscar proveedor...">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-success float-right" onclick="exportarProveedores()">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaProveedores">
                        <thead class="bg-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Contacto Principal</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Dirección</th>
                                <th>Productos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProveedores">
                            <tr>
                                <td colspan="8" class="text-center">Cargando proveedores...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN PRODUCTO-PROVEEDOR -->
    <div id="seccionRelaciones" class="seccion-inventario" style="display:none;">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-link"></i> Relaciones Producto-Proveedor</h3>
                <div class="card-tools">
                    <button class="btn btn-tool btn-sm bg-white" onclick="abrirModalRelacion()">
                        <i class="fas fa-plus"></i> Nueva Relación
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select class="form-control" id="filtroProductoRel">
                            <option value="">Todos los productos</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="filtroProveedorRel">
                            <option value="">Todos los proveedores</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success float-right" onclick="exportarRelaciones()">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaRelaciones">
                        <thead class="bg-dark">
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Proveedor</th>
                                <th>Precio Última Compra</th>
                                <th>Fecha Última Compra</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRelaciones">
                            <tr>
                                <td colspan="6" class="text-center">Cargando relaciones...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PRODUCTO -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="tituloModalProducto">Nuevo Producto</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formProducto">
                    <div class="modal-body">
                        <input type="hidden" id="codProducto">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombreProducto" required maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Proveedor Principal</label>
                                    <select class="form-control" id="proveedorProducto">
                                        <option value="">Seleccione proveedor...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" id="descripcionProducto" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Precio Venta <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="precioVenta" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Costo Compra <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="costoCompra" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Stock <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="cantidadStock" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Vencimiento</label>
                                    <input type="date" class="form-control" id="fechaVencimiento">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Precio Última Compra</label>
                                    <input type="number" class="form-control" id="precioUltimaCompra" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL PROVEEDOR -->
    <div class="modal fade" id="modalProveedor" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="tituloModalProveedor">Nuevo Proveedor</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formProveedor">
                    <div class="modal-body">
                        <input type="hidden" id="codProveedor">
                        <div class="form-group">
                            <label>Nombre del Proveedor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombreProveedor" required>
                        </div>
                        <div class="form-group">
                            <label>Contacto Principal</label>
                            <input type="text" class="form-control" id="contactoPrincipal">
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" class="form-control" id="telefonoProveedor">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="emailProveedor">
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <textarea class="form-control" id="direccionProveedor" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL RELACIÓN PRODUCTO-PROVEEDOR -->
    <div class="modal fade" id="modalRelacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="tituloModalRelacion">Nueva Relación Producto-Proveedor</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formRelacion">
                    <div class="modal-body">
                        <input type="hidden" id="codProdProv">
                        <div class="form-group">
                            <label>Producto <span class="text-danger">*</span></label>
                            <select class="form-control" id="productoRelacion" required>
                                <option value="">Seleccione producto...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control" id="proveedorRelacion" required>
                                <option value="">Seleccione proveedor...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Precio Última Compra <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="precioUltimaCompraRel" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha Última Compra <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fechaUltimaCompraRel" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    .small-box {
        border-radius: 10px;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    }
    .small-box > .inner {
        padding: 10px;
    }
    .small-box h3 {
        font-size: 38px;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box .icon {
        position: absolute;
        top: -10px;
        right: 10px;
        z-index: 0;
        font-size: 90px;
        color: rgba(0,0,0,0.15);
    }
    .seccion-inventario {
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .table th {
        text-align: center;
        vertical-align: middle;
    }
    .badge-stock {
        font-size: 0.9rem;
        padding: 5px 10px;
    }
    .producto-vencido {
        background-color: #ffe0e0 !important;
    }
    .producto-proximo-vencer {
        background-color: #fff3cd !important;
    }
    .producto-stock-bajo {
        background-color: #ffeaa7 !important;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// Variables globales
let productos = [];
let proveedores = [];
let relaciones = [];

// Inicialización
$(document).ready(function() {
    cargarDatos();
    configurarEventListeners();
    setInterval(actualizarEstadisticas, 30000);
});

// Configurar Event Listeners
function configurarEventListeners() {
    // Búsquedas en tiempo real
    $('#buscarProducto').on('input', filtrarProductos);
    $('#buscarProveedor').on('input', filtrarProveedores);
    
    // Filtros
    $('#filtroProveedor, #filtroStock').on('change', filtrarProductos);
    $('#filtroProductoRel, #filtroProveedorRel').on('change', filtrarRelaciones);
    
    // Formularios
    $('#formProducto').on('submit', guardarProducto);
    $('#formProveedor').on('submit', guardarProveedor);
    $('#formRelacion').on('submit', guardarRelacion);
}

// Cargar todos los datos
async function cargarDatos() {
    await Promise.all([
        cargarProductos(),
        cargarProveedores(),
        cargarRelaciones()
    ]);
    actualizarEstadisticas();
    actualizarFiltros();
}

// FUNCIONES DE CARGA DE DATOS
async function cargarProductos() {
    try {
        const response = await fetch('/api/inventario/productos');
        const data = await response.json();
        if (data.success) {
            productos = data.data;
            renderizarProductos();
        }
    } catch (error) {
        console.error('Error cargando productos:', error);
        mostrarError('Error al cargar productos');
    }
}

async function cargarProveedores() {
    try {
        const response = await fetch('/api/inventario/proveedores');
        const data = await response.json();
        if (data.success) {
            proveedores = Object.entries(data.data).map(([id, nombre]) => ({
                Cod_Proveedor: id,
                Nombre_Proveedor: nombre
            }));
            renderizarProveedores();
        }
    } catch (error) {
        console.error('Error cargando proveedores:', error);
        mostrarError('Error al cargar proveedores');
    }
}

async function cargarRelaciones() {
    try {
        const response = await fetch('/api/inventario/relaciones');
        const data = await response.json();
        if (data.success) {
            relaciones = data.data;
            renderizarRelaciones();
        }
    } catch (error) {
        console.error('Error cargando relaciones:', error);
    }
}

// FUNCIONES DE RENDERIZADO
function renderizarProductos() {
    const tbody = $('#tbodyProductos');
    tbody.empty();
    
    if (productos.length === 0) {
        tbody.html('<tr><td colspan="9" class="text-center">No hay productos registrados</td></tr>');
        return;
    }
    
    productos.forEach(producto => {
        const estado = determinarEstadoProducto(producto);
        const claseEstado = obtenerClaseEstado(estado);
        const badgeStock = producto.Cantidad_En_Stock < 10 ? 'badge-warning' : 'badge-success';
        
        tbody.append(`
            <tr class="${claseEstado}">
                <td class="text-center">${producto.Cod_Producto}</td>
                <td>${producto.Nombre_Producto}</td>
                <td>${producto.Descripcion || '-'}</td>
                <td class="text-right">$${parseFloat(producto.Precio_Venta).toFixed(2)}</td>
                <td class="text-right">$${parseFloat(producto.Costo_Compra).toFixed(2)}</td>
                <td class="text-center">
                    <span class="badge ${badgeStock} badge-stock">${producto.Cantidad_En_Stock}</span>
                </td>
                <td class="text-center">${formatearFecha(producto.Fecha_Vencimiento)}</td>
                <td>${producto.Nombre_Proveedor || '-'}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="editarProducto(${producto.Cod_Producto})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="eliminarProducto(${producto.Cod_Producto})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);
    });
}

function renderizarProveedores() {
    const tbody = $('#tbodyProveedores');
    tbody.empty();
    
    if (proveedores.length === 0) {
        tbody.html('<tr><td colspan="8" class="text-center">No hay proveedores registrados</td></tr>');
        return;
    }
    
    proveedores.forEach(proveedor => {
        const cantidadProductos = productos.filter(p => p.Nombre_Proveedor === proveedor.Nombre_Proveedor).length;
        
        tbody.append(`
            <tr>
                <td class="text-center">${proveedor.Cod_Proveedor}</td>
                <td>${proveedor.Nombre_Proveedor}</td>
                <td>${proveedor.Contacto_Principal || '-'}</td>
                <td>${proveedor.Telefono || '-'}</td>
                <td>${proveedor.Email || '-'}</td>
                <td>${proveedor.Direccion || '-'}</td>
                <td class="text-center">
                    <span class="badge badge-primary">${cantidadProductos}</span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="editarProveedor(${proveedor.Cod_Proveedor})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="eliminarProveedor(${proveedor.Cod_Proveedor})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);
    });
}

function renderizarRelaciones() {
    const tbody = $('#tbodyRelaciones');
    tbody.empty();
    
    if (relaciones.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center">No hay relaciones registradas</td></tr>');
        return;
    }
    
    relaciones.forEach(relacion => {
        tbody.append(`
            <tr>
                <td class="text-center">${relacion.Cod_Prod_Prov}</td>
                <td>${relacion.Nombre_Producto}</td>
                <td>${relacion.Nombre_Proveedor}</td>
                <td class="text-right">$${parseFloat(relacion.Precio_Ultima_Compra).toFixed(2)}</td>
                <td class="text-center">${formatearFecha(relacion.Fecha_Ultima_Compra)}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="editarRelacion(${relacion.Cod_Prod_Prov})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="eliminarRelacion(${relacion.Cod_Prod_Prov})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);
    });
}

// FUNCIONES CRUD - PRODUCTOS
function abrirModalProducto() {
    $('#modalProducto').modal('show');
    $('#formProducto')[0].reset();
    $('#codProducto').val('');
    $('#tituloModalProducto').text('Nuevo Producto');
    cargarSelectProveedores('#proveedorProducto');
}

function editarProducto(id) {
    const producto = productos.find(p => p.Cod_Producto == id);
    if (!producto) return;
    
    $('#codProducto').val(producto.Cod_Producto);
    $('#nombreProducto').val(producto.Nombre_Producto);
    $('#descripcionProducto').val(producto.Descripcion);
    $('#precioVenta').val(producto.Precio_Venta);
    $('#costoCompra').val(producto.Costo_Compra);
    $('#cantidadStock').val(producto.Cantidad_En_Stock);
    $('#fechaVencimiento').val(producto.Fecha_Vencimiento);
    $('#precioUltimaCompra').val(producto.Precio_Ultima_Compra);
    
    cargarSelectProveedores('#proveedorProducto', obtenerCodProveedor(producto.Nombre_Proveedor));
    
    $('#tituloModalProducto').text('Editar Producto');
    $('#modalProducto').modal('show');
}

async function guardarProducto(e) {
    e.preventDefault();
    
    const id = $('#codProducto').val();
    const esEdicion = !!id;
    
    const datos = {
        Nombre_Producto: $('#nombreProducto').val(),
        Descripcion: $('#descripcionProducto').val(),
        Precio_Venta: parseFloat($('#precioVenta').val()),
        Costo_Compra: parseFloat($('#costoCompra').val()),
        Cantidad_En_Stock: parseInt($('#cantidadStock').val()),
        Fecha_Vencimiento: $('#fechaVencimiento').val() || null,
        Cod_Proveedor: $('#proveedorProducto').val() || null,
        Precio_Ultima_Compra: parseFloat($('#precioUltimaCompra').val()) || parseFloat($('#costoCompra').val())
    };
    
    try {
        const url = esEdicion ? `/api/inventario/productos/${id}` : '/api/inventario/productos';
        const method = esEdicion ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(datos)
        });
        
        const result = await response.json();
        
        if (result.success) {
            $('#modalProducto').modal('hide');
            mostrarExito(esEdicion ? 'Producto actualizado correctamente' : 'Producto agregado correctamente');
            cargarDatos();
        } else {
            mostrarError(result.message || 'Error al guardar el producto');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error de conexión');
    }
}

async function eliminarProducto(id) {
    const confirmar = await Swal.fire({
        title: '¿Eliminar producto?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        allowEscapeKey: true,
        reverseButtons: true
    });
    
    if (confirmar.isConfirmed) {
        try {
            const response = await fetch(`/api/inventario/productos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                mostrarExito('Producto eliminado correctamente');
                cargarDatos();
            } else {
                mostrarError(result.message || 'Error al eliminar');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarError('Error de conexión');
        }
    }
}

// FUNCIONES CRUD - PROVEEDORES
function abrirModalProveedor() {
    $('#modalProveedor').modal('show');
    $('#formProveedor')[0].reset();
    $('#codProveedor').val('');
    $('#tituloModalProveedor').text('Nuevo Proveedor');
}

function editarProveedor(id) {
    // Aquí necesitarías cargar los detalles completos del proveedor desde una API
    mostrarInfo('Función de edición de proveedor en desarrollo');
}

async function guardarProveedor(e) {
    e.preventDefault();
    
    const id = $('#codProveedor').val();
    const esEdicion = !!id;
    
    const datos = {
        Nombre_Proveedor: $('#nombreProveedor').val(),
        Contacto_Principal: $('#contactoPrincipal').val(),
        Telefono: $('#telefonoProveedor').val(),
        Email: $('#emailProveedor').val(),
        Direccion: $('#direccionProveedor').val()
    };
    
    try {
        const url = esEdicion ? `/api/proveedor` : '/api/proveedor';
        const method = esEdicion ? 'PUT' : 'POST';
        
        if (esEdicion) {
            datos.Cod_Proveedor = id;
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(datos)
        });
        
        const result = await response.text();
        
        $('#modalProveedor').modal('hide');
        mostrarExito(result);
        cargarDatos();
        
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al guardar proveedor');
    }
}

async function eliminarProveedor(id) {
    const confirmar = await Swal.fire({
        title: '¿Eliminar proveedor?',
        text: 'Se eliminarán también las relaciones con productos',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        allowEscapeKey: true,
        reverseButtons: true
    });
    
    if (confirmar.isConfirmed) {
        try {
            const response = await fetch('/api/proveedor', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ Cod_Proveedor: id })
            });
            
            const result = await response.text();
            mostrarExito(result);
            cargarDatos();
            
        } catch (error) {
            console.error('Error:', error);
            mostrarError('Error al eliminar proveedor');
        }
    }
}

// FUNCIONES CRUD - RELACIONES
function abrirModalRelacion() {
    $('#modalRelacion').modal('show');
    $('#formRelacion')[0].reset();
    $('#codProdProv').val('');
    $('#tituloModalRelacion').text('Nueva Relación Producto-Proveedor');
    cargarSelectProductos('#productoRelacion');
    cargarSelectProveedores('#proveedorRelacion');
}

function editarRelacion(id) {
    // Implementar edición de relación
    mostrarInfo('Función de edición de relación en desarrollo');
}

async function guardarRelacion(e) {
    e.preventDefault();
    
    const id = $('#codProdProv').val();
    const esEdicion = !!id;
    
    const datos = {
        Cod_Producto: $('#productoRelacion').val(),
        Cod_Proveedor: $('#proveedorRelacion').val(),
        Precio_Ultima_Compra: parseFloat($('#precioUltimaCompraRel').val()),
        Fecha_Ultima_Compra: $('#fechaUltimaCompraRel').val()
    };
    
    try {
        const url = esEdicion ? '/api/producto_proveedor' : '/api/producto_proveedor';
        const method = esEdicion ? 'PUT' : 'POST';
        
        if (esEdicion) {
            datos.Cod_Prod_Prov = id;
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(datos)
        });
        
        const result = await response.text();
        
        $('#modalRelacion').modal('hide');
        mostrarExito(result);
        cargarDatos();
        
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al guardar relación');
    }
}

async function eliminarRelacion(id) {
    const confirmar = await Swal.fire({
        title: '¿Eliminar relación?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        allowEscapeKey: true,
        reverseButtons: true
    });
    
    if (confirmar.isConfirmed) {
        try {
            const response = await fetch('/api/producto_proveedor', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ Cod_Prod_Prov: id })
            });
            
            const result = await response.text();
            mostrarExito(result);
            cargarDatos();
            
        } catch (error) {
            console.error('Error:', error);
            mostrarError('Error al eliminar relación');
        }
    }
}

// FUNCIONES DE UTILIDAD
function mostrarSeccion(seccion) {
    $('.seccion-inventario').hide();
    switch(seccion) {
        case 'productos':
            $('#seccionProductos').show();
            break;
        case 'proveedores':
            $('#seccionProveedores').show();
            break;
        case 'relaciones':
            $('#seccionRelaciones').show();
            break;
    }
}

function actualizarEstadisticas() {
    fetch('/api/inventario/estadisticas')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#totalProductos').text(data.data.total_productos || 0);
                $('#totalProveedores').text(proveedores.length);
                $('#stockBajo').text(data.data.stock_bajo || 0);
                $('#proximosVencer').text(data.data.proximos_vencer || 0);
            }
        })
        .catch(error => console.error('Error:', error));
}

function actualizarFiltros() {
    // Actualizar filtros de proveedores
    const selectProveedor = $('#filtroProveedor');
    selectProveedor.empty().append('<option value="">Todos los proveedores</option>');
    proveedores.forEach(p => {
        selectProveedor.append(`<option value="${p.Nombre_Proveedor}">${p.Nombre_Proveedor}</option>`);
    });
    
    // Actualizar filtros de relaciones
    const selectProductoRel = $('#filtroProductoRel');
    selectProductoRel.empty().append('<option value="">Todos los productos</option>');
    productos.forEach(p => {
        selectProductoRel.append(`<option value="${p.Nombre_Producto}">${p.Nombre_Producto}</option>`);
    });
    
    const selectProveedorRel = $('#filtroProveedorRel');
    selectProveedorRel.empty().append('<option value="">Todos los proveedores</option>');
    proveedores.forEach(p => {
        selectProveedorRel.append(`<option value="${p.Nombre_Proveedor}">${p.Nombre_Proveedor}</option>`);
    });
}

function cargarSelectProveedores(selector, valorSeleccionado = '') {
    const select = $(selector);
    select.empty().append('<option value="">Seleccione proveedor...</option>');
    proveedores.forEach(p => {
        const selected = p.Cod_Proveedor == valorSeleccionado ? 'selected' : '';
        select.append(`<option value="${p.Cod_Proveedor}" ${selected}>${p.Nombre_Proveedor}</option>`);
    });
}

function cargarSelectProductos(selector, valorSeleccionado = '') {
    const select = $(selector);
    select.empty().append('<option value="">Seleccione producto...</option>');
    productos.forEach(p => {
        const selected = p.Cod_Producto == valorSeleccionado ? 'selected' : '';
        select.append(`<option value="${p.Cod_Producto}" ${selected}>${p.Nombre_Producto}</option>`);
    });
}

function obtenerCodProveedor(nombreProveedor) {
    const proveedor = proveedores.find(p => p.Nombre_Proveedor === nombreProveedor);
    return proveedor ? proveedor.Cod_Proveedor : '';
}

function determinarEstadoProducto(producto) {
    const stock = producto.Cantidad_En_Stock;
    const diasVencimiento = calcularDiasHastaVencer(producto.Fecha_Vencimiento);
    
    if (diasVencimiento < 0) return 'vencido';
    if (diasVencimiento <= 30) return 'proximo_vencer';
    if (stock < 10) return 'stock_bajo';
    return 'normal';
}

function obtenerClaseEstado(estado) {
    const clases = {
        'vencido': 'producto-vencido',
        'proximo_vencer': 'producto-proximo-vencer',
        'stock_bajo': 'producto-stock-bajo',
        'normal': ''
    };
    return clases[estado] || '';
}

function calcularDiasHastaVencer(fecha) {
    if (!fecha) return 999;
    const hoy = new Date();
    const vencimiento = new Date(fecha);
    const diff = vencimiento - hoy;
    return Math.ceil(diff / (1000 * 60 * 60 * 24));
}

function formatearFecha(fecha) {
    if (!fecha) return '-';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES');
}

// Filtros
function filtrarProductos() {
    const busqueda = $('#buscarProducto').val().toLowerCase();
    const proveedor = $('#filtroProveedor').val();
    const stock = $('#filtroStock').val();
    
    const productosFiltrados = productos.filter(p => {
        let cumple = true;
        
        if (busqueda) {
            cumple = p.Nombre_Producto.toLowerCase().includes(busqueda) ||
                    (p.Descripcion && p.Descripcion.toLowerCase().includes(busqueda));
        }
        
        if (proveedor && cumple) {
            cumple = p.Nombre_Proveedor === proveedor;
        }
        
        if (stock && cumple) {
            if (stock === 'bajo') {
                cumple = p.Cantidad_En_Stock < 10;
            } else if (stock === 'normal') {
                cumple = p.Cantidad_En_Stock >= 10;
            }
        }
        
        return cumple;
    });
    
    productos = productosFiltrados;
    renderizarProductos();
    productos = productos; // Restaurar array original
}

function filtrarProveedores() {
    const busqueda = $('#buscarProveedor').val().toLowerCase();
    // Implementar filtrado
}

function filtrarRelaciones() {
    const producto = $('#filtroProductoRel').val();
    const proveedor = $('#filtroProveedorRel').val();
    // Implementar filtrado
}

// Funciones de exportación
function exportarProductos() {
    exportarAExcel(productos, 'productos');
}

function exportarProveedores() {
    exportarAExcel(proveedores, 'proveedores');
}

function exportarRelaciones() {
    exportarAExcel(relaciones, 'relaciones');
}

function exportarAExcel(datos, nombre) {
    if (datos.length === 0) {
        mostrarError('No hay datos para exportar');
        return;
    }
    
    const ws = XLSX.utils.json_to_sheet(datos);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, nombre);
    
    const fecha = new Date().toISOString().split('T')[0];
    XLSX.writeFile(wb, `${nombre}_${fecha}.xlsx`);
    mostrarExito('Archivo exportado correctamente');
}

// Funciones de notificación
function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: mensaje,
        confirmButtonText: 'OK',
        confirmButtonColor: '#8B7355',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: true
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#d33',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: true
    });
}

function mostrarInfo(mensaje) {
    Swal.fire({
        icon: 'info',
        title: 'Información',
        text: mensaje,
        confirmButtonText: 'OK',
        confirmButtonColor: '#3085d6',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: true
    });
}

// Función especial para mensajes con más detalles
function mostrarExitoDetallado(titulo, mensaje, detalles = '') {
    return Swal.fire({
        icon: 'success',
        title: titulo,
        html: `
            <div style="text-align: left;">
                <p style="font-size: 16px; margin-bottom: 10px;">${mensaje}</p>
                ${detalles ? `<div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">
                    <small style="color: #666;">${detalles}</small>
                </div>` : ''}
            </div>
        `,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#8B7355',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    });
}

// Configuración de CSRF Token para todas las peticiones
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>

<meta name="csrf-token" content="{{ csrf_token() }}">
@stop