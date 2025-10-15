@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Verificación de Dos Pasos</div>

                <div class="card-body">
                    <p class="mb-3">Ingresa el código de 6 dígitos de tu aplicación de autenticación:</p>

                    <form method="POST" action="{{ route('2fa.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="text" 
                                   name="one_time_password" 
                                   class="form-control form-control-lg text-center @error('one_time_password') is-invalid @enderror"
                                   placeholder="123456"
                                   maxlength="6"
                                   style="letter-spacing: 0.5em; font-size: 2rem;"
                                   required 
                                   autofocus>
                            
                            @error('one_time_password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Verificar</button>
                        <a href="{{ route('login') }}" class="btn btn-link w-100 mt-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection