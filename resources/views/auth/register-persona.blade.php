<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #F8F4F0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }

        .register-card {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .logo {
            width: 150px;
            margin-bottom: 2rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4C342C;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            font-size: 1rem;
            color: #333;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4C342C;
            box-shadow: 0 0 0 2px rgba(76, 52, 44, 0.2);
        }

        .section-title {
            color: #4C342C;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 2rem 0 1rem 0;
            text-align: left;
            border-bottom: 2px solid #4C342C;
            padding-bottom: 0.5rem;
        }

        .optional-text {
            font-size: 0.8rem;
            color: #666;
            font-weight: normal;
        }

        .button-primary {
            width: 100%;
            padding: 0.75rem;
            background-color: #4C342C;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .button-primary:hover {
            background-color: #6a493f;
            color: #fff;
            text-decoration: none;
        }

        .error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .success-message {
            background-color: #dcfce7;
            border: 1px solid #16a34a;
            color: #166534;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .error-message {
            background-color: #fef2f2;
            border: 1px solid #dc2626;
            color: #dc2626;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .button-secondary {
            width: 100%;
            padding: 0.75rem;
            background-color: #6b7280;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 1rem;
            text-decoration: none;
            display: inline-block;
        }

        .button-secondary:hover {
            background-color: #4b5563;
            color: #fff;
            text-decoration: none;
        }

        .phone-group {
            display: flex;
            gap: 0.5rem;
        }

        .phone-group .form-group {
            margin-bottom: 0;
        }

        .phone-group .cod-pais {
            flex: 0 0 80px;
        }

        .phone-group .numero {
            flex: 1;
        }

        /* Estilos mejorados para el campo de fecha */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.5);
        }

        input[type="date"] {
            position: relative;
        }

        .date-info {
            font-size: 0.75rem;
            color: #666;
            margin-top: 0.25rem;
        }

        .validation-message {
            font-size: 0.75rem;
            color: #dc2626;
            margin-top: 0.25rem;
            display: none;
        }

        .validation-message.show {
            display: block;
        }

        input.invalid {
            border-color: #dc2626;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <img src="{{ asset('images/logo_salus.jpeg') }}" alt="Logo de Salus" class="logo">

        @if(session('success'))
            <div class="success-message">
                <h3 style="margin: 0 0 0.5rem 0; color: #166534;">¡Registro Exitoso!</h3>
                <p style="margin: 0;">
                    Hola <strong>{{ session('nombre') }} {{ session('apellido') }}</strong>,
                    tu cuenta ha sido creada exitosamente y ya has iniciado sesión.
                    <br><br>
                    Ya puedes acceder a tu dashboard.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="button-primary">Ingresar</a>
            <a href="{{ url('/register-persona') }}" class="button-secondary">Registrar Otra Cuenta</a>
        @else
            @if(session('error'))
                <div class="error-message">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif

            <h2 style="color: #4C342C; margin-bottom: 2rem;">Registrarse</h2>

            <form method="POST" action="{{ route('register.persona') }}" id="registerForm">
                @csrf

                <!-- Datos Personales -->
                <div class="section-title">Datos Personales</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="Nombre">Nombre</label>
                        <input type="text" id="Nombre" name="Nombre" value="{{ old('Nombre') }}" 
                               required autofocus maxlength="25"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                               title="Solo se permiten letras y espacios">
                        <span class="validation-message" id="nombre-error">Solo se permiten letras y espacios</span>
                        @error('Nombre')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="Apellido">Apellido</label>
                        <input type="text" id="Apellido" name="Apellido" value="{{ old('Apellido') }}" 
                               required maxlength="25"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                               title="Solo se permiten letras y espacios">
                        <span class="validation-message" id="apellido-error">Solo se permiten letras y espacios</span>
                        @error('Apellido')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="DNI">DNI / Cédula</label>
                        <input type="text" id="DNI" name="DNI" value="{{ old('DNI') }}" 
                               required maxlength="25"
                               pattern="[0-9A-Za-z\-]+"
                               title="Solo se permiten números, letras y guiones"
                               placeholder="Ej: 0801-1990-12345">
                        <span class="validation-message" id="dni-error">Formato inválido. Solo números, letras y guiones</span>
                        @error('DNI')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="Fecha_Nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="Fecha_Nacimiento" name="Fecha_Nacimiento"
                               value="{{ old('Fecha_Nacimiento') }}" 
                               required
                               min="1900-01-01"
                               max="">
                        <div class="date-info">Usa el calendario o escribe en formato DD/MM/AAAA</div>
                        <span class="validation-message" id="fecha-error">Fecha inválida</span>
                        @error('Fecha_Nacimiento')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="Genero">Género</label>
                        <select id="Genero" name="Genero" required>
                            <option value="">Seleccionar...</option>
                            <option value="Masculino" {{ old('Genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('Genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @error('Genero')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="Password">Contraseña</label>
                        <input type="password" id="Password" name="Password" required minlength="6"
                               placeholder="Mínimo 6 caracteres">
                        <span class="validation-message" id="password-error">La contraseña debe tener al menos 6 caracteres</span>
                        @error('Password')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="section-title">Información de Contacto</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="Correo">Correo Electrónico</label>
                        <input type="email" id="Correo" name="Correo" value="{{ old('Correo') }}" 
                               maxlength="50" required
                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                               placeholder="ejemplo@correo.com">
                        <span class="validation-message" id="correo-error">Formato de correo inválido</span>
                        @error('Correo')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="Tipo_correo">Tipo de Correo</label>
                        <select id="Tipo_correo" name="Tipo_correo">
                            <option value="Personal" {{ old('Tipo_correo', 'Personal') == 'Personal' ? 'selected' : '' }}>
                                Personal</option>
                            <option value="Laboral" {{ old('Tipo_correo') == 'Laboral' ? 'selected' : '' }}>Laboral</option>
                            <option value="Otro" {{ old('Tipo_correo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('Tipo_correo')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Teléfono <span class="optional-text">(Opcional)</span></label>
                    <div class="phone-group">
                        <div class="form-group cod-pais">
                            <input type="text" id="Cod_Pais" name="Cod_Pais" value="{{ old('Cod_Pais', '+504') }}"
                                   placeholder="+504" maxlength="10"
                                   pattern="\+[0-9]+"
                                   title="Formato: +504">
                        </div>
                        <div class="form-group numero">
                            <input type="tel" id="Numero" name="Numero" value="{{ old('Numero') }}"
                                   placeholder="Número de teléfono" maxlength="20"
                                   pattern="[0-9\-\s]+"
                                   title="Solo números, espacios y guiones">
                            <span class="validation-message" id="telefono-error">Solo se permiten números</span>
                        </div>
                    </div>
                    @error('Numero')
                        <span class="error">{{ $message }}</span>
                    @enderror
                    @error('Cod_Pais')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="Tipo">Tipo de Teléfono</label>
                        <select id="Tipo" name="Tipo">
                            <option value="Movil" {{ old('Tipo', 'Movil') == 'Movil' ? 'selected' : '' }}>Móvil</option>
                            <option value="Fijo" {{ old('Tipo') == 'Fijo' ? 'selected' : '' }}>Fijo</option>
                        </select>
                        @error('Tipo')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="Descripcion_Tel">Descripción del Teléfono</label>
                        <input type="text" id="Descripcion_Tel" name="Descripcion_Tel" value="{{ old('Descripcion_Tel') }}"
                            placeholder="Ej: Trabajo, Casa..." maxlength="50">
                        @error('Descripcion_Tel')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Dirección -->
                <div class="section-title">Dirección <span class="optional-text">(Opcional)</span></div>

                <div class="form-group">
                    <label for="Direccion">Dirección</label>
                    <input type="text" id="Direccion" name="Direccion" value="{{ old('Direccion') }}"
                        placeholder="Ingrese su dirección completa" maxlength="50">
                    @error('Direccion')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Descripcion_Dir">Descripción de la Dirección</label>
                    <input type="text" id="Descripcion_Dir" name="Descripcion_Dir" value="{{ old('Descripcion_Dir') }}"
                        placeholder="Ej: Casa, Oficina, Referencias..." maxlength="120">
                    @error('Descripcion_Dir')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="button-primary">Registrarse</button>
            </form>

            <p style="margin-top: 1rem; font-size: 0.875rem;">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color: #4C342C; text-decoration: none;">Inicia
                    sesión aquí</a>
            </p>
        @endif
    </div>

    <script>
        // Establecer fecha máxima (hoy) y mínima para el campo de fecha
        document.addEventListener('DOMContentLoaded', function() {
            const fechaNacimiento = document.getElementById('Fecha_Nacimiento');
            const today = new Date();
            const maxDate = today.toISOString().split('T')[0];
            fechaNacimiento.setAttribute('max', maxDate);
            
            // Validación en tiempo real para nombre
            document.getElementById('Nombre').addEventListener('input', function(e) {
                const valor = e.target.value;
                const regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]*$/;
                
                if (!regex.test(valor)) {
                    e.target.value = valor.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
                    e.target.classList.add('invalid');
                    document.getElementById('nombre-error').classList.add('show');
                } else {
                    e.target.classList.remove('invalid');
                    document.getElementById('nombre-error').classList.remove('show');
                }
            });
            
            // Validación en tiempo real para apellido
            document.getElementById('Apellido').addEventListener('input', function(e) {
                const valor = e.target.value;
                const regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]*$/;
                
                if (!regex.test(valor)) {
                    e.target.value = valor.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
                    e.target.classList.add('invalid');
                    document.getElementById('apellido-error').classList.add('show');
                } else {
                    e.target.classList.remove('invalid');
                    document.getElementById('apellido-error').classList.remove('show');
                }
            });
            
            // Validación en tiempo real para DNI
            document.getElementById('DNI').addEventListener('input', function(e) {
                const valor = e.target.value;
                const regex = /^[0-9A-Za-z\-]*$/;
                
                if (!regex.test(valor)) {
                    e.target.value = valor.replace(/[^0-9A-Za-z\-]/g, '');
                    e.target.classList.add('invalid');
                    document.getElementById('dni-error').classList.add('show');
                } else {
                    e.target.classList.remove('invalid');
                    document.getElementById('dni-error').classList.remove('show');
                }
            });
            
            // Validación para teléfono - solo números
            document.getElementById('Numero').addEventListener('input', function(e) {
                const valor = e.target.value;
                const regex = /^[0-9\-\s]*$/;
                
                if (!regex.test(valor)) {
                    e.target.value = valor.replace(/[^0-9\-\s]/g, '');
                    e.target.classList.add('invalid');
                    document.getElementById('telefono-error').classList.add('show');
                } else {
                    e.target.classList.remove('invalid');
                    document.getElementById('telefono-error').classList.remove('show');
                }
            });
            
            // Validación para código de país
            document.getElementById('Cod_Pais').addEventListener('input', function(e) {
                const valor = e.target.value;
                
                // Asegurar que comience con +
                if (valor.length > 0 && !valor.startsWith('+')) {
                    e.target.value = '+' + valor.replace(/[^0-9]/g, '');
                } else {
                    e.target.value = valor.replace(/[^+0-9]/g, '');
                }
            });
            
            // Validación de contraseña
            document.getElementById('Password').addEventListener('input', function(e) {
                if (e.target.value.length < 6 && e.target.value.length > 0) {
                    e.target.classList.add('invalid');
                    document.getElementById('password-error').classList.add('show');
                } else {
                    e.target.classList.remove('invalid');
                    document.getElementById('password-error').classList.remove('show');
                }
            });
            
            // Validación de correo electrónico
            document.getElementById('Correo').addEventListener('blur', function(e) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(e.target.value) && e.target.value.length > 0) {
                    e.target.classList.add('invalid');
                    document.getElementById('correo-error').classList.add('show');
                } else {
                    e.target.classList.remove('invalid');
                    document.getElementById('correo-error').classList.remove('show');
                }
            });
            
            // Validación de fecha
            document.getElementById('Fecha_Nacimiento').addEventListener('change', function(e) {
                const selectedDate = new Date(e.target.value);
                const today = new Date();
                const minDate = new Date('1900-01-01');
                
                if (selectedDate > today || selectedDate < minDate) {
                    e.target.classList.add('invalid');
                    document.getElementById('fecha-error').classList.add('show');
                    e.target.value = '';
                } else {
                    e.target.classList.remove('invalid');
                    document.getElementById('fecha-error').classList.remove('show');
                }
            });
        });
    </script>
</body>

</html>