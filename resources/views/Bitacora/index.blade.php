@extends('layouts.app')

@section('content')
<style>
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

    .bitacora-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        position: relative;
    }

    .bitacora-icon::before {
        content: '';
        position: absolute;
        width: 28px;
        height: 28px;
        background: white;
        clip-path: polygon(
            50% 0%, 
            61% 35%, 
            98% 35%, 
            68% 57%, 
            79% 91%, 
            50% 70%, 
            21% 91%, 
            32% 57%, 
            2% 35%, 
            39% 35%
        );
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
        padding: 8px 40px 8px 15px;
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

    /* Badge para acciones */
    .badge-action {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-delete {
        background-color: #fee;
        color: #c53030;
    }

    .badge-update {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-insert {
        background-color: #d1fae5;
        color: #065f46;
    }

    /* Usuario destacado */
    .user-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px;
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

    .btn-update {
        background-color: #A0522D;
    }

    .btn-update:hover {
        background-color: #8B4513;
        transform: scale(1.1);
    }

    .btn-delete {
        background-color: #654321;
    }

    .btn-delete:hover {
        background-color: #4a2c16;
        transform: scale(1.1);
    }

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
    }
</style>

<div class="container-fluid">
    <div class="bitacora-container">
        <!-- Header -->
        <div class="bitacora-header">
            <div class="header-left">
                <div class="bitacora-icon"></div>
                <div class="bitacora-title">
                    <h2>Bitácora del Sistema</h2>
                    <p>Historial completo de movimientos - Clínica Salus</p>
                </div>
            </div>
            <div class="header-actions no-print">
                <button class="btn-export btn-print" onclick="window.print()">
                    <span>🖨️</span>
                    <span>Imprimir</span>
                </button>
                <button class="btn-export btn-pdf" onclick="exportarPDF()">
                    <span>📄</span>
                    <span>Exportar PDF</span>
                </button>
            </div>
        </div>

        <!-- Controles -->
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
                    placeholder="Buscar por usuario, módulo, acción..."
                    value="{{ request('buscar') }}"
                >
                <i>🔍</i>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-wrapper">
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
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($registro->Nombre_Usuario, 0, 2)) }}
                                </div>
                                <div>
                                    <strong>{{ $registro->Nombre_Usuario }}</strong>
                                    <br>
                                    <small style="color: #718096;">ID: {{ $registro->Cod_Usuario }}</small>
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
                                } else {
                                    $badgeClass .= 'badge-insert';
                                }
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $registro->Accion }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($registro->Observaciones, 50) }}</td>
                        <td><code>{{ $registro->IP_Address }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($registro->Fecha_Registro)->format('d/m/Y H:i:s') }}</td>
                        <td class="no-print">
                            <div class="action-buttons">
                                <button 
                                    class="btn-action btn-update" 
                                    onclick="verDetalles({{ $registro->Cod_Bitacora }})"
                                    title="Ver Detalles"
                                >
                                    ↻
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
                        <td colspan="7" class="text-center py-4">
                            <div style="font-size: 48px;">📋</div>
                            <p class="text-muted">No se encontraron registros en la bitácora</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
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

<!-- Modal Detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    📋 Detalles del Registro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBodyDetalles">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
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

    // Búsqueda
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

    // Ver detalles
    function verDetalles(id) {
        $.ajax({
            url: `/administracion/bitacora/${id}`,
            method: 'GET',
            success: function(data) {
                let html = `
                    <div class="detail-row">
                        <div class="detail-label">Código:</div>
                        <div class="detail-value">${data.Cod_Bitacora}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Usuario:</div>
                        <div class="detail-value"><strong>${data.Nombre_Usuario}</strong> (ID: ${data.Cod_Usuario})</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Módulo:</div>
                        <div class="detail-value">${data.Modulo}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Acción:</div>
                        <div class="detail-value"><span class="badge bg-info">${data.Accion}</span></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Observaciones:</div>
                        <div class="detail-value">${data.Observaciones}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Dirección IP:</div>
                        <div class="detail-value"><code>${data.IP_Address}</code></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Fecha y Hora:</div>
                        <div class="detail-value">${new Date(data.Fecha_Registro).toLocaleString('es-HN')}</div>
                    </div>
                    <div class="mt-3 text-center">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                `;
                
                $('#modalBodyDetalles').html(html);
                new bootstrap.Modal(document.getElementById('modalDetalles')).show();
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar los detalles',
                    icon: 'error',
                    confirmButtonColor: '#8B4513'
                });
            }
        });
    }

    // Eliminar registro
    function eliminarRegistro(id) {
        Swal.fire({
            title: '¿Eliminar este registro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#654321',
            cancelButtonColor: '#a0aec0',
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
                                text: 'El registro ha sido eliminado',
                                icon: 'success',
                                timer: 2000,
                                confirmButtonColor: '#8B4513'
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
                            confirmButtonColor: '#8B4513'
                        });
                    }
                });
            }
        });
    }

    function exportarPDF() {
        window.location.href = '?' + route('bitacora') + '?' + new URLSearchParams(window.location.search);
// Falla porque la app no encuentra una ruta llamada 'bitacora'.
    }
</script>
@endpush