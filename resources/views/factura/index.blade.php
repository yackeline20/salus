@extends('adminlte::page')

@section('title', 'Gestión de Facturas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-invoice-dollar text-info"></i> Gestión de Facturas
        </h1>
        {{-- ¡BOTÓN CORRECTO! Usa la ruta de vista de Laravel --}}
        <a href="{{ route('factura.create') }}" class="btn btn-info btn-lg shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i> Crear Nueva Factura
        </a>
    </div>
@stop

@section('content')

{{-- Mensajes de Sesión (Éxito/Error de las operaciones de Laravel) --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Tarjeta de Bienvenida y Resumen --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-info shadow-lg border-0 panel-facturacion-card"> {{-- Agregamos clase personalizada aquí --}}
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

{{-- Lista de facturas recientes (Tabla de datos) --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-white border-bottom-0 pb-1"> {{-- Ajuste de padding --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0 text-bold"><i class="fas fa-list-alt mr-2 text-primary"></i> Últimas Facturas Registradas</h4>

                    {{-- 🔎 CAMPO DE BÚSQUEDA POR CÓDIGO DE CLIENTE --}}
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por Cód. Cliente...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm" style="width:100%">
                        <thead class="table-info">
                            <tr>
                                <th style="width: 10%;">No. Factura</th>
                                <th style="width: 10%;">Cod. Cliente</th>
                                <th style="width: 10%;">Fecha</th>
                                <th style="width: 12%;">Método de Pago</th>
                                <th style="width: 8%;">Desc.</th>
                                <th style="width: 12%;">Monto Final</th>
                                <th style="width: 12%;">Estado de Pago</th>
                                <th style="width: 16%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="facturas-body">
                            <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando facturas...</td></tr>
                        </tbody>
                    </table>
                </div>
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

        /* ESTILOS PERSONALIZADOS PARA REDUCIR TAMAÑOS */

        /* Ajuste para el Panel de Facturación */
        .panel-facturacion-card .card-body {
            padding: 1.25rem;
        }
        .panel-facturacion-card h2 {
            font-size: 1.8rem;
        }
        .panel-facturacion-card p {
            font-size: 0.9rem;
        }
        .panel-facturacion-card .fa-3x {
            font-size: 2.5em !important;
        }

        /* AJUSTE CLAVE PARA HACER LA TABLA MÁS COMPACTA (como la imagen 2) */
        .table-sm th,
        .table-sm td {
            padding: 0.4rem !important;
            vertical-align: middle;
            font-size: 0.85rem;
        }

        .table-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }

        .table-sm .badge {
            font-size: 0.75rem;
            padding: 0.3em 0.5em;
        }

        /* Ajuste para que los botones de acción se vean mejor */
        .table-sm td.d-flex {
            justify-content: flex-start;
            flex-wrap: nowrap;
        }
    </style>
@stop

@section('js')
    {{-- 💡 AGREGAR CDN DE SWEETALERT2 PARA MENSAJES ESTÉTICOS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // URL base de tu API Node.js
        const API_URL = 'http://localhost:3000';
        let allFacturas = [];

        // Rutas de Laravel (generadas en Blade)
        const facturaReciboUrl = "{{ route('factura.recibo', ['factura' => ':id_placeholder']) }}";
        const facturaEditUrl = "{{ route('factura.edit', ['factura' => ':id_placeholder']) }}";
        const facturaDestroyUrl = "{{ route('factura.destroy', ['factura' => ':id_placeholder']) }}";


        // Función de ayuda para mostrar alertas de AdminLTE (ya no se usa para éxito de eliminación)
        function showAdminlteAlert(message, type = 'success') {
            const icon = type === 'danger' ? 'fas fa-exclamation-triangle' : 'fas fa-check';
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 10px; right: 10px; z-index: 1050; min-width: 300px;">
                    <i class="icon ${icon} mr-2"></i>${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            $('body').append(alertHtml);
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        // ----------------------------------------------------
        // 1. FUNCIÓN PARA OBTENER Y ALMACENAR LAS FACTURAS
        // ----------------------------------------------------
        async function loadAllFacturas() {
            const tableBody = document.getElementById('facturas-body');
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando facturas...</td></tr>';

            try {
                const response = await fetch(`${API_URL}/facturas`);
                if (!response.ok) {
                    throw new Error(`Error en la API: ${response.statusText}`);
                }
                allFacturas = await response.json();

                // Una vez cargadas, renderizamos la lista completa
                renderFacturas(allFacturas);

            } catch (error) {
                console.error("Error al obtener facturas:", error);
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i> **Error Crítico:** No se pudo conectar con la API en `http://localhost:3000`. Verifique que el servidor de Node.js esté funcionando.</td></tr>';
            }
        }

        // ----------------------------------------------------
        // 2. FUNCIÓN PARA RENDERIZAR UN ARRAY DE FACTURAS
        // ----------------------------------------------------
        function renderFacturas(facturasToRender) {
            const tableBody = document.getElementById('facturas-body');

            if (!facturasToRender || facturasToRender.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-info-circle mr-2"></i> No hay facturas que coincidan con la búsqueda.</td></tr>';
                return;
            }

            let htmlContent = '';
            facturasToRender.forEach(factura => {
                const id = factura.Cod_Factura;
                const codCliente = factura.Cod_Cliente;
                const rawDate = factura.Fecha_Factura ? new Date(factura.Fecha_Factura) : new Date();
                const fecha = rawDate.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });

                const total = factura.Total_Factura ? parseFloat(factura.Total_Factura).toFixed(2) : '0.00';
                const estado = factura.Estado_Pago || 'Pendiente';
                const metodoPago = factura.Metodo_Pago || 'No especificado';
                const descuento = factura.Descuento_Aplicado ? parseFloat(factura.Descuento_Aplicado).toFixed(2) : '0.00';

                const reciboUrl = facturaReciboUrl.replace(':id_placeholder', id);
                const editUrl = facturaEditUrl.replace(':id_placeholder', id);
                const destroyUrl = facturaDestroyUrl.replace(':id_placeholder', id);

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
                    <tr class="align-middle" id="factura-row-${id}">
                        <td><strong>#F-${String(id).padStart(4, '0')}</strong></td>
                        <td>Cod: ${codCliente}</td>
                        <td>${fecha}</td>
                        <td>${metodoPago}</td>
                        <td>$${descuento}</td>
                        <td><strong class="text-success">$${total}</strong></td>
                        <td>
                            <span class="badge badge-${badge_class} font-weight-bold">${estado}</span>
                        </td>
                        <td class="d-flex">
                            {{-- ENLACE DE VISTA (Recibo) --}}
                            <a href="${reciboUrl}" class="btn btn-sm btn-outline-info mr-1" title="Ver Recibo">
                                <i class="fas fa-eye"></i>
                            </a>
                            {{-- ENLACE DE EDICIÓN --}}
                            <a href="${editUrl}" class="btn btn-sm btn-outline-secondary mr-1" title="Editar Factura">
                                <i class="fas fa-edit"></i>
                            </a>
                            {{-- 🚨 BOTÓN MODIFICADO PARA USAR SWEETALERT2 🚨 --}}
                            <form id="delete-form-${id}" action="${destroyUrl}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Factura" onclick="confirmDeleteFactura(${id}, '${String(id).padStart(4, '0')}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = htmlContent;
        }

        // ----------------------------------------------------
        // 3. FUNCIÓN SWEETALERT2 PARA CONFIRMACIÓN DE ELIMINACIÓN
        // ----------------------------------------------------
        function confirmDeleteFactura(facturaId, facturaCodFormatted) {
            Swal.fire({
                title: '¿Está seguro?',
                html: `¡La eliminación de la factura **#F-${facturaCodFormatted}** es **IRREVERSIBLE**! <br>Se eliminarán todos los detalles y comisiones asociadas.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class="fas fa-trash"></i> Sí, ¡Eliminar!',
                cancelButtonText: '<i class="fas fa-times-circle"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si confirma, llamamos a la función de eliminación por AJAX
                    deleteFacturaAjax(facturaId, facturaCodFormatted);
                }
            });
        }

        // ----------------------------------------------------
        // 4. FUNCIÓN AJAX PARA ELIMINAR LA FACTURA (LARAVEL)
        // ----------------------------------------------------
        async function deleteFacturaAjax(facturaId, facturaCodFormatted) {
            const form = document.getElementById(`delete-form-${facturaId}`);

            try {
                // Obtenemos el token CSRF y la URL de la acción DELETE
                const url = form.action;
                const csrfToken = form.querySelector('input[name="_token"]').value;
                const method = form.querySelector('input[name="_method"]').value; // DELETE

                // 🚨 Nota: Esta eliminación se realiza a través de la ruta DELETE de Laravel.
                // Si la lógica de eliminación también requiere una llamada a la API de Node.js,
                // debes modificar el controlador de Laravel para que primero elimine en la API.

                const response = await fetch(url, {
                    method: 'POST', // Fetch enviará un POST, Laravel lo interpretará como DELETE
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `_method=${method}`
                });

                if (response.ok || response.status === 200 || response.status === 204) {
                    // Si se elimina correctamente:

                    // 1. Mostrar mensaje de éxito (SWEETALERT2)
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: `La factura #F-${facturaCodFormatted} ha sido eliminada exitosamente.`,
                        icon: 'success',
                        timer: 2500, // Cierra automáticamente después de 2.5 segundos
                        showConfirmButton: false
                    });

                    // 2. Eliminar la fila de la tabla sin recargar la página
                    // Se asume que si la llamada a Laravel fue exitosa, la fila se puede quitar.
                    const rowToRemove = document.getElementById(`factura-row-${facturaId}`);
                    if (rowToRemove) {
                        rowToRemove.remove();
                    }

                    // 3. Opcional: Recargar los datos para refrescar la lista (si lo deseas)
                    // loadAllFacturas();

                } else {
                    // Manejo de errores de Laravel
                    const errorText = await response.text();
                    Swal.fire(
                        '¡Error!',
                        `No se pudo eliminar la factura. Respuesta del servidor: ${response.status} ${response.statusText}`,
                        'error'
                    );
                    console.error('Error al eliminar:', errorText);
                }

            } catch (error) {
                console.error('Error de red al intentar eliminar la factura:', error);
                 Swal.fire(
                    '¡Error de Conexión!',
                    'Hubo un problema de red al intentar eliminar la factura. Intente de nuevo.',
                    'error'
                );
            }
        }


        // ----------------------------------------------------
        // 5. FUNCIÓN DE FILTRO (Ejecutada al teclear)
        // ----------------------------------------------------
        function filterFacturas() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();

            if (!searchTerm) {
                // Si la caja de búsqueda está vacía, mostramos todas las facturas
                renderFacturas(allFacturas);
                return;
            }

            // Filtramos las facturas donde Cod_Cliente (convertido a string) contenga el término de búsqueda
            const filteredFacturas = allFacturas.filter(factura => {
                const codClienteString = String(factura.Cod_Cliente).toLowerCase();
                return codClienteString.includes(searchTerm);
            });

            renderFacturas(filteredFacturas);
        }

        // ----------------------------------------------------
        // 6. INICIALIZACIÓN Y EVENT LISTENERS
        // ----------------------------------------------------
        $(document).ready(function() {
            // 1. Cargamos todas las facturas al inicio
            loadAllFacturas();

            // 2. Adjuntamos el evento 'input' al campo de búsqueda
            $('#searchInput').on('input', filterFacturas);
        });

    </script>
@stop
