@extends('adminlte::page')

@section('title', 'Bitácora del Sistema')

@section('content_header')
    <h1>
        <i class="fas fa-history"></i> Bitácora del Sistema
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="card-title"><strong>Registro de Actividades</strong></h3>
        </div>
        <div class="card-body">
            <!-- Formulario de Filtros -->
            <form method="GET" action="{{ route('administracion.bitacora') }}">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Fecha Inicial:</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="fecha_inicial" 
                                   value="{{ request('fecha_inicial', date('Y-m-01')) }}">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Fecha Final:</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="fecha_final" 
                                   value="{{ request('fecha_final', date('Y-m-d')) }}">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Buscar:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="buscar" 
                                   placeholder="Usuario, acción, observaciones..." 
                                   value="{{ request('buscar') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div>
                            <a href="{{ route('administracion.bitacora') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Barra de herramientas -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="btn-toolbar" role="toolbar">
                        <div class="btn-group mr-2" role="group">
                            <button type="button" class="btn btn-default" title="Imprimir" onclick="window.print()">
                                <i class="fas fa-print text-primary"></i> Imprimir
                            </button>
                            <a href="{{ route('administracion.bitacora.export.pdf', request()->all()) }}" 
                               class="btn btn-default" title="Exportar a PDF" target="_blank">
                                <i class="far fa-file-pdf text-danger"></i> PDF
                            </a>
                            <a href="{{ route('administracion.bitacora.export.excel', request()->all()) }}" 
                               class="btn btn-default" title="Exportar a Excel">
                                <i class="far fa-file-excel text-success"></i> Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Bitácora -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 180px;">Fecha y Hora</th>
                            <th style="width: 150px;">Usuario</th>
                            <th>Acción</th>
                            <th style="width: 120px;">Módulo</th>
                            <th style="width: 200px;">Observaciones</th>
                            <th style="width: 120px;">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $registro)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($registro->Fecha_Registro)->format('d/m/Y H:i:s') }}</td>
                            <td><strong>{{ $registro->Nombre_Usuario }}</strong></td>
                            <td>{{ $registro->Accion }}</td>
                            <td>
                                @if($registro->Modulo)
                                    <span class="badge badge-info">{{ $registro->Modulo }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $registro->Observaciones ?? '-' }}</td>
                            <td><small class="text-muted">{{ $registro->IP_Address ?? '-' }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>No hay registros en la bitácora con los filtros aplicados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="row mt-3">
                <div class="col-md-12">
                    {{ $registros->links() }}
                </div>
            </div>

            <!-- Información de registros -->
            <div class="row mt-2">
                <div class="col-md-12 text-muted">
                    <small>
                        Mostrando {{ $registros->firstItem() ?? 0 }} a {{ $registros->lastItem() ?? 0 }} 
                        de {{ $registros->total() }} registros
                    </small>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <a href="{{ url('/administracion') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table-sm td, .table-sm th {
        padding: 0.4rem;
        font-size: 0.9rem;
    }
    @media print {
        .btn-toolbar, .card-footer, .pagination, .card-header { 
            display: none !important; 
        }
    }
</style>
@stop

@section('js')
<script>
    console.log('Bitácora cargada correctamente');
</script>
@stop