@extends('adminlte::page')

@section('title', 'Gestión de Servicios')

@section('content_header')
    <div class="header-section">
        <div class="d-flex align-items-center">
            <div class="page-icon-box">
                <i class="fas fa-spa"></i>
            </div>
            <div class="ml-3">
                <h1 class="header-title mb-0">Gestión de Servicios</h1>
                <p class="header-subtitle mb-0">Administración de servicios y tratamientos del spa</p>
            </div>
        </div>
    </div>
@stop

@section('content')
    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card card-cafe">
                <div class="stats-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stats-info">
                    <h3 class="stats-number">{{ count($tratamientos) }}</h3>
                    <p class="stats-label">Total Servicios</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card card-beige">
                <div class="stats-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stats-info">
                    <h3 class="stats-number">L {{ number_format(array_sum(array_column($tratamientos, 'Precio_Estandar')), 2) }}</h3>
                    <p class="stats-label">Valor Total</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card card-cafe-claro">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-info">
                    <h3 class="stats-number">{{ array_sum(array_column($tratamientos, 'Duracion_Estimada_Min')) }}</h3>
                    <p class="stats-label">Minutos Totales</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card card-gold">
                <div class="stats-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stats-info">
                    <h3 class="stats-number">L {{ count($tratamientos) > 0 ? number_format(array_sum(array_column($tratamientos, 'Precio_Estandar')) / count($tratamientos), 2) : 0 }}</h3>
                    <p class="stats-label">Precio Promedio</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Principal -->
    <div class="main-card">
        <div class="card-header-custom">
            <!-- Nueva barra de herramientas mejorada -->
            <div class="toolbar-container">
                <!-- Botones de acción -->
                <div class="action-buttons-wrapper">
                    <button class="btn-action btn-add" onclick="openModal()">
                        <div class="btn-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <span class="btn-text">Nuevo Servicio</span>
                    </button>
                    
                    <button class="btn-action btn-back" onclick="goBack()">
                        <div class="btn-icon">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                        <span class="btn-text">Regresar</span>
                    </button>
                </div>

                <!-- Buscador elegante -->
                <div class="search-wrapper">
                    <div class="search-box-elegant">
                        <i class="fas fa-search search-icon-elegant"></i>
                        <input type="text" class="search-input-elegant" placeholder="Buscar por nombre o descripción..." id="searchInput">
                        <button class="btn-clear-elegant" id="clearSearch" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Filtros y exportar -->
                <div class="filters-wrapper">
                    <div class="filter-dropdown-wrapper">
                        <i class="fas fa-dollar-sign filter-icon"></i>
                        <select class="filter-select-elegant" id="filterPrice">
                            <option value="">Todos los precios</option>
                            <option value="0-500">L 0 - L 500</option>
                            <option value="501-1000">L 501 - L 1,000</option>
                            <option value="1001-1500">L 1,001 - L 1,500</option>
                            <option value="1501-9999">Más de L 1,500</option>
                        </select>
                    </div>

                    <div class="filter-dropdown-wrapper">
                        <i class="fas fa-clock filter-icon"></i>
                        <select class="filter-select-elegant" id="filterDuration">
                            <option value="">Todas las duraciones</option>
                            <option value="0-30">0 - 30 min</option>
                            <option value="31-60">31 - 60 min</option>
                            <option value="61-90">61 - 90 min</option>
                            <option value="91-999">Más de 90 min</option>
                        </select>
                    </div>

                    <button class="btn-clear-filters-elegant" id="clearFilters">
                        <i class="fas fa-redo-alt"></i>
                        <span>Limpiar</span>
                    </button>

                    <a href="{{ route('servicios.export') }}" class="btn-export-elegant">
                        <i class="fas fa-file-excel"></i>
                        <span>Exportar</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body-custom">
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th class="col-codigo"><i class="fas fa-hashtag mr-1"></i>Cód</th>
                            <th class="col-servicio"><i class="fas fa-spa mr-1"></i>Servicio</th>
                            <th class="col-descripcion"><i class="fas fa-align-left mr-1"></i>Descripción</th>
                            <th class="col-precio"><i class="fas fa-money-bill-wave mr-1"></i>Precio</th>
                            <th class="col-duracion"><i class="far fa-clock mr-1"></i>Duración</th>
                            <th class="col-acciones text-center"><i class="fas fa-cog mr-1"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="servicesTableBody">
                        @forelse($tratamientos as $tratamiento)
                        <tr class="table-row-hover" 
                            data-id="{{ $tratamiento['Cod_Tratamiento'] }}"
                            data-price="{{ $tratamiento['Precio_Estandar'] }}"
                            data-duration="{{ $tratamiento['Duracion_Estimada_Min'] }}">
                            <td>
                                <span class="badge-codigo">#{{ $tratamiento['Cod_Tratamiento'] }}</span>
                            </td>
                            <td>
                                <div class="service-name-container">
                                    <div class="service-icon-wrapper">
                                        <i class="fas fa-spa"></i>
                                    </div>
                                    <span class="service-name">{{ $tratamiento['Nombre_Tratamiento'] ?? 'Sin nombre' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="description-text">{{ Str::limit($tratamiento['Descripcion'] ?? 'Sin descripción', 50) }}</span>
                            </td>
                            <td>
                                <span class="price-badge">L {{ number_format($tratamiento['Precio_Estandar'] ?? 0, 2) }}</span>
                            </td>
                            <td>
                                <span class="duration-badge">
                                    <i class="far fa-clock"></i> {{ $tratamiento['Duracion_Estimada_Min'] ?? 0 }} min
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons-group">
                                    <button class="btn-edit-custom" onclick='editService(@json($tratamiento))' title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>
                                    <button class="btn-delete-custom" onclick="deleteService({{ $tratamiento['Cod_Tratamiento'] }}, '{{ addslashes($tratamiento['Nombre_Tratamiento']) }}')" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h4>No hay servicios registrados</h4>
                                <p>Comience agregando su primer servicio</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-custom">
                <div class="modal-header-custom">
                    <div class="modal-icon">
                        <i class="fas fa-spa"></i>
                    </div>
                    <div>
                        <h5 class="modal-title-custom" id="modalTitle">Agregar Nuevo Servicio</h5>
                        <p class="modal-subtitle">Complete la información del servicio</p>
                    </div>
                    <button type="button" class="close-modal" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="serviceForm">
                    <input type="hidden" id="serviceId">
                    <div class="modal-body-custom">
                        <div class="form-group-custom">
                            <label class="label-custom">
                                <i class="fas fa-spa"></i>
                                Nombre del servicio
                                <span class="required">*</span>
                            </label>
                            <input type="text" class="input-custom" id="serviceName" placeholder="Ej: Masaje Relajante" required>
                        </div>
                        
                        <div class="form-group-custom">
                            <label class="label-custom">
                                <i class="fas fa-align-left"></i>
                                Descripción
                            </label>
                            <textarea class="textarea-custom" id="serviceDescription" rows="3" placeholder="Describa el servicio..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="label-custom">
                                        <i class="fas fa-money-bill-wave"></i>
                                        Precio (L)
                                        <span class="required">*</span>
                                    </label>
                                    <input type="number" class="input-custom" id="servicePrice" step="0.01" min="0" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="label-custom">
                                        <i class="far fa-clock"></i>
                                        Duración (minutos)
                                        <span class="required">*</span>
                                    </label>
                                    <input type="number" class="input-custom" id="serviceDuration" min="0" placeholder="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn-cancel" data-dismiss="modal">
                            <i class="fas fa-times-circle"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-check-circle"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #FAF6F0 0%, #F5EFE6 100%);
            font-size: 13px;
        }

        /* Header */
        .header-section {
            background: linear-gradient(135deg, #A67C52 0%, #8B6F47 100%);
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(166, 124, 82, 0.2);
            margin-bottom: 20px;
        }

        .page-icon-box {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .page-icon-box i {
            font-size: 22px;
            color: #fff;
        }

        .header-title {
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: 12px;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            margin-bottom: 15px;
            border: none;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        .card-cafe { border-left: 4px solid #A67C52; }
        .card-beige { border-left: 4px solid #D4A574; }
        .card-cafe-claro { border-left: 4px solid #E8C4A0; }
        .card-gold { border-left: 4px solid #DEB887; }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .card-cafe .stats-icon { background: linear-gradient(135deg, #A67C52, #8B6F47); }
        .card-beige .stats-icon { background: linear-gradient(135deg, #D4A574, #C19A6B); }
        .card-cafe-claro .stats-icon { background: linear-gradient(135deg, #E8C4A0, #D2B48C); }
        .card-gold .stats-icon { background: linear-gradient(135deg, #DEB887, #D2A67C); }

        .stats-number {
            font-size: 22px;
            font-weight: 700;
            color: #6B5B3D;
            margin: 0;
            line-height: 1;
        }

        .stats-label {
            color: #8B7355;
            font-size: 11px;
            margin: 5px 0 0 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Main Card */
        .main-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #F5E6D3 0%, #EBD9C5 100%);
            padding: 20px 25px;
            border-bottom: 2px solid #D4A574;
        }

        /* Nueva Toolbar */
        .toolbar-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .action-buttons-wrapper {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-add {
            background: linear-gradient(135deg, #A67C52 0%, #8B6F47 100%);
            color: white;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(166, 124, 82, 0.3);
        }

        .btn-back {
            background: white;
            color: #A67C52;
            border: 2px solid #D4A574;
        }

        .btn-back:hover {
            background: #F5E6D3;
            border-color: #A67C52;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 16px;
        }

        .btn-add .btn-icon {
            background: rgba(255, 255, 255, 0.25);
        }

        .btn-back .btn-icon {
            background: #F5E6D3;
            color: #A67C52;
        }

        /* Buscador Elegante */
        .search-wrapper {
            flex: 1;
            min-width: 300px;
            max-width: 500px;
        }

        .search-box-elegant {
            position: relative;
            display: flex;
            align-items: center;
            background: white;
            border: 2px solid #E8C4A0;
            border-radius: 14px;
            padding: 10px 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .search-box-elegant:focus-within {
            border-color: #A67C52;
            box-shadow: 0 4px 16px rgba(166, 124, 82, 0.15);
            transform: translateY(-1px);
        }

        .search-icon-elegant {
            color: #A67C52;
            font-size: 16px;
            margin-right: 12px;
        }

        .search-input-elegant {
            flex: 1;
            border: none;
            outline: none;
            font-size: 13px;
            color: #6B5B3D;
            background: transparent;
        }

        .search-input-elegant::placeholder {
            color: #B8A68F;
        }

        .btn-clear-elegant {
            background: linear-gradient(135deg, #E8998D, #D78A7E);
            color: white;
            border: none;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 8px;
        }

        .btn-clear-elegant:hover {
            background: linear-gradient(135deg, #D78A7E, #C67A6B);
            transform: rotate(90deg) scale(1.1);
        }

        /* Filtros */
        .filters-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-dropdown-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .filter-icon {
            position: absolute;
            left: 12px;
            color: #A67C52;
            font-size: 13px;
            pointer-events: none;
            z-index: 1;
        }

        .filter-select-elegant {
            background: white;
            border: 2px solid #E8C4A0;
            border-radius: 10px;
            padding: 10px 14px 10px 36px;
            font-size: 12px;
            color: #6B5B3D;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23A67C52' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 32px;
        }

        .filter-select-elegant:hover {
            border-color: #D4A574;
            box-shadow: 0 2px 8px rgba(166, 124, 82, 0.1);
        }

        .filter-select-elegant:focus {
            border-color: #A67C52;
            box-shadow: 0 0 0 3px rgba(166, 124, 82, 0.1);
        }

        .btn-clear-filters-elegant {
            background: linear-gradient(135deg, #D4A574, #C19A6B);
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(212, 165, 116, 0.3);
        }

        .btn-clear-filters-elegant:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(212, 165, 116, 0.4);
            background: linear-gradient(135deg, #C19A6B, #A67C52);
        }

        .btn-export-elegant {
            background: linear-gradient(135deg, #52A67C, #47886B);
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(82, 166, 124, 0.3);
        }

        .btn-export-elegant:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(82, 166, 124, 0.4);
            background: linear-gradient(135deg, #47886B, #3D7359);
            color: white;
            text-decoration: none;
        }

        /* Table */
        .card-body-custom {
            padding: 0;
        }

        .table-custom {
            width: 100%;
            margin: 0;
            font-size: 12px;
        }

        .table-custom thead {
            background: linear-gradient(135deg, #A67C52, #8B6F47);
        }

        .table-custom thead th {
            color: white;
            font-weight: 600;
            font-size: 11px;
            padding: 14px 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .col-codigo { width: 8%; }
        .col-servicio { width: 20%; }
        .col-descripcion { width: 32%; }
        .col-precio { width: 12%; }
        .col-duracion { width: 13%; }
        .col-acciones { width: 15%; }

        .table-custom tbody tr {
            border-bottom: 1px solid #F5E6D3;
            transition: all 0.2s ease;
        }

        .table-row-hover:hover {
            background: linear-gradient(135deg, #FFF8F0 0%, #FAF6F0 100%);
            transform: scale(1.002);
        }

        .table-custom tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 12px;
        }

        .badge-codigo {
            background: linear-gradient(135deg, #E8C4A0, #D2B48C);
            color: #6B5B3D;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
            display: inline-block;
        }

        .service-name-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .service-icon-wrapper {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #F5E6D3, #EBD9C5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #A67C52;
            font-size: 14px;
        }

        .service-name {
            font-weight: 600;
            color: #6B5B3D;
            font-size: 13px;
        }

        .description-text {
            color: #8B7355;
            font-size: 11px;
            line-height: 1.5;
        }

        .price-badge {
            background: linear-gradient(135deg, #52A67C, #47886B);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            display: inline-block;
        }

        .duration-badge {
            background: #F5E6D3;
            color: #A67C52;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Action Buttons */
        .action-buttons-group {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-edit-custom, .btn-delete-custom {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-edit-custom {
            background: linear-gradient(135deg, #D4A574, #C19A6B);
            border-color: #C19A6B;
        }

        .btn-edit-custom svg {
            stroke: white;
            transition: all 0.3s ease;
        }

        .btn-edit-custom:hover {
            background: linear-gradient(135deg, #C19A6B, #A67C52);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 20px rgba(193, 154, 107, 0.4);
        }

        .btn-edit-custom:hover svg {
            transform: rotate(-10deg);
        }

        .btn-delete-custom {
            background: linear-gradient(135deg, #E8998D, #D78A7E);
            border-color: #D78A7E;
        }

        .btn-delete-custom svg {
            stroke: white;
            transition: all 0.3s ease;
        }

        .btn-delete-custom:hover {
            background: linear-gradient(135deg, #D78A7E, #C67A6B);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 20px rgba(215, 138, 126, 0.4);
        }

        .btn-delete-custom:hover svg {
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 60px;
            color: #D4A574;
            margin-bottom: 15px;
        }

        .empty-state h4 {
            color: #A67C52;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .empty-state p {
            color: #8B7355;
            font-size: 12px;
        }

        /* Modal */
        .modal-custom {
            border-radius: 16px;
            overflow: hidden;
            border: none;
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #A67C52, #8B6F47);
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: none;
        }

        .modal-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .modal-title-custom {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .modal-subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: 11px;
            margin: 0;
        }

        .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .modal-body-custom {
            padding: 25px;
        }

        .form-group-custom {
            margin-bottom: 18px;
        }

        .label-custom {
            color: #A67C52;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .required {
            color: #E8998D;
        }

        .input-custom, .textarea-custom {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #E8C4A0;
            border-radius: 8px;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .input-custom:focus, .textarea-custom:focus {
            outline: none;
            border-color: #A67C52;
            box-shadow: 0 0 0 3px rgba(166, 124, 82, 0.1);
        }

        .modal-footer-custom {
            padding: 18px 25px;
            background: #F5E6D3;
            border-top: 2px solid #EBD9C5;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancel, .btn-save {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-cancel {
            background: #8B7355;
            color: white;
        }

        .btn-cancel:hover {
            background: #6B5B3D;
        }

        .btn-save {
            background: linear-gradient(135deg, #52A67C, #47886B);
            color: white;
            box-shadow: 0 2px 8px rgba(82, 166, 124, 0.3);
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(82, 166, 124, 0.4);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .toolbar-container {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons-wrapper,
            .search-wrapper,
            .filters-wrapper {
                width: 100%;
                max-width: 100%;
            }

            .search-wrapper {
                min-width: 100%;
            }

            .filters-wrapper {
                justify-content: space-between;
            }
        }

        @media (max-width: 768px) {
            .btn-text {
                display: none;
            }

            .btn-action {
                padding: 12px;
                justify-content: center;
            }

            .stats-card {
                padding: 15px;
            }

            .table-custom {
                font-size: 11px;
            }
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const API_BASE_URL = '/api/servicios';

        function openModal() {
            $('#modalTitle').text('Agregar Nuevo Servicio');
            $('#serviceForm')[0].reset();
            $('#serviceId').val('');
            $('#serviceModal').modal('show');
        }

        function editService(tratamiento) {
            $('#modalTitle').text('Editar Servicio');
            $('#serviceId').val(tratamiento.Cod_Tratamiento);
            $('#serviceName').val(tratamiento.Nombre_Tratamiento);
            $('#serviceDescription').val(tratamiento.Descripcion || '');
            $('#servicePrice').val(tratamiento.Precio_Estandar);
            $('#serviceDuration').val(tratamiento.Duracion_Estimada_Min);
            $('#serviceModal').modal('show');
        }

        function deleteService(id, serviceName) {
            Swal.fire({
                title: '¿Está seguro?',
                html: `¿Desea eliminar el servicio<br><strong>"${serviceName}"</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E8998D',
                cancelButtonColor: '#8B7355',
                confirmButtonText: '<i class="fas fa-trash"></i> Eliminar',
                cancelButtonText: 'Cancelar',
                background: '#fff',
                backdrop: 'rgba(166, 124, 82, 0.3)'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: API_BASE_URL + '/' + id,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: response.message,
                                confirmButtonColor: '#A67C52'
                            }).then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'No se pudo eliminar',
                                confirmButtonColor: '#A67C52'
                            });
                        }
                    });
                }
            });
        }

        function goBack() {
            window.location.href = '{{ route("dashboard") }}';
        }

        $('#serviceForm').on('submit', function(e) {
            e.preventDefault();
            const serviceId = $('#serviceId').val();
            const isEdit = serviceId !== '';
            const formData = {
                Nombre_Tratamiento: $('#serviceName').val(),
                Descripcion: $('#serviceDescription').val(),
                Precio_Estandar: parseFloat($('#servicePrice').val()),
                Duracion_Estimada_Min: parseInt($('#serviceDuration').val())
            };

            $.ajax({
                url: isEdit ? API_BASE_URL + '/' + serviceId : API_BASE_URL,
                type: isEdit ? 'PUT' : 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        confirmButtonColor: '#A67C52'
                    }).then(() => {
                        $('#serviceModal').modal('hide');
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al guardar',
                        confirmButtonColor: '#A67C52'
                    });
                }
            });
        });

        // Función de filtrado combinada
        function applyFilters() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const priceRange = $('#filterPrice').val();
            const durationRange = $('#filterDuration').val();

            $('#servicesTableBody tr').each(function() {
                const row = $(this);
                const serviceName = row.find('.service-name').text().toLowerCase();
                const description = row.find('.description-text').text().toLowerCase();
                const price = parseFloat(row.data('price')) || 0;
                const duration = parseInt(row.data('duration')) || 0;

                const matchesSearch = serviceName.includes(searchTerm) || description.includes(searchTerm);

                let matchesPrice = true;
                if (priceRange) {
                    const [min, max] = priceRange.split('-').map(Number);
                    matchesPrice = price >= min && price <= max;
                }

                let matchesDuration = true;
                if (durationRange) {
                    const [min, max] = durationRange.split('-').map(Number);
                    matchesDuration = duration >= min && duration <= max;
                }

                if (matchesSearch && matchesPrice && matchesDuration) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        }

        // Búsqueda con botón de limpiar
        $('#searchInput').on('input', function() {
            const hasValue = this.value.length > 0;
            $('#clearSearch').toggle(hasValue);
            applyFilters();
        });

        $('#clearSearch').on('click', function() {
            $('#searchInput').val('').trigger('input');
            $(this).hide();
        });

        // Filtros
        $('#filterPrice, #filterDuration').on('change', function() {
            applyFilters();
        });

        // Limpiar todos los filtros
        $('#clearFilters').on('click', function() {
            $('#searchInput').val('');
            $('#filterPrice').val('');
            $('#filterDuration').val('');
            $('#clearSearch').hide();
            $('#servicesTableBody tr').show();
        });
    </script>
@stop