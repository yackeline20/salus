@extends('adminlte::page')

@section('title', 'Bitácora del Sistema')



@section('content')
<style>
    body {
        background-color: #f5f7fa;
    }

    .bitacora-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .bitacora-header {
        background: linear-gradient(135deg, #D2B48C 0%, #C19A6B 100%);
        color: white;
        padding: 20px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    /* Animación del icono de carga */
    .spinner-icon {
        animation: spin 1.5s linear infinite;
        font-size: 28px;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .header-title h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .header-title p {
        margin: 0;
        font-size: 13px;
        opacity: 0.9;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .btn-header {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-header:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-1px);
    }

    .controls-bar {
        padding: 20px 30px;
        background: #fafbfc;
        border-bottom: 1px solid #e1e4e8;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .entries-control {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #586069;
    }

    .entries-control select {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 14px;
    }

    .search-control {
        position: relative;
        width: 300px;
    }

    .search-control input {
        width: 100%;
        padding: 8px 35px 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }

    .search-control input:focus {
        outline: none;
        border-color: #C19A6B;
        box-shadow: 0 0 0 3px rgba(193, 154, 107, 0.1);
    }

    .search-control i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .table-container {
        overflow-x: auto;
    }

    .bitacora-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bitacora-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .bitacora-table thead th {
        padding: 12px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bitacora-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        color: #1f2937;
    }

    .bitacora-table tbody tr:hover {
        background: #f9fafb;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #D2B48C 0%, #C19A6B 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .user-info strong {
        display: block;
        font-size: 14px;
        color: #111827;
    }

    .user-info small {
        font-size: 12px;
        color: #6b7280;
    }

    .badge-action {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-delete {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-update {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-insert {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-export {
        background: #e0e7ff;
        color: #4338ca;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 14px;
    }

    .btn-view {
        background: linear-gradient(135deg, #D2B48C 0%, #C19A6B 100%);
        color: white;
    }

    .btn-view:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(210, 180, 140, 0.4);
    }

    .btn-refresh {
        background: linear-gradient(135deg, #C19A6B 0%, #B8956A 100%);
        color: white;
    }

    .btn-refresh:hover {
        transform: scale(1.1) rotate(180deg);
        box-shadow: 0 4px 12px rgba(193, 154, 107, 0.4);
    }

    .btn-delete-action {
        background: linear-gradient(135deg, #A0826D 0%, #8B7355 100%);
        color: white;
    }

    .btn-delete-action:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(160, 130, 109, 0.4);
    }

    .pagination-bar {
        padding: 20px 30px;
        background: #fafbfc;
        border-top: 1px solid #e1e4e8;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination-info {
        font-size: 13px;
        color: #586069;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .pagination-info .badge {
        background: linear-gradient(135deg, #D2B48C 0%, #C19A6B 100%);
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 11px;
    }

    .pagination {
        display: flex;
        gap: 4px;
        margin: 0;
    }

    .page-item {
        list-style: none;
    }

    .page-link {
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #374151;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
    }

    .page-link:hover {
        background: #f3f4f6;
        border-color: #C19A6B;
        color: #C19A6B;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #D2B48C 0%, #C19A6B 100%);
        border-color: #C19A6B;
        color: white;
    }

    .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Panel de detalles mejorado */
    .details-panel {
        background: linear-gradient(135deg, #D2B48C 0%, #C19A6B 100%);
        padding: 15px 30px;
        color: white;
        border-radius: 0 0 12px 12px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .details-panel:hover {
        background: linear-gradient(135deg, #C19A6B 0%, #B8956A 100%);
    }

    .details-panel i {
        font-size: 18px;
    }

    .details-panel h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Modal mejorado */
    .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, #D2B48C 0%, #C19A6B 100%);
        color: white;
        border: none;
        padding: 20px 30px;
    }

    .modal-header .close {
        color: white;
        opacity: 1;
    }

    .modal-body {
        padding: 30px;
    }

    .detail-row {
        display: flex;
        padding: 15px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        width: 180px;
        font-weight: 600;
        color: #6b7280;
        font-size: 13px;
    }

    .detail-value {
        flex: 1;
        color: #111827;
        font-size: 14px;
    }

    .detail-value code {
        background: #f3f4f6;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 13px;
        color: #C19A6B;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }

    /* Botón Limpiar Llamativo */
.btn-clear-attractive {
    background: linear-gradient(135deg, #D2B48C 0%, #C19A6B 100%);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(210, 180, 140, 0.3);
}

.btn-clear-attractive:hover {
    background: linear-gradient(135deg, #C19A6B 0%, #B8956A 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(210, 180, 140, 0.5);
}

.btn-clear-attractive:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(210, 180, 140, 0.4);
}

.btn-clear-attractive i {
    font-size: 16px;
    animation: sweep 2s ease-in-out infinite;
}

/* Animación de la escoba */
@keyframes sweep {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(-15deg);
    }
    75% {
        transform: rotate(15deg);
    }
}

.btn-clear-attractive:hover i {
    animation: sweep 0.5s ease-in-out infinite;
}
</style>

<div class="container-fluid">
    <div class="bitacora-card">
        <!-- Header -->
        <div class="bitacora-header no-print">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-circle-notch spinner-icon"></i>
                </div>
                <div class="header-title">
                    <h3>Bitácora del Sistema</h3>
                    <p>Historial completo de movimientos - Clínica Salus</p>
                </div>
            </div>
            <div class="header-actions">
                <button class="btn-header" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <button class="btn-header" onclick="exportarPDF()">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
            </div>
        </div>

       <!-- Controles -->
<div class="controls-bar no-print">
    <div class="entries-control">
        <span>Mostrar</span>
        <select id="entriesSelect">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
        <span>entradas</span>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <div class="search-control">
            <input 
                type="text" 
                id="quickSearch" 
                placeholder="Buscar..."
                value="{{ request('buscar') }}"
            >
            <i class="fas fa-search"></i>
        </div>
        
        <button 
            onclick="limpiarBusqueda()" 
            class="btn-clear-attractive"
            title="Limpiar búsqueda y filtros"
        >
            <i class="fas fa-broom"></i>
            <span>Limpiar</span>
        </button>
    </div>
</div>
        <!-- Tabla -->
        <div class="table-container">
            <table class="bitacora-table">
                <thead>
                    <tr>
                        <th>USUARIO</th>
                        <th>MÓDULO</th>
                        <th>ACCIÓN</th>
                        <th>DETALLES</th>
                        <th>IP</th>
                        <th>FECHA Y HORA</th>
                        <th class="no-print">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $registro)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($registro->Nombre_Usuario, 0, 2)) }}
                                </div>
                                <div class="user-info">
                                    <strong>{{ $registro->Nombre_Usuario }}</strong>
                                    <small>ID: {{ $registro->Cod_Usuario }}</small>
                                </div>
                            </div>
                        </td>
                        <td><strong>{{ $registro->Modulo }}</strong></td>
                        <td>
                            @php
                                $accion = strtoupper($registro->Accion);
                                $badgeClass = 'badge-action ';
                                if(str_contains($accion, 'DELETE') || str_contains($accion, 'ELIMINÓ')) {
                                    $badgeClass .= 'badge-delete';
                                } elseif(str_contains($accion, 'UPDATE') || str_contains($accion, 'MODIFICÓ') || str_contains($accion, 'ACTUALIZÓ')) {
                                    $badgeClass .= 'badge-update';
                                } elseif(str_contains($accion, 'EXPORT') || str_contains($accion, 'EXPORTÓ')) {
                                    $badgeClass .= 'badge-export';
                                } else {
                                    $badgeClass .= 'badge-update';
                                }
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $registro->Accion }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($registro->Observaciones, 40) }}</td>
                        <td><code>{{ $registro->IP_Address }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($registro->Fecha_Registro)->format('d/m/Y H:i:s') }}</td>
                        <td class="no-print">
                            <div class="action-buttons">
                                <button 
                                    class="btn-action btn-view" 
                                    onclick="verDetalles({{ $registro->Cod_Bitacora }})"
                                    title="Ver Detalles"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button 
                                    class="btn-action btn-refresh" 
                                    onclick="recargarRegistro({{ $registro->Cod_Bitacora }})"
                                    title="Recargar Información"
                                >
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button 
                                    class="btn-action btn-delete-action" 
                                    onclick="eliminarRegistro({{ $registro->Cod_Bitacora }})"
                                    title="Eliminar Registro"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db;"></i>
                            <p class="mt-3" style="color: #6b7280;">No se encontraron registros</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="pagination-bar no-print">
            <div class="pagination-info">
                <span>Mostrando</span>
                <span class="badge">{{ $registros->firstItem() ?? 0 }}</span>
                <span>a</span>
                <span class="badge">{{ $registros->lastItem() ?? 0 }}</span>
                <span>de</span>
                <span class="badge">{{ $registros->total() }}</span>
                <span>entradas</span>
            </div>
            <div>
                {{ $registros->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>

        <!-- Panel de Detalles (Expandible) -->
        <div class="details-panel no-print" onclick="toggleDetailsPanel()">
            <h5>
                <i class="fas fa-info-circle"></i>
                Detalles del Registro
            </h5>
            <i class="fas fa-chevron-down" id="chevronIcon"></i>
        </div>
    </div>

    <!-- Panel expandible de detalles (oculto por defecto) -->
    <div id="detailsContent" style="display: none; margin-top: 20px;">
        <div class="bitacora-card" style="padding: 30px;">
            <h5 style="color: #C19A6B; margin-bottom: 20px; font-weight: 600;">
                <i class="fas fa-clipboard-list"></i> Información Adicional
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-label">Total de Registros:</div>
                        <div class="detail-value"><strong>{{ $registros->total() }}</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Página Actual:</div>
                        <div class="detail-value">{{ $registros->currentPage() }} de {{ $registros->lastPage() }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-label">Registros por Página:</div>
                        <div class="detail-value">{{ $registros->perPage() }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Última Actualización:</div>
                        <div class="detail-value">{{ now()->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt"></i> Detalles Completos del Registro
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBodyDetalles">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Cambiar entradas
$('#entriesSelect').on('change', function() {
    const perPage = $(this).val();
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    window.location.href = url.toString();
});

// Búsqueda rápida
let searchTimeout;
$('#quickSearch').on('keyup', function() {
    clearTimeout(searchTimeout);
    const searchValue = $(this).val();
    
    searchTimeout = setTimeout(function() {
        const url = new URL(window.location.href);
        if (searchValue) {
            url.searchParams.set('buscar', searchValue);
        } else {
            url.searchParams.delete('buscar');
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }, 800);
});

// Ver detalles
function verDetalles(id) {
    $.ajax({
        url: `/administracion/bitacora/${id}`,
        method: 'GET',
        success: function(data) {
            let html = `
                <div class="detail-row">
                    <div class="detail-label">Código de Registro:</div>
                    <div class="detail-value"><strong>${data.Cod_Bitacora}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Usuario:</div>
                    <div class="detail-value"><strong>${data.Nombre_Usuario}</strong> (ID: ${data.Cod_Usuario})</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Módulo Afectado:</div>
                    <div class="detail-value"><span class="badge badge-info">${data.Modulo}</span></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tipo de Acción:</div>
                    <div class="detail-value"><span class="badge badge-success">${data.Accion}</span></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Observaciones:</div>
                    <div class="detail-value">${data.Observaciones || 'Sin observaciones'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Dirección IP:</div>
                    <div class="detail-value"><code>${data.IP_Address}</code></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Fecha y Hora:</div>
                    <div class="detail-value"><strong>${new Date(data.Fecha_Registro).toLocaleString('es-HN', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    })}</strong></div>
                </div>
            `;
            
            $('#modalBodyDetalles').html(html);
            $('#modalDetalles').modal('show');
        },
        error: function() {
            Swal.fire({
                title: 'Error',
                text: 'No se pudieron cargar los detalles del registro',
                icon: 'error',
                confirmButtonColor: '#C19A6B'
            });
        }
    });
}

// Recargar registro (botón actualizar)
function recargarRegistro(id) {
    const btn = event.target.closest('.btn-refresh');
    btn.style.transform = 'scale(1.1) rotate(360deg)';
    
    setTimeout(() => {
        $.ajax({
            url: `/administracion/bitacora/${id}`,
            method: 'GET',
            success: function(data) {
                Swal.fire({
                    title: 'Información Actualizada',
                    html: `
                        <div style="text-align: left;">
                            <p><strong>Usuario:</strong> ${data.Nombre_Usuario}</p>
                            <p><strong>Acción:</strong> ${data.Accion}</p>
                            <p><strong>Módulo:</strong> ${data.Modulo}</p>
                            <p><strong>Fecha:</strong> ${new Date(data.Fecha_Registro).toLocaleString('es-HN')}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonColor: '#C19A6B'
                });
                btn.style.transform = 'scale(1)';
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo recargar la información',
                    icon: 'error',
                    confirmButtonColor: '#C19A6B'
                });
                btn.style.transform = 'scale(1)';
            }
        });
    }, 300);
}

// Eliminar registro
function eliminarRegistro(id) {
    Swal.fire({
        title: '¿Eliminar este registro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#A0826D',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/administracion/bitacora/${id}`,
                method: 'DELETE',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: 'El registro ha sido eliminado correctamente',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar el registro',
                        icon: 'error',
                        confirmButtonColor: '#C19A6B'
                    });
                }
            });
        }
    });
}

// Toggle panel de detalles
function toggleDetailsPanel() {
    const content = $('#detailsContent');
    const icon = $('#chevronIcon');
    
    if (content.is(':visible')) {
        content.slideUp(300);
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    } else {
        content.slideDown(300);
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
}

// Exportar PDF
function exportarPDF() {
    Swal.fire({
        title: 'Generando PDF...',
        html: 'Por favor espere mientras se genera el documento',
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        window.location.href = '{{ route("bitacora.export.pdf") }}?' + new URLSearchParams(window.location.search);
        Swal.close();
    }, 1000);
}

console.log('Bitácora del Sistema cargada exitosamente ✓');
</script>
@stop