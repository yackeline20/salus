@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Configurar Autenticación de Dos Pasos</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(!auth()->user()->google2fa_enabled)
                        <p class="mb-3">Escanea este código QR con tu aplicación de autenticación (Google Authenticator, Microsoft Authenticator, Authy, etc.):</p>
                        
                        <div class="text-center my-4">
                            <img src="data:image/svg+xml;base64,{{ $qrCodeImage }}" alt="QR Code">
                        </div>

                        <p class="text-center mb-4">O ingresa manualmente este código: <strong>{{ $secret }}</strong></p>

                        <form method="POST" action="{{ route('2fa.enable') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="one_time_password" class="form-label">Código de verificación (6 dígitos):</label>
                                <input type="text" 
                                       id="one_time_password"
                                       name="one_time_password" 
                                       class="form-control @error('one_time_password') is-invalid @enderror"
                                       maxlength="6"
                                       placeholder="123456"
                                       required 
                                       autofocus>
                                
                                @error('one_time_password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Habilitar 2FA</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancelar</a>
                        </form>
                    @else
                        <div class="alert alert-success">
                            ✓ La autenticación de dos pasos está habilitada
                        </div>

                        <form method="POST" action="{{ route('2fa.disable') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de querer deshabilitar 2FA?')">Deshabilitar 2FA</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection