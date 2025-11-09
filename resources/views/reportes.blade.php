@extends('adminlte::page')

@section('title', 'Reportes')

@section('content_header')
    {{-- Header personalizado --}}
@stop

@section('content')
    <div class="container-fluid p-0">
        <div class="welcome-header">
            <div class="welcome-content">
                <h1>Módulo de Reportes</h1>
                <p>Gestiona y visualiza todos los reportes de tu clínica estética.</p>
            </div>
            <div class="welcome-date">
                {{-- Establecer el locale para esta instancia de Carbon (solo si no funciona la configuración global) --}}
                @php
                    use Carbon\Carbon;
                    // Si la configuración global (config/app.php) no funciona, esto la fuerza
                    Carbon::setLocale('es'); 
                @endphp
    
                {{-- Formato en Español y con la zona horaria correcta --}}
                <span class="date">{{ Carbon::now()->translatedFormat('l, d \d\e F Y') }}</span>
    
                {{-- Hora local --}}
                <span class="time">{{ Carbon::now()->format('H:i') }}</span>
            </div>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="row mt-4">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card blue-border">
                    <div class="stat-header">
                        <span class="stat-icon blue-bg">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <span class="stat-label">CITAS REGISTRADAS</span>
                    </div>
                    <div class="stat-value">{{ count($citas) }}</div>
                    <div class="stat-change positive">
                        <span>En el periodo seleccionado</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card green-border">
                    <div class="stat-header">
                        <span class="stat-icon green-bg">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <span class="stat-label">INGRESOS TOTALES</span>
                    </div>
                    <div class="stat-value">${{ number_format($ingresos_totales ?? 0, 2) }}</div>
                    <div class="stat-change positive">
                        <span>Periodo actual</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card yellow-border">
                    <div class="stat-header">
                        <span class="stat-icon yellow-bg">
                            <i class="fas fa-box"></i>
                        </span>
                        <span class="stat-label">PRODUCTOS STOCK</span>
                    </div>
                    <div class="stat-value">{{ $productos_stock ?? 0 }}</div>
                    <div class="stat-change negative">
                        <span>En inventario</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-section mb-4">
            <h3 class="section-title mb-4">Filtros de Búsqueda</h3>
            <form method="POST" action="{{ route('reportes.consultar') }}" id="filtro-reporte-form">
                @csrf
                <input type="hidden" name="tipo" id="reporte-tipo-activo" value="{{ $tipo ?? 'citas' }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" value="{{ isset($fecha_inicio) ? \Carbon\Carbon::parse($fecha_inicio)->format('Y-m-d') : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" value="{{ isset($fecha_fin) ? \Carbon\Carbon::parse($fecha_fin)->format('Y-m-d') : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-salus btn-block">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @section('js')
            <script>
            // ... (Tu función showNotification y estilos)

            // Lógica para actualizar el campo 'tipo' oculto y la pestaña activa
            const reportTabs = document.getElementById('reportTabs');
            const reporteTipoActivoInput = document.getElementById('reporte-tipo-activo');
        
            // 1. Almacena el tipo activo al cambiar de pestaña
            reportTabs.addEventListener('click', function(e) {
                if (e.target.closest('.nav-link')) {
                    const tipo = e.target.getAttribute('href').replace('#', '');
                    reporteTipoActivoInput.value = tipo;
                    // Opcional: Si el controlador retorna un tipo, haz que esa sea la pestaña activa
               }
            });
        
            // 2. Asegúrate de que la pestaña activa sea la que se consultó
            // Esto es necesario para que al cargar los resultados, se muestre la pestaña correcta.
            let tipoActual = '{{ $tipo ?? 'citas' }}';
    
            // **APLICAR CORRECCIÓN:** Si el tipo actual es 'tratamientos', forzarlo a 'citas' (el primer reporte disponible).
            if (tipoActual === 'tratamientos') {
                tipoActual = 'citas';
            }

            document.querySelectorAll('#reportTabs .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').replace('#', '') === tipoActual) {
                    link.classList.add('active');
                    // Activar el contenido del tab
                    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
                    document.getElementById(tipoActual).classList.add('show', 'active');
                }
            });
        
            // 3. Confirmación antes de exportar
            document.querySelectorAll('a[href*="exportar"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // 1. CAPTURAR el formato (excel o pdf) del enlace original
                    let href = this.getAttribute('href');
                    // Esto crea un objeto de parámetros a partir de la URL actual
                    const urlParams = new URLSearchParams(href.split('?')[1] || '');
                    const formato = urlParams.get('formato'); // Obtiene 'excel' o 'pdf'
        
                    // 2. Obtener los valores actuales de los filtros
                    const fecha_inicio = document.querySelector('input[name="fecha_inicio"]').value;
                    const fecha_fin = document.querySelector('input[name="fecha_fin"]').value;
                    const tipo = reporteTipoActivoInput.value;

                    //let href = this.getAttribute('href');
                    // 3. Limpiar la URL y RECONSTRUIRLA con el formato capturado
                    href = href.split('?')[0]; // Limpiar cualquier query string anterior
                    const newUrl = `${href}?tipo=${tipo}&formato=${formato}&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`;
                    
                    // 4. Redireccionar con la nueva URL
                    this.setAttribute('href', newUrl);
                    window.location.href = newUrl;

                showNotification('Generando reporte...', 'info');
            });
        });
    </script>
