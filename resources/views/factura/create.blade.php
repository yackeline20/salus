@extends('adminlte::page')

@section('title', 'Crear Factura')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-invoice"></i> Crear Nueva Factura
            <small>Generar factura para cliente/paciente</small>
        </h1>
        <a href="{{ route('factura.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar a Facturas
        </a>
    </div>
@stop

@section('content')
<div id="message-box" class="fixed top-0 right-0 p-4 z-50" style="position: fixed; top: 1rem; right: 1rem; z-index: 1050; opacity: 0; transition: opacity 0.3s;"></div>

<div class="row">
    <div class="col-md-12">
        <form id="invoice-form" class="space-y-4">

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-circle me-1"></i> Datos de la Factura y Cliente</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="fecha_emision">Fecha de Emisión <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_emision" name="Fecha_Emision" required value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="form-group col-md-8">
                            {{-- Espacio para posibles campos futuros --}}
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="form-group col-md-7">
                            <label for="search_cliente">Buscar Cliente <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="search_cliente"
                                list="clientes_list"
                                placeholder="Escriba Nombre y Apellido del Cliente"
                                required
                                onchange="handleClientSearch(this.value)"
                            >
                            <datalist id="clientes_list"></datalist>
                        </div>

                        <div class="form-group col-md-5">
                            <label for="cod_cliente_seleccionado">Código de Cliente</label>
                            <input type="text" class="form-control" id="cod_cliente_seleccionado" name="Cod_Cliente" readonly required placeholder="Seleccione un cliente arriba">
                            <small class="form-text text-muted">Aquí se mostrará el Código de Cliente.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-pills me-1"></i> Productos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-sm-8">
                                    <label for="producto_id">Producto</label>
                                    <select class="form-control select2" id="producto_id" style="width: 100%;">
                                        <option value="">Seleccione un producto</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="cantidad_producto">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad_producto" value="1" min="1">
                                </div>
                            </div>

                            <button type="button" class="btn btn-success btn-block mt-3" onclick="addProductDetail()">
                                <i class="fas fa-cart-plus"></i> Añadir Producto
                            </button>
                            <hr>
                            <h5>Detalles de Productos Añadidos</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mt-3" id="productos-table">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th style="width: 80px;">Cant.</th>
                                            <th>P. Unit.</th>
                                            <th>Total</th>
                                            <th style="width: 40px;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productos-details">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-medkit me-1"></i> Tratamientos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-sm-8">
                                    <label for="tratamiento_id">Tratamiento</label>
                                    <select class="form-control select2" id="tratamiento_id" style="width: 100%;">
                                        <option value="">Seleccione un tratamiento</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="cantidad_tratamiento">Sesiones</label>
                                    <input type="number" class="form-control" id="cantidad_tratamiento" value="1" min="1">
                                </div>
                            </div>

                            <button type="button" class="btn btn-info btn-block mt-3" onclick="addTreatmentDetail()">
                                <i class="fas fa-plus-square"></i> Añadir Tratamiento
                            </button>
                            <hr>
                            <h5>Detalles de Tratamientos Añadidos</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mt-3" id="tratamientos-table">
                                    <thead>
                                        <tr>
                                            <th>Tratamiento</th>
                                            <th style="width: 80px;">Sesiones</th>
                                            <th>P. Unit.</th>
                                            <th>Total</th>
                                            <th style="width: 40px;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tratamientos-details">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-default">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <p class="lead">Observaciones:</p>
                            <textarea class="form-control" name="Observacion" id="observacion" rows="4" placeholder="Notas adicionales sobre la factura..."></textarea>

                            <div class="row mt-3">
                                <div class="form-group col-md-6">
                                    <label for="metodo_pago">Método de Pago <span class="text-danger">*</span></label>
                                    <select class="form-control" id="metodo_pago" name="Metodo_Pago" required disabled>
                                        <option value="" disabled selected>-- Cargando Opciones --</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="estado_pago">Estado del Pago <span class="text-danger">*</span></label>
                                    <select class="form-control" id="estado_pago" name="Estado_Pago" required disabled>
                                        <option value="" disabled selected>-- Cargando Opciones --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-right">L. <span id="subtotal_display" class="font-weight-bold">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <th class="align-middle">Descuento Aplicado:</th>
                                        <td>
                                            <div class="input-group input-group-sm float-right" style="width: 150px;">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">L.</span>
                                                </div>
                                                <input
                                                    type="number"
                                                    class="form-control form-control-sm text-right"
                                                    id="descuento_aplicado_input"
                                                    value="0.00"
                                                    min="0.00"
                                                    step="0.01"
                                                    onchange="calculateTotals()"
                                                    onkeyup="calculateTotals()"
                                                >
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="align-middle">Tasa ISV (%)</th>
                                        <td>
                                            <div class="input-group input-group-sm float-right" style="width: 150px;">
                                                <input
                                                    type="number"
                                                    class="form-control form-control-sm text-right"
                                                    id="isv_rate_input"
                                                    value="15.00"
                                                    min="0.00"
                                                    step="0.01"
                                                    onchange="calculateTotals()"
                                                    onkeyup="calculateTotals()"
                                                >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>ISV Aplicado:</th>
                                        <td class="text-right">L. <span id="isv_display" class="font-weight-bold">0.00</span></td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th class="h4 mb-0">Total a Pagar:</th>
                                        <td class="text-right h4 mb-0 text-success">L. <span id="total_display">0.00</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="submit-button">
                                <span id="submit-text"><i class="fas fa-file-export me-1"></i> Emitir Factura</span>
                                <span id="loading-spinner" class="spinner-border spinner-border-sm ml-2" role="status" aria-hidden="true" style="display: none;"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@stop

