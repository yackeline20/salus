@extends('adminlte::page')

{{-- Asegúrese de que su layout principal (o AdminLTE) tiene <meta name="csrf-token" content="{{ csrf_token() }}"> en el <head> --}}

@section('title', 'Crear Factura')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-plus"></i> Crear Nueva Factura
            <small>Generar factura para cliente/paciente</small>
        </h1>
        {{-- CORRECCIÓN: La ruta para el índice debe ser 'factura.index' (o la que hayas definido) --}}
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
            {{-- SECCIÓN 1: INFORMACIÓN DEL CLIENTE --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-circle mr-2"></i>1. Información del Cliente</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client-search">Buscar Cliente (Nombre o DNI):</label>
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
                                {{-- ESTE CAMPO HIDDEN ES NECESARIO PARA ENVIAR EL CÓDIGO A LARAVEL --}}
                                <input type="hidden" id="cod-cliente" name="Cod_Cliente">
                            </div>
                        </div>
                    </div>

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
                                {{-- Usamos el mismo nombre del request, aunque no se envíe en este formulario --}}
                                <input type="date" class="form-control" id="fecha" name="Fecha_Factura" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: ARTÍCULOS DE LA FACTURA --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-box-open mr-2"></i>2. Artículos de la Factura</h3>
                </div>
                <div class="card-body">
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

                    {{-- Tabla de Detalles de la Factura --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="min-width: 600px;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 10%;">Tipo</th>
                                    <th style="width: 40%;">Descripción</th>
                                    <th style="width: 15%;">Cant.</th>
                                    <th style="width: 15%;" class="text-right">Precio Unitario</th>
                                    <th style="width: 15%;" class="text-right">Subtotal</th>
                                    <th style="width: 5%;" class="text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-details">
                                <tr><td colspan="6" class="text-center text-muted">No hay artículos en la factura.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: DATOS DE PAGO Y RESUMEN --}}
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
                                        {{-- NOMBRE COINCIDE CON FacturaStoreRequest --}}
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
                                        {{-- NOMBRE COINCIDE CON FacturaStoreRequest --}}
                                        <select id="estado-pago" name="Estado_Pago" required class="form-control">
                                            <option value="PAGADA">PAGADA</option>
                                            <option value="PENDIENTE">PENDIENTE</option>
                                            <option value="ANULADA">ANULADA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="descuento-aplicado">Descuento Aplicado (%):</label>
                                        {{-- NOMBRE COINCIDE CON FacturaStoreRequest --}}
                                        <input type="number" id="descuento-aplicado" name="Descuento_Aplicado" value="0" min="0" max="100" class="form-control" oninput="calculateTotals()">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                {{-- NOTA: Este campo no está en el FacturaStoreRequest, lo enviaremos por si la API lo necesita --}}
                                <textarea class="form-control" id="observaciones" rows="3" name="Observaciones" placeholder="Notas adicionales sobre los servicios..."></textarea>
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

                            <hr>

                            <div class="d-flex justify-content-between text-lg">
                                <strong>TOTAL:</strong>
                                <strong id="total-factura-display" class="text-success">$0.00</strong>
                                {{-- CAMPO OCULTO CLAVE: NOMBRE COINCIDE CON FacturaStoreRequest --}}
                                <input type="hidden" id="total-factura-input" name="Total_Factura">
                            </div>

                            <button type="submit" id="submit-button" class="btn btn-primary btn-lg btn-block mt-4" disabled>
                                <span id="submit-text">Emitir Factura</span>
                                <span id="loading-spinner" class="hidden spinner-border text-white" role="status" aria-hidden="true" style="display: none;"></span>
                            </button>

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
    // ** IMPORTANTE: AJUSTAR ESTA URL A DONDE ESTÉ SU CONTROLADOR DE LARAVEL (no a Node.js directamente) **
    // Asumo que el endpoint de Laravel que recibirá el POST es el que maneja facturas.store
    const LARAVEL_STORE_URL = '{{ route('factura.store') }}';

    // URL BASE DE SU API DE NODE.JS (para obtener datos iniciales)
    const API_NODE_BASE_URL = 'http://localhost:3000'; // Ajuste si es necesario

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
        const num = parseFloat(amount);
        if (isNaN(num)) return '$0.00';
        // Usar formato local de dólar americano ($) con 2 decimales
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(num);
    }

    function toggleSubmitButton() {
        const button = document.getElementById('submit-button');
        // REGLA CLAVE: La factura es válida si hay cliente y al menos 1 artículo.
        const isReady = selectedClient !== null && invoiceItems.length > 0;
        button.disabled = !isReady;
        document.getElementById('submit-text').textContent = isReady ? 'Emitir Factura' : 'Pendiente Cliente o Artículos';
    }

    // --- LÓGICA DE OBTENCIÓN DE DATOS (DE LA API DE NODE.JS) ---

    async function fetchData(endpoint) {
        try {
            // Se usa la URL de la API de Node.js
            const response = await fetch(`${API_NODE_BASE_URL}/${endpoint}`);
            if (!response.ok) {
                const errorText = await response.text();
                console.error(`Error de API en ${endpoint} (Status: ${response.status}):`, errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            // Basado en tus capturas, la API puede devolver un array o un objeto que contiene los datos.
            return Array.isArray(data) ? data : (Array.isArray(data[0]) ? data[0] : (data.rows || []));
        } catch (error) {
            console.error(`Error al obtener datos de ${endpoint}:`, error);
            showMessage(`Error al cargar ${endpoint}. Verifique la URL de la API de Node.js o la conexión.`, 'error');
            return []; // Devolver array vacío en caso de error
        }
    }

    async function loadInitialData() {
        // Obtener todos los datos concurrentemente (usando las rutas singulares de tu API Node.js)
        const [clients, products, treatments] = await Promise.all([
            fetchData('cliente'),
            fetchData('producto'),
            fetchData('tratamiento')
        ]);

        availableData.clients = clients;
        availableData.products = products;
        availableData.treatments = treatments;

        // 1. Cargar Clientes para la búsqueda (datalist)
        const clientDatalist = document.getElementById('client-options');
        clientDatalist.innerHTML = availableData.clients.map(c => {
            // Usamos Cod_Cliente y Nombre + Apellido + DNI para el valor de búsqueda
            const clientName = `${c.Nombre || ''} ${c.Apellido || ''}`;
            const clientDNI = c.DNI || 'N/A';
            const displayValue = `${clientName.trim()} (${clientDNI})`;
            return `<option value="${displayValue}" data-cod-cliente="${c.Cod_Cliente}">`;
        }).join('');

        // 2. Cargar Productos para el select
        const productSelect = document.getElementById('add-product-select');
        productSelect.innerHTML += availableData.products.map(p =>
            `<option value="${p.Cod_Producto}" data-price="${p.Precio_Venta}">${p.Nombre_Producto} (${formatCurrency(p.Precio_Venta)})</option>`
        ).join('');

        // 3. Cargar Tratamientos para el select
        const treatmentSelect = document.getElementById('add-treatment-select');
        treatmentSelect.innerHTML += availableData.treatments.map(t =>
            `<option value="${t.Cod_Tratamiento}" data-price="${t.Precio_Estandar}">${t.Nombre_Tratamiento} (${formatCurrency(t.Precio_Estandar)})</option>`
        ).join('');

        // Listener para seleccionar cliente y envío del formulario
        document.getElementById('client-search').addEventListener('input', handleClientSelection);
        document.getElementById('invoice-form').addEventListener('submit', submitInvoice);

        calculateTotals();
        toggleSubmitButton(); // Deshabilitar inicialmente

        if (clients.length === 0 || products.length === 0 || treatments.length === 0) {
            showMessage("Advertencia: No se pudieron cargar todos los datos (clientes, productos o tratamientos).", 'error');
        } else {
            showMessage("Datos iniciales cargados con éxito.", 'info');
        }
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
        infoDisplay.className = 'p-2 border rounded text-muted bg-light min-h-[38px]';

        const option = Array.from(datalist.options).find(opt => opt.value === value);

        if (option) {
            const codCliente = option.dataset.codCliente;
            const clientData = availableData.clients.find(c => c.Cod_Cliente == codCliente);

            if (clientData) {
                selectedClient = clientData;
                codClienteInput.value = clientData.Cod_Cliente;
                infoDisplay.innerHTML = `
                    <p class="mb-0"><strong>${clientData.Nombre} ${clientData.Apellido || ''}</strong></p>
                    <small>DNI: ${clientData.DNI || 'N/A'} | Cód: ${clientData.Cod_Cliente}</small>
                `;
                infoDisplay.className = 'p-2 border rounded text-success font-weight-bold bg-light min-h-[38px]';
            }
        }
        toggleSubmitButton();
    }

    // --- LÓGICA DE ARTÍCULOS (DETALLES) ---

    function addItemToInvoice(type) {
        let selectElement, sourceArray, codKey, nameKey, priceKey, isProduct;

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

        const selectedCod = selectElement.value;
        if (!selectedCod) {
            showMessage(`Debe seleccionar un ${type} válido.`, 'error');
            return;
        }

        const sourceItem = sourceArray.find(item => item[codKey] == selectedCod);

        const existingItem = invoiceItems.find(item =>
            item.type === type && item.Cod_Item == selectedCod
        );

        if (existingItem && isProduct) {
            // Producto: aumenta la cantidad
            existingItem.Cantidad++;
            showMessage(`${sourceItem[nameKey]} Cantidad actualizada a ${existingItem.Cantidad}.`, 'info');
        } else if (existingItem && !isProduct) {
            // Tratamiento: ya existe, no se aumenta
            showMessage(`El tratamiento ${sourceItem[nameKey]} ya está agregado.`, 'error');
        }
        else {
            // Agregar nuevo artículo
            const newItem = {
                id: Date.now(), // ID temporal para el manejo en el frontend
                type: type, // 'product' o 'treatment'
                Cod_Item: sourceItem[codKey],
                Nombre: sourceItem[nameKey],
                Precio_Unitario_Venta: parseFloat(sourceItem[priceKey] || 0),
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

        // Cálculo del total: Subtotal - Descuento
        const total = subtotal - discountAmount;


        document.getElementById('subtotal-display').textContent = formatCurrency(subtotal);
        document.getElementById('discount-amount-display').textContent = `-${formatCurrency(discountAmount)}`;
        document.getElementById('total-factura-display').textContent = formatCurrency(total);
        // Actualiza el campo oculto para que sea enviado en el formulario
        document.getElementById('total-factura-input').value = total.toFixed(2);
    }

    // --- LÓGICA DE ENVÍO DE FACTURA (POST UNIFICADO A LARAVEL) ---

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
        spinner.style.display = 'inline-block';

        const form = event.target;

        // ** 1. Construir los arrays items_productos y items_tratamientos para el FacturaStoreRequest **
        const items_productos = [];
        const items_tratamientos = [];

        invoiceItems.forEach(item => {
            const subtotal = (item.Precio_Unitario_Venta * item.Cantidad).toFixed(2);

            if (item.isProduct) {
                items_productos.push({
                    Cod_Producto: parseInt(item.Cod_Item),
                    Cantidad_Vendida: parseInt(item.Cantidad),
                    Precio_Unitario_Venta: parseFloat(item.Precio_Unitario_Venta).toFixed(2),
                    Subtotal_Producto: subtotal // Campo requerido por tu Request
                });
            } else {
                items_tratamientos.push({
                    Cod_Tratamiento: parseInt(item.Cod_Item),
                    // Nota: Tu Request usa 'Precio_Unitario_Venta' para ambos, lo mantengo por consistencia
                    Precio_Unitario_Venta: parseFloat(item.Precio_Unitario_Venta).toFixed(2),
                    Subtotal_Tratamiento: subtotal // Campo requerido por tu Request
                });
            }
        });


        // ** 2. Construir el payload completo (Cabecera + Detalles) **
        const payload = {
            _token: CSRF_TOKEN, // Laravel necesita el token para POST
            Cod_Cliente: parseInt(form.elements['Cod_Cliente'].value),
            Fecha_Factura: form.elements['Fecha_Factura'].value, // Usamos la fecha del input
            Total_Factura: parseFloat(form.elements['Total_Factura'].value),
            Metodo_Pago: form.elements['Metodo_Pago'].value,
            Estado_Pago: form.elements['Estado_Pago'].value,
            Descuento_Aplicado: parseFloat(form.elements['Descuento_Aplicado'].value),
            Observaciones: form.elements['Observaciones'].value, // Campo extra para Node.js

            // Arrays de ítems (CRUCIAL para FacturaStoreRequest)
            items_productos: items_productos,
            items_tratamientos: items_tratamientos
        };

        try {
            // ** 3. Enviar el POST al controlador de Laravel (facturas.store) **
            const response = await fetch(LARAVEL_STORE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Si bien usamos JSON, el token se envía en el cuerpo, pero lo dejaremos aquí
                    // por si Laravel espera el X-CSRF-TOKEN en los headers para peticiones JSON.
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify(payload)
            });

            // Si Laravel detecta errores de validación (FacturaStoreRequest), devuelve 422 Unprocessable Entity
            if (response.status === 422) {
                const errorData = await response.json();
                let validationErrors = "<ul>";
                // Itera sobre los errores de Laravel
                for (const key in errorData.errors) {
                    validationErrors += `<li>${errorData.errors[key].join('<br>')}</li>`;
                }
                validationErrors += "</ul>";
                throw new Error(`Error de Validación:<br>${validationErrors}`);
            }

            if (!response.ok) {
                 const errorText = await response.text();
                 throw new Error(`Error inesperado al guardar la factura (HTTP ${response.status}). ${errorText.substring(0, 100)}...`);
            }

            const result = await response.json();

            // Asumo que tu controlador de Laravel devuelve el Cod_Factura de la API de Node.js
            const codFactura = result.Cod_Factura || result.id || 'N/A';

            // 4. Éxito Final
            showMessage(`Factura #${codFactura} generada con éxito!`, 'success');
            // Recargar la página o redirigir tras un breve retraso
            setTimeout(() => window.location.href = '{{ route("factura.index") }}', 2000);

        } catch (error) {
            console.error("Error completo en el proceso de facturación:", error);
            // Muestra el mensaje de error de forma segura.
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