@endsection

        <!-- Pestañas de Reportes -->
        <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar" style="color: #c9a876; margin-right: 0.5rem;"></i>
                    Reportes Disponibles
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-tabs custom-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#citas" role="tab">
                            <i class="fas fa-calendar-check mr-1"></i> Citas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#financiero" role="tab">
                            <i class="fas fa-dollar-sign mr-1"></i> Financiero
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#inventario" role="tab">
                            <i class="fas fa-box mr-1"></i> Inventario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#compras" role="tab">
                            <i class="fas fa-shopping-cart mr-1"></i> Compras
                        </a>
                    </li>
                </ul>

                <div class="tab-content p-4">
                    <!-- Reporte de Citas -->
                    <div class="tab-pane fade show active" id="citas" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">
                                <i class="fas fa-calendar-check" style="color: #c9a876;"></i> Reporte de Citas
                            </h4>
                            <div>
                                <a href="{{ route('reportes.exportar', ['tipo' => 'citas', 'formato' => 'excel']) }}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Excel
                                </a>
                                <a href="{{ route('reportes.exportar', ['tipo' => 'citas', 'formato' => 'pdf']) }}" 
                                   class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf mr-1"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @php $citas = is_array($citas) ? $citas : []; @endphp
                            <table class="table table-hover table-striped modern-table">
                                <thead>
                                    <tr>
                                        @foreach(array_keys($citas[0] ?? []) as $col)
                                            <th>{{ ucfirst($col) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($citas as $fila)
                                        <tr>
                                            @foreach($fila as $valor)
                                                <td>{{ $valor }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count(array_keys($citas[0] ?? [])) }}" class="text-center text-muted">
                                                No hay datos disponibles
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Reporte Financiero -->
                    <div class="tab-pane fade" id="financiero" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">
                                <i class="fas fa-dollar-sign" style="color: #c9a876;"></i> Reporte Financiero
                            </h4>
                            <div>
                                <a href="{{ route('reportes.exportar', ['tipo' => 'financiero', 'formato' => 'excel']) }}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Excel
                                </a>
                                <a href="{{ route('reportes.exportar', ['tipo' => 'financiero', 'formato' => 'pdf']) }}" 
                                   class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf mr-1"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @php $financiero = is_array($financiero) ? $financiero : []; @endphp
                            <table class="table table-hover table-striped modern-table">
                                <thead>
                                    <tr>
                                        @foreach(array_keys($financiero[0] ?? []) as $col)
                                            <th>{{ ucfirst($col) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($financiero as $fila)
                                        <tr>
                                            @foreach($fila as $valor)
                                                <td>{{ $valor }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count(array_keys($financiero[0] ?? [])) }}" class="text-center text-muted">
                                                No hay datos disponibles
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Reporte de Inventario -->
                    <div class="tab-pane fade" id="inventario" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">
                                <i class="fas fa-box" style="color: #c9a876;"></i> Reporte de Inventario
                            </h4>
                            <div>
                                <a href="{{ route('reportes.exportar', ['tipo' => 'inventario', 'formato' => 'excel']) }}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Excel
                                </a>
                                <a href="{{ route('reportes.exportar', ['tipo' => 'inventario', 'formato' => 'pdf']) }}" 
                                   class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf mr-1"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @php $inventario = is_array($inventario) ? $inventario : []; @endphp
                            <table class="table table-hover table-striped modern-table">
                                <thead>
                                    <tr>
                                        @foreach(array_keys($inventario[0] ?? []) as $col)
                                            <th>{{ ucfirst($col) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inventario as $fila)
                                        <tr>
                                            @foreach($fila as $valor)
                                                <td>{{ $valor }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count(array_keys($inventario[0] ?? [])) }}" class="text-center text-muted">
                                                No hay datos disponibles
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Reporte de Compras -->
                    <div class="tab-pane fade" id="compras" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">
                                <i class="fas fa-shopping-cart" style="color: #c9a876;"></i> Reporte de Compras
                            </h4>
                            <div>
                                <a href="{{ route('reportes.exportar', ['tipo' => 'compras', 'formato' => 'excel']) }}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Excel
                                </a>
                                <a href="{{ route('reportes.exportar', ['tipo' => 'compras', 'formato' => 'pdf']) }}" 
                                   class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf mr-1"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @php $compras = is_array($compras) ? $compras : []; @endphp
                            <table class="table table-hover table-striped modern-table">
                                <thead>
                                    <tr>
                                        @foreach(array_keys($compras[0] ?? []) as $col)
                                            <th>{{ ucfirst($col) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($compras as $fila)
                                        <tr>
                                            @foreach($fila as $valor)
                                                <td>{{ $valor }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count(array_keys($compras[0] ?? [])) }}" class="text-center text-muted">
                                                No hay datos disponibles
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Variables de colores */
        :root {
            --primary-color: #c9a876;
            --secondary-color: #2c3e50;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
        }

        /* Header de bienvenida */
        .welcome-header {
            background: linear-gradient(135deg, var(--primary-color), #b89968);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(201, 168, 118, 0.3);
            margin-bottom: 2rem;
        }

        .welcome-content h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-content p {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
        }

        .welcome-date {
            text-align: right;
        }

        .welcome-date .date {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .welcome-date .time {
            display: block;
            font-size: 2rem;
            font-weight: 700;
        }

        /* Tarjetas de estadísticas */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-card.blue-border { border-left-color: var(--info-color); }
        .stat-card.green-border { border-left-color: var(--success-color); }
        .stat-card.yellow-border { border-left-color: var(--warning-color); }
        .stat-card.red-border { border-left-color: var(--danger-color); }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.blue-bg { background: var(--info-color); }
        .stat-icon.green-bg { background: var(--success-color); }
        .stat-icon.yellow-bg { background: var(--warning-color); }
        .stat-icon.red-bg { background: var(--danger-color); }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .stat-change.positive { color: var(--success-color); }
        .stat-change.negative { color: var(--danger-color); }

        /* Sección de filtros */
        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            color: var(--secondary-color);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .custom-input {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
        }

        .custom-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(201, 168, 118, 0.1);
        }

        .btn-salus {
            background: linear-gradient(135deg, var(--primary-color), #b89968);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-salus:hover {
            background: linear-gradient(135deg, #b89968, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(201, 168, 118, 0.3);
            color: white;
        }

        /* Card moderna */
        .modern-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .modern-card .card-header {
            background: white;
            border-bottom: 2px solid #f3f4f6;
            padding: 1.5rem;
        }

        .modern-card .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin: 0;
        }

        /* Pestañas personalizadas */
        .custom-tabs {
            border-bottom: 2px solid #e5e7eb;
            padding: 0 1.5rem;
            background: #f9fafb;
        }

        .custom-tabs .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 600;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .custom-tabs .nav-link:hover {
            color: var(--primary-color);
            background: rgba(201, 168, 118, 0.05);
        }

        .custom-tabs .nav-link.active {
            color: var(--primary-color);
            background: white;
            border-bottom-color: var(--primary-color);
        }

        /* Tabla moderna */
        .modern-table {
            margin-bottom: 0;
        }

        .modern-table thead th {
            background: #f9fafb;
            color: var(--secondary-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
            padding: 1rem;
        }

        .modern-table tbody tr {
            transition: all 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background: rgba(201, 168, 118, 0.05);
        }

        .modern-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #4b5563;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-header {
                flex-direction: column;
                text-align: center;
            }

            .welcome-date {
                text-align: center;
                margin-top: 1rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }
        }
    </style>
@endsection

@section('js')
    <script>
        // Función para mostrar notificaciones
        function showNotification(message, type = 'success') {
            const colors = {
                'success': 'linear-gradient(135deg, #10b981, #059669)',
                'error': 'linear-gradient(135deg, #ef4444, #dc2626)',
                'info': 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                'warning': 'linear-gradient(135deg, #f59e0b, #d97706)'
            };

            const icons = {
                'success': 'check-circle',
                'error': 'exclamation-circle',
                'info': 'info-circle',
                'warning': 'exclamation-triangle'
            };

            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 15px 25px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 1050;
                animation: slideIn 0.3s ease-out;
                display: flex;
                align-items: center;
                gap: 10px;
                max-width: 350px;
            `;
            
            notification.innerHTML = `
                <i class="fas fa-${icons[type]}"></i>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Animaciones
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Confirmación antes de exportar
        document.querySelectorAll('a[href*="exportar"]').forEach(link => {
            link.addEventListener('click', function(e) {
                showNotification('Generando reporte...', 'info');
            });
        });
    </script>
@endsection