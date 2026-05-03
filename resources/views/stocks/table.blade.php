@include('sweetalert::alert')

<div class="card shadow border-0 rounded-lg">
    <div class="card-header bg-success text-white d-flex align-items-center">
        <i class="fas fa-file-import mr-2"></i>
        <h3 class="card-title mb-0">Importar Stock</h3>
    </div>

    <div class="card-body p-4">

        <form id="formImportStock" action="{{ route('import.stock') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="font-weight-bold text-dark">
                    Archivo Excel
                </label>

                <div class="custom-file">
                    <input type="file" name="archivo" class="custom-file-input" id="archivoInput" accept=".xlsx,.xls"
                        required>

                    <label class="custom-file-label" for="archivoInput">
                        Seleccionar archivo...
                    </label>
                </div>

                <small class="text-muted">
                    Formatos permitidos: .xlsx / .xls
                </small>
            </div>

            <button type="submit" id="btnImportar" class="btn btn-success btn-lg px-4 shadow-sm">
                <i class="fas fa-file-excel mr-2"></i>
                Importar desde Excel
            </button>

        </form>

    </div>
</div>

<!-- ================= OVERLAY PROFESIONAL ================= -->
<div id="loadingOverlay">

    <div class="loading-box">

        <!-- Logo/Icono -->
        <div class="icon-circle">
            <i class="fas fa-database"></i>
        </div>

        <!-- Spinner -->
        <div class="spinner-border text-success mb-3" style="width:55px;height:55px;"></div>

        <!-- Texto -->
        <h4>Importando Stock</h4>
        <p>Procesando archivo y actualizando sucursales...</p>

        <!-- Barra progreso -->
        <div class="progress-custom">
            <div id="progressBar"></div>
        </div>

        <!-- Tiempo -->
        <div id="counter">0s</div>

    </div>

</div>

<style>
    .card {
        overflow: hidden;
    }

    .card-header {
        font-size: 18px;
        font-weight: 600;
        padding: 16px 20px;
    }

    .custom-file-label::after {
        content: "Buscar";
    }

    #btnImportar {
        transition: all .25s ease;
        border-radius: 10px;
    }

    #btnImportar:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
    }

    /* ================= OVERLAY ================= */
    #loadingOverlay {
        position: fixed;
        inset: 0;
        display: none;
        z-index: 99999;
        background: rgba(10, 10, 10, .65);
        backdrop-filter: blur(8px);
        justify-content: center;
        align-items: center;
    }

    .loading-box {
        width: 380px;
        background: #ffffff;
        border-radius: 18px;
        padding: 35px 30px;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
        animation: fadeUp .35s ease;
    }

    .icon-circle {
        width: 70px;
        height: 70px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, #16a34a, #22c55e);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        box-shadow: 0 10px 25px rgba(34, 197, 94, .35);
    }

    .loading-box h4 {
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
    }

    .loading-box p {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 20px;
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
        border-radius: 30px;
        background: linear-gradient(90deg, #16a34a, #22c55e, #4ade80);
        transition: width .4s ease;
    }

    #counter {
        font-size: 18px;
        font-weight: 700;
        color: #16a34a;
        letter-spacing: 1px;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(25px) scale(.96);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<script>
    // Mostrar nombre archivo
    document.getElementById('archivoInput').addEventListener('change', function(e) {
        let fileName = e.target.files[0]?.name || 'Seleccionar archivo...';
        document.querySelector('.custom-file-label').innerText = fileName;
    });

    let seconds = 0;
    let interval;
    let progress = 0;
    let fakeBar;

    document.getElementById('formImportStock').addEventListener('submit', function() {

        // Mostrar overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Desactivar botón
        let btn = document.getElementById('btnImportar');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"></span>
            Importando...
        `;

        // Reiniciar
        seconds = 0;
        progress = 0;

        document.getElementById('counter').innerText = "0s";
        document.getElementById('progressBar').style.width = "0%";

        // Contador tiempo
        interval = setInterval(() => {
            seconds++;
            document.getElementById('counter').innerText = seconds + "s";
        }, 1000);

        // Barra fake elegante
        fakeBar = setInterval(() => {
            if (progress < 92) {
                progress += Math.random() * 7;
                document.getElementById('progressBar').style.width = progress + "%";
            }
        }, 600);
    });
</script>
