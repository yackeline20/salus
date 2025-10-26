@extends('adminlte::page')

@section('title', 'Factura')

@section('content_header')
    <h1>
        <i class="fas fa-file-invoice-dollar"></i> Gestión de Facturas
        <small>Sistema de facturación</small>
    </h1>
@stop

@section('content')
<div class="row">
    <!-- Header -->
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #d4a574 0%, #c8956a 100%); border: none;">
            <div class="card-body text-white">
                <h2 class="mb-2">¡Gestión de Facturas!</h2>
                <p class="mb-0">Aquí puedes crear, editar y gestionar todas las facturas de tu clínica estética.</p>
            </div>
        </div>
    </div>
</div>

<!-- Acciones principales -->
<div class="row mt-4">
    <div class="col-md-3">
        <a href="{{ route('factura.create') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-plus-circle text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title text-dark">Nueva Factura</h5>
                    <p class="card-text text-muted">Crear una nueva factura para un paciente</p>
                    <span class="btn btn-success btn-sm">Crear Factura</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-search text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="card-title">Buscar Factura</h5>
                <p class="card-text text-muted">Buscar facturas por número o paciente</p>
                <button class="btn btn-primary btn-sm">Buscar</button>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-file-pdf text-danger" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="card-title">Generar Reporte</h5>
                <p class="card-text text-muted">Exportar facturas en PDF o Excel</p>
                <button class="btn btn-danger btn-sm">Generar</button>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-cog text-warning" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="card-title">Configuración</h5>
                <p class="card-text text-muted">Configurar formatos y numeración</p>
                <button class="btn btn-warning btn-sm">Configurar</button>
            </div>
        </div>
    </div>
</div>

<!-- Lista de facturas recientes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Facturas Recientes</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No. Factura</th>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Tratamiento</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>#F-001</strong></td>
                                <td>María González</td>
                                <td>28/07/2025</td>
                                <td>Botox Facial</td>
                                <td><strong>$850.00</strong></td>
                                <td><span class="badge badge-success">Pagada</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary mr-1">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary mr-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease-in-out;
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Factura page loaded!');
    </script>
@stop
