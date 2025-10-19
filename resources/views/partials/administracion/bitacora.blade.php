@extends('adminlte::page')

@section('title', 'Bitácora')

@section('content_header')
    <h1>
        <i class="fas fa-history"></i> Bitácora del Sistema
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="card-title"><strong>Bitácora</strong></h3>
        </div>
        <div class="card-body">
            <!-- Filtros de Fecha -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Fecha Inicial:</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="fecha_inicial" value="2011-08-01">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="button">
                                <i class="far fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Fecha Final:</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="fecha_final" value="2011-08-23">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="button">
                                <i class="far fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>&nbsp;</label>
                    <div class="btn-group" role="group">
                        <button class="btn btn-default" type="button">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-default" type="button">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Barra de herramientas -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="btn-toolbar" role="toolbar">
                        <div class="btn-group mr-2" role="group">
                            <button type="button" class="btn btn-default" title="Añadir">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-default" title="Eliminar">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="btn-group mr-2" role="group">
                            <button type="button" class="btn btn-default" title="Imprimir">
                                <i class="fas fa-print text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-default" title="PDF">
                                <i class="far fa-file-pdf text-danger"></i>
                            </button>
                            <button type="button" class="btn btn-default" title="Guardar">
                                <i class="far fa-save text-success"></i>
                            </button>
                            <button type="button" class="btn btn-default" title="Word">
                                <i class="far fa-file-word text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-default" title="Usuarios">
                                <i class="fas fa-users text-info"></i>
                            </button>
                            <button type="button" class="btn btn-default" title="Subir">
                                <i class="fas fa-arrow-up text-success"></i>
                            </button>
                        </div>
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control" placeholder="Buscar...">
                            <div class="input-group-append">
                                <button class="btn btn-default" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Bitácora -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 200px;">Fecha</th>
                            <th style="width: 120px;">Usuario</th>
                            <th>Acción</th>
                            <th style="width: 150px;">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>22/Ago/2011 17:04 p.m.</td>
                            <td>Admin</td>
                            <td>Modificar Fotografía</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>11/Ago/2011 12:12 p.m.</td>
                            <td>Admin</td>
                            <td>Deshacer último cambio Crédito INFONAVIT</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>11/Ago/2011 12:11 p.m.</td>
                            <td>Admin</td>
                            <td>Activar Crédito INFONAVIT</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>05/Ago/2011 15:16 p.m.</td>
                            <td>Admin</td>
                            <td>Borrar Checada</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>05/Ago/2011 15:16 p.m.</td>
                            <td>Admin</td>
                            <td>Modificar Checada</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>05/Ago/2011 15:15 p.m.</td>
                            <td>Admin</td>
                            <td>Agregar Checada</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>05/Ago/2011 08:51 a.m.</td>
                            <td>Admin</td>
                            <td>Calcular Promedio de Variables</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>02/Ago/2011 10:25 a.m.</td>
                            <td>Admin</td>
                            <td>Cancelar Baja de Herramienta</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>02/Ago/2011 10:24 a.m.</td>
                            <td>Admin</td>
                            <td>Registrar Baja de Herramienta</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>02/Ago/2011 10:23 a.m.</td>
                            <td>Admin</td>
                            <td>Cancelar Baja de Herramienta</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>02/Ago/2011 10:22 a.m.</td>
                            <td>Admin</td>
                            <td>Registrar Baja de Herramienta</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>02/Ago/2011 09:59 a.m.</td>
                            <td>Admin</td>
                            <td>Modificar Forma de Pago</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>01/Ago/2011 16:09 p.m.</td>
                            <td>Admin</td>
                            <td>Modificar Forma de Pago</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default" title="Editar">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <span class="badge badge-secondary">1</span>
                    <span>2</span>
                </div>
                <div class="col-md-6 text-right">
                    <small class="text-muted">Mostrando página 1 de 2</small>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('administracion') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table-sm td, .table-sm th {
        padding: 0.3rem;
        font-size: 0.9rem;
    }
    .btn-xs {
        padding: 0.1rem 0.3rem;
        font-size: 0.75rem;
    }
</style>
@stop


