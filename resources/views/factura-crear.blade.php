@extends('adminlte::page')

@section('title', 'Crear Factura')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-plus"></i> Crear Nueva Factura
            <small>Generar factura para paciente</small>
        </h1>
        <a href="{{ route('factura') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar a Facturas
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">
                    <i class="fas fa-file-invoice mr-2"></i>Información de la Factura
                </h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_factura">Número de Factura</label>
                                <input type="text" class="form-control" id="numero_factura" value="#F-005" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control" id="fecha" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="paciente">Seleccionar Paciente</label>
                        <select class="form-control" id="paciente">
                            <option value="">Seleccionar paciente...</option>
                            <option value="1">María González - Cédula: 0801199012345</option>
                            <option value="2">Ana Martínez - Cédula: 0801198567890</option>
                            <option value="3">Carmen López - Cédula: 0801197123456</option>
                            <option value="4">Sofía Rivera - Cédula: 0801196789012</option>
                        </select>
                    </div>

                    <hr>

                    <h5><i class="fas fa-list mr-2"></i>Servicios</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="servicios-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Servicio/Tratamiento</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Total</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control servicio-select">
                                            <option>Seleccionar servicio...</option>
                                            <option data-precio="850">Botox Facial - $850.00</option>
                                            <option data-precio="120">Limpieza Facial - $120.00</option>
                                            <option data-precio="450">Relleno Labial - $450.00</option>
                                            <option data-precio="300">Peeling Químico - $300.00</option>
                                            <option data-precio="200">Hidratación Facial - $200.00</option>
                                            <option data-precio="600">Tratamiento Antiarrugas - $600.00</option>
                                            <option value="custom">📝 Servicio Personalizado</option>
                                        </select>
                                        <input type="text" class="form-control servicio-custom mt-2" placeholder="Escribir nombre del servicio..." style="display: none;">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control cantidad" value="1" min="1">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control precio" step="0.01" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control total" step="0.01" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm eliminar-fila">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <button type="button" class="btn btn-success" id="agregar-servicio">
                            <i class="fas fa-plus"></i> Agregar Servicio
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" id="observaciones" rows="3" placeholder="Notas adicionales sobre los servicios..."></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Resumen de la factura -->
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title text-white">
                    <i class="fas fa-calculator mr-2"></i>Resumen
                </h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>ISV (15%):</span>
                    <span id="isv">$0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Total a Pagar:</strong>
                    <strong id="total-final" class="text-success">$0.00</strong>
                </div>
            </div>
        </div>

        <!-- Información del paciente -->
        <div class="card mt-3">
            <div class="card-header bg-info">
                <h3 class="card-title text-white">
                    <i class="fas fa-user mr-2"></i>Datos del Paciente
                </h3>
            </div>
            <div class="card-body" id="info-paciente">
                <p class="text-muted">Seleccione un paciente para ver sus datos</p>
            </div>
        </div>

        <!-- Acciones -->
        <div class="card mt-3">
            <div class="card-body">
                <button type="button" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-save mr-2"></i>Guardar Factura
                </button>
                <button type="button" class="btn btn-success btn-block mb-2" id="imprimir-factura">
                    <i class="fas fa-print mr-2"></i>Guardar e Imprimir
                </button>
                <a href="{{ route('factura') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .card-header {
            border-bottom: 1px solid #dee2e6;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        #total-final {
            font-size: 1.2em;
        }
        
        /* Estilos para la factura imprimible */
        .factura-print {
            display: none;
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px;
        }
        
        @media print {
            /* Ocultar todo el contenido de la página */
            .content-wrapper,
            .main-sidebar,
            .main-header,
            .main-footer,
            nav,
            .navbar {
                display: none !important;
            }
            
            /* Mostrar solo la factura */
            .factura-print {
                display: block !important;
                position: static !important;
                margin: 0 !important;
                padding: 20px !important;
            }
            
            /* Resetear estilos para impresión */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            body {
                margin: 0;
                padding: 0;
                background: white !important;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Datos de pacientes (en un proyecto real esto vendría de la base de datos)
            const pacientes = {
                '1': {
                    nombre: 'María González',
                    cedula: '0801199012345',
                    telefono: '+504 9876-5432',
                    email: 'maria.gonzalez@email.com'
                },
                '2': {
                    nombre: 'Ana Martínez',
                    cedula: '0801198567890',
                    telefono: '+504 8765-4321',
                    email: 'ana.martinez@email.com'
                },
                '3': {
                    nombre: 'Carmen López',
                    cedula: '0801197123456',
                    telefono: '+504 7654-3210',
                    email: 'carmen.lopez@email.com'
                },
                '4': {
                    nombre: 'Sofía Rivera',
                    cedula: '0801196789012',
                    telefono: '+504 6543-2109',
                    email: 'sofia.rivera@email.com'
                }
            };

            // Mostrar información del paciente seleccionado
            $('#paciente').change(function() {
                const pacienteId = $(this).val();
                if (pacienteId && pacientes[pacienteId]) {
                    const p = pacientes[pacienteId];
                    $('#info-paciente').html(`
                        <h6>${p.nombre}</h6>
                        <p class="mb-1"><strong>Cédula:</strong> ${p.cedula}</p>
                        <p class="mb-1"><strong>Teléfono:</strong> ${p.telefono}</p>
                        <p class="mb-0"><strong>Email:</strong> ${p.email}</p>
                    `);
                } else {
                    $('#info-paciente').html('<p class="text-muted">Seleccione un paciente para ver sus datos</p>');
                }
            });

            // Actualizar precio cuando se selecciona un servicio
            $(document).on('change', '.servicio-select', function() {
                const fila = $(this).closest('tr');
                const customInput = fila.find('.servicio-custom');
                
                if ($(this).val() === 'custom') {
                    // Mostrar campo personalizado
                    customInput.show().focus();
                    fila.find('.precio').val('').removeAttr('readonly');
                } else {
                    // Ocultar campo personalizado y usar precio predefinido
                    customInput.hide();
                    const precio = $(this).find(':selected').data('precio') || 0;
                    fila.find('.precio').val(precio);
                }
                
                actualizarTotal(fila);
            });

            // Actualizar total cuando cambia la cantidad o el precio
            $(document).on('input', '.cantidad, .precio', function() {
                actualizarTotal($(this).closest('tr'));
            });

            // Eliminar fila
            $(document).on('click', '.eliminar-fila', function() {
                if ($('#servicios-table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    actualizarResumen();
                } else {
                    alert('Debe mantener al menos un servicio.');
                }
            });

            // Agregar nueva fila de servicio
            $('#agregar-servicio').click(function() {
                const nuevaFila = `
                    <tr>
                        <td>
                            <select class="form-control servicio-select">
                                <option>Seleccionar servicio...</option>
                                <option data-precio="850">Botox Facial - $850.00</option>
                                <option data-precio="120">Limpieza Facial - $120.00</option>
                                <option data-precio="450">Relleno Labial - $450.00</option>
                                <option data-precio="300">Peeling Químico - $300.00</option>
                                <option data-precio="200">Hidratación Facial - $200.00</option>
                                <option data-precio="600">Tratamiento Antiarrugas - $600.00</option>
                                <option value="custom">📝 Servicio Personalizado</option>
                            </select>
                            <input type="text" class="form-control servicio-custom mt-2" placeholder="Escribir nombre del servicio..." style="display: none;">
                        </td>
                        <td>
                            <input type="number" class="form-control cantidad" value="1" min="1">
                        </td>
                        <td>
                            <input type="number" class="form-control precio" step="0.01" placeholder="0.00">
                        </td>
                        <td>
                            <input type="number" class="form-control total" step="0.01" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm eliminar-fila">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#servicios-table tbody').append(nuevaFila);
            });

            function actualizarTotal(fila) {
                const cantidad = parseFloat(fila.find('.cantidad').val()) || 0;
                const precio = parseFloat(fila.find('.precio').val()) || 0;
                const total = cantidad * precio;
                fila.find('.total').val(total.toFixed(2));
                actualizarResumen();
            }

            function actualizarResumen() {
                let subtotal = 0;
                $('.total').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });
                
                const isv = subtotal * 0.15;
                const totalFinal = subtotal + isv;
                
                $('#subtotal').text('$' + subtotal.toFixed(2));
                $('#isv').text('$' + isv.toFixed(2));
                $('#total-final').text('$' + totalFinal.toFixed(2));
            }

            // Funcionalidad de impresión
            $('#imprimir-factura').click(function() {
                // Validar que haya al menos un servicio y un paciente
                if ($('#paciente').val() === '') {
                    alert('Por favor seleccione un paciente antes de imprimir.');
                    return;
                }

                let hayServicios = false;
                $('.total').each(function() {
                    if (parseFloat($(this).val()) > 0) {
                        hayServicios = true;
                        return false;
                    }
                });

                if (!hayServicios) {
                    alert('Por favor agregue al menos un servicio con precio antes de imprimir.');
                    return;
                }

                generarFacturaImprimible();
            });

            function generarFacturaImprimible() {
                const pacienteId = $('#paciente').val();
                const pacienteNombre = $('#paciente option:selected').text();
                const pacienteData = pacientes[pacienteId];
                const numeroFactura = $('#numero_factura').val();
                const fecha = $('#fecha').val();
                const observaciones = $('#observaciones').val();

                // Generar tabla de servicios
                let serviciosHTML = '';
                let subtotal = 0;

                $('#servicios-table tbody tr').each(function() {
                    const fila = $(this);
                    const servicioSelect = fila.find('.servicio-select');
                    const servicioCustom = fila.find('.servicio-custom');
                    const cantidad = fila.find('.cantidad').val();
                    const precio = fila.find('.precio').val();
                    const total = fila.find('.total').val();

                    if (parseFloat(total) > 0) {
                        let nombreServicio;
                        if (servicioSelect.val() === 'custom' && servicioCustom.is(':visible')) {
                            nombreServicio = servicioCustom.val() || 'Servicio personalizado';
                        } else {
                            nombreServicio = servicioSelect.find(':selected').text().split(' - ')[0];
                        }

                        serviciosHTML += `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">${nombreServicio}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${cantidad}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(precio).toFixed(2)}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(total).toFixed(2)}</td>
                            </tr>
                        `;
                        subtotal += parseFloat(total);
                    }
                });

                const isv = subtotal * 0.15;
                const totalFinal = subtotal + isv;

                // Crear HTML de la factura
                const facturaHTML = `
                    <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #d4a574; padding-bottom: 20px;">
                        <h1 style="color: #d4a574; margin: 0; font-size: 28px;">CLÍNICA ESTÉTICA SALUS</h1>
                        <p style="margin: 5px 0; font-size: 14px;">Dirección: Tegucigalpa, Francisco Morazán</p>
                        <p style="margin: 5px 0; font-size: 14px;">Teléfono: +504 0000-0000 | Email: info@salus.hn</p>
                    </div>

                    <div style="display: table; width: 100%; margin-bottom: 30px;">
                        <div style="display: table-cell; width: 50%; vertical-align: top;">
                            <h2 style="color: #333; margin-bottom: 15px; font-size: 22px;">FACTURA</h2>
                            <p style="margin: 8px 0;"><strong>Número:</strong> ${numeroFactura}</p>
                            <p style="margin: 8px 0;"><strong>Fecha:</strong> ${new Date(fecha).toLocaleDateString('es-HN')}</p>
                        </div>
                        <div style="display: table-cell; width: 50%; vertical-align: top; text-align: right;">
                            <h3 style="color: #d4a574; margin-bottom: 15px; font-size: 18px;">DATOS DEL PACIENTE</h3>
                            <p style="margin: 8px 0;"><strong>${pacienteData.nombre}</strong></p>
                            <p style="margin: 8px 0;">Cédula: ${pacienteData.cedula}</p>
                            <p style="margin: 8px 0;">Teléfono: ${pacienteData.telefono}</p>
                            <p style="margin: 8px 0;">Email: ${pacienteData.email}</p>
                        </div>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <thead>
                            <tr style="background-color: #d4a574; color: white;">
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Servicio/Tratamiento</th>
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Cantidad</th>
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Precio Unitario</th>
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${serviciosHTML}
                        </tbody>
                    </table>

                    <div style="text-align: right;">
                        <div style="display: inline-block; min-width: 300px; border: 1px solid #ddd; padding: 15px;">
                            <div style="display: table; width: 100%; margin-bottom: 8px;">
                                <span style="display: table-cell;">Subtotal:</span>
                                <span style="display: table-cell; text-align: right;">${subtotal.toFixed(2)}</span>
                            </div>
                            <div style="display: table; width: 100%; margin-bottom: 8px;">
                                <span style="display: table-cell;">ISV (15%):</span>
                                <span style="display: table-cell; text-align: right;">${isv.toFixed(2)}</span>
                            </div>
                            <div style="display: table; width: 100%; border-top: 2px solid #d4a574; padding-top: 8px; font-weight: bold; font-size: 18px;">
                                <span style="display: table-cell;">TOTAL A PAGAR:</span>
                                <span style="display: table-cell; text-align: right; color: #28a745;">${totalFinal.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>

                    ${observaciones ? `
                    <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #d4a574;">
                        <h4 style="margin: 0 0 10px 0; color: #d4a574;">Observaciones:</h4>
                        <p style="margin: 0;">${observaciones}</p>
                    </div>
                    ` : ''}

                    <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666;">
                        <p>Gracias por confiar en nosotros para su cuidado estético</p>
                        <p>Esta factura fue generada el ${new Date().toLocaleString('es-HN')}</p>
                    </div>
                `;

                // Eliminar factura anterior si existe
                $('.factura-print').remove();
                
                // Agregar la nueva factura al body
                $('body').append(`<div class="factura-print">${facturaHTML}</div>`);
                
                // Imprimir después de un pequeño delay
                setTimeout(() => {
                    window.print();
                    // Remover después de imprimir
                    setTimeout(() => {
                        $('.factura-print').remove();
                    }, 1000);
                }, 100);
            }
        });
    </script>
@stop