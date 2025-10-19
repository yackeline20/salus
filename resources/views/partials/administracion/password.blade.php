@extends('adminlte::page')

@section('title', 'Cambio de Contraseña')

@section('content_header')
    <h1>
        <i class="fas fa-key"></i> Cambio de Contraseña: ADMINISTRADOR
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Mensajes -->
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header text-center bg-warning">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('administracion.password.cambiar') }}" method="POST">
                        @csrf
                        
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle fa-5x text-warning"></i>
                            <h4 class="mt-2">{{ Auth::user()->name }}</h4>
                        </div>

                        <div class="form-group">
                            <label for="password_actual">
                                <i class="fas fa-lock"></i> Contraseña Actual:
                            </label>
                            <input type="password" 
                                   class="form-control @error('password_actual') is-invalid @enderror" 
                                   id="password_actual" 
                                   name="password_actual" 
                                   required
                                   placeholder="Ingrese su contraseña actual">
                        </div>

                        <div class="form-group">
                            <label for="password_nueva">
                                <i class="fas fa-key"></i> Nueva Contraseña:
                            </label>
                            <input type="password" 
                                   class="form-control @error('password_nueva') is-invalid @enderror" 
                                   id="password_nueva" 
                                   name="password_nueva" 
                                   required
                                   placeholder="Ingrese la nueva contraseña">
                            <small class="form-text text-muted">Mínimo 6 caracteres</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmar">
                                <i class="fas fa-check-double"></i> Repita Contraseña:
                            </label>
                            <input type="password" 
                                   class="form-control @error('password_confirmar') is-invalid @enderror" 
                                   id="password_confirmar" 
                                   name="password_confirmar" 
                                   required
                                   placeholder="Confirme la nueva contraseña">
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save"></i> Cambiar Contraseña
                            </button>
                            <a href="{{ route('administracion') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop