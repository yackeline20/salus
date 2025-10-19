@extends('adminlte::page')

@section('title', 'Administración')

@section('content_header')
    <h1>
        <i class="fas fa-user-shield"></i> Administración del Sistema
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-database fa-5x text-primary mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Backup y Restore</h3>
                    <p class="text-muted text-center">Crear respaldos y restaurar la base de datos</p>
                    <a href="{{ route('administracion.backup') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-arrow-right"></i> Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-warning card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-key fa-5x text-warning mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Cambiar Contraseña</h3>
                    <p class="text-muted text-center">Cambiar contraseña del administrador</p>
                    <a href="{{ route('administracion.password') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-arrow-right"></i> Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-history fa-5x text-info mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Bitácora</h3>
                    <p class="text-muted text-center">Registro de acciones del sistema</p>
                    <a href="{{ route('administracion.bitacora') }}" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-right"></i> Acceder
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop