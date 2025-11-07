@extends('adminlte::page')

@section('title', 'Editar Factura')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-edit text-secondary"></i> Editar Factura
            <small class="text-muted" id="invoice-id-display">Cargando...</small>
        </h1>
        <a href="{{ route('factura.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar a Facturas
        </a>
    </div>
@stop

@section('content')

{{-- Contenedor de mensajes de notificación --}}
<div id="message-box" class="fixed top-0 right-0 p-4 z-50" style="position: fixed; top: 1rem; right: 1rem; z-index: 1050; opacity: 0; transition: opacity 0.3s;"></div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-warning shadow-lg"> {{-- Cambiado a card-warning para hacer juego con el botón --}}
            <div class="card-header">
                <h3 class="card-title">Detalles de la Factura</h3>
            </div>
            <form id="edit-invoice-form">
                <div class="card-body">
                    {{-- Campo Oculto para el ID de Factura --}}
                    <input type="hidden" id="cod_factura" name="Cod_Factura">

                    <div class="form-group">
                        <label for="cod_cliente">Código de Cliente (Solo lectura)</label>
                        <input type="number" class="form-control" id="cod_cliente" name="Cod_Cliente" required readonly placeholder="Cargando...">
                    </div>

                    <div class="form-group">
                        <label for="fecha_factura">Fecha de Factura</label>
                        <input type="date" class="form-control" id="fecha_factura" name="Fecha_Factura" required>
                    </div>

                    <div class="form-group">
                        <label for="metodo_pago">Método de Pago</label>
                        <select class="form-control" id="metodo_pago" name="Metodo_Pago" required>
                            <option value="">Seleccione un método</option>
                            <option value="Tarjeta">Tarjeta de Crédito/Débito</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia Bancaria</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado_pago">Estado de Pago</label>
                        <select class="form-control" id="estado_pago" name="Estado_Pago" required>
                            <option value="">Seleccione el estado</option>
                            <option value="Pagada">Pagada</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Cancelada">Cancelada/Anulada</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descuento_aplicado">Descuento Aplicado ($)</label>
                        <input type="number" class="form-control" id="descuento_aplicado" name="Descuento_Aplicado" step="0.01" min="0" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label for="total_factura">Monto Final de la Factura ($)</label>
                        <input type="number" class="form-control font-weight-bold text-success" id="total_factura" name="Total_Factura" step="0.01" min="0" required>
                        <small class="form-text text-muted">Asegúrese de que este monto refleje el costo final correcto.</small>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-warning btn-lg shadow-sm" id="submit-button">
                        <span id="submit-text"><i class="fas fa-save mr-1"></i> Guardar Cambios</span>
                        <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    </button>
                </div>
            </form>
            <div id="loading-overlay" class="overlay" style="display: none;"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>
        </div>
    </div>
</div>

@stop

@section('css')
    <style>
        /* Estilos para el overlay de carga */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 0.25rem;
        }
    </style>
@stop

