@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <!-- Icono de seguridad -->
                <div class="card-header bg-primary text-white text-center border-0 pt-4 pb-3">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-4x"></i>
                    </div>
                    <h4 class="mb-1">Verificación de Seguridad</h4>
                    <p class="mb-0 small">Autenticación de Dos Factores</p>
                </div>

                <div class="card-body p-4">
                    <p class="text-center text-muted mb-4">
                        Ingresa el código de <strong>6 dígitos</strong> desde tu aplicación de autenticación
                    </p>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Error:</strong> {{ $errors->first('one_time_password') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('2fa.verify') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="one_time_password" class="form-label text-center w-100">
                                <i class="fas fa-mobile-alt"></i> Código de verificación
                            </label>
                            <input type="text" 
                                   id="one_time_password"
                                   name="one_time_password" 
                                   class="form-control form-control-lg text-center @error('one_time_password') is-invalid @enderror"
                                   placeholder="● ● ● ● ● ●"
                                   maxlength="6"
                                   style="letter-spacing: 0.8rem; font-size: 2rem; font-weight: 600;"
                                   autocomplete="off"
                                   required 
                                   autofocus>
                            
                            @error('one_time_password')
                                <div class="invalid-feedback text-center">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> Verificar y Continuar
                        </button>

                        <div class="text-center">
                            <p class="text-muted small mb-2">¿Problemas para acceder?</p>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none">
                                    <i class="fas fa-arrow-left"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light text-center border-0">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Usa Google Authenticator, Microsoft Authenticator o cualquier app TOTP
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animación para el escudo */
    @keyframes shield-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .fa-shield-alt {
        animation: shield-pulse 2s infinite;
    }
    
    /* Efecto hover en el input */
    #one_time_password:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        transform: scale(1.02);
        transition: all 0.3s ease;
    }
    
    /* Mejora visual del botón */
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: transform 0.2s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
</style>
@endsection