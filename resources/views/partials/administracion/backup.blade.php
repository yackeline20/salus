@extends('adminlte::page')

@section('title', 'Backup y Restore')

@section('content_header')
    <h1>
        <i class="fas fa-database"></i> Crear BACKUP y RESTORE Base de Datos desde VB.NET
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card card-primary card-outline card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="backup-tab" data-toggle="pill" href="#backup" role="tab">
                        <i class="fas fa-save"></i> Backup
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="restore-tab" data-toggle="pill" href="#restore" role="tab">
                        <i class="fas fa-undo"></i> Restore
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-tabContent">
                
                <!-- TAB BACKUP -->
                <div class="tab-pane fade show active" id="backup" role="tabpanel">
                    <h4>Crear Backup</h4>
                    <form action="{{ route('administracion.backup.crear') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Servidor Local:</label>
                                    <input type="text" class="form-control" name="servidor" value="{{ env('DB_HOST', 'localhost') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Base de Datos:</label>
                                    <input type="text" class="form-control" name="base_datos" value="{{ env('DB_DATABASE', 'Farmacia') }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Directorio de Respaldo:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="directorio" value="D:\DEMO\bak" placeholder="Seleccione directorio">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button">
                                        <i class="fas fa-folder-open"></i> Examinar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="backup_automatico" name="backup_automatico">
                                <label class="custom-control-label" for="backup_automatico">
                                    Configurar Backup Automático (Job)
                                </label>
                            </div>
                        </div>

                        <div id="job_config" style="display: none;">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0">Configuración de Job Automático</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Frecuencia:</label>
                                                <select class="form-control" name="frecuencia">
                                                    <option value="diario">Diario</option>
                                                    <option value="semanal">Semanal</option>
                                                    <option value="mensual">Mensual</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hora:</label>
                                                <input type="time" class="form-control" name="hora" value="02:00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Crear Backup
                            </button>
                            <a href="{{ route('administracion') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Salir
                            </a>
                        </div>
                    </form>
                </div>

                <!-- TAB RESTORE -->
                <div class="tab-pane fade" id="restore" role="tabpanel">
                    <h4>Restaurar Base de Datos</h4>
                    <form action="{{ route('administracion.backup.restaurar') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Servidor Local:</label>
                                    <input type="text" class="form-control" name="servidor" value="{{ env('DB_HOST', 'localhost') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Base de Datos:</label>
                                    <input type="text" class="form-control" name="base_datos" value="{{ env('DB_DATABASE', 'Farmacia') }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Archivo de Backup:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="archivo_backup" placeholder="Seleccione archivo .bak">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button">
                                        <i class="fas fa-folder-open"></i> Examinar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Advertencia:</strong> Esta acción reemplazará todos los datos actuales de la base de datos.
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-undo"></i> Restaurar
                            </button>
                            <a href="{{ route('administracion') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Salir
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="card-footer text-center text-muted">
        <small>Desarrollado por Cristian Fabricio Izquierdo - Para CFPIPRODESIGN</small>
    </div>
</div>
@stop

@section('js')
<script>
    // Mostrar/ocultar configuración de Job automático
    $('#backup_automatico').change(function() {
        if($(this).is(':checked')) {
            $('#job_config').slideDown();
        } else {
            $('#job_config').slideUp();
        }
    });
</script>
@stop