@section('js')
<script>
    let API_BASE_URL = '{{ config('app.salus_api_base_url') }}';
    if (typeof API_BASE_URL === 'undefined' || API_BASE_URL === '') {
        API_BASE_URL = "http://localhost:3000";
    }

    let clientsData = [];
    let productsData = [];
    let treatmentsData = [];
    let detailCounter = 0;
    let currentSubtotal = 0; // Almacena el subtotal antes de aplicar descuento/ISV

    function showMessage(message, type = 'info') {
        const box = document.getElementById('message-box');
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type];

        box.innerHTML = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                             ${message}
                             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                 <span aria-hidden="true">&times;</span>
                             </button>
                         </div>`;
        box.style.opacity = 1;

        setTimeout(() => {
            box.style.opacity = 0;
        }, 5000);
    }

    function formatPrice(price) {
        // Aseguramos que el precio sea un número válido antes de formatear
        const numberPrice = parseFloat(price);
        return isNaN(numberPrice) ? "0.00" : numberPrice.toFixed(2);
    }

    function toggleSubmitButton(disable = true) {
        document.getElementById('submit-button').disabled = disable;
    }

    function togglePaymentFields(enable, message = "Error: No se pudo cargar el catálogo de pagos.") {
        const metodoSelect = document.getElementById('metodo_pago');
        const estadoSelect = document.getElementById('estado_pago');

        metodoSelect.disabled = !enable;
        estadoSelect.disabled = !enable;

        if (!enable) {
             metodoSelect.innerHTML = `<option value="" disabled selected>${message}</option>`;
             estadoSelect.innerHTML = `<option value="" disabled selected>${message}</option>`;
        } else {
             metodoSelect.innerHTML = '';
             estadoSelect.innerHTML = '';
        }
    }

    async function fetchData(endpoint) {
        try {
            const url = `${API_BASE_URL}/${endpoint}`;
            const response = await fetch(url);
            if (!response.ok) {
                const errorBody = await response.text();
                throw new Error(`HTTP error! status: ${response.status} for endpoint: ${endpoint}. Body: ${errorBody.substring(0, 50)}...`);
            }
            const result = await response.json();
            // Su API devuelve un array de arrays, por eso accedemos al primer elemento ([0]) si existe.
            return Array.isArray(result) ? (Array.isArray(result[0]) ? result[0] : result) : (result || []);
        } catch (error) {
            console.error(`Error fetching data from ${endpoint}:`, error);
            if (endpoint !== 'facturas') {
                 showMessage(`Error al cargar datos críticos (${endpoint}). Verifique la conexión con la API.`, 'error');
            }
            return null;
        }
    }

    function processFacturaData(invoices) {
        if (!invoices) {
            return { metodos: [], estados: [] };
        }

        // Definimos valores predeterminados
        const uniqueMetodos = new Set(['Transferencia', 'Efectivo', 'Tarjeta']);
        const uniqueEstados = new Set(['PENDIENTE', 'PAGADA', 'ANULADA']);

        invoices.forEach(invoice => {
            if (invoice.Metodo_Pago && typeof invoice.Metodo_Pago === 'string' && invoice.Metodo_Pago.trim() !== '') {
                uniqueMetodos.add(invoice.Metodo_Pago.trim());
            }
            if (invoice.Estado_Pago && typeof invoice.Estado_Pago === 'string' && invoice.Estado_Pago.trim() !== '') {
                uniqueEstados.add(invoice.Estado_Pago.trim());
            }
        });

        return {
            metodos: Array.from(uniqueMetodos).sort(),
            estados: Array.from(uniqueEstados).sort()
        };
    }

    function populatePaymentSelect(selectElement, data) {
        selectElement.innerHTML = '<option value="" disabled>-- Seleccione --</option>';

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            // Capitalizar la primera letra y poner el resto en minúsculas para visualización
            const displayItem = item.charAt(0).toUpperCase() + item.slice(1).toLowerCase();
            option.textContent = displayItem;
            selectElement.appendChild(option);
        });

        if (data.length > 0) {
            const defaultStatus = data.find(item => item.toUpperCase() === 'PENDIENTE') || data[0];
            selectElement.value = defaultStatus;
        }
    }

    async function loadInitialData() {
        toggleSubmitButton(true);
        togglePaymentFields(false, "Cargando opciones...");

        const [clients, products, treatments, allInvoices] = await Promise.all([
            fetchData('clientes-persona-info'),
            fetchData('producto'),
            fetchData('tratamiento'),
            fetchData('facturas')
        ]);

        let loadSuccess = true;

        if (clients && products && treatments) {
            clientsData = clients;
            productsData = products;
            treatmentsData = treatments;

            populateProductSelect(document.getElementById('producto_id'), productsData);
            populateTreatmentSelect(document.getElementById('tratamiento_id'), treatmentsData);
            populateDatalist(document.getElementById('clientes_list'), clientsData);

            toggleSubmitButton(false);
        } else {
            showMessage("Error: No se pudieron cargar datos críticos (Clientes/Productos/Tratamientos). El formulario de envío está deshabilitado.", 'error');
            loadSuccess = false;
        }

        if (allInvoices !== null) {
            const { metodos, estados } = processFacturaData(allInvoices);

            if (metodos.length > 0) {
                togglePaymentFields(true);

                populatePaymentSelect(document.getElementById('metodo_pago'), metodos);
                populatePaymentSelect(document.getElementById('estado_pago'), estados);

            } else {
                togglePaymentFields(false, "No se encontraron métodos/estados de pago válidos.");
                if(loadSuccess) {
                    showMessage("Advertencia: No se encontraron métodos/estados de pago históricos válidos en la API.", 'warning');
                }
            }
        } else {
             togglePaymentFields(false, "API de facturas no disponible. No se pudo cargar métodos de pago.");
             if(loadSuccess) {
                 showMessage("Advertencia: No se pudieron cargar los métodos de pago. Verifique la API.", 'warning');
             }
        }

        if (loadSuccess) {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "Seleccione una opción",
                allowClear: true
            });
            calculateTotals();
        }
    }

    function populateProductSelect(selectElement, data) {
        selectElement.innerHTML = '<option value="">Seleccione un producto</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            const precio = parseFloat(item.Precio_Venta || 0);
            option.value = item.Cod_Producto;
            option.textContent = `${item.Nombre_Producto} - L. ${formatPrice(precio)}`;
            option.dataset.precio = precio;
            selectElement.appendChild(option);
        });
    }

    function populateTreatmentSelect(selectElement, data) {
        selectElement.innerHTML = '<option value="">Seleccione un tratamiento</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            const precio = parseFloat(item.Precio_Estandar || 0);
            option.value = item.Cod_Tratamiento;
            option.textContent = `${item.Nombre_Tratamiento} - L. ${formatPrice(precio)}`;
            option.dataset.precio = precio;
            selectElement.appendChild(option);
        });
    }

    function populateDatalist(datalistElement, data) {
        datalistElement.innerHTML = '';
        data.forEach(client => {
            const option = document.createElement('option');
            option.value = `${client.Nombre} ${client.Apellido}`;
            option.dataset.codCliente = client.Cod_Cliente;
            datalistElement.appendChild(option);
        });
    }

    function handleClientSearch(searchValue) {
        const selectedClient = clientsData.find(client =>
            `${client.Nombre} ${client.Apellido}`.trim() === searchValue.trim()
        );

        const codClienteInput = document.getElementById('cod_cliente_seleccionado');

        if (selectedClient) {
            codClienteInput.value = selectedClient.Cod_Cliente;
            codClienteInput.placeholder = `Cod: ${selectedClient.Cod_Cliente} | DNI: ${selectedClient.DNI || 'N/A'}`;
            codClienteInput.setCustomValidity("");
        } else {
            codClienteInput.value = '';
            codClienteInput.placeholder = 'Seleccione un cliente válido de la lista';
            codClienteInput.setCustomValidity("Debe seleccionar un cliente de la lista desplegable.");
        }
    }

    function calculateTotals() {
        // Obtenemos las tasas y el descuento
        const isvRateInput = document.getElementById('isv_rate_input');
        const isvRate = parseFloat(isvRateInput.value) / 100 || 0; // Tasa ISV en decimal (ej: 0.15)
        const descuentoInput = document.getElementById('descuento_aplicado_input');
        // Aseguramos que el descuento sea un número válido antes de trabajar con él
        let descuento = parseFloat(descuentoInput.value) || 0;
        if (descuento < 0) {
            descuento = 0;
            descuentoInput.value = '0.00';
        }

        const productRows = document.querySelectorAll('#productos-details tr');
        const treatmentRows = document.querySelectorAll('#tratamientos-details tr');

        let subtotalItems = 0; // Subtotal de todos los productos/tratamientos

        productRows.forEach(row => {
            const totalCell = row.querySelector('.total-product-cell');
            if (totalCell) {
                subtotalItems += parseFloat(totalCell.dataset.total || 0);
            }
        });

        treatmentRows.forEach(row => {
            const totalCell = row.querySelector('.total-treatment-cell');
            if (totalCell) {
                subtotalItems += parseFloat(totalCell.dataset.total || 0);
            }
        });

        // 1. AJUSTAR DESCUENTO Y CALCULAR BASE IMPONIBLE
        if (descuento > subtotalItems) {
            descuento = subtotalItems;
            descuentoInput.value = formatPrice(subtotalItems);
        }

        const baseImponible = subtotalItems - descuento;
        const subtotalConDescuento = Math.max(0, baseImponible); // No puede ser negativo

        // 2. CALCULAR ISV
        const isv = subtotalConDescuento * isvRate;

        // 3. CALCULAR TOTAL A PAGAR
        const totalPagar = subtotalConDescuento + isv;

        // Almacenamos el subtotal original de los items (sin descuento ni ISV)
        currentSubtotal = subtotalItems;

        // 4. ACTUALIZAR VISUALIZACIÓN
        document.getElementById('subtotal_display').textContent = formatPrice(subtotalItems);
        document.getElementById('isv_display').textContent = formatPrice(isv);
        document.getElementById('total_display').textContent = formatPrice(totalPagar);
    }

    function addProductDetail() {
        const productSelect = document.getElementById('producto_id');
        const quantityInput = document.getElementById('cantidad_producto');
        const selectedOption = productSelect.options[productSelect.selectedIndex];

        const codProducto = productSelect.value;
        const cantidad = parseInt(quantityInput.value);

        if (!codProducto || cantidad <= 0) {
            showMessage("Seleccione un producto y una cantidad válida.", 'warning');
            return;
        }

        const precioUnitario = parseFloat(selectedOption.dataset.precio || 0);
        const nombreProducto = selectedOption.textContent.substring(0, selectedOption.textContent.lastIndexOf(' - L.'));

        if (precioUnitario === 0) {
            showMessage("El precio unitario del producto es L. 0.00. Se agregará, pero verifique el catálogo.", 'info');
        }

        const total = precioUnitario * cantidad;
        const detailId = `product-${detailCounter++}`;

        const newRow = createDetailRow(detailId, nombreProducto, cantidad, precioUnitario, total, 'P', codProducto);

        document.getElementById('productos-details').appendChild(newRow);

        productSelect.value = '';
        $('#producto_id').val(null).trigger('change');
        quantityInput.value = 1;
        calculateTotals();
    }

    function addTreatmentDetail() {
        const treatmentSelect = document.getElementById('tratamiento_id');
        const quantityInput = document.getElementById('cantidad_tratamiento');
        const selectedOption = treatmentSelect.options[treatmentSelect.selectedIndex];

        const codTratamiento = treatmentSelect.value;
        const cantidad = parseInt(quantityInput.value);

        if (!codTratamiento || cantidad <= 0) {
            showMessage("Seleccione un tratamiento y una cantidad válida.", 'warning');
            return;
        }

        const precioUnitario = parseFloat(selectedOption.dataset.precio || 0);
        const nombreTratamiento = selectedOption.textContent.substring(0, selectedOption.textContent.lastIndexOf(' - L.'));

        if (precioUnitario === 0) {
            showMessage("El precio unitario del tratamiento es L. 0.00. Se agregará, pero verifique el catálogo.", 'info');
        }

        const total = precioUnitario * cantidad;
        const detailId = `treatment-${detailCounter++}`;

        const newRow = createDetailRow(detailId, nombreTratamiento, cantidad, precioUnitario, total, 'T', codTratamiento);

        document.getElementById('tratamientos-details').appendChild(newRow);

        treatmentSelect.value = '';
        $('#tratamiento_id').val(null).trigger('change');
        quantityInput.value = 1;
        calculateTotals();
    }

    function createDetailRow(id, name, quantity, unitPrice, total, type, cod) {
        const newRow = document.createElement('tr');
        newRow.id = id;
        newRow.innerHTML = `
            <td>${name}</td>
            <td class="text-center" data-quantity="${quantity}">${quantity}</td>
            <td class="text-right" data-price="${unitPrice}">L. ${formatPrice(unitPrice)}</td>
            <td class="text-right total-${type === 'P' ? 'product' : 'treatment'}-cell" data-total="${total}">L. ${formatPrice(total)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs" onclick="removeDetail('${id}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        newRow.dataset.type = type;
        newRow.dataset.cod = cod;
        newRow.dataset.cantidad = quantity;
        newRow.dataset.precio = unitPrice;
        newRow.dataset.nombre = name;
        newRow.dataset.total = total;

        return newRow;
    }

    function removeDetail(id) {
        document.getElementById(id).remove();
        calculateTotals();
    }

    document.getElementById('invoice-form').addEventListener('submit', handleFormSubmit);

    async function handleFormSubmit(event) {
        event.preventDefault();

        // 1. Validación inicial
        const codCliente = document.getElementById('cod_cliente_seleccionado').value;
        if (!codCliente) {
            showMessage("Debe seleccionar un cliente válido de la lista.", 'warning');
            return;
        }

        const allDetails = Array.from(document.querySelectorAll('#productos-details tr, #tratamientos-details tr'));
        if (allDetails.length === 0) {
            showMessage("Debe agregar al menos un producto o tratamiento para facturar.", 'warning');
            return;
        }

        if (document.getElementById('metodo_pago').disabled || document.getElementById('estado_pago').disabled) {
             showMessage("Error: Los métodos de pago no están disponibles. Recargue la página si el problema persiste.", 'error');
             return;
        }

        // 2. Extracción de valores y CÁLCULO FINAL para el backend
        const metodoPago = document.getElementById('metodo_pago').value;
        const estadoPago = document.getElementById('estado_pago').value;
        const fechaEmision = document.getElementById('fecha_emision').value;
        const observacion = document.getElementById('observacion').value;

        // Recalculamos para obtener el valor final exacto
        calculateTotals();

        const isvRatePercent = parseFloat(document.getElementById('isv_rate_input').value) || 0;
        const isvRateValue = isvRatePercent / 100; // Tasa ISV en decimal

        const subtotalItems = currentSubtotal; // Subtotal antes del descuento (sub_total_calculado)
        const descuentoAplicado = parseFloat(document.getElementById('descuento_aplicado_input').value) || 0.00; // OBLIGATORIO

        const subtotalConDescuento = subtotalItems - descuentoAplicado;
        const isvCalculado = subtotalConDescuento * isvRateValue; // isv_calculado
        const totalPagar = subtotalConDescuento + isvCalculado; // Total_Factura

        if (!metodoPago || !estadoPago) {
             showMessage("Debe seleccionar el Método de Pago y el Estado del Pago.", 'warning');
             return;
        }

        if (totalPagar <= 0 && allDetails.length > 0) {
            showMessage("El Total a Pagar debe ser mayor a cero para emitir la factura.", 'warning');
            return;
        }

        // 3. Obtener y SEPARAR detalles de productos/tratamientos para el Request
        const detalles_producto = [];
        const detalles_tratamiento = [];

        allDetails.forEach(row => {
            const type = row.dataset.type; // 'P' o 'T'
            const codItem = row.dataset.cod;
            const cantidad = parseInt(row.dataset.cantidad);
            const precioUnitario = parseFloat(row.dataset.precio);
            const totalDetalle = cantidad * precioUnitario; // Requerido para detalles_producto

            if (type === 'P') {
                detalles_producto.push({
                    Cod_Producto: parseInt(codItem),
                    Cantidad: cantidad,
                    Precio_Unitario: precioUnitario,
                    Total_Detalle: totalDetalle
                });
            } else if (type === 'T') {
                // Mapeo Tratamiento: Cantidad (frontend) -> Sesiones (backend), Precio_Unitario (frontend) -> Costo (backend)
                detalles_tratamiento.push({
                    Cod_Tratamiento: parseInt(codItem),
                    Sesiones: cantidad, // El request pide 'Sesiones'
                    Costo: precioUnitario, // El request pide 'Costo'
                    Precio_Unitario: precioUnitario // Incluimos ambos por si acaso
                });
            }
        });


        // 4. CONSTRUIMOS EL OBJETO DE LA FACTURA
        const invoiceData = {
            // CABECERA OBLIGATORIA (Incluyendo los campos que desea forzar)
            Cod_Cliente: parseInt(codCliente),
            Fecha_Factura: fechaEmision,
            Metodo_Pago: metodoPago,
            Estado_Pago: estadoPago,
            Observacion: observacion,

            // CAMPOS FORZADOS Y CALCULADOS OBLIGATORIOS
            Descuento_Aplicado: descuentoAplicado, // FORZADO/OBLIGATORIO
            Total_Factura: totalPagar, // FORZADO/OBLIGATORIO

            // CAMPOS CALCULADOS REQUERIDOS POR FacturaStoreRequest.php
            sub_total_calculado: subtotalItems,
            isv_calculado: isvCalculado,

            // Dato auxiliar (Tasa)
            ISV_Tasa: isvRatePercent,

            // DETALLES (Estructurados según FacturaStoreRequest.php)
            detalles_producto: detalles_producto,
            detalles_tratamiento: detalles_tratamiento
        };

        // 5. Control de UI (Loading)
        const spinner = document.getElementById('loading-spinner');
        spinner.style.display = 'inline-block';
        document.getElementById('submit-text').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        toggleSubmitButton(true);

        try {
            // 6. INTENTAMOS ENVIAR A LA API (Controlador de Laravel)
            const response = await fetch('{{ route("factura.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(invoiceData)
            });

            // 7. Manejo de Errores de Laravel o API
            if (!response.ok) {
                 const errorText = await response.text();
                 let message = `Error inesperado al guardar la factura (HTTP ${response.status}).`;
                 try {
                    const errorJson = JSON.parse(errorText);
                    const validationErrors = errorJson.errors ? Object.values(errorJson.errors).flat().join('; ') : '';
                    message = errorJson.message || validationErrors || errorJson.error || message;
                 } catch (e) {
                    message = `Error de conexión o de seguridad. Verifique su sesión o la API. Respuesta: ${errorText.substring(0, 100)}...`;
                 }
                 throw new Error(message);
            }

            // 8. ÉXITO EN LA CREACIÓN
            const result = await response.json();
            const codFactura = result.Cod_Factura || result.id || 'N/A';

            showMessage(`Factura #${codFactura} generada con éxito! Redirigiendo al detalle...`, 'success');

            // 9. ✅ REDIRECCIÓN A LA VISTA DE DETALLE (CORRECCIÓN FINAL APLICADA AQUÍ)
            setTimeout(() => {
                if (codFactura === 'N/A') {
                     // Si falla la captura del ID (porque la API devolvió 'N/A'), redirigimos al listado.
                     showMessage("Advertencia: No se pudo obtener el ID de la factura. Redirigiendo a listado.", 'warning');
                     window.location.href = '{{ route("factura.index") }}';
                     return;
                }

                // Usamos la ruta estándar de Laravel 'factura.show'
                const redirectUrl = `{{ route('factura.show', ['factura' => ':codFactura']) }}`.replace(':codFactura', codFactura);
                window.location.href = redirectUrl;
            }, 2000);

        } catch (error) {
            // 10. MANEJO DEL ERROR
            console.error("Error completo en el proceso de facturación:", error);
            showMessage(`Error al guardar la factura: ${error.message}.`, 'error');

        } finally {
            spinner.style.display = 'none';
            document.getElementById('submit-text').innerHTML = '<i class="fas fa-file-export me-1"></i> Emitir Factura';
            if (document.getElementById('cod_cliente_seleccionado').value && !document.getElementById('metodo_pago').disabled) {
                 toggleSubmitButton(false);
            }
        }
    }

    document.getElementById('search_cliente').addEventListener('change', (e) => handleClientSearch(e.target.value));
    document.addEventListener('DOMContentLoaded', loadInitialData);
</script>
@stop
