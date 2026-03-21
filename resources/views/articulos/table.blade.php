<!DOCTYPE html>

<html lang="es">
<!-- Loader -->
<div id="loader" style="display:none;">
    <div class="loader-box">
        <div class="spinner"></div>
        <div id="percent">0%</div>
    </div>
</div>

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

        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.3);
            /* semitransparente */
            z-index: 9999;
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

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4caf50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }

        #percent {
            font-size: 16px;
            font-weight: bold;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
                            <button type="button" id="btn-import" class="btn btn-success">Importar Excel</button>
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

</body>

</html>
<script>
    const btnImport = document.getElementById('btn-import');
    const fileInput = document.getElementById('file');
    const loader = document.getElementById('loader');
    const percentText = document.getElementById('percent');

    btnImport.addEventListener('click', async () => {
        if (!fileInput.files.length) {
            alert('Seleccione un archivo');
            return;
        }

        loader.style.display = 'flex';
        percentText.innerText = '0%';

        let formData = new FormData();
        formData.append('archivo', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        await fetch('{{ route('articulos.importar') }}', {
            method: 'POST',
            body: formData
        });

        // Consultar progreso cada 500ms
        const interval = setInterval(async () => {
            const res = await fetch('{{ route('import.progress') }}');
            const data = await res.json();
            percentText.innerText = (data.progress ?? 0) + '%';

            if ((data.progress ?? 0) >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    loader.style.display = 'none';
                    location.reload();
                }, 500);
            }
        }, 500);
    });
</script>
