@extends('adminlte::page')

@section('title', 'Gestión de Facturas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-invoice-dollar text-info"></i> Gestión de Facturas
        </h1>
        {{-- Botón rápido para crear una factura. Esta es la ruta que corregiremos después. --}}
        <a href="{{ route('factura.create') }}" class="btn btn-info btn-lg shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i> Crear Nueva Factura
        </a>
    </div>
@stop

@section('content')

{{-- Tarjeta de Bienvenida y Resumen --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-info shadow-lg border-0" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 text-bold">Panel de Facturación</h2>
                        <p class="mb-0">Administre y supervise todas las transacciones de su clínica estética.</p>
                    </div>
                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Acciones principales (Tarjetas de navegación/funcionalidad) --}}
<div class="row mt-4 mb-4">
    <div class="col-md-3 mb-3">
        {{-- Enlace principal: Crear Nueva Factura --}}
        <a href="{{ route('factura.create') }}" class="text-decoration-none h-100 d-block">
            <div class="card border-primary border-left-lg shadow-sm h-100 hover-card">
                <div class="card-body text-center">
                    <i class="fas fa-plus-circle text-primary mb-3" style="font-size: 2rem;"></i>
                    <h5 class="card-title text-dark font-weight-bold">Nueva Factura</h5>
                    <p class="card-text text-muted small">Generar una nueva venta.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-info border-left-lg shadow-sm h-100 hover-card">
            <div class="card-body text-center">
                <i class="fas fa-search text-info mb-3" style="font-size: 2rem;"></i>
                <h5 class="card-title font-weight-bold">Buscar Factura</h5>
                <p class="card-text text-muted small">Encontrar facturas específicas.</p>
                <button class="btn btn-sm btn-info">Buscar</button>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-danger border-left-lg shadow-sm h-100 hover-card">
            <div class="card-body text-center">
                <i class="fas fa-file-pdf text-danger mb-3" style="font-size: 2rem;"></i>
                <h5 class="card-title font-weight-bold">Generar Reporte</h5>
                <p class="card-text text-muted small">Exportar datos de facturación.</p>
                <button class="btn btn-sm btn-danger">Exportar</button>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-secondary border-left-lg shadow-sm h-100 hover-card">
            <div class="card-body text-center">
                <i class="fas fa-calculator text-secondary mb-3" style="font-size: 2rem;"></i>
                <h5 class="card-title font-weight-bold">Métricas</h5>
                <p class="card-text text-muted small">Ver estadísticas rápidas.</p>
                <button class="btn btn-sm btn-secondary">Ver Dashboard</button>
            </div>
        </div>
    </div>
</div>

{{-- Lista de facturas recientes (Tabla de datos) --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-white border-bottom-0">
                <h4 class="mb-0 text-bold"><i class="fas fa-list-alt mr-2 text-primary"></i> Últimas Facturas Registradas</h4>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" style="width:100%">
                        <thead class="table-info">
                            <tr>
                                <th>No. Factura</th>
                                <th>Cod. Cliente</th>
                                <th>Fecha</th>
                                <th>Método de Pago</th>
                                <th>Desc.</th>
                                <th>Monto Final</th>
                                <th>Estado de Pago</th> {{-- Título de columna ajustado --}}
                                <th style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="facturas-body">
                            {{-- Contenido inyectado por JavaScript --}}
                            <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando facturas...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Confirmación de Eliminación --}}
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content border-danger border-left-lg shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i> Confirmar Eliminación</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p>¿Seguro que desea eliminar la Factura No. <strong><span id="factura-a-eliminar-id" class="text-danger font-weight-bold"></span></strong>?</p>
                <p class="text-danger small font-weight-bold">Esta acción es irreversible.</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-delete"><i class="fas fa-trash-alt mr-1"></i> Eliminar</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
    <style>
        .hover-card:hover {
            transform: translateY(-4px);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15) !important;
        }
        .table-info th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .bg-info {
            /* Mismo gradiente que el card de bienvenida */
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%) !important;
        }
        .border-left-lg {
            border-left: 5px solid !important;
        }
    </style>
@stop

