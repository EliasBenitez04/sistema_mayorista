@include('sweetalert::alert')

<div class="card shadow border-0 rounded-lg">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <i class="fas fa-project-diagram mr-2"></i>
        <h3 class="card-title mb-0">Importar Trazabilidad de OT</h3>
    </div>

    <div class="card-body p-4">

        <form id="formImportOT" action="{{ route('ot.importar') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="font-weight-bold text-dark">
                    Archivo Excel de Trazabilidad
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

            <button type="submit" id="btnImportar" class="btn btn-primary btn-lg px-4 shadow-sm">
                <i class="fas fa-file-excel mr-2"></i>
                Importar Trazabilidad
            </button>

        </form>

    </div>
    

</div>

<div id="loadingOverlay">

    <div class="loading-box">

        <div class="icon-circle">
            <i class="fas fa-industry"></i>
        </div>

        <div class="spinner-border text-primary mb-3" style="width:55px;height:55px;"></div>

        <h4>Importando Órdenes de Trabajo</h4>

        <p>
            Procesando trazabilidad, procesos y registros históricos...
        </p>

        <div class="progress-custom">
            <div id="progressBar"></div>
        </div>

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
        width: 420px;
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
        background: linear-gradient(135deg, #2563eb, #3b82f6);
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
        border-radius: 30px;
        background: linear-gradient(90deg, #2563eb, #3b82f6, #60a5fa);
        transition: width .4s ease;
    }

    #counter {
        font-size: 18px;
        font-weight: 700;
        color: #2563eb;
    }
</style>

<script>
    document.getElementById('archivoInput').addEventListener('change', function(e) {

        let fileName = e.target.files[0]?.name || 'Seleccionar archivo...';

        document.querySelector('.custom-file-label').innerText = fileName;
    });

    let seconds = 0;
    let progress = 0;

    document.getElementById('formImportOT').addEventListener('submit', function() {

        document.getElementById('loadingOverlay').style.display = 'flex';

        let btn = document.getElementById('btnImportar');

        btn.disabled = true;

        btn.innerHTML = `
        <span class="spinner-border spinner-border-sm mr-2"></span>
        Importando...
    `;

        setInterval(() => {

            seconds++;

            document.getElementById('counter').innerText = seconds + "s";

        }, 1000);

        setInterval(() => {

            if (progress < 92) {

                progress += Math.random() * 7;

                document.getElementById('progressBar').style.width =
                    progress + "%";
            }

        }, 600);

    });
</script>
