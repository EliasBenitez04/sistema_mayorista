<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artículos</title>
    <style>
        th,
        td {
            text-align: center;
            vertical-align: middle;
        }

        td.producto,
        th.producto {
            text-align: center;
            vertical-align: middle;
        }

        td.producto_descri {
            text-align: left;
            vertical-align: middle;
        }

        .btn-group {
            display: flex;
            justify-content: center;
        }

        .btn-group .btn {
            margin: 0;
        }

        .btn-info,
        .btn-danger {
            transition: background-color 0.3s ease;
        }

        .btn-info:hover,
        .btn-danger:hover {
            transform: scale(1.1);
        }

        .loader-box {
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>

    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="card-header">
                <div class="row align-items-center">

                    <!-- Formulario de Filtro por Precio -->
                    <div class="col-md-6 mb-2 mb-md-0">
                        <form method="GET" action="{{ route('articulos.index') }}" class="form-inline">
                            <label for="ordenar" class="mr-2">Ordenar por Precio:</label>
                            <select name="ordenar" id="ordenar" class="form-control mr-2">
                                <option value="">Seleccionar Orden</option>
                                <option value="asc" {{ request('ordenar') == 'asc' ? 'selected' : '' }}>Menor a Mayor
                                </option>
                                <option value="desc" {{ request('ordenar') == 'desc' ? 'selected' : '' }}>Mayor a
                                    Menor</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                        </form>
                    </div>

                    <!-- Formulario de Importación de Excel -->
                    <div class="col-md-6 text-md-right">
                        <form id="import-form" enctype="multipart/form-data" class="d-inline-block">
                            @csrf
                            <input type="file" name="archivo" id="file" class="form-control mb-2" required>
                            <button type="button" id="btn-import" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Importar desde Excel
                            </button>
                        </form>
                    </div>

                </div>
            </div>

            <table class="table table-striped table-bordered table-hover" id="articulos-table">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th class="producto">Codigo</th>
                        <th class="producto">Descripcion</th>
                        <th class="producto">Costo</th>
                        <th class="producto">Venta</th>
                        <th class="producto">Iva</th>
                        <th colspan="3" class="text-center">Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articulos as $articulo)
                        <tr>
                            <td class="producto">{{ $articulo->id_articulo }}</td>
                            <td class="producto_descri">{{ $articulo->art_codigo }}</td>
                            <td class="producto_descri">{{ $articulo->art_descripcion }}</td>
                            <td class="producto">{{ number_format($articulo->art_precio, 0, ',', '.') }}</td>
                            <td class="producto">{{ number_format($articulo->prec_vent, 0, ',', '.') }}</td>
                            <td class="producto">{{ $articulo->art_iva }}%</td>
                            <td style="width: 150px" class="text-center">
                                {!! Form::open([
                                    'route' => ['articulos.destroy', $articulo->id_articulo],
                                    'method' => 'delete',
                                    'class' => 'd-inline',
                                ]) !!}
                                <div class='btn-group'>
                                    @can('articulos edit')
                                        <a href="{{ route('articulos.edit', [$articulo->id_articulo]) }}"
                                            class='btn btn-info btn-sx' title="Editar">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('articulos destroy')
                                        {!! Form::button('<i class="far fa-trash-alt"></i>', [
                                            'type' => 'submit',
                                            'class' => 'btn btn-danger btn-sx alert-delete',
                                            'title' => 'Eliminar',
                                        ]) !!}
                                    @endcan
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix bg-light">
            <div class="float-left text-muted">
                Mostrando {{ $articulos->firstItem() }} -
                {{ $articulos->lastItem() }} de
                {{ $articulos->total() }} registros
            </div>
            <div class="float-right">
                {{ $articulos->links() }}
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                Importando artículos
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
</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const btnImport = document.getElementById('btn-import');
        const fileInput = document.getElementById('file');

        const loader = document.getElementById('loadingOverlay');
        const counter = document.getElementById('counter');

        let seconds = 0;
        let interval;

        btnImport.addEventListener('click', async () => {

            if (!fileInput.files.length) {
                alert('Seleccione un archivo');
                return;
            }

            // mostrar overlay
            loader.style.display = 'flex';

            // desactivar botón
            btnImport.disabled = true;
            btnImport.innerHTML = `
            <span class="spinner-border spinner-border-sm"></span>
            Importando...
        `;

            // contador
            seconds = 0;
            counter.innerText = "0s";

            interval = setInterval(() => {
                seconds++;
                counter.innerText = seconds + "s";
            }, 1000);

            let formData = new FormData();
            formData.append('archivo', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                await fetch("{{ route('articulos.importar') }}", {
                    method: 'POST',
                    body: formData
                });

            } catch (error) {
                alert('Error en la importación');
            }

            clearInterval(interval);

            setTimeout(() => {
                loader.style.display = 'none';
                location.reload();
            }, 500);

        });

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
