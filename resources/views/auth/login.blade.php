<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ingresar | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body.login-page-gts {
            margin: 0; min-height: 100vh; min-height: 100dvh; padding: 28px 16px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Inter', sans-serif; color: #24344c;
            background:
                linear-gradient(120deg,rgba(2,14,34,.85),rgba(7,55,112,.72) 51%,rgba(2,17,36,.85)),
                url("{{ asset('img/articulos/bg_sistema.png') }}") center/cover no-repeat fixed;
            position: relative; overflow-x: hidden;
        }
        body.login-page-gts::before {
            content: ""; position: fixed; inset: 0; pointer-events: none;
            background: radial-gradient(ellipse at 52% 20%,rgba(64,154,255,.20),transparent 48%);
        }
        .gts-login-wrap { width: 100%; max-width: 450px; position: relative; z-index: 1; }
        .gts-login-card {
            background: #fff; border-radius: 20px; overflow: hidden;
            box-shadow: 0 24px 70px rgba(0,10,32,.35);
            border: 1px solid rgba(255,255,255,.35);
        }
        .gts-login-header {
            background: linear-gradient(125deg,#1761db,#163e94);
            color: #fff; text-align: center; padding: 30px 28px 28px;
        }
        .gts-login-brand {
            width: 70px; height: 70px; margin: 0 auto 16px; border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.97); box-shadow: 0 8px 20px rgba(0,20,70,.17);
            overflow: hidden;
        }
        .gts-login-brand img { width: 61px; height: 61px; object-fit: contain; }
        .gts-login-header h1 { font-size: 23px; font-weight: 800; letter-spacing: -.5px; margin: 0; color: #fff; }
        .gts-login-header p { font-size: 13px; color: rgba(255,255,255,.84); margin: 8px 0 0; }
        .gts-login-body { padding: 30px 32px 24px; }
        .gts-login-welcome { font-size: 17px; font-weight: 750; letter-spacing: -.3px; color: #21314b; margin: 0 0 5px; }
        .gts-login-subtitle { margin: 0 0 24px; color: #76859b; font-size: 12px; line-height: 1.6; }
        .gts-login-field { margin-bottom: 20px; }
        .gts-login-field label { font-size: 13px; font-weight: 700; color: #34445b; margin-bottom: 8px; display: block; }
        .gts-login-input { position: relative; }
        .gts-login-input .field-icon {
            position: absolute; left: 15px; top: 50%; transform: translateY(-50%);
            color: #7186a1; z-index: 1; font-size: 15px; pointer-events: none;
        }
        .gts-login-input input {
            height: 48px; width: 100%; padding: 0 49px 0 43px;
            background: #f7f9fd; border: 1px solid #dce5f1; border-radius: 10px;
            font-size: 14px; color: #24344c; box-shadow: none !important;
            transition: border-color .18s,background .18s,box-shadow .18s;
        }
        .gts-login-input input::placeholder { color: #97a4b6; }
        .gts-login-input input:focus { background: #fff; border-color: #4383e7; outline: none; box-shadow: 0 0 0 3px rgba(45,116,228,.12) !important; }
        .gts-password-toggle {
            position: absolute; right: 5px; top: 5px; bottom: 5px;
            border: 0; border-radius: 8px; padding: 0; width: 39px;
            background: transparent; color: #5477ae; cursor: pointer; font-size: 15px;
        }
        .gts-password-toggle:hover,.gts-password-toggle:focus { background: #e7efff; outline: none; }
        .gts-login-submit {
            display: flex; align-items: center; justify-content: center; gap: 9px;
            background: #2463e6; color: #fff; font-size: 14px; font-weight: 700;
            border: 0; border-radius: 10px; min-height: 48px; width: 100%;
            margin-top: 26px; transition: background .2s,transform .2s,box-shadow .2s;
            box-shadow: 0 6px 15px rgba(36,99,230,.18); cursor: pointer;
        }
        .gts-login-submit:hover { background: #174cbc; box-shadow: 0 8px 19px rgba(36,99,230,.24); transform: translateY(-1px); color: #fff; }
        .gts-login-submit:disabled { opacity: .7; cursor: wait; transform: none; }
        .gts-login-alert {
            margin: 0 0 18px; background: #fff5f4; border: 1px solid #ffdcd8;
            border-radius: 9px; padding: 11px 13px; color: #a5332b; font-size: 12px; line-height: 1.5;
        }
        .gts-login-footer {
            border-top: 1px solid #edf0f5; padding-top: 19px; margin-top: 26px;
            color: #8a97a8; text-align: center; font-size: 11px; line-height: 1.7;
        }
        .gts-login-footer i { color: #4d8b69; margin-right: 5px; }
        @media(max-width: 480px) {
            body.login-page-gts { padding: 16px 12px; align-items: center; }
            .gts-login-header { padding: 25px 22px 23px; }
            .gts-login-body { padding: 25px 22px 20px; }
            .gts-login-header h1 { font-size: 21px; }
        }
        @media(prefers-reduced-motion:reduce) { .gts-login-submit,.gts-login-input input { transition: none; } }

        /* Versión premium: superficie luminosa, mayor jerarquía y microinteracciones */
        body.login-page-gts {
            background:
                radial-gradient(circle at 18% 85%,rgba(21,121,225,.32),transparent 35%),
                linear-gradient(120deg,rgba(2,12,31,.89),rgba(4,45,101,.66) 55%,rgba(2,13,34,.89)),
                url("{{ asset('img/articulos/bg_sistema.png') }}") center/cover no-repeat fixed;
        }
        .gts-login-wrap { max-width: 475px; }
        .gts-login-card { border-radius: 24px; box-shadow:0 30px 90px rgba(0,9,31,.43); }
        .gts-login-header { padding:34px 34px 29px; background:linear-gradient(130deg,#1749b7,#235ee3 58%,#1884d5); position:relative; }
        .gts-login-header:after { content:"";position:absolute;left:11%;right:11%;bottom:0;height:1px;background:rgba(255,255,255,.25); }
        .gts-login-brand { border-radius:21px; width:76px;height:76px;margin-bottom:18px;transform:rotate(-3deg); }
        .gts-login-brand img { width:67px;height:67px;transform:rotate(3deg); }
        .gts-login-header h1 { font-size:25px;letter-spacing:-.65px; }
        .gts-login-body { padding:34px 36px 28px; }
        .gts-login-welcome { font-size:20px; }
        .gts-login-subtitle { font-size:13px; margin-bottom:27px; }
        .gts-login-field { margin-bottom:22px; }
        .gts-login-input input { height:52px; border-radius:12px; background:#f8fafd; }
        .gts-login-input .field-icon { left:17px; }
        .gts-login-input input { padding-left:46px; }
        .gts-login-input:focus-within .field-icon { color:#2362dc; }
        .gts-login-submit { height:52px;border-radius:12px;margin-top:28px; }
        .gts-login-footer { margin-top:28px; }
        .gts-login-small-label { color:#9ec2ff;font-size:10px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px;display:block; }
        .gts-login-secure { display:flex;align-items:center;justify-content:center;gap:7px;font-size:11px;color:#7b8da3;margin-top:15px; }
        .gts-login-secure i { color:#23865d; }
        @media(max-width:480px){.gts-login-wrap{max-width:100%}.gts-login-header{padding:28px 22px 25px}.gts-login-body{padding:27px 23px 22px}.gts-login-card{border-radius:18px}}

        /* Ajuste compacto: proporciones equilibradas para escritorio y móvil */
        .gts-login-wrap { max-width: 390px; }
        .gts-login-card { border-radius: 17px; box-shadow: 0 20px 55px rgba(0,9,31,.32); }
        .gts-login-header { padding: 22px 25px 20px; }
        .gts-login-brand { width: 55px; height: 55px; border-radius: 15px; margin-bottom: 11px; transform: none; }
        .gts-login-brand img { width: 49px; height: 49px; transform: none; }
        .gts-login-small-label { font-size: 9px; margin-bottom: 6px; letter-spacing: 1.1px; }
        .gts-login-header h1 { font-size: 21px; letter-spacing: -.3px; }
        .gts-login-header p { font-size: 11px; margin-top: 5px; }
        .gts-login-body { padding: 22px 26px 19px; }
        .gts-login-welcome { font-size: 17px; margin-bottom: 3px; }
        .gts-login-subtitle { font-size: 11px; margin-bottom: 19px; }
        .gts-login-field { margin-bottom: 15px; }
        .gts-login-field label { font-size: 12px; margin-bottom: 6px; }
        .gts-login-input input { height: 43px; border-radius: 9px; font-size: 13px; }
        .gts-login-submit { height: 44px; min-height: 44px; margin-top: 20px; border-radius: 9px; font-size: 13px; }
        .gts-login-secure { margin-top: 11px; font-size: 10px; text-align: center; }
        .gts-login-footer { margin-top: 17px; padding-top: 13px; font-size: 10px; }
        @media(max-width:480px) {
            .gts-login-wrap { max-width: 390px; }
            .gts-login-header { padding: 20px 20px 18px; }
            .gts-login-body { padding: 21px 20px 17px; }
        }
    </style>
</head>
<body class="login-page-gts">
    <main class="gts-login-wrap">
        <div class="gts-login-card">
            <header class="gts-login-header">
                <div class="gts-login-brand">
                    <img src="{{ asset('storage/logos/gts_logo.jpg') }}" alt="Logo GTS Mayorista">
                </div>
                <span class="gts-login-small-label">Plataforma de gestión</span>
                <h1>GTS MAYORISTA</h1>
                <p>Sistema de gestión mayorista</p>
            </header>
            <div class="gts-login-body">
                <h2 class="gts-login-welcome">Bienvenido de nuevo</h2>
                <p class="gts-login-subtitle">Ingresá tus credenciales para acceder al sistema.</p>

                @if ($errors->any())
                    <div class="gts-login-alert" role="alert">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        No pudimos iniciar sesión. Verificá tu usuario y contraseña.
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}" id="gtsLoginForm">
                    @csrf
                    <div class="gts-login-field">
                        <label for="name">Usuario</label>
                        <div class="gts-login-input">
                            <i class="far fa-user field-icon"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                placeholder="Ingresá tu usuario" autocomplete="username"
                                autocapitalize="characters" required maxlength="255"
                                oninput="this.value = this.value.toUpperCase();">
                        </div>
                    </div>
                    <div class="gts-login-field">
                        <label for="password">Contraseña</label>
                        <div class="gts-login-input">
                            <i class="fas fa-lock field-icon"></i>
                            <input id="password" type="password" name="password"
                                placeholder="Ingresá tu contraseña" autocomplete="current-password" required>
                            <button id="gtsPasswordToggle" class="gts-password-toggle" type="button"
                                aria-label="Mostrar contraseña" aria-pressed="false">
                                <i class="far fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <button class="gts-login-submit" type="submit" id="gtsLoginSubmit">
                        <i class="fas fa-sign-in-alt"></i> Ingresar al sistema
                    </button>
                </form>
                <div class="gts-login-secure"><i class="fas fa-lock"></i> Tus credenciales se envían de forma segura cuando utilizás HTTPS.</div>
                <div class="gts-login-footer">
                    <i class="fas fa-shield-alt"></i> Acceso exclusivo para usuarios autorizados
                    <div>© {{ date('Y') }} GTS MAYORISTA</div>
                </div>
            </div>
        </div>
    </main>
    <script src="{{ mix('js/app.js') }}"></script>
    @include('sweetalert::alert')
    <script>
        (function () {
            const password = document.getElementById('password');
            const toggle = document.getElementById('gtsPasswordToggle');
            const submit = document.getElementById('gtsLoginSubmit');
            const form = document.getElementById('gtsLoginForm');
            toggle.addEventListener('click', function () {
                const visible = password.type === 'password';
                password.type = visible ? 'text' : 'password';
                toggle.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
                toggle.setAttribute('aria-pressed', visible ? 'true' : 'false');
                toggle.querySelector('i').className = visible ? 'far fa-eye-slash' : 'far fa-eye';
            });
            form.addEventListener('submit', function () {
                if (!form.checkValidity()) return;
                submit.disabled = true;
                submit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ingresando...';
            });
        })();
    </script>
</body>
</html>