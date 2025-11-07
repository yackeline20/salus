@extends('adminlte::page')

{{-- Asegúrese de que su layout principal (o AdminLTE) tiene <meta name="csrf-token" content="{{ csrf_token() }}"> en el <head> --}}

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
{{-- Contenedor de mensajes de notificación (Ajustado para AdminLTE/Bootstrap) --}}
<div id="message-box" class="fixed top-0 right-0 p-4 z-50" style="position: fixed; top: 1rem; right: 1rem; z-index: 1050; opacity: 0; transition: opacity 0.3s;"></div>

<div class="row">
    <div class="col-md-12">
        {{-- Asegúrate de que el formulario tenga el ID correcto para el JS --}}
        <form id="invoice-form" class="space-y-4">

            {{-- --- SECCIÓN 1: INFORMACIÓN DE FACTURA Y CLIENTE --- --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-circle me-1"></i> Datos de la Factura y Cliente</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- CAMPO FECHA DE EMISIÓN --}}
                        <div class="form-group col-md-4">
                            <label for="fecha_emision">Fecha de Emisión <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_emision" name="Fecha_Emision" required value="{{ date('Y-m-d') }}">
                        </div>

                        {{-- CAMPO SERVICIO MEDICO --}}
                        <div class="form-group col-md-8">
                            <label for="servicio_medico">Servicio Médico (Área de Atención)</label>
                            <select class="form-control select2" id="servicio_medico" name="Servicio_Medico" style="width: 100%;">
                                <option value="">Seleccione un servicio (Opcional)...</option>
                                <option value="Medicina General">Medicina General</option>
                                <option value="Odontología">Odontología</option>
                                <option value="Fisioterapia">Fisioterapia</option>
                                {{-- Agregue más opciones aquí si es necesario --}}
                            </select>
                        </div>
                    </div>

                    <hr> {{-- Separador visual --}}

                    <div class="row">
                        {{-- ✅ CAMPO BUSCAR CLIENTE (INPUT CON DATALIST) --}}
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
                            {{-- Datalist para el desplegable con la lista de clientes --}}
                            <datalist id="clientes_list"></datalist>
                        </div>

                        {{-- ✅ CAMPO COD_CLIENTE SELECCIONADO (OUTPUT) --}}
                        <div class="form-group col-md-5">
                            <label for="cod_cliente_seleccionado">Código de Cliente</label>
                            {{-- Este campo contendrá el Cod_Cliente real para el envío --}}
                            <input type="text" class="form-control" id="cod_cliente_seleccionado" name="Cod_Cliente" readonly required placeholder="Seleccione un cliente arriba">
                            <small class="form-text text-muted">Aquí se mostrará el Código de Cliente.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- --- SECCIÓN 2: DETALLES DE PRODUCTOS Y TRATAMIENTOS --- --}}
            <div class="row">
                {{-- COLUMNA DE PRODUCTOS --}}
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
                                        {{-- Opciones cargadas por JS --}}
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
                                        {{-- Detalles de productos añadidos (Se llenan con JS) --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DE TRATAMIENTOS --}}
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
                                        {{-- Opciones cargadas por JS --}}
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
                                        {{-- Detalles de tratamientos añadidos (Se llenan con JS) --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- --- SECCIÓN 3: TOTALES Y ENVÍO --- --}}
            <div class="card card-default">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <p class="lead">Observaciones:</p>
                            <textarea class="form-control" name="Observacion" id="observacion" rows="4" placeholder="Notas adicionales sobre la factura..."></textarea>

                            {{-- INICIO: CAMPOS AGREGADOS Metodo_Pago y Estado_Pago (Ahora dinámicos) --}}
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
                            {{-- FIN: CAMPOS AGREGADOS Metodo_Pago y Estado_Pago --}}
                        </div>
                        <div class="col-md-5">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-right">L. <span id="subtotal_display" class="font-weight-bold">0.00</span></td>
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
    // Variables globales para almacenar datos iniciales de la API
    let API_BASE_URL = '{{ config('app.salus_api_base_url') }}';
    if (typeof API_BASE_URL === 'undefined' || API_BASE_URL === '') {
        // Fallback si la configuración de Laravel no pasa la variable (Ajustar si es necesario)
        API_BASE_URL = "http://localhost:3000";
    }

    // Variables globales para la lógica
    let clientsData = [];
    let productsData = [];
    let treatmentsData = [];
    let detailCounter = 0;
    let currentSubtotal = 0;

    // --- UTILITIES ---

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
        }, 5000); // Muestra por 5 segundos
    }

    // **IMPORTANTE: Formatea el precio para MOSTRARLO, pero NO para los cálculos**
    function formatPrice(price) {
        return parseFloat(price).toFixed(2);
    }

    function toggleSubmitButton(disable = true) {
        document.getElementById('submit-button').disabled = disable;
    }

    /**
     * Habilita/Deshabilita y muestra un mensaje en los campos de pago/estado.
     * @param {boolean} enable - True para habilitar, False para deshabilitar.
     * @param {string} [message="Error: No se pudo cargar el catálogo de pagos."] - Mensaje a mostrar si se deshabilita.
     */
    function togglePaymentFields(enable, message = "Error: No se pudo cargar el catálogo de pagos.") {
        const metodoSelect = document.getElementById('metodo_pago');
        const estadoSelect = document.getElementById('estado_pago');

        metodoSelect.disabled = !enable;
        estadoSelect.disabled = !enable;

        if (!enable) {
             metodoSelect.innerHTML = `<option value="" disabled selected>${message}</option>`;
             estadoSelect.innerHTML = `<option value="" disabled selected>${message}</option>`;
        } else {
             // Limpiar el estado de carga/error antes de poblar
             metodoSelect.innerHTML = '';
             estadoSelect.innerHTML = '';
        }
    }


    // --- API & DATA LOADING ---

    // Función genérica para obtener datos de la API
    async function fetchData(endpoint) {
        try {
            const url = `${API_BASE_URL}/${endpoint}`;
            const response = await fetch(url);
            if (!response.ok) {
                // Leer el cuerpo del error para un mejor debug.
                const errorBody = await response.text();
                // Lanzar un error específico para capturarlo y manejar la UI
                throw new Error(`HTTP error! status: ${response.status} for endpoint: ${endpoint}. Body: ${errorBody.substring(0, 50)}...`);
            }
            const result = await response.json();
            // Aseguramos que se devuelve un array, si la respuesta está en rows[0] o es un array directo.
            return Array.isArray(result) ? result : (result[0] || []);
        } catch (error) {
            console.error(`Error fetching data from ${endpoint}:`, error);
            // Mostrar mensaje de error SÓLO para los campos críticos (clientes, productos, tratamientos)
            if (endpoint !== 'facturas') {
                 showMessage(`Error al cargar datos críticos (${endpoint}). Verifique la conexión con la API.`, 'error');
            }
            return null; // <-- DEVOLVER NULL en caso de error para diferenciar de un array vacío.
        }
    }

    /**
     * Procesa los datos de todas las facturas para extraer los valores únicos de
     * Método de Pago y Estado de Pago.
     * * @param {object[]} invoices - Array de objetos de factura. Puede ser null si la API falló.
     * @returns {object} - Objeto con arrays de métodos y estados. Devuelve arrays vacíos si hay error/sin datos.
     */
    function processFacturaData(invoices) {
        // Si la llamada a la API falló (devuelve null), retornamos arrays vacíos.
        if (!invoices) {
            return { metodos: [], estados: [] };
        }

        // 1. Iniciar con los métodos de pago estándar requeridos (Transferencia, Efectivo, Tarjeta).
        const uniqueMetodos = new Set(['Transferencia', 'Efectivo', 'Tarjeta']);

        // 2. Inicializar el set de estados de pago (no hay estados base requeridos, se basan solo en historial)
        // Incluimos los estados estándar PENDIENTE, PAGADA, ANULADA por la validación del controlador.
        const uniqueEstados = new Set(['PENDIENTE', 'PAGADA', 'ANULADA']);

        // 3. Procesar datos históricos para añadir métodos y estados
        invoices.forEach(invoice => {
            if (invoice.Metodo_Pago && typeof invoice.Metodo_Pago === 'string' && invoice.Metodo_Pago.trim() !== '') {
                uniqueMetodos.add(invoice.Metodo_Pago.trim());
            }
            if (invoice.Estado_Pago && typeof invoice.Estado_Pago === 'string' && invoice.Estado_Pago.trim() !== '') {
                uniqueEstados.add(invoice.Estado_Pago.trim());
            }
        });

        return {
            // Convertir Set a Array y asegurar orden alfabético para los métodos de pago
            metodos: Array.from(uniqueMetodos).sort(),
            // Convertir Set a Array para los estados
            estados: Array.from(uniqueEstados).sort()
        };
    }

    /**
     * Llena un elemento <select> con opciones dinámicas y selecciona la primera.
     * @param {HTMLElement} selectElement - El elemento <select> a poblar.
     * @param {string[]} data - Array de strings con las opciones.
     */
    function populatePaymentSelect(selectElement, data) {
        // La limpieza ya se hizo en togglePaymentFields(true)
        selectElement.innerHTML = '<option value="" disabled>-- Seleccione --</option>';

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            // Capitalizar la primera letra para una mejor visualización (ej: 'pagada' -> 'Pagada')
            const displayItem = item.charAt(0).toUpperCase() + item.slice(1).toLowerCase();
            option.textContent = displayItem;
            selectElement.appendChild(option);
        });

        // Seleccionar el primer valor disponible (que no sea el disabled) como predeterminado
        if (data.length > 0) {
            selectElement.value = data[0];
        }
    }


    // Función principal para cargar todos los datos necesarios
    async function loadInitialData() {
        // Deshabilitar botón de envío y campos de pago
        toggleSubmitButton(true);
        togglePaymentFields(false, "Cargando opciones...");


        // Cargar datos de clientes, productos, tratamientos y TODAS las facturas
        const [clients, products, treatments, allInvoices] = await Promise.all([
            fetchData('clientes-persona-info'),
            fetchData('producto'),
            fetchData('tratamiento'),
            fetchData('facturas')
        ]);

        let loadSuccess = true;

        // 1. Verificación y Almacenamiento de Datos Críticos (Clientes, Productos, Tratamientos)
        if (clients && products && treatments) {
            clientsData = clients;
            productsData = products;
            treatmentsData = treatments;

            // Poblar campos dependientes
            populateProductSelect(document.getElementById('producto_id'), productsData);
            populateTreatmentSelect(document.getElementById('tratamiento_id'), treatmentsData);
            populateDatalist(document.getElementById('clientes_list'), clientsData);

            // Si los críticos están bien, habilitamos el botón de envío
            toggleSubmitButton(false);
        } else {
             // Si falló alguno de los críticos, mostrar mensaje y detener
            showMessage("Error: No se pudieron cargar datos críticos (Clientes/Productos/Tratamientos). El formulario de envío está deshabilitado.", 'error');
            loadSuccess = false;
        }


        // 2. Verificación y Procesamiento de Datos de Pago
        if (allInvoices !== null) { // allInvoices puede ser [] (array vacío) o un array con datos, pero NO null si la API respondió 200
            const { metodos, estados } = processFacturaData(allInvoices);

            if (metodos.length > 0) {
                // Habilitar y poblar si se encontraron opciones válidas
                togglePaymentFields(true);

                // Poblar métodos de pago
                populatePaymentSelect(document.getElementById('metodo_pago'), metodos);

                // Poblar estados de pago
                populatePaymentSelect(document.getElementById('estado_pago'), estados);

                // Forzamos 'pendiente' como valor por defecto si existe, si no, toma el primero de la lista.
                const defaultEstadoPago = estados.find(e => e.toUpperCase() === 'PENDIENTE') || estados[0];
                if (defaultEstadoPago) {
                    document.getElementById('estado_pago').value = defaultEstadoPago;
                }
            } else {
                 // Caso 2b: La API corrió (no es null), pero no hay facturas históricas
                togglePaymentFields(false, "No se encontraron métodos/estados de pago válidos.");
                if(loadSuccess) {
                    showMessage("Advertencia: No se encontraron métodos/estados de pago históricos válidos en la API.", 'warning');
                }
            }

        } else {
             // Caso 2c: La API de facturas falló (allInvoices es null)
             togglePaymentFields(false, "API de facturas no disponible. No se pudo cargar métodos de pago.");
             if(loadSuccess) {
                 showMessage("Advertencia: No se pudieron cargar los métodos de pago. Verifique la API.", 'warning');
             }
        }


        // 3. Finalización
        // Solo inicializar Select2 y calcular si los datos críticos se cargaron
        if (loadSuccess) {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "Seleccione una opción",
                allowClear: true
            });
            calculateTotals();
        }
    }

    // Llena el select de PRODUCTOS con precio limpio en data-price
    function populateProductSelect(selectElement, data) {
        selectElement.innerHTML = '<option value="">Seleccione un producto</option>'; // Limpiar antes de poblar
        data.forEach(item => {
            const option = document.createElement('option');
            const precio = parseFloat(item.Precio_Venta || 0);
            option.value = item.Cod_Producto;
            option.textContent = `${item.Nombre_Producto} - L. ${formatPrice(precio)}`;
            option.dataset.precio = precio;
            selectElement.appendChild(option);
        });
    }

    // Llena el select de TRATAMIENTOS con precio limpio en data-price (Usando Precio_Estandar)
    function populateTreatmentSelect(selectElement, data) {
        selectElement.innerHTML = '<option value="">Seleccione un tratamiento</option>'; // Limpiar antes de poblar
        data.forEach(item => {
            const option = document.createElement('option');
            const precio = parseFloat(item.Precio_Estandar || 0);
            option.value = item.Cod_Tratamiento;
            option.textContent = `${item.Nombre_Tratamiento} - L. ${formatPrice(precio)}`;
            option.dataset.precio = precio;
            selectElement.appendChild(option);
        });
    }

    // Llena el Datalist con Nombre y Apellido
    function populateDatalist(datalistElement, data) {
        datalistElement.innerHTML = '';
        data.forEach(client => {
            const option = document.createElement('option');
            option.value = `${client.Nombre} ${client.Apellido}`;
            option.dataset.codCliente = client.Cod_Cliente;
            datalistElement.appendChild(option);
        });
    }

    // Maneja la selección del cliente desde el datalist
    function handleClientSearch(searchValue) {
        const selectedClient = clientsData.find(client =>
            `${client.Nombre} ${client.Apellido}`.trim() === searchValue.trim()
        );

        const codClienteInput = document.getElementById('cod_cliente_seleccionado');

        if (selectedClient) {
            // Asigna el Cod_Cliente al campo oculto para el envío al servidor
            codClienteInput.value = selectedClient.Cod_Cliente;
            // Actualiza el placeholder solo para feedback visual
            codClienteInput.placeholder = `Cod: ${selectedClient.Cod_Cliente} | DNI: ${selectedClient.DNI || 'N/A'}`;
            // Remueve la validación
            codClienteInput.setCustomValidity("");
        } else {
            // Si no encuentra coincidencia exacta, limpia y marca como inválido
            codClienteInput.value = '';
            codClienteInput.placeholder = 'Seleccione un cliente válido de la lista';
            codClienteInput.setCustomValidity("Debe seleccionar un cliente de la lista desplegable.");
        }
    }


    // --- CALCULATION LOGIC ---

    // Calcula los totales de la factura
    function calculateTotals() {
        // 1. Obtener la tasa de ISV del input
        const isvRateInput = document.getElementById('isv_rate_input');
        // Convertir a float y dividir por 100 para obtener la tasa (e.g., 15.00 -> 0.15)
        const isvRate = parseFloat(isvRateInput.value) / 100 || 0;

        const productRows = document.querySelectorAll('#productos-details tr');
        const treatmentRows = document.querySelectorAll('#tratamientos-details tr');

        let subtotal = 0;

        // Sumar totales de Productos
        productRows.forEach(row => {
            const totalCell = row.querySelector('.total-product-cell');
            if (totalCell) {
                subtotal += parseFloat(totalCell.dataset.total || 0);
            }
        });

        // Sumar totales de Tratamientos
        treatmentRows.forEach(row => {
            const totalCell = row.querySelector('.total-treatment-cell');
            if (totalCell) {
                subtotal += parseFloat(totalCell.dataset.total || 0);
            }
        });

        currentSubtotal = subtotal;
        // 2. Usar la tasa dinámica
        const isv = subtotal * isvRate;
        const totalPagar = subtotal + isv;

        // Mostrar valores formateados
        document.getElementById('subtotal_display').textContent = formatPrice(subtotal);
        document.getElementById('isv_display').textContent = formatPrice(isv);
        document.getElementById('total_display').textContent = formatPrice(totalPagar);
    }

    // --- DETAIL MANAGEMENT (Sin cambios, solo llama a calculateTotals) ---

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
        // Usar el texto de la opción, eliminando el precio de la etiqueta
        const nombreProducto = selectedOption.textContent.substring(0, selectedOption.textContent.lastIndexOf(' - L.'));

        if (precioUnitario === 0) {
            showMessage("El precio unitario del producto es L. 0.00. Se agregará, pero verifique el catálogo.", 'info');
        }

        const total = precioUnitario * cantidad;
        const detailId = `product-${detailCounter++}`;

        const newRow = createDetailRow(detailId, nombreProducto, cantidad, precioUnitario, total, 'P', codProducto);

        document.getElementById('productos-details').appendChild(newRow);

        productSelect.value = '';
        $('#producto_id').val(null).trigger('change'); // Limpiar select2
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
        // Usar el texto de la opción, eliminando el precio de la etiqueta
        const nombreTratamiento = selectedOption.textContent.substring(0, selectedOption.textContent.lastIndexOf(' - L.'));

        if (precioUnitario === 0) {
            showMessage("El precio unitario del tratamiento es L. 0.00. Se agregará, pero verifique el catálogo.", 'info');
        }

        const total = precioUnitario * cantidad;
        const detailId = `treatment-${detailCounter++}`;

        const newRow = createDetailRow(detailId, nombreTratamiento, cantidad, precioUnitario, total, 'T', codTratamiento);

        document.getElementById('tratamientos-details').appendChild(newRow);

        treatmentSelect.value = '';
        $('#tratamiento_id').val(null).trigger('change'); // Limpiar select2
        quantityInput.value = 1;
        calculateTotals();
    }

    // Se modificó para recibir explícitamente el 'cod'
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
        // Datos ocultos para el envío al servidor
        newRow.dataset.type = type;
        newRow.dataset.cod = cod; // Usar el código real
        newRow.dataset.cantidad = quantity;
        newRow.dataset.precio = unitPrice;

        return newRow;
    }

    function removeDetail(id) {
        document.getElementById(id).remove();
        calculateTotals();
    }

    // --- FORM SUBMISSION ---

    // Manejador del envío del formulario
    document.getElementById('invoice-form').addEventListener('submit', handleFormSubmit);

    async function handleFormSubmit(event) {
        event.preventDefault();

        // 1. Obtener la tasa ISV actual (como porcentaje, ej: 15.00)
        const isvRateValue = parseFloat(document.getElementById('isv_rate_input').value) / 100 || 0;

        const codCliente = document.getElementById('cod_cliente_seleccionado').value;
        if (!codCliente) {
            showMessage("Debe seleccionar un cliente válido de la lista.", 'warning');
            return;
        }

        // Validación: Asegurar que los campos de pago no estén deshabilitados
        if (document.getElementById('metodo_pago').disabled || document.getElementById('estado_pago').disabled) {
             showMessage("Error: Los métodos de pago no están disponibles. Recargue la página si el problema persiste.", 'error');
             return;
        }

        const allDetails = Array.from(document.querySelectorAll('#productos-details tr, #tratamientos-details tr'));
        if (allDetails.length === 0) {
            showMessage("Debe agregar al menos un producto o tratamiento para facturar.", 'warning');
            return;
        }

        // Obtener los valores de la cabecera
        const metodoPago = document.getElementById('metodo_pago').value;
        const estadoPago = document.getElementById('estado_pago').value;
        const fechaEmision = document.getElementById('fecha_emision').value;
        const servicioMedico = document.getElementById('servicio_medico').value;
        const observacion = document.getElementById('observacion').value;

        // CÁLCULO DE TOTALES
        const subTotal = currentSubtotal;
        const totalISV = subTotal * isvRateValue;
        const totalPagar = subTotal + totalISV;

        // Validar que se hayan seleccionado
        if (!metodoPago || !estadoPago) {
             showMessage("Debe seleccionar el Método de Pago y el Estado del Pago.", 'warning');
             return;
        }

        // Estado de carga ON
        const spinner = document.getElementById('loading-spinner');
        spinner.style.display = 'inline-block';
        document.getElementById('submit-text').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        toggleSubmitButton(true);

        try {
            // 2. CÁLCULO Y COLECCIÓN DE DETALLES
            const detallesProducto = [];
            const detallesTratamiento = [];

            allDetails.forEach(row => {
                const type = row.dataset.type; // 'P' or 'T'
                const cod = row.dataset.cod;
                const cantidad = parseFloat(row.dataset.cantidad);
                const precio = parseFloat(row.dataset.precio);
                const totalDetalle = cantidad * precio;

                if (type === 'P') {
                    detallesProducto.push({
                        Cod_Producto: parseInt(cod),
                        Cantidad: cantidad,
                        Precio_Unitario: precio,
                        Total_Detalle: totalDetalle
                    });
                } else if (type === 'T') {
                    detallesTratamiento.push({
                        Cod_Tratamiento: parseInt(cod),
                        Sesiones: cantidad,
                        Costo: totalDetalle,
                        Precio_Unitario: precio, // Incluir por consistencia
                    });
                }
            });

            // 3. Creación del objeto de datos principal (CABECERA + DETALLES + CÁLCULOS)
            // Este objeto es el que debe pasar la validación en Laravel
            const invoiceData = {
                // CAMPOS DE CABECERA (Los que se registrarán en la tabla Factura)
                Cod_Cliente: parseInt(codCliente),
                Fecha_Factura: fechaEmision,
                Total_Factura: totalPagar,
                Metodo_Pago: metodoPago,
                Estado_Pago: estadoPago,
                Descuento_Aplicado: 0.00, // Hardcodeado por ahora

                // CAMPOS CALCULADOS (Requeridos por FacturaStoreRequest para validar)
                // Usando snake_case como se corrigió en el Request
                sub_total_calculado: subTotal,
                isv_calculado: totalISV,

                // CAMPOS ADICIONALES (Para la API y/o historial)
                Observacion: observacion,
                Servicio_Medico: servicioMedico,

                // DETALLES (Requeridos por FacturaStoreRequest para validar y por la API externa)
                detalles_producto: detallesProducto,
                detalles_tratamiento: detallesTratamiento,
            };

            console.log("Datos completos a enviar:", invoiceData);


            // 4. Envío a Laravel (Proxy)
            const response = await fetch('{{ route("factura.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    // CORRECCIÓN CLAVE para evitar el error de JSON con HTML
                    'Accept': 'application/json'
                },
                body: JSON.stringify(invoiceData)
            });

            // Manejo de Errores de Laravel/API
            if (response.status === 422) {
                const errorData = await response.json();
                let validationErrors = "<ul>";
                for (const field in errorData.errors) {
                    // Aquí se mostrarán los mensajes amigables definidos en attributes()
                    validationErrors += `<li>${errorData.errors[field].join(', ')}</li>`;
                }
                validationErrors += "</ul>";
                // Este throw dispara el catch que muestra el mensaje de error.
                throw new Error(`Error de Validación:<br>${validationErrors}`);
            }

            if (!response.ok) {
                 const errorText = await response.text();
                 let message = `Error inesperado al guardar la factura (HTTP ${response.status}).`;
                 try {
                    const errorJson = JSON.parse(errorText);
                    // Intentar extraer el mensaje de error de la API o el controlador
                    message = errorJson.message || errorJson.api_error?.message || errorJson.error || message;
                 } catch (e) {
                     // Error es HTML (Page Expired, etc.) o texto plano
                    message = `Error de conexión o de seguridad. Verifique su sesión o la API de Node.js. Respuesta: ${errorText.substring(0, 100)}...`;
                 }

                 throw new Error(message);
            }

            const result = await response.json();

            // 5. Éxito Final
            const codFactura = result.Cod_Factura || result.id || 'N/A';
            showMessage(`Factura #${codFactura} generada con éxito!`, 'success');

            // Redirigir tras un breve retraso
            setTimeout(() => window.location.href = '{{ route("factura.index") }}', 2000);

        } catch (error) {
            console.error("Error completo en el proceso de facturación:", error);
            showMessage(`Error al procesar la factura: ${error.message}`, 'error');
        } finally {
            // Estado de carga OFF
            spinner.style.display = 'none';
            document.getElementById('submit-text').innerHTML = '<i class="fas fa-file-export me-1"></i> Emitir Factura';
            // Re-habilitar el botón si hay cliente seleccionado y los campos de pago no fallaron
            if (document.getElementById('cod_cliente_seleccionado').value && !document.getElementById('metodo_pago').disabled) {
                 toggleSubmitButton(false);
            }
        }
    }

    // --- INICIALIZACIÓN ---
    // Listener para que la función handleClientSearch se ejecute al cambiar el valor del input
    document.getElementById('search_cliente').addEventListener('change', (e) => handleClientSearch(e.target.value));
    document.addEventListener('DOMContentLoaded', loadInitialData);
</script>
@stop
