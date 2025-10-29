@extends('layouts.app')

@section('content')
<style>
    /* * Nota: Mantengo tu CSS original ya que es muy bueno y temático.
    * Solo añado el ícono de Font Awesome si no está en tu layout.
    */
    body {
        background-color: #f5f5f5;
    }

    .bitacora-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-top: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .bitacora-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-left {
        display: flex;
        align-items: center;
    }

    /* Estilos generales para el contenedor (sin fondo marrón) */
    .bitacora-icon {
        width: 50px;
        height: 50px;
        background: transparent; /* O background: none; */
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    /* Estilo para el spinner de CSS puro */
    .bitacora-icon.spinner {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #8B4513; /* Brown */
        border-radius: 50%;
        width: 36px; /* Ajusta el tamaño del spinner */
        height: 36px;
        animation: spin 2s linear infinite; /* Animación de giro */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .bitacora-title h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
        color: #2d3748;
    }

    .bitacora-title p {
        margin: 0;
        font-size: 14px;
        color: #718096;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .btn-export {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        color: white;
        cursor: pointer;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-print {
        background-color: #8B4513;
    }

    .btn-print:hover {
        background-color: #6d3410;
    }

    .btn-pdf {
        background-color: #A0522D;
    }

    .btn-pdf:hover {
        background-color: #8B4513;
    }

    .controls-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 15px;
        flex-wrap: wrap;
    }

    .entries-selector {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .entries-selector select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
    }

    .search-box {
        position: relative;
        max-width: 300px;
        flex: 1;
    }

    .search-box input {
        width: 100%;
        padding-right: 40px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .search-box i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table.bitacora-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    table.bitacora-table thead th {
        background-color: #f7fafc;
        color: #4a5568;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        padding: 15px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    table.bitacora-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #2d3748;
        vertical-align: middle;
    }

    table.bitacora-table tbody tr {
        transition: all 0.2s;
    }

    table.bitacora-table tbody tr:hover {
        background-color: #f7fafc;
    }

    table.bitacora-table tbody tr.destacado {
        background-color: #fff5f5;
        border-left: 4px solid #f56565;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        color: white;
        font-size: 16px;
    }

    /* Botón Detalle - Tono Café Claro */
    .btn-update {
        background-color: #A0522D;
    }

    .btn-update:hover {
        background-color: #8B4513;
        transform: scale(1.1);
    }

    /* Botón Eliminar - Tono Café Oscuro */
    .btn-delete {
        background-color: #654321;
    }

    .btn-delete:hover {
        background-color: #4a2c16;
        transform: scale(1.1);
    }

    /* PAGINACIÓN MEJORADA */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
        padding: 20px;
        background: linear-gradient(to right, #f7fafc, #ffffff);
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .showing-entries {
        color: #4a5568;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .showing-entries .badge {
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .pagination {
        margin: 0;
        display: flex;
        gap: 5px;
    }

    .page-item {
        list-style: none;
    }

    .page-link {
        color: #4a5568;
        background-color: white;
        border: 1px solid #cbd5e0;
        padding: 8px 14px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
    }

    .page-link:hover {
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        color: white;
        border-color: #8B4513;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(139, 69, 19, 0.2);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        border-color: #8B4513;
        color: white;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
    }

    .page-item.disabled .page-link {
        color: #cbd5e0;
        background-color: #f7fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
    }

    /* Flechas de paginación */
    .page-link[rel="prev"],
    .page-link[rel="next"] {
        font-weight: 700;
        padding: 8px 12px;
    }

    .modal-content {
        border-radius: 15px;
        border: none;
    }

    .modal-header {
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }

    .detail-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .detail-label {
        font-weight: 600;
        width: 150px;
        color: #4a5568;
    }

    .detail-value {
        flex: 1;
        color: #2d3748;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .bitacora-container {
            box-shadow: none;
            padding: 0;
        }
    }

    @media (max-width: 768px) {
        .bitacora-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            justify-content: stretch;
        }

        .btn-export {
            flex: 1;
            justify-content: center;
        }

        .controls-row {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }

        .search-box {
            max-width: 100%;
        }

        .pagination-wrapper {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>

<!-- Asegúrate de tener Font Awesome cargado en tu layouts.app o aquí. -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<div class="container-fluid">
    <div class="bitacora-container">
        <!-- Header -->
        <div class="bitacora-header">
            <div class="header-left">
                <div class="bitacora-icon spinner">
                    <!-- Icono de spinner generado con CSS -->
                </div>
                <div class="bitacora-title">
                    <h2>Bitácora</h2>
                    <p>Registro de actividad Clínica Salus</p>
                </div>
            </div>
            <div class="header-actions no-print">
                <button class="btn-export btn-print" onclick="imprimirBitacora()">
                    <i class="fas fa-print"></i>
                    <span>Imprimir</span>
                </button>
                <button class="btn-export btn-pdf" onclick="exportarPDF()">
                    <i class="fas fa-file-pdf"></i>
                    <span>Exportar PDF</span>
                </button>
            </div>
        </div>

        <!-- Controles: Entries y Búsqueda -->
        <div class="controls-row no-print">
            <div class="entries-selector">
                <span>Mostrar</span>
                <select id="entriesSelect" class="form-select form-select-sm">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
                <span>entradas</span>
            </div>

            <div class="search-box">
                <input 
                    type="text" 
                    class="form-control" 
                    id="searchInput" 
                    placeholder="Buscar..."
                    value="{{ request('buscar') }}"
                >
                <i class="fas fa-search"></i>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-wrapper">
            <table class="bitacora-table">
                <thead>
                    <tr>
                        <th>TABLA</th>
                        <th>REGISTRO ELIMINADO</th>
                        <th>USUARIO REGISTRO</th>
                        <th>FECHA ELIMINACIÓN</th>
                        <th class="no-print">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $index => $registro)
                    <tr>
                        <td>{{ $registro->Modulo }}</td>
                        <td>{{ $registro->Observaciones }}</td>
                        <td>{{ $registro->Nombre_Usuario }}</td>
                        <td>{{ \Carbon\Carbon::parse($registro->Fecha_Registro)->format('d/m/Y H:i') }}</td>
                        <td class="no-print">
                            <div class="action-buttons">
                                <button 
                                    class="btn-action btn-update" 
                                    onclick="actualizarRegistro({{ $registro->Cod_Bitacora }})"
                                    title="Ver Detalle"
                                >
                                    <i class="fas fa-eye"></i> <!-- Ícono cambiado a OJO para DETALLE -->
                                </button>
                                <button 
                                    class="btn-action btn-delete" 
                                    onclick="eliminarRegistro({{ $registro->Cod_Bitacora }})"
                                    title="Eliminar"
                                >
                                    🗑
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">No se encontraron registros</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación Mejorada -->
        <div class="pagination-wrapper no-print">
            <div class="showing-entries">
                Mostrando 
                <span class="badge">{{ $registros->firstItem() ?? 0 }}</span>
                a 
                <span class="badge">{{ $registros->lastItem() ?? 0 }}</span>
                de 
                <span class="badge">{{ $registros->total() }}</span>
                entradas
            </div>
            <div>
                {{ $registros->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle Registro -->
<!-- El nombre del modal y el título se han ajustado para reflejar que es una vista de DETALLE -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Detalle del Registro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBodyDetalle">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Asumo que jQuery y SweetAlert2 están cargados. Si no lo están, debes incluirlos aquí. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Asegúrate de tener jQuery cargado, ya sea en el layout o aquí:
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
-->


<script>
    // CSRF Token para peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Cambiar cantidad de entradas
    $('#entriesSelect').on('change', function() {
        const perPage = $(this).val();
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    });

    // Búsqueda en tiempo real
    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        const searchValue = $(this).val();
        
        searchTimeout = setTimeout(function() {
            const url = new URL(window.location.href);
            if (searchValue) {
                url.searchParams.set('buscar', searchValue);
            } else {
                url.searchParams.delete('buscar');
            }
            window.location.href = url.toString();
        }, 800);
    });

    // Función para imprimir
    function imprimirBitacora() {
        window.print();
    }

    // Función para exportar a PDF (CORREGIDA: Usa la ruta de exportación y mantiene los filtros)
    function exportarPDF() {
        Swal.fire({
            title: 'Exportando a PDF...',
            text: 'Preparando el documento, por favor espere',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // 1. Obtener la URL base del controlador de exportación
        // USANDO EL HELPER DE RUTA DE LARAVEL
        let url = '{{ route("bitacora.export.pdf") }}';
        
        // 2. Crear un objeto URL con la URL base
        const exportUrl = new URL(url);

        // 3. Copiar los parámetros de búsqueda y paginación actuales
        const searchParams = new URLSearchParams(window.location.search);
        searchParams.forEach((value, key) => {
            // Se agregan todos los parámetros actuales (buscar, per_page, etc.)
            exportUrl.searchParams.append(key, value);
        });

        // 4. Crear un elemento temporal para descargar
        const link = document.createElement('a');
        link.href = exportUrl.toString(); // Usa la URL con filtros
        link.download = 'bitacora-' + new Date().toISOString().slice(0,10) + '.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Cierra el modal de carga y muestra el mensaje de éxito
        setTimeout(() => {
            Swal.close();
            Swal.fire({
                title: '¡Éxito!',
                text: 'PDF exportado correctamente',
                icon: 'success',
                timer: 2000,
                confirmButtonColor: '#8B4513'
            });
        }, 1000);
    }

    // Mostrar Detalle de registro (Función 'actualizarRegistro' renombrada para claridad en el modal)
    function actualizarRegistro(id) {
        // CORREGIDO: Usando el helper de ruta para show
        const showUrl = '{{ route("bitacora.show", ["id" => "TEMP_ID"]) }}'.replace('TEMP_ID', id);

        $.ajax({
            url: showUrl,
            method: 'GET',
            success: function(data) {
                let html = `
                    <div class="detail-row">
                        <div class="detail-label">Código:</div>
                        <div class="detail-value">${data.Cod_Bitacora}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Usuario:</div>
                        <div class="detail-value">${data.Nombre_Usuario}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Módulo:</div>
                        <div class="detail-value">${data.Modulo}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Acción:</div>
                        <div class="detail-value"><span class="badge bg-danger">${data.Accion}</span></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Observaciones:</div>
                        <div class="detail-value">${data.Observaciones}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">IP Address:</div>
                        <div class="detail-value">${data.IP_Address}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Fecha:</div>
                        <div class="detail-value">${new Date(data.Fecha_Registro).toLocaleString('es-HN')}</div>
                    </div>
                    <div class="mt-3 text-center">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                `;
                
                $('#modalBodyDetalle').html(html); // Usar el ID del modalBody corregido
                // Asegurar que el modal se muestre correctamente (usando Bootstrap 5)
                const detalleModal = new bootstrap.Modal(document.getElementById('modalDetalle'));
                detalleModal.show();
            },
            error: function(xhr) {
                console.error("Error al cargar detalle:", xhr);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar los detalles. Verifique las rutas.',
                    icon: 'error',
                    confirmButtonColor: '#8B4513'
                });
            }
        });
    }

    // Eliminar/Restaurar registro de bitácora
    function eliminarRegistro(id) {
        Swal.fire({
            title: '¿Eliminar registro?',
            text: "Esta acción eliminará físicamente el registro del log de Bitácora.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#654321',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // CORREGIDO: Usando el helper de ruta para Restaurar/Eliminar (se mapea a POST)
                const deleteUrl = '{{ route("bitacora.restaurar", ["id" => "TEMP_ID"]) }}'.replace('TEMP_ID', id);

                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: 'El registro ha sido eliminado',
                                icon: 'success',
                                timer: 2000,
                                confirmButtonColor: '#8B4513'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Error desconocido al eliminar el registro.',
                                icon: 'error',
                                confirmButtonColor: '#8B4513'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error("Error al eliminar:", xhr);
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo eliminar el registro (Error de servidor).',
                            icon: 'error',
                            confirmButtonColor: '#8B4513'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
