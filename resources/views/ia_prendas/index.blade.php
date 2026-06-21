@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>IA - Eliminación de Fondo</h1>
                </div>

            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('sweetalert::alert')

        <div class="clearfix"></div>

        <div class="card shadow border-0 rounded-lg">

            <div class="card-header bg-blue text-white d-flex align-items-center">
                <i class="fas fa-magic mr-2"></i>
                <h3 class="card-title mb-0">Procesador de Imágenes IA</h3>
            </div>

            <div class="card-body p-4">

                {{-- MENSAJES --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>

                    @if (session('log'))
                        <pre>{{ print_r(session('log'), true) }}</pre>
                    @endif
                @endif

                {{-- FORM --}}
                <form id="formIA" method="POST" action="{{ route('ia.subir') }}" enctype="multipart/form-data">

                    @csrf

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">
                            Seleccionar imágenes
                        </label>

                        <input type="file" name="imagenes[]" class="form-control" multiple accept="image/*" required>
                    </div>

                    <button type="submit" id="btnIA" class="btn btn-primary btn-lg px-4 shadow-sm">
                        <i class="fas fa-magic mr-2"></i>
                        Procesar IA
                    </button>

                    <a href="{{ route('ia.descargar') }}" class="btn btn-success btn-lg px-4 shadow-sm">
                        <i class="fas fa-download mr-2"></i>
                        Descargar ZIP
                    </a>

                </form>

            </div>

        </div>

    </div>

    {{-- ================= OVERLAY IA ================= --}}
    <div id="loadingOverlay">

        <div class="loading-box">

            <div class="icon-circle">
                <i class="fas fa-robot"></i>
            </div>

            <div class="spinner-border text-primary mb-3" style="width:55px;height:55px;"></div>

            <h4>Procesando IA</h4>
            <p>Eliminando fondo de las imágenes...</p>

            <div class="progress-custom">
                <div id="progressBar"></div>
            </div>

            <div id="counter">0s</div>

        </div>

    </div>

    @if (session('preview_ia'))
        <div class="mt-4">

            <h4 class="mb-3">Vista previa antes / después</h4>

            <div class="row">

                @foreach (session('preview_ia') as $img)
                    <div class="col-md-4 mb-4">

                        <div class="card shadow-sm">

                            <div class="card-body">

                                <div class="text-center mb-2">
                                    <small class="text-muted">ANTES</small>
                                    <img src="{{ $img['original'] }}" class="img-fluid rounded border"
                                        style="height: 180px; object-fit: cover;">
                                </div>

                                <div class="text-center">
                                    <small class="text-success">DESPUÉS (IA)</small>
                                    <img src="{{ $img['procesada'] }}" class="img-fluid rounded border border-success"
                                        style="height: 180px; object-fit: cover;">
                                </div>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>
    @endif

    <style>
        #loadingOverlay {
            position: fixed;
            inset: 0;
            display: none;
            z-index: 99999;
            background: rgba(0, 0, 0, .65);
            backdrop-filter: blur(8px);
            justify-content: center;
            align-items: center;
        }

        .loading-box {
            width: 380px;
            background: #fff;
            border-radius: 18px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
        }

        .progress-custom {
            width: 100%;
            height: 10px;
            background: #e5e7eb;
            border-radius: 30px;
            overflow: hidden;
            margin-bottom: 18px;
        }

        #progressBar {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #3b82f6, #60a5fa);
            transition: width .4s ease;
        }

        #counter {
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
        }

        .card img {
            transition: all .3s ease;
        }

        .card img:hover {
            transform: scale(1.03);
        }
    </style>

    <script>
        let seconds = 0;
        let interval;
        let progress = 0;
        let fakeBar;

        document.getElementById('formIA').addEventListener('submit', function() {

            document.getElementById('loadingOverlay').style.display = 'flex';

            let btn = document.getElementById('btnIA');
            btn.disabled = true;
            btn.innerHTML = `
        <span class="spinner-border spinner-border-sm mr-2"></span>
        Procesando...
    `;

            seconds = 0;
            progress = 0;

            document.getElementById('counter').innerText = "0s";
            document.getElementById('progressBar').style.width = "0%";

            interval = setInterval(() => {
                seconds++;
                document.getElementById('counter').innerText = seconds + "s";
            }, 1000);

            fakeBar = setInterval(() => {
                if (progress < 90) {
                    progress += Math.random() * 8;
                    document.getElementById('progressBar').style.width = progress + "%";
                }
            }, 500);

        });
    </script>
@endsection