@section('js')
    <script>
        // ⭐ Asegúrese de que esta URL sea la correcta para su API de Node.js
        const API_URL = 'http://localhost:3000';

        // Función de ayuda para mostrar alertas (igual que en index.blade.php)
        function showMessage(message, type = 'success') {
            const icon = type === 'danger' ? 'fas fa-exclamation-triangle' : type === 'warning' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="z-index: 10;">
                    <i class="icon ${icon} mr-2"></i>${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            const box = document.getElementById('message-box');
            box.innerHTML = alertHtml;
            box.style.opacity = '1';
            // Autocerrar y ocultar después de 5 segundos
            setTimeout(() => {
                box.style.opacity = '0';
                setTimeout(() => box.innerHTML = '', 300);
            }, 5000);
        }

        function toggleLoading(isLoading) {
            document.getElementById('loading-overlay').style.display = isLoading ? 'flex' : 'none';
            document.getElementById('submit-button').disabled = isLoading;
            document.getElementById('spinner').style.display = isLoading ? 'inline-block' : 'none';
            document.getElementById('submit-text').innerHTML = isLoading ? 'Guardando...' : '<i class="fas fa-save mr-1"></i> Guardar Cambios';
        }

        // ----------------------------------------------------
        // 1. OBTENER ID DE LA URL Y CARGAR DATOS
        // ----------------------------------------------------
        async function loadFacturaData() {
            // Extrae el ID de la URL: /facturas/ID_AQUI/edit
            const pathSegments = window.location.pathname.split('/');
            // El penúltimo segmento (índice -2) es el ID de la factura
            const facturaId = pathSegments[pathSegments.length - 2];

            if (!facturaId || isNaN(facturaId)) {
                showMessage("Error: No se pudo obtener el ID de la factura de la URL.", 'danger');
                toggleLoading(false);
                return;
            }

            document.getElementById('invoice-id-display').textContent = `Factura #F-${String(facturaId).padStart(4, '0')}`;
            document.getElementById('cod_factura').value = facturaId;
            toggleLoading(true);

            try {
                // Paso 1: Obtener la factura por ID (GET)
                const response = await fetch(`${API_URL}/facturas/${facturaId}`);

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.error || `Error ${response.status}: No se encontró la factura.`);
                }

                const factura = await response.json();

                // Paso 2: Rellenar el formulario con los datos
                document.getElementById('cod_cliente').value = factura.Cod_Cliente;

                // Formatear la fecha a YYYY-MM-DD para el input type="date"
                const rawDate = factura.Fecha_Factura ? new Date(factura.Fecha_Factura) : new Date();
                const formattedDate = rawDate.toISOString().split('T')[0];
                document.getElementById('fecha_factura').value = formattedDate;

                document.getElementById('metodo_pago').value = factura.Metodo_Pago || '';
                document.getElementById('estado_pago').value = factura.Estado_Pago || '';

                // Usar parseFloat y toFixed para asegurar el formato decimal
                document.getElementById('descuento_aplicado').value = parseFloat(factura.Descuento_Aplicado || 0).toFixed(2);
                document.getElementById('total_factura').value = parseFloat(factura.Total_Factura || 0).toFixed(2);

            } catch (error) {
                console.error("Error al cargar la factura:", error);
                showMessage(`Error al cargar los datos: ${error.message}`, 'danger');
            } finally {
                toggleLoading(false);
            }
        }

        // ----------------------------------------------------
        // 2. MANEJADOR DE ENVÍO DEL FORMULARIO (UPDATE/PUT)
        // ----------------------------------------------------
        document.getElementById('edit-invoice-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            toggleLoading(true);

            const facturaId = document.getElementById('cod_factura').value;
            const form = event.target;

            // Recolección de datos
            const data = {
                Cod_Cliente: parseInt(form.elements['Cod_Cliente'].value), // Se envía aunque sea readonly
                Fecha_Factura: form.elements['Fecha_Factura'].value,
                Total_Factura: parseFloat(form.elements['Total_Factura'].value),
                Metodo_Pago: form.elements['Metodo_Pago'].value,
                Estado_Pago: form.elements['Estado_Pago'].value,
                Descuento_Aplicado: parseFloat(form.elements['Descuento_Aplicado'].value)
            };

            try {
                // Petición PUT al endpoint que ya definiste en Node.js
                const response = await fetch(`${API_URL}/facturas/${facturaId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Error desconocido al actualizar la factura.');
                }

                // Éxito
                showMessage(`Factura #${String(facturaId).padStart(4, '0')} actualizada correctamente.`, 'success');
                // Opcional: Redirigir al índice después de un breve retraso
                setTimeout(() => window.location.href = '{{ route("factura.index") }}', 1500);

            } catch (error) {
                console.error("Error en la actualización:", error);
                showMessage(`Error de API/Servidor: ${error.message}`, 'danger');
            } finally {
                toggleLoading(false);
            }
        });

        // ----------------------------------------------------
        // 3. INICIALIZACIÓN
        // ----------------------------------------------------
        document.addEventListener('DOMContentLoaded', loadFacturaData);
    </script>
@stop