@section('js')
    <script>
        // URL base de tu API Node.js (Asegúrate de que el puerto 3000 sea accesible)
        const API_URL = 'http://localhost:3000';
        let facturaToDeleteId = null; // Variable global para almacenar el ID a eliminar

        // Función de ayuda para mostrar alertas de AdminLTE
        function showAdminlteAlert(message, type = 'success') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 10px; right: 10px; z-index: 1050; min-width: 300px;">
                    <i class="icon fas fa-check mr-2"></i>${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            // Añadir al body
            $('body').append(alertHtml);
            // Autocerrar después de 5 segundos
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        // ----------------------------------------------------
        // 1. FUNCIÓN PARA OBTENER Y RENDERIZAR LAS FACTURAS
        // ----------------------------------------------------
        async function fetchAndRenderFacturas() {
            const tableBody = document.getElementById('facturas-body');
            // Muestra un indicador de carga
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando facturas...</td></tr>';

            try {
                // Llamada a la API GET /facturas
                const response = await fetch(`${API_URL}/facturas`);
                if (!response.ok) {
                    throw new Error(`Error en la API: ${response.statusText}`);
                }
                const facturas = await response.json();

                if (!facturas || facturas.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-2"></i> No hay facturas recientes para mostrar.</td></tr>';
                    return;
                }

                let htmlContent = '';
                facturas.forEach(factura => {
                    // Mapeo de propiedades de tu API (basado en la estructura de tu tabla)
                    const id = factura.Cod_Factura;
                    const codCliente = factura.Cod_Cliente;
                    const rawDate = factura.Fecha_Factura ? new Date(factura.Fecha_Factura) : new Date();
                    const fecha = rawDate.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });

                    // Total_Factura como Monto Final
                    const total = factura.Total_Factura ? parseFloat(factura.Total_Factura).toFixed(2) : '0.00';

                    // Propiedades OBTENIDAS del JSON: Metodo_Pago, Estado_Pago, Descuento_Aplicado
                    const estado = factura.Estado_Pago || 'Pendiente';
                    const metodoPago = factura.Metodo_Pago || 'No especificado';
                    // Descuento. Se asume que viene como número en su JSON.
                    const descuento = factura.Descuento_Aplicado ? parseFloat(factura.Descuento_Aplicado).toFixed(2) : '0.00';


                    // Lógica para el badge según el Estado_Pago
                    let badge_class = 'secondary';
                    if (estado === 'Pagada') {
                        badge_class = 'success';
                    } else if (estado === 'Pendiente') {
                        badge_class = 'warning';
                    } else if (estado === 'Cancelada' || estado === 'Anulada') {
                        badge_class = 'danger';
                    }

                    // Fila de la tabla (8 columnas)
                    htmlContent += `
                        <tr class="align-middle">
                            <td><strong>#F-${String(id).padStart(4, '0')}</strong></td>
                            <td>Cod: ${codCliente}</td>
                            <td>${fecha}</td>
                            <td>${metodoPago}</td>
                            <td>$${descuento}</td>
                            <td><strong class="text-success">$${total}</strong></td>
                            <td>
                                <span class="badge badge-${badge_class} font-weight-bold">${estado}</span>
                            </td>
                            <td>
                                {{-- Los botones de edición y vista DEBEN usar rutas de Laravel, aquí simuladas --}}
                                <a href="{{ url('factura') }}/${id}/show" class="btn btn-sm btn-outline-info mr-1" title="Ver Factura">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('factura') }}/${id}/edit" class="btn btn-sm btn-outline-secondary mr-1" title="Editar Factura">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Botón de eliminación llama a la función JS --}}
                                <button onclick="prepareDelete(${id})" class="btn btn-sm btn-outline-danger" title="Eliminar Factura">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = htmlContent;

            } catch (error) {
                console.error("Error al obtener facturas:", error);
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i> No se pudo conectar con la API en `http://localhost:3000`. Verifique el servidor.</td></tr>';
            }
        }

        // ----------------------------------------------------
        // 2. FUNCIÓN PARA INICIAR EL PROCESO DE ELIMINACIÓN
        // ----------------------------------------------------
        window.prepareDelete = (facturaId) => {
            facturaToDeleteId = facturaId;
            document.getElementById('factura-a-eliminar-id').textContent = `#F-${String(facturaId).padStart(4, '0')}`;
            $('#deleteConfirmationModal').modal('show');
        };

        // ----------------------------------------------------
        // 3. FUNCIÓN PARA CONFIRMAR Y EJECUTAR LA ELIMINACIÓN
        // ----------------------------------------------------
        document.getElementById('btn-confirmar-delete').addEventListener('click', async () => {
            const id = facturaToDeleteId;
            if (!id) return;

            // Deshabilita el botón mientras se procesa
            const deleteButton = document.getElementById('btn-confirmar-delete');
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Eliminando...';

            $('#deleteConfirmationModal').modal('hide');

            try {
                // Llamada a la API DELETE /facturas?cod={id}
                const response = await fetch(`${API_URL}/facturas?cod=${id}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' }
                });

                const result = await response.json();

                if (response.ok) {
                    showAdminlteAlert(`Factura #${String(id).padStart(4, '0')} eliminada correctamente.`, 'success');
                    fetchAndRenderFacturas(); // Recargar los datos de la tabla
                } else {
                    showAdminlteAlert(`Error (${response.status}) al eliminar: ${result.error || 'Problema con la API.'}`, 'danger');
                    console.error('Error de API:', result);
                }

            } catch (error) {
                console.error('Error de red al eliminar factura:', error);
                showAdminlteAlert('Error de conexión con el servidor API. Intente de nuevo.', 'danger');
            } finally {
                // Habilitar el botón nuevamente
                deleteButton.disabled = false;
                deleteButton.innerHTML = '<i class="fas fa-trash-alt mr-1"></i> Eliminar';
            }
        });

        // ----------------------------------------------------
        // 4. INICIALIZACIÓN
        // ----------------------------------------------------
        $(document).ready(function() {
            fetchAndRenderFacturas();
        });

    </script>
@stop
