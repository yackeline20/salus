@extends('adminlte::page')

{{-- Asegúrese de que su layout principal (o AdminLTE) tiene <meta name="csrf-token" content="{{ csrf_token() }}"> en el <head> --}}

@section('title', 'Crear Factura')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-plus"></i> Crear Nueva Factura
            <small>Generar factura para cliente/paciente</small>
        </h1>
        {{-- ¡IMPORTANTE! CORRECCIÓN: La ruta para el índice debe ser 'factura.index' --}}
        <a href="{{ route('factura.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar a Facturas
        </a>
    </div>
@stop

@section('content')
{{-- Contenedor de mensajes de notificación (Ajustado para AdminLTE/Bootstrap) --}}
<div id="message-box" class="fixed top-0 right-0 p-4 z-50" style="position: fixed; top: 1rem; right: 1rem; z-index: 1050; opacity: 0; transition: opacity 0.3s;"></div>

<div class="row">
    <div class="col-md-12">
        <form id="invoice-form" class="space-y-4">
            {{-- SECCIÓN 1: INFORMACIÓN DEL CLIENTE (Ajustada a la Card de AdminLTE) --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-circle mr-2"></i>1. Información del Cliente</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client-search">Buscar Cliente (Nombre o DNI):</label>
                                {{-- El datalist se llenará con los clientes obtenidos de la API --}}
                                <input type="text" id="client-search" list="client-options" class="form-control" placeholder="Escriba el nombre o DNI" autocomplete="off">
                                <datalist id="client-options"></datalist>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cliente Seleccionado:</label>
                                <p id="selected-client-info" class="p-2 border rounded text-muted bg-light min-h-[38px]">
                                    (Seleccione un cliente para continuar)
                                </p>
                                <input type="hidden" id="cod-cliente" name="Cod_Cliente">
                            </div>
                        </div>
                    </div>
                    {{-- Los campos originales de Número de Factura y Fecha se eliminan de aquí para usar los del JS si es necesario, o puede mantenerlos como info fija --}}
                    {{-- Suponiendo que la API genera el número de factura, he mantenido la fecha estática aquí por si la requiere --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_factura">Número de Factura</label>
                                <input type="text" class="form-control" id="numero_factura" value="Generado por API" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="Fecha_Factura_Display" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: ARTÍCULOS DE LA FACTURA (Original de su código) --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-box-open mr-2"></i>2. Artículos de la Factura</h3>
                </div>
                <div class="card-body">
                    {{-- Controles de selección de Artículo (Ajustado a AdminLTE/Bootstrap) --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-product-select">Agregar Producto:</label>
                                <div class="input-group">
                                    <select id="add-product-select" class="form-control form-control-sm">
                                        <option value="">-- Seleccionar Producto --</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" onclick="addItemToInvoice('product')" class="btn btn-primary btn-sm">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-treatment-select">Agregar Tratamiento:</label>
                                <div class="input-group">
                                    <select id="add-treatment-select" class="form-control form-control-sm">
                                        <option value="">-- Seleccionar Tratamiento --</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" onclick="addItemToInvoice('treatment')" class="btn btn-primary btn-sm">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de Detalles de la Factura (Ajustada a la Card de AdminLTE) --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="min-width: 600px;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="w-1/12">Tipo</th>
                                    <th class="w-5/12">Descripción</th>
                                    <th class="w-2/12">Cant.</th>
                                    <th class="w-2/12 text-right">Precio Unitario</th>
                                    <th class="w-2/12 text-right">Subtotal</th>
                                    <th class="w-1/12 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-details">
                                <tr><td colspan="6" class="text-center text-muted">No hay artículos en la factura.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: DATOS DE PAGO Y RESUMEN (Ajuste de columnas) --}}
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-credit-card mr-2"></i>3. Datos de Pago</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="metodo-pago">Método de Pago:</label>
                                        <select id="metodo-pago" name="Metodo_Pago" required class="form-control">
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Tarjeta">Tarjeta</option>
                                            <option value="Transferencia">Transferencia</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="estado-pago">Estado de Pago:</label>
                                        <select id="estado-pago" name="Estado_Pago" required class="form-control">
                                            <option value="Pagada">Pagada</option>
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="Anulada">Anulada</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="descuento-aplicado">Descuento Aplicado (%):</label>
                                        <input type="number" id="descuento-aplicado" name="Descuento_Aplicado" value="0" min="0" max="100" class="form-control" oninput="calculateTotals()">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea class="form-control" id="observaciones" rows="3" placeholder="Notas adicionales sobre los servicios..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    {{-- Resumen de Factura --}}
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calculator mr-2"></i>Resumen de Factura</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal-display" class="font-weight-bold">$0.00</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Descuento (<span id="discount-percent">0</span>%):</span>
                                <span id="discount-amount-display" class="font-weight-bold text-danger">-$0.00</span>
                            </div>

                            {{-- Si quiere agregar el ISV (IVA) de su código original: --}}
                            {{-- <div class="d-flex justify-content-between mb-2">
                                <span>ISV (15%):</span>
                                <span id="isv-display" class="font-weight-bold">$0.00</span>
                            </div> --}}

                            <hr>

                            <div class="d-flex justify-content-between text-lg">
                                <strong>TOTAL:</strong>
                                <strong id="total-factura-display" class="text-success">$0.00</strong>
                                <input type="hidden" id="total-factura-input" name="Total_Factura">
                            </div>

                            <button type="submit" id="submit-button" class="btn btn-primary btn-lg btn-block mt-4" disabled>
                                <span id="submit-text">Emitir Factura</span>
                                <span id="loading-spinner" class="hidden spinner-border text-white" role="status" aria-hidden="true" style="display: none;"></span>
                            </button>

                            {{-- Botón de Imprimir (Si lo desea, aunque la impresión está manejada en JS) --}}
                            {{-- <button type="button" class="btn btn-outline-secondary btn-block mt-2" onclick="generarFacturaImprimible()">
                                <i class="fas fa-print mr-2"></i>Previsualizar Impresión
                            </button> --}}

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('css')
    {{-- Estilos para el spinner de carga, si no están en AdminLTE --}}
    <style>
        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: -0.125em;
            border: 0.15em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: .75s linear infinite spinner-border;
        }
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        /* Estilos para la notificación */
        .toast-message {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
@stop

@section('js')
<script>
    // URL BASE DE SU API DE LARAVEL. CAMBIAR SI ES NECESARIO
    const API_BASE_URL = 'http://127.0.0.1:8000/api';
    // Obtener CSRF Token de la meta-tag de AdminLTE/Laravel
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ?
                       document.querySelector('meta[name="csrf-token"]').getAttribute('content') :
                       '';

    // Estado global de la factura
    let invoiceItems = [];
    let selectedClient = null;
    let availableData = {
        clients: [],
        products: [],
        treatments: []
    };

    // --- FUNCIONES DE UTILIDAD ---

    function showMessage(message, type = 'success') {
        const box = document.getElementById('message-box');
        let colorClass;
        if (type === 'success') {
            colorClass = 'bg-success text-white';
        } else if (type === 'error') {
            colorClass = 'bg-danger text-white';
        } else {
            colorClass = 'bg-info text-white';
        }

        const toast = document.createElement('div');
        toast.className = `toast-message ${colorClass}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = message;

        box.innerHTML = ''; // Limpiar mensajes anteriores
        box.appendChild(toast);
        box.style.opacity = '1';

        setTimeout(() => {
            box.style.opacity = '0';
            setTimeout(() => toast.remove(), 300); // Eliminar después de la transición
        }, 4000);
    }

    function formatCurrency(amount) {
        // Formateo para moneda local, asumiendo una divisa con 2 decimales
        const num = parseFloat(amount);
        if (isNaN(num)) return '$0.00';
        return `$${num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
    }

    function toggleSubmitButton() {
        const button = document.getElementById('submit-button');
        const isReady = selectedClient !== null && invoiceItems.length > 0;
        button.disabled = !isReady;
        document.getElementById('submit-text').textContent = isReady ? 'Emitir Factura' : 'Pendiente Cliente o Artículos';
    }

    // --- LÓGICA DE OBTENCIÓN DE DATOS (REAL API FETCH) ---

    async function fetchData(endpoint) {
        try {
            const response = await fetch(`${API_BASE_URL}/${endpoint}`);
            if (!response.ok) {
                // Registrar el error en consola para depuración
                const errorText = await response.text();
                console.error(`Error de API en ${endpoint} (Status: ${response.status}):`, errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error(`Error al obtener datos de ${endpoint}:`, error);
            showMessage(`Error al cargar ${endpoint}. Verifique la URL de la API o la conexión.`, 'error');
            return []; // Devolver array vacío en caso de error
        }
    }

    async function loadInitialData() {
        // Obtener todos los datos concurrentemente
        const [clients, products, treatments] = await Promise.all([
            fetchData('clientes'), // Su endpoint real para obtener clientes
            fetchData('productos'), // Su endpoint real para obtener productos
            fetchData('tratamientos') // Su endpoint real para obtener tratamientos
        ]);

        availableData.clients = Array.isArray(clients) ? clients : [];
        availableData.products = Array.isArray(products) ? products : [];
        availableData.treatments = Array.isArray(treatments) ? treatments : [];

        // 1. Cargar Clientes para la búsqueda (datalist)
        const clientDatalist = document.getElementById('client-options');
        clientDatalist.innerHTML = availableData.clients.map(c =>
            // Asumo que Cod_Cliente, Nombre, Apellido y DNI son campos válidos.
            `<option value="${c.DNI} - ${c.Nombre} ${c.Apellido}" data-cod-cliente="${c.Cod_Cliente}">`
        ).join('');

        // 2. Cargar Productos para el select
        const productSelect = document.getElementById('add-product-select');
        productSelect.innerHTML += availableData.products.map(p =>
            // Asumo Cod_Producto, Nombre_Producto y Precio_Venta.
            `<option value="${p.Cod_Producto}" data-price="${p.Precio_Venta}">${p.Nombre_Producto} (${formatCurrency(p.Precio_Venta)})</option>`
        ).join('');

        // 3. Cargar Tratamientos para el select
        const treatmentSelect = document.getElementById('add-treatment-select');
        treatmentSelect.innerHTML += availableData.treatments.map(t =>
            // Asumo Cod_Tratamiento, Nombre_Tratamiento y Precio_Estandar.
            `<option value="${t.Cod_Tratamiento}" data-price="${t.Precio_Estandar}">${t.Nombre_Tratamiento} (${formatCurrency(t.Precio_Estandar)})</option>`
        ).join('');

        // Listener para seleccionar cliente
        document.getElementById('client-search').addEventListener('input', handleClientSelection);
        document.getElementById('invoice-form').addEventListener('submit', submitInvoice);

        calculateTotals();
    }

    // --- LÓGICA DE CLIENTES ---

    function handleClientSelection(event) {
        const input = event.target;
        const value = input.value;
        const datalist = document.getElementById('client-options');
        const infoDisplay = document.getElementById('selected-client-info');
        const codClienteInput = document.getElementById('cod-cliente');

        selectedClient = null;
        codClienteInput.value = '';
        infoDisplay.innerHTML = '(Seleccione un cliente para continuar)';
        infoDisplay.classList.remove('text-success', 'font-weight-bold'); // Estilos de éxito en AdminLTE
        infoDisplay.classList.add('text-muted');

        // Buscar el cliente en el datalist para obtener el Cod_Cliente
        const option = Array.from(datalist.options).find(opt => opt.value === value);

        if (option) {
            const codCliente = option.dataset.codCliente;
            const clientData = availableData.clients.find(c => c.Cod_Cliente == codCliente);

            if (clientData) {
                selectedClient = clientData;
                codClienteInput.value = clientData.Cod_Cliente;
                // Asumo que también tiene campos Cedula/DNI/Teléfono
                infoDisplay.innerHTML = `
                    <p class="mb-0"><strong>${clientData.Nombre} ${clientData.Apellido}</strong></p>
                    <small>DNI: ${clientData.DNI || 'N/A'}</small>
                `;
                infoDisplay.classList.add('text-success', 'font-weight-bold');
                infoDisplay.classList.remove('text-muted');
            }
        }
        toggleSubmitButton();
    }

    // --- LÓGICA DE ARTÍCULOS (DETALLES) ---

    function addItemToInvoice(type) {
        let selectElement, sourceItem, codKey, nameKey, priceKey, isProduct, sourceArray;

        if (type === 'product') {
            selectElement = document.getElementById('add-product-select');
            sourceArray = availableData.products;
            codKey = 'Cod_Producto';
            nameKey = 'Nombre_Producto';
            priceKey = 'Precio_Venta';
            isProduct = true;
        } else if (type === 'treatment') {
            selectElement = document.getElementById('add-treatment-select');
            sourceArray = availableData.treatments;
            codKey = 'Cod_Tratamiento';
            nameKey = 'Nombre_Tratamiento';
            priceKey = 'Precio_Estandar';
            isProduct = false;
        } else {
            return;
        }

        sourceItem = sourceArray.find(item => item[codKey] == selectElement.value);

        if (!sourceItem) {
            showMessage(`Debe seleccionar un ${type === 'product' ? 'producto' : 'tratamiento'} válido.`, 'error');
            return;
        }

        const existingItem = invoiceItems.find(item =>
            item.type === type && item.Cod_Item === sourceItem[codKey]
        );

        if (existingItem) {
            // Si existe, aumentar la cantidad (solo aplica a productos, tratamientos siempre 1)
            if (isProduct) {
                existingItem.Cantidad++;
                showMessage(`${sourceItem[nameKey]} Cantidad actualizada a ${existingItem.Cantidad}.`, 'info');
            } else {
                showMessage(`El tratamiento ${sourceItem[nameKey]} ya está agregado.`, 'error');
            }
        } else {
            // Si no existe, agregar nuevo artículo
            const newItem = {
                id: Date.now(), // ID temporal para el manejo en el frontend
                type: type,
                Cod_Item: sourceItem[codKey], // Usa Cod_Item para generalizar (Cod_Producto o Cod_Tratamiento)
                Nombre: sourceItem[nameKey],
                Precio_Unitario_Venta: sourceItem[priceKey] || 0,
                Cantidad: 1,
                isProduct: isProduct
            };
            invoiceItems.push(newItem);
            showMessage(`${newItem.Nombre} agregado a la factura.`, 'success');
        }

        renderInvoiceDetails();
        selectElement.value = "";
        calculateTotals();
    }

    function updateQuantity(id, newQuantity) {
        const item = invoiceItems.find(i => i.id == id);
        if (item && item.isProduct) {
            const quantity = parseInt(newQuantity);
            if (!isNaN(quantity) && quantity > 0) {
                item.Cantidad = quantity;
            } else if (quantity === 0) {
                removeItem(id);
            } else {
                 // Revertir a la cantidad anterior si el input es inválido
                 renderInvoiceDetails();
                 return;
            }
            renderInvoiceDetails();
            calculateTotals();
        }
    }

    function removeItem(id) {
        invoiceItems = invoiceItems.filter(item => item.id !== id);
        renderInvoiceDetails();
        calculateTotals();
        showMessage('Artículo eliminado.', 'info');
    }

    function renderInvoiceDetails() {
        const detailsTable = document.getElementById('invoice-details');
        if (invoiceItems.length === 0) {
            detailsTable.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-muted">No hay artículos en la factura.</td></tr>';
            toggleSubmitButton();
            return;
        }

        detailsTable.innerHTML = invoiceItems.map(item => {
            const subtotal = item.Precio_Unitario_Venta * item.Cantidad;

            const quantityInput = item.isProduct ?
                `<input type="number" value="${item.Cantidad}" min="1" class="form-control form-control-sm text-right" style="width: 70px;" onchange="updateQuantity(${item.id}, this.value)" onfocus="this.select()">` :
                `1 (Servicio)`;

            const typeLabel = item.isProduct ?
                `<span class="badge badge-warning">PRODUCTO</span>` :
                `<span class="badge badge-info">TRATAMIENTO</span>`;

            return `
                <tr>
                    <td class="align-middle">${typeLabel}</td>
                    <td class="align-middle">${item.Nombre}</td>
                    <td class="align-middle">${quantityInput}</td>
                    <td class="align-middle text-right">${formatCurrency(item.Precio_Unitario_Venta)}</td>
                    <td class="align-middle text-right font-weight-bold">${formatCurrency(subtotal)}</td>
                    <td class="align-middle text-right">
                        <button type="button" onclick="removeItem(${item.id})" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        toggleSubmitButton();
    }

    // --- LÓGICA DE CÁLCULO ---

    function calculateTotals() {
        const subtotal = invoiceItems.reduce((acc, item) =>
            acc + (item.Precio_Unitario_Venta * item.Cantidad), 0
        );

        const discountPercent = parseFloat(document.getElementById('descuento-aplicado').value) || 0;

        document.getElementById('discount-percent').textContent = discountPercent;

        const discountFactor = discountPercent / 100;
        const discountAmount = subtotal * discountFactor;

        // **OPCIONAL: Lógica para incluir ISV (15%) de su código original**
        // Si no usa ISV, mantenga solo:
        const total = subtotal - discountAmount;


        document.getElementById('subtotal-display').textContent = formatCurrency(subtotal);
        document.getElementById('discount-amount-display').textContent = `-${formatCurrency(discountAmount)}`;
        document.getElementById('total-factura-display').textContent = formatCurrency(total);
        document.getElementById('total-factura-input').value = total.toFixed(2);
    }

    // --- LÓGICA DE ENVÍO DE FACTURA (REAL API FETCH) ---

    async function submitInvoice(event) {
        event.preventDefault();

        if (!selectedClient || invoiceItems.length === 0) {
            showMessage('Debe seleccionar un cliente y agregar al menos un artículo.', 'error');
            return;
        }

        const button = document.getElementById('submit-button');
        const spinner = document.getElementById('loading-spinner');

        // Estado de carga ON
        button.disabled = true;
        document.getElementById('submit-text').textContent = 'Procesando...';
        spinner.classList.remove('hidden');
        spinner.style.display = 'inline-block'; // Asegurar visibilidad en AdminLTE

        const form = event.target;
        const facturaData = {
            Cod_Cliente: parseInt(form.elements['Cod_Cliente'].value),
            Fecha_Factura: new Date().toISOString().split('T')[0], // Formato YYYY-MM-DD
            Total_Factura: parseFloat(form.elements['Total_Factura'].value),
            Metodo_Pago: form.elements['Metodo_Pago'].value,
            Estado_Pago: form.elements['Estado_Pago'].value,
            Descuento_Aplicado: parseFloat(form.elements['Descuento_Aplicado'].value)
            // Aquí puede agregar Observaciones si su API lo requiere
        };

        let codFactura;

        try {
            // 1. POST Factura (Cabecera)
            const cabeceraResponse = await fetch(`${API_BASE_URL}/facturas`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN // Esto es crucial para Laravel/API si no usa Sanctum
                },
                body: JSON.stringify(facturaData)
            });

            if (!cabeceraResponse.ok) {
                const errorData = await cabeceraResponse.json().catch(() => ({ message: 'No se pudo parsear el JSON de error' }));
                throw new Error(`Error al crear la cabecera: ${cabeceraResponse.statusText} - ${errorData.message || 'Error desconocido'}`);
            }

            const cabeceraResult = await cabeceraResponse.json();
            // Asumo que la API de Laravel para facturas devuelve el ID/Código de la factura creada.
            codFactura = cabeceraResult.Cod_Factura;
            document.getElementById('numero_factura').value = `#F-${codFactura}`; // Actualiza la info en el form
            showMessage(`Factura #${codFactura} creada. Insertando detalles...`, 'info');

            // 2. POST Detalle de Artículos
            for (const item of invoiceItems) {
                let endpoint, detalleData;
                if (item.isProduct) {
                    endpoint = 'detalle_factura_producto';
                    detalleData = {
                        Cod_Factura: codFactura,
                        Cod_Producto: item.Cod_Item,
                        Cantidad_Vendida: item.Cantidad,
                        Precio_Unitario_Venta: item.Precio_Unitario_Venta,
                        Subtotal: (item.Precio_Unitario_Venta * item.Cantidad).toFixed(2)
                    };
                } else {
                    endpoint = 'detalle_factura_tratamiento';
                    detalleData = {
                        Cod_Factura: codFactura,
                        Cod_Tratamiento: item.Cod_Item,
                        Precio_Tratamiento_Venta: item.Precio_Unitario_Venta,
                        Subtotal: (item.Precio_Unitario_Venta * item.Cantidad).toFixed(2)
                    };
                }

                const detalleResponse = await fetch(`${API_BASE_URL}/${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(detalleData)
                });

                if (!detalleResponse.ok) {
                    const errorData = await detalleResponse.json().catch(() => ({ message: 'No se pudo parsear el JSON de error' }));
                    throw new Error(`Error al insertar detalle ${item.Nombre}. Detalle: ${detalleResponse.statusText} - ${errorData.error || errorData.message || 'Desconocido'}`);
                }
            }

            // 3. Éxito Final
            showMessage(`Factura #${codFactura} generada y detalles guardados con éxito!`, 'success');
            // Recargar la página o redirigir tras un breve retraso
            // CORRECCIÓN: La ruta corregida es 'factura.index' para el listado principal
            setTimeout(() => window.location.href = '{{ route("factura.index") }}', 2000);

        } catch (error) {
            console.error("Error completo en el proceso de facturación:", error);
            showMessage(`Error al procesar la factura: ${error.message}`, 'error');
        } finally {
            // Estado de carga OFF
            spinner.style.display = 'none';
            document.getElementById('submit-text').textContent = 'Emitir Factura';
            toggleSubmitButton();
        }
    }

    // --- INICIALIZACIÓN ---
    document.addEventListener('DOMContentLoaded', loadInitialData);
</script>
@stop
