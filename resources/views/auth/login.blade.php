<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- App CSS -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Fuente moderna -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0c0c05, #184a5a, #38a0cc);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 380px;
        }

        .login-card {
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .25);
            overflow: hidden;
        }

        .login-header {
            background: #1f2937;
            color: #fff;
            padding: 25px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 22px;
            font-weight: 600;
            margin: 0;
        }

        .login-header p {
            font-size: 13px;
            opacity: .8;
            margin-top: 5px;
        }

        .login-body {
            padding: 30px;
            background: #fff;
        }

        .form-control {
            height: 45px;
            border-radius: 8px;
        }

        .btn-login {
            height: 45px;
            border-radius: 8px;
            font-weight: 500;
            background: #2563eb;
            border: none;
        }

        .btn-login:hover {
            background: #1e40af;
        }

        .input-group-text {
            background: transparent;
            border-left: 0;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #2563eb;
        }

        .footer-text {
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <div class="login-card">

            <div class="login-header">
                <h1>{{ config('app.name') }}</h1>
                <p>Acceso al Sistema</p>
            </div>

            <div class="login-body">

                <form method="POST" action="{{ url('/login') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label class="mb-1">Usuario</label>
                        <div class="input-group">
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control text-uppercase @error('name') is-invalid @enderror"
                                placeholder="USUARIO" required oninput="this.value = this.value.toUpperCase();">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                        </div>
                        @error('name')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="mb-1">Contraseña</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="••••••••"
                                required>
                            <div class="input-group-append">
                                <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-login btn-block">
                        <i class="fas fa-sign-in-alt mr-1"></i> Ingresar
                    </button>
                </form>

                <div class="footer-text">
                    © {{ date('Y') }} {{ config('app.name') }} · Todos los derechos reservados
                </div>
            </div>
        </div>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
    @include('sweetalert::alert')
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
