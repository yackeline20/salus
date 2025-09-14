@extends('adminlte::page')

@section('title', 'Reportes')

@section('content_header')
    {{-- Header personalizado --}}
@stop

@section('content')
    <div class="container-fluid p-0">
        <div class="welcome-header">
            <div class="welcome-content">
                <h1>Reportes y Análisis</h1>
                <p>Gestiona y visualiza todos los reportes de tu clínica estética.</p>
            </div>
            <div class="welcome-date">
                <span class="date">{{ now()->format('l, d \d\e F Y') }}</span>
                <span class="time">{{ now()->format('H:i') }}</span>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card blue-border">
                    <div class="stat-header">
                        <span class="stat-icon blue-bg">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        <span class="stat-label">REPORTES GENERADOS</span>
                    </div>
                    <div class="stat-value">1,428</div>
                    <div class="stat-change positive">
                        <span>+12% este mes</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card green-border">
                    <div class="stat-header">
                        <span class="stat-icon green-bg">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <span class="stat-label">INGRESOS TOTALES</span>
                    </div>
                    <div class="stat-value">$128,450</div>
                    <div class="stat-change positive">
                        <span>+8.2% vs mes anterior</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card yellow-border">
                    <div class="stat-header">
                        <span class="stat-icon yellow-bg">
                            <i class="fas fa-box"></i>
                        </span>
                        <span class="stat-label">PRODUCTOS STOCK</span>
                    </div>
                    <div class="stat-value">3,847</div>
                    <div class="stat-change negative">
                        <span>-3.1% esta semana</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card red-border">
                    <div class="stat-header">
                        <span class="stat-icon red-bg">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <span class="stat-label">SERVICIOS ACTIVOS</span>
                    </div>
                    <div class="stat-value">124</div>
                    <div class="stat-change positive">
                        <span>+5.4% este mes</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-section mb-4">
            <h3 class="section-title mb-4">Filtros de Búsqueda</h3>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Buscar Reporte</label>
                        <input type="text" id="searchReport" class="form-control custom-input" placeholder="Buscar por nombre o código...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Área</label>
                        <select id="filterArea" class="form-control custom-input">
                            <option value="">Todas las áreas</option>
                            <option value="financiero">Financiero</option>
                            <option value="operativo">Operativo</option>
                            <option value="administrativo">Administrativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Fecha Desde</label>
                        <input type="date" id="dateFrom" class="form-control custom-input">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Fecha Hasta</label>
                        <input type="date" id="dateTo" class="form-control custom-input">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-salus btn-block" onclick="applyFilters()">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card modern-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line" style="color: #c9a876; margin-right: 0.5rem;"></i>
                            Reportes Financieros
                        </h3>
                        <button class="btn btn-outline-primary btn-sm" onclick="generateAllFinancialReports()">
                            <i class="fas fa-download mr-1"></i> Descargar Todos
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="report-list" id="financialReports">
                            <div class="report-item" data-type="financiero" data-name="ingresos-mensuales">
                                <div class="report-info">
                                    <h4>Reporte de Ingresos Mensuales</h4>
                                    <p>Análisis detallado de ingresos por servicios</p>
                                    <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-action btn-view" onclick="viewReport('ingresos-mensuales')" title="Ver Reporte">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-download" onclick="downloadReport('ingresos-mensuales', 'pdf')" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn-action btn-excel" onclick="downloadReport('ingresos-mensuales', 'excel')" title="Descargar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="report-item" data-type="financiero" data-name="gastos-operativos">
                                <div class="report-info">
                                    <h4>Reporte de Gastos Operativos</h4>
                                    <p>Control de gastos por categoría</p>
                                    <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-action btn-view" onclick="viewReport('gastos-operativos')" title="Ver Reporte">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-download" onclick="downloadReport('gastos-operativos', 'pdf')" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn-action btn-excel" onclick="downloadReport('gastos-operativos', 'excel')" title="Descargar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="report-item" data-type="financiero" data-name="balance-general">
                                <div class="report-info">
                                    <h4>Balance General</h4>
                                    <p>Estado financiero completo</p>
                                    <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-action btn-view" onclick="viewReport('balance-general')" title="Ver Reporte">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-download" onclick="downloadReport('balance-general', 'pdf')" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn-action btn-excel" onclick="downloadReport('balance-general', 'excel')" title="Descargar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="report-item" data-type="financiero" data-name="flujo-caja">
                                <div class="report-info">
                                    <h4>Flujo de Caja</h4>
                                    <p>Movimientos de efectivo del período</p>
                                    <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-action btn-view" onclick="viewReport('flujo-caja')" title="Ver Reporte">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-download" onclick="downloadReport('flujo-caja', 'pdf')" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn-action btn-excel" onclick="downloadReport('flujo-caja', 'excel')" title="Descargar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card modern-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list" style="color: #c9a876; margin-right: 0.5rem;"></i>
                            Reportes Operativos
                        </h3>
                        <button class="btn btn-outline-success btn-sm" onclick="generateAllOperationalReports()">
                            <i class="fas fa-download mr-1"></i> Descargar Todos
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="report-list" id="operationalReports">
                            <div class="report-item" data-type="operativo" data-name="citas-agendadas">
                                <div class="report-info">
                                    <h4>Reporte de Citas Agendadas</h4>
                                    <p>Resumen de citas por especialidad</p>
                                    <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-action btn-view" onclick="viewReport('citas-agendadas')" title="Ver Reporte">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-download" onclick="downloadReport('citas-agendadas', 'pdf')" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn-action btn-excel" onclick="downloadReport('citas-agendadas', 'excel')" title="Descargar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="report-item" data-type="operativo" data-name="inventario">
                                <div class="report-info">
                                    <h4>Reporte de Inventario</h4>
                                    <p>Existencias de productos</p>
                                    <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-action btn-view" onclick="viewReport('inventario')" title="Ver Reporte">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-download" onclick="downloadReport('inventario', 'pdf')" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn-action btn-excel" onclick="downloadReport('inventario', 'excel')" title="Descargar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="report-item" data-type="operativo" data-name="desempeno-empleados">
                                <div class="report-info">
                                    <h4>Desempeño de Empleados</h4>
                                    <p>Análisis de productividad</p>
                                    <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-action btn-view" onclick="viewReport('desempeno-empleados')" title="Ver Reporte">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-download" onclick="downloadReport('desempeno-empleados', 'pdf')" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn-action btn-excel" onclick="downloadReport('desempeno-empleados', 'excel')" title="Descargar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="report-item" data-type="operativo" data-name="satisfaccion-pacientes">
                                <div class="report-info">
                                    <h4>Satisfacción de Pacientes</h4>
                                    <p>Encuestas y evaluaciones del servicio</p>
                                    <small class="text-muted">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-action btn-view" onclick="viewReport('satisfaccion-pacientes')" title="Ver Reporte">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-download" onclick="downloadReport('satisfaccion-pacientes', 'pdf')" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn-action btn-excel" onclick="downloadReport('satisfaccion-pacientes', 'excel')" title="Descargar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-8 mb-3">
                <div class="card modern-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Ingresos por Mes</h3>
                        <div class="chart-controls">
                            <button class="btn btn-outline-info btn-sm" onclick="exportChart('chartIngresos', 'Ingresos_por_Mes')">
                                <i class="fas fa-camera mr-1"></i> Exportar Gráfico
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartIngresos" width="800" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card modern-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Distribución de Servicios</h3>
                        <div class="chart-controls">
                            <button class="btn btn-outline-info btn-sm" onclick="exportChart('chartServicios', 'Distribucion_Servicios')">
                                <i class="fas fa-camera mr-1"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartServicios" width="400" height="300"></canvas>
                        </div>
                        <div class="service-legend mt-3">
                            <div class="legend-item">
                                <span class="legend-color" style="background: #c9a876;"></span>
                                <span>Botox - 35%</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #d4b896;"></span>
                                <span>Rellenos - 25%</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #e5d4c1;"></span>
                                <span>Tratamientos Faciales - 20%</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #f5efe6;"></span>
                                <span>Otros - 20%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para vista previa de reportes -->
    <div class="modal fade" id="reportPreviewModal" tabindex="-1" role="dialog" aria-labelledby="reportPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportPreviewModalLabel">Vista Previa del Reporte</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="reportPreviewContent">
                    <!-- Contenido del reporte se carga aquí -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="downloadFromPreview">
                        <i class="fas fa-download mr-2"></i>Descargar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Reset de algunos estilos de AdminLTE */
        .content-wrapper {
            background: #f8f9fa !important;
        }

        /* Ajuste para prevenir espacio en blanco al final */
        .content {
            padding-bottom: 20px !important;
        }

        /* Header de bienvenida */
        .welcome-header {
            background: linear-gradient(135deg, #c9a876 0%, #d4b896 100%);
            border-radius: 20px;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(201, 168, 118, 0.2);
        }

        .welcome-content h1 {
            color: white;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .welcome-content p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            margin: 0;
        }

        .welcome-date {
            text-align: right;
            color: white;
        }

        .welcome-date .date {
            display: block;
            font-size: 14px;
            opacity: 0.9;
        }

        .welcome-date .time {
            display: block;
            font-size: 24px;
            font-weight: 600;
            margin-top: 5px;
        }

        /* Tarjetas de estadísticas */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            height: 100%;
            border-left: 4px solid;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card.blue-border { border-left-color: #5DADE2; }
        .stat-card.green-border { border-left-color: #52C41A; }
        .stat-card.yellow-border { border-left-color: #FFA500; }
        .stat-card.red-border { border-left-color: #FF6B6B; }

        .stat-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
            color: white;
        }

        .stat-icon.blue-bg { background: linear-gradient(135deg, #5DADE2, #3498DB); }
        .stat-icon.green-bg { background: linear-gradient(135deg, #52C41A, #73D13D); }
        .stat-icon.yellow-bg { background: linear-gradient(135deg, #FFA500, #FFB732); }
        .stat-icon.red-bg { background: linear-gradient(135deg, #FF6B6B, #FF8787); }

        .stat-label {
            color: #8C8C8C;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #2C3E50;
            margin-bottom: 10px;
        }

        .stat-change {
            background: #E8F5E9;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        .stat-change.positive {
            background: #E8F5E9;
            color: #52C41A;
        }

        .stat-change.negative {
            background: #FFEBEE;
            color: #F44336;
        }

        .stat-change span {
            font-size: 13px;
            font-weight: 500;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2C3E50;
        }

        .custom-input {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .custom-input:focus {
            border-color: #c9a876;
            box-shadow: 0 0 0 3px rgba(201, 168, 118, 0.1);
        }

        .btn-salus {
            background: linear-gradient(135deg, #c9a876, #d4b896);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-salus:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(201, 168, 118, 0.3);
        }

        /* Cards modernos */
        .modern-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .modern-card .card-header {
            background: white;
            border-bottom: 1px solid #f0f0f0;
            border-radius: 20px 20px 0 0;
            padding: 20px 25px;
        }

        .modern-card .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #2C3E50;
        }

        /* Report Items */
        .report-list {
            padding: 10px 0;
        }

        .report-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid #e9ecef;
        }

        .report-item:hover {
            background: #f5efe6;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .report-info {
            flex: 1;
        }

        .report-info h4 {
            font-size: 16px;
            font-weight: 600;
            color: #2C3E50;
            margin-bottom: 5px;
        }

        .report-info p {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .report-info small {
            font-size: 12px;
        }

        .report-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-action {
            background: transparent;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            color: #6c757d;
            font-size: 16px;
        }

        .btn-action:hover {
            background: rgba(201, 168, 118, 0.1);
            color: #c9a876;
        }

        .btn-action.btn-view {
            color: #17a2b8;
        }

        .btn-action.btn-view:hover {
            background: rgba(23, 162, 184, 0.1);
        }

        .btn-action.btn-download {
            color: #dc3545;
        }

        .btn-action.btn-download:hover {
            background: rgba(220, 53, 69, 0.1);
        }

        .btn-action.btn-excel {
            color: #28a745;
        }

        .btn-action.btn-excel:hover {
            background: rgba(40, 167, 69, 0.1);
        }

        /* Charts */
        .chart-container {
            position: relative;
            width: 100%;
            min-height: 300px;
        }

        .chart-controls {
            display: flex;
            gap: 10px;
        }

        /* Service Legend */
        .service-legend {
            padding: 15px 0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
            color: #6c757d;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            margin-right: 10px;
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #c9a876;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Report preview styles */
        .report-preview {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #c9a876;
            padding-bottom: 20px;
        }

        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: #2C3E50;
            margin-bottom: 10px;
        }

        .report-subtitle {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .report-date {
            font-size: 14px;
            color: #8c8c8c;
        }

        .report-section {
            margin-bottom: 25px;
        }

        .report-section h3 {
            font-size: 18px;
            font-weight: 600;
            color: #c9a876;
            margin-bottom: 15px;
            border-left: 4px solid #c9a876;
            padding-left: 15px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .report-table th,
        .report-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .report-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2C3E50;
        }

        .report-table tbody tr:hover {
            background: #f8f9fa;
        }

        .report-summary {
            background: linear-gradient(135deg, #c9a876, #d4b896);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-label {
            font-weight: 500;
        }

        .summary-value {
            font-weight: 700;
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-header {
                flex-direction: column;
                text-align: center;
            }

            .welcome-date {
                margin-top: 20px;
                text-align: center;
            }

            .stat-value {
                font-size: 28px;
            }

            .welcome-content h1 {
                font-size: 24px;
            }

            .report-actions {
                flex-direction: column;
                gap: 5px;
            }

            .chart-container {
                min-height: 250px;
            }
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        let chartIngresos, chartServicios;

        // Datos de ejemplo para reportes
        const reportData = {
            'ingresos-mensuales': {
                title: 'Reporte de Ingresos Mensuales',
                data: [
                    { mes: 'Enero', botox: 15000, rellenos: 12000, faciales: 8000, otros: 5000, total: 40000 },
                    { mes: 'Febrero', botox: 18000, rellenos: 14000, faciales: 9000, otros: 6000, total: 47000 },
                    { mes: 'Marzo', botox: 16000, rellenos: 13000, faciales: 7500, otros: 5500, total: 42000 },
                    { mes: 'Abril', botox: 20000, rellenos: 15000, faciales: 10000, otros: 7000, total: 52000 },
                    { mes: 'Mayo', botox: 17000, rellenos: 12500, faciales: 8500, otros: 6000, total: 44000 },
                    { mes: 'Junio', botox: 22000, rellenos: 16000, faciales: 11000, otros: 8000, total: 57000 }
                ]
            },
            'gastos-operativos': {
                title: 'Reporte de Gastos Operativos',
                data: [
                    { categoria: 'Salarios', enero: 25000, febrero: 25000, marzo: 26000, total: 76000 },
                    { categoria: 'Productos', enero: 8000, febrero: 9500, marzo: 8500, total: 26000 },
                    { categoria: 'Servicios', enero: 3000, febrero: 3200, marzo: 3100, total: 9300 },
                    { categoria: 'Marketing', enero: 2000, febrero: 2500, marzo: 2200, total: 6700 },
                    { categoria: 'Otros', enero: 1500, febrero: 1800, marzo: 1600, total: 4900 }
                ]
            }
        };

        // Inicializar gráficos
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        function initializeCharts() {
            // Gráfico de Ingresos
            const ctxIngresos = document.getElementById('chartIngresos');
            if (ctxIngresos) {
                chartIngresos = new Chart(ctxIngresos, {
                    type: 'bar',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Ingresos ($)',
                            backgroundColor: 'rgba(201, 168, 118, 0.8)',
                            borderColor: '#c9a876',
                            borderWidth: 2,
                            borderRadius: 8,
                            data: [40000, 47000, 42000, 52000, 44000, 57000]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return 'Ingresos:  + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    color: '#8C8C8C',
                                    callback: function(value) {
                                        return ' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#8C8C8C'
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de Servicios
            const ctxServicios = document.getElementById('chartServicios');
            if (ctxServicios) {
                chartServicios = new Chart(ctxServicios, {
                    type: 'doughnut',
                    data: {
                        labels: ['Botox', 'Rellenos', 'Tratamientos Faciales', 'Otros'],
                        datasets: [{
                            data: [35, 25, 20, 20],
                            backgroundColor: [
                                '#c9a876',
                                '#d4b896',
                                '#e5d4c1',
                                '#f5efe6'
                            ],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Función para ver reporte
        function viewReport(reportType) {
            const data = reportData[reportType];
            if (!data) {
                showNotification('Reporte no encontrado', 'error');
                return;
            }

            let content = generateReportPreview(reportType, data);
            
            document.getElementById('reportPreviewModalLabel').textContent = data.title;
            document.getElementById('reportPreviewContent').innerHTML = content;
            document.getElementById('downloadFromPreview').setAttribute('data-report', reportType);
            
            $('#reportPreviewModal').modal('show');
        }

        // Generar vista previa del reporte
        function generateReportPreview(reportType, data) {
            const currentDate = new Date().toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            let content = `
                <div class="report-preview">
                    <div class="report-header">
                        <div class="report-title">${data.title}</div>
                        <div class="report-subtitle">Clínica Estética Salus</div>
                        <div class="report-date">Generado el ${currentDate}</div>
                    </div>
            `;

            if (reportType === 'ingresos-mensuales') {
                content += `
                    <div class="report-section">
                        <h3>Resumen de Ingresos por Mes</h3>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Botox</th>
                                    <th>Rellenos</th>
                                    <th>Faciales</th>
                                    <th>Otros</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.data.forEach(row => {
                    content += `
                        <tr>
                            <td>${row.mes}</td>
                            <td>${row.botox.toLocaleString()}</td>
                            <td>${row.rellenos.toLocaleString()}</td>
                            <td>${row.faciales.toLocaleString()}</td>
                            <td>${row.otros.toLocaleString()}</td>
                            <td><strong>${row.total.toLocaleString()}</strong></td>
                        </tr>
                    `;
                });

                const totalIngresos = data.data.reduce((sum, row) => sum + row.total, 0);
                const promedioMensual = totalIngresos / data.data.length;

                content += `
                            </tbody>
                        </table>
                        <div class="report-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total de Ingresos:</span>
                                <span class="summary-value">${totalIngresos.toLocaleString()}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Promedio Mensual:</span>
                                <span class="summary-value">${Math.round(promedioMensual).toLocaleString()}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Mejor Mes:</span>
                                <span class="summary-value">Junio ($57,000)</span>
                            </div>
                        </div>
                    </div>
                `;
            } else if (reportType === 'gastos-operativos') {
                content += `
                    <div class="report-section">
                        <h3>Gastos por Categoría</h3>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th>Enero</th>
                                    <th>Febrero</th>
                                    <th>Marzo</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.data.forEach(row => {
                    content += `
                        <tr>
                            <td>${row.categoria}</td>
                            <td>${row.enero.toLocaleString()}</td>
                            <td>${row.febrero.toLocaleString()}</td>
                            <td>${row.marzo.toLocaleString()}</td>
                            <td><strong>${row.total.toLocaleString()}</strong></td>
                        </tr>
                    `;
                });

                const totalGastos = data.data.reduce((sum, row) => sum + row.total, 0);

                content += `
                            </tbody>
                        </table>
                        <div class="report-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total de Gastos:</span>
                                <span class="summary-value">${totalGastos.toLocaleString()}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Mayor Gasto:</span>
                                <span class="summary-value">Salarios (${((76000/totalGastos)*100).toFixed(1)}%)</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                content += `
                    <div class="report-section">
                        <h3>Información del Reporte</h3>
                        <p>Este reporte contiene información detallada sobre ${data.title.toLowerCase()}.</p>
                        <p>Los datos están actualizados hasta la fecha de generación del reporte.</p>
                        
                        <div class="report-summary">
                            <div class="summary-item">
                                <span class="summary-label">Estado:</span>
                                <span class="summary-value">Actualizado</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Período:</span>
                                <span class="summary-value">Últimos 6 meses</span>
                            </div>
                        </div>
                    </div>
                `;
            }

            content += '</div>';
            return content;
        }

        // Función para descargar reportes
        function downloadReport(reportType, format) {
            const button = event.target.closest('.btn-action');
            const originalContent = button.innerHTML;
            
            // Mostrar loading
            button.innerHTML = '<div class="loading-spinner"></div>';
            button.disabled = true;

            setTimeout(() => {
                if (format === 'pdf') {
                    generatePDF(reportType);
                } else if (format === 'excel') {
                    generateExcel(reportType);
                }
                
                // Restaurar botón
                button.innerHTML = originalContent;
                button.disabled = false;
                
                showNotification(`Reporte ${reportType} descargado en formato ${format.toUpperCase()}`, 'success');
            }, 2000);
        }

        // Generar PDF
        function generatePDF(reportType) {
            const data = reportData[reportType];
            if (!data) return;

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Header
            doc.setFontSize(20);
            doc.setTextColor(201, 168, 118);
            doc.text(data.title, 20, 30);
            
            doc.setFontSize(12);
            doc.setTextColor(0, 0, 0);
            doc.text('Clínica Estética Salus', 20, 45);
            doc.text(`Generado el ${new Date().toLocaleDateString('es-ES')}`, 20, 55);
            
            // Línea separadora
            doc.setDrawColor(201, 168, 118);
            doc.line(20, 65, 190, 65);
            
            let yPosition = 80;
            
            if (reportType === 'ingresos-mensuales' && data.data) {
                doc.setFontSize(14);
                doc.text('Resumen de Ingresos por Mes', 20, yPosition);
                yPosition += 20;
                
                // Headers
                doc.setFontSize(10);
                doc.setFont(undefined, 'bold');
                doc.text('Mes', 20, yPosition);
                doc.text('Botox', 50, yPosition);
                doc.text('Rellenos', 80, yPosition);
                doc.text('Faciales', 110, yPosition);
                doc.text('Otros', 140, yPosition);
                doc.text('Total', 170, yPosition);
                yPosition += 10;
                
                // Data rows
                doc.setFont(undefined, 'normal');
                data.data.forEach(row => {
                    doc.text(row.mes, 20, yPosition);
                    doc.text(`${row.botox.toLocaleString()}`, 50, yPosition);
                    doc.text(`${row.rellenos.toLocaleString()}`, 80, yPosition);
                    doc.text(`${row.faciales.toLocaleString()}`, 110, yPosition);
                    doc.text(`${row.otros.toLocaleString()}`, 140, yPosition);
                    doc.text(`${row.total.toLocaleString()}`, 170, yPosition);
                    yPosition += 8;
                });
                
                // Totals
                const totalIngresos = data.data.reduce((sum, row) => sum + row.total, 0);
                yPosition += 10;
                doc.setFont(undefined, 'bold');
                doc.text(`Total de Ingresos: ${totalIngresos.toLocaleString()}`, 20, yPosition);
            }
            
            // Footer
            doc.setFontSize(8);
            doc.setTextColor(128, 128, 128);
            doc.text('Documento generado automáticamente por el sistema Salus', 20, 280);
            
            doc.save(`${reportType}_${new Date().toISOString().split('T')[0]}.pdf`);
        }

        // Generar Excel (simulado con CSV)
        function generateExcel(reportType) {
            const data = reportData[reportType];
            if (!data) return;

            let csvContent = '';
            const currentDate = new Date().toLocaleDateString('es-ES');
            
            // Header
            csvContent += `${data.title}\n`;
            csvContent += `Clínica Estética Salus\n`;
            csvContent += `Generado el ${currentDate}\n\n`;
            
            if (reportType === 'ingresos-mensuales' && data.data) {
                csvContent += 'Mes,Botox,Rellenos,Faciales,Otros,Total\n';
                data.data.forEach(row => {
                    csvContent += `${row.mes},${row.botox},${row.rellenos},${row.faciales},${row.otros},${row.total}\n`;
                });
            } else if (reportType === 'gastos-operativos' && data.data) {
                csvContent += 'Categoría,Enero,Febrero,Marzo,Total\n';
                data.data.forEach(row => {
                    csvContent += `${row.categoria},${row.enero},${row.febrero},${row.marzo},${row.total}\n`;
                });
            }
            
            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `${reportType}_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
        }

        // Exportar gráfico
        function exportChart(chartId, filename) {
            const canvas = document.getElementById(chartId);
            const link = document.createElement('a');
            link.download = `${filename}_${new Date().toISOString().split('T')[0]}.png`;
            link.href = canvas.toDataURL();
            link.click();
            
            showNotification(`Gráfico ${filename} exportado exitosamente`, 'success');
        }

        // Aplicar filtros
        function applyFilters() {
            const search = document.getElementById('searchReport').value.toLowerCase();
            const area = document.getElementById('filterArea').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            
            const reportItems = document.querySelectorAll('.report-item');
            
            reportItems.forEach(item => {
                const name = item.dataset.name.toLowerCase();
                const type = item.dataset.type;
                const title = item.querySelector('h4').textContent.toLowerCase();
                
                let show = true;
                
                // Filtro de búsqueda
                if (search && !name.includes(search) && !title.includes(search)) {
                    show = false;
                }
                
                // Filtro de área
                if (area && type !== area) {
                    show = false;
                }
                
                // Mostrar/ocultar item
                item.style.display = show ? 'flex' : 'none';
            });
            
            showNotification('Filtros aplicados correctamente', 'success');
        }

        // Generar todos los reportes financieros
        function generateAllFinancialReports() {
            showNotification('Generando todos los reportes financieros...', 'info');
            
            setTimeout(() => {
                const reports = ['ingresos-mensuales', 'gastos-operativos', 'balance-general', 'flujo-caja'];
                reports.forEach(report => {
                    generatePDF(report);
                });
                showNotification('Todos los reportes financieros han sido descargados', 'success');
            }, 1000);
        }

        // Generar todos los reportes operativos
        function generateAllOperationalReports() {
            showNotification('Generando todos los reportes operativos...', 'info');
            
            setTimeout(() => {
                const reports = ['citas-agendadas', 'inventario', 'desempeno-empleados', 'satisfaccion-pacientes'];
                reports.forEach(report => {
                    generatePDF(report);
                });
                showNotification('Todos los reportes operativos han sido descargados', 'success');
            }, 1000);
        }

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

        // Event listener para el botón de descarga del modal
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('downloadFromPreview').addEventListener('click', function() {
                const reportType = this.getAttribute('data-report');
                downloadReport(reportType, 'pdf');
                $('#reportPreviewModal').modal('hide');
            });
        });
    </script>
@stop