@include('sweetalert::alert')

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Importar Stock</h3>
    </div>

    <div class="card-body">

        <form id="formImportStock" action="{{ route('import.stock') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Archivo Excel</label>

                <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls" required>
            </div>

            <button type="submit" id="btnImportar" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Importar desde Excel
            </button>

        </form>

    </div>
</div>

<!-- 🔥 OVERLAY DE CARGA -->
<div id="loadingOverlay"
    style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    backdrop-filter: blur(6px);
    background: rgba(0,0,0,0.5); /* 🔥 importante */
    z-index:9999;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    color:#fff;
    font-family: 'Segoe UI', sans-serif;
">

    <div
        style="
        background: rgba(20,20,20,0.85); /* 🔥 fondo oscuro */
        padding:30px 40px;
        border-radius:16px;
        text-align:center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        width: 300px;
    ">

        <!-- Spinner -->
        <div style="margin-bottom:15px;">
            <div class="spinner-border text-light" style="width:50px;height:50px;"></div>
        </div>

        <!-- Texto -->
        <h5 style="margin-bottom:5px; font-weight:600; color:#fff;">
            Importando Stock Por Sucursal,
        </h5>

        <span style="font-size:13px; opacity:0.8; color:#d1d5db;">
            Por favor espere...
        </span>

        <!-- Barra -->
        <div
            style="
            width:100%;
            height:8px;
            background:rgba(255,255,255,0.1); /* 🔥 visible */
            border-radius:10px;
            margin-top:20px;
            overflow:hidden;
        ">
            <div id="progressBar"
                style="
                height:100%;
                width:0%;
                background:linear-gradient(90deg, #22c55e, #4ade80);
                transition: width 0.5s ease;
            ">
            </div>
        </div>

        <!-- Contador -->
        <div id="counter"
            style="
            margin-top:15px;
            font-size:15px;
            font-weight:600;
            color:#22c55e; /* 🔥 verde visible */
            letter-spacing:1px;
        ">
            0s
        </div>

    </div>
</div>

<script>
    document.getElementById('formImportStock').addEventListener('submit', function() {

        // Mostrar overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Desactivar botón
        let btn = document.getElementById('btnImportar');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm"></span>
            Importando...
        `;
    });

    let seconds = 0;
    let interval;

    document.getElementById('formImportStock').addEventListener('submit', function() {

        // Mostrar overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Desactivar botón
        let btn = document.getElementById('btnImportar');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm"></span>
            Importando...
        `;

        // Reiniciar contador
        seconds = 0;
        document.getElementById('counter').innerText = "0s";

        // Iniciar contador
        interval = setInterval(() => {
            seconds++;
            document.getElementById('counter').innerText = seconds + "s";
        }, 1000);

    });

    let progressFake = 0;

    setInterval(() => {
        if (progressFake < 90) {
            progressFake += Math.random() * 5;
            document.getElementById("progressBar").style.width = progressFake + "%";
        }
    }, 800);
    document.getElementById("progressBar").style.width = "100%";
</script>
