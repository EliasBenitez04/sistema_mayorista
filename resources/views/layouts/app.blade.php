<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
        integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
        crossorigin="anonymous" />

    {{-- App CSS --}}
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    @stack('third_party_stylesheets')
    @stack('page_css')

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Roboto', sans-serif;
        }

        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 38px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding: 4px 5px;
            width: 100%;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3c8dbc;
            border-color: #367fa9;
            color: #fff;
            padding: 1px 10px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border-radius: 3px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 35px;
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
        }

        .select2-selection__arrow {
            height: 42px !important;
        }

        /* Menú de usuario GTS: estilos limitados al desplegable de la barra superior */
        .gts-user-trigger { gap: 7px; padding-left: 12px !important; padding-right: 12px !important; border-radius: 9px; transition: background .18s ease; }
        .gts-user-trigger:hover, .gts-user-trigger[aria-expanded="true"] { background: rgba(255,255,255,.13); }
        .gts-user-trigger-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.8); }
        .gts-user-trigger-name { font-size: 13px; font-weight: 700; max-width: 145px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .gts-user-chevron { font-size: 10px; opacity: .75; transition: transform .2s; }
        .gts-user-trigger[aria-expanded="true"] .gts-user-chevron { transform: rotate(180deg); }
        .gts-user-dropdown { width: min(340px, calc(100vw - 24px)); border: 1px solid #e6ebf2; border-radius: 15px; background: #fff; overflow: hidden; box-shadow: 0 14px 36px rgba(18,37,63,.22); margin-top: 9px; }
        .gts-user-dropdown-header { display: flex; gap: 13px; align-items: center; padding: 21px 18px; background: linear-gradient(120deg,#f1f6ff,#fff); border-bottom: 1px solid #e7edf5; min-width: 0; }
        .gts-user-avatar { width: 62px; height: 62px; flex-shrink: 0; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 3px 11px rgba(39,70,114,.14); }
        .gts-user-details { display: flex; flex-direction: column; min-width: 0; gap: 3px; }
        .gts-user-caption { font-size: 10px; font-weight: 800; letter-spacing: .9px; color: #267452; }
        .gts-user-fullname { font-size: 17px; line-height: 1.3; color: #1e2d43; overflow-wrap: anywhere; }
        .gts-user-email { color: #687a90; font-size: 12px; overflow-wrap: anywhere; }
        .gts-user-dropdown-actions { padding: 10px; display: flex; flex-direction: column; gap: 5px; }
        .gts-user-action { display: flex; align-items: center; gap: 11px; width: 100%; padding: 11px 12px; background: transparent; border: 0; border-radius: 9px; font-weight: 650; font-size: 13px; text-decoration: none !important; text-align: left; cursor: pointer; color: #34465d; transition: background .18s ease, transform .18s ease; }
        .gts-user-action:hover, .gts-user-action:focus { background: #eff5ff; color: #1e5bc4; outline: none; transform: translateX(2px); }
        .gts-user-action-icon { width: 34px; height: 34px; display: inline-flex; justify-content: center; align-items: center; background: #eff5fc; border-radius: 9px; color: #3568ac; font-size: 15px; }
        .gts-user-action-arrow { font-size: 10px; margin-left: auto; color: #94a3b8; }
        .gts-user-logout { color: #b53642; }
        .gts-user-logout .gts-user-action-icon { color: #cc3a46; background: #fff0f1; }
        .gts-user-logout:hover, .gts-user-logout:focus { color: #aa2834; background: #fff1f2; }
        @media(max-width: 575px) { .gts-user-dropdown { margin-right: 2px; } .gts-user-dropdown-header { padding: 17px 14px; } }
    </style>
</head>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form.confirm-submit').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Desea Guardar Los Cambios?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Guardar',
                    cancelButtonText: 'No, Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Alertas desde sesión
        @if (Session::has('swal-alert'))
            let alertData = @json(Session::get('swal-alert'));
            Swal.fire({
                icon: alertData.icon,
                title: alertData.title,
                text: alertData.text,
                confirmButtonText: 'Aceptar'
            });
        @endif
    });
</script>

<body class="hold-transition sidebar-mini layout-navbar-not-fixed">
    <div class="wrapper">

        {{-- NAVBAR --}}
        <nav class="main-header navbar navbar-expand navbar-lightblue navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle gts-user-trigger d-flex align-items-center"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ asset('storage/logos/gts_logo.jpg') }}" class="gts-user-trigger-avatar"
                            alt="Avatar de usuario">
                        <span class="d-none d-md-inline gts-user-trigger-name">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down gts-user-chevron ml-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right gts-user-dropdown p-0">
                        <div class="gts-user-dropdown-header">
                            <img src="{{ asset('storage/logos/gts_logo.jpg') }}"
                                alt="Avatar de usuario" class="gts-user-avatar">
                            <div class="gts-user-details">
                                <span class="gts-user-caption">CUENTA ACTIVA</span>
                                <strong class="gts-user-fullname">{{ Auth::user()->name }}</strong>
                                @if (Auth::user()->email)
                                    <span class="gts-user-email">{{ Auth::user()->email }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="gts-user-dropdown-actions">
                            <a href="{{ url('users/detail/perfil') }}" class="gts-user-action gts-user-profile">
                                <span class="gts-user-action-icon"><i class="far fa-user-circle"></i></span>
                                <span>Mi perfil</span>
                                <i class="fas fa-chevron-right gts-user-action-arrow"></i>
                            </a>
                            <button type="submit" form="logout-form" class="gts-user-action gts-user-logout">
                                <span class="gts-user-action-icon"><i class="fas fa-sign-out-alt"></i></span>
                                <span>Cerrar sesión</span>
                                <i class="fas fa-chevron-right gts-user-action-arrow"></i>
                            </button>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>

        {{-- SIDEBAR --}}
        @include('layouts.sidebar')

        {{-- CONTENT --}}
        <div class="content-wrapper">
            @yield('content')
        </div>

        {{-- FOOTER --}}
        {{-- <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.0.5
            </div>
            <strong>
                Copyright &copy; 2014-2022
                <a href="https://adminlte.io">AdminLTE.io</a>.
            </strong>
            All rights reserved.
        </footer> --}}
    </div>

    {{-- ================== SCRIPTS ================== --}}

    {{-- App JS (incluye jQuery + Bootstrap + AdminLTE) --}}
    <script src="{{ mix('js/app.js') }}"></script>

    {{-- Select2 JS (DESPUÉS de jQuery) --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- SweetAlert --}}

    {{-- SweetAlert Confirm --}}
    <script>
        $('.alert-delete').click(function(event) {
            event.preventDefault();

            let form = $(this).closest("form");
            let valor = $(this).data("mensaje") || "Este Registro";
            let accion = $(this).data("accion") || "Borrar";

            Swal.fire({
                title: "Atención",
                text: `¿Desea ${accion} ${valor}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    <script>
        $('.alert-confirm').click(function(event) {
            event.preventDefault();

            let form = $(this).closest("form");
            let valor = $(this).data("mensaje") || "este pedido";
            let accion = $(this).data("accion") || "confirmar";

            Swal.fire({
                title: "Atención",
                text: `¿Desea ${accion} ${valor}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>

    {{-- BUSCADOR AJAX --}}
    <script>
        $(document).ready(function() {
            $('.buscar').on('keyup', function() {
                let query = this.value;
                let url = this.getAttribute('data-url');

                fetch(url + '?buscar=' + encodeURIComponent(query), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        $('.tabla-container').html(html);
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>
    {{-- BUSCADOR AJAX FOTOS --}}
    <script>
        $(document).ready(function() {
            $('#form-busqueda-fotos .buscar-fotos').on('keyup', function() {
                let query = this.value;
                let url = this.getAttribute('data-url');

                // AJAX usando Fetch
                fetch(url + '?search=' + encodeURIComponent(query), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        // Reemplaza la tabla de fotos
                        $('.tabla-fotos-container').html(html);
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>


    {{-- STACKS --}}
    @stack('third_party_scripts')
    @stack('page_scripts')
    @include('sweetalert::alert')

    <script>
        $(".select2:visible").select2({
            placeholder: "Seleccione...",
            width: '100%',
            allowClear: true
        });
    </script>
</body>

</html>
