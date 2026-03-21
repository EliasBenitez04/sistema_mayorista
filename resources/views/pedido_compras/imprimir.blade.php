<!DOCTYPE html>
<html lang="es">

<head>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta charset="utf-8">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-size: 13px;
            background: #fff;
            margin: 0 auto;
            width: 210mm;
            padding: 25px;
        }

        .header {
            border-bottom: 3px solid #0051a3;
            margin-bottom: 15px;
        }

        .header-logo img {
            max-height: 130px;
        }

        .invoice-title {
            font-weight: bold;
            font-size: 22px;
            color: #0051a3;
        }

        table th {
            background-color: #0051a3;
            color: #fff;
        }

        .totales-table th {
            background-color: #0051a3;
        }

        .firma-section {
            margin-top: 60px;
        }

        .firma {
            border-top: 1px solid #000;
            width: 250px;
            text-align: center;
            font-size: 12px;
        }

        @media print {
            .btn-print {
                display: none !important;
            }
        }

        .compact-card {
            font-size: 12px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }

        .compact-card .row {
            margin-bottom: 3px;
        }

        .compact-card strong {
            color: #000000;
        }
    </style>

    <title>Pedido {{ $pedido->nro_pedido }}</title>
</head>

<body>

    @php
        // Verificar si hay algún descuento aplicado
        $hayDescuento = $detalle->contains(function ($d) {
            return $d->det_descuento > 0;
        });
    @endphp

    <!-- ENCABEZADO -->
    <div class="row header align-items-center mb-3">
        <div class="col-6 header-logo">
            <img src="{{ asset('storage/logos/logo_gts.jpeg') }}" alt="Logo">
        </div>
        <div class="col-6 text-right header-info">
            <p class="mb-1"><strong>SEDAMA S.A.</strong></p>
            <p class="mb-1">Lomas Valentina casi Sargento González</p>
            <p class="mb-1">R.U.C. 80093399-0</p>
            <p class="mb-0">☎ 021 513 824 | 0984-261-267</p>
        </div>
    </div>

    <div class="text-center mb-3">
        <span class="invoice-title">PEDIDO DE COMPRA</span>
        <hr style="width: 40%; border: 1px solid #0051a3;">
    </div>

    <!-- DATOS CLIENTE -->
    <div class="cliente-info compact-card">
        <div class="row mb-1">
            <div class="col-4"><strong>N° Pedido:</strong> {{ $pedido->nro_pedido }}</div>
            <div class="col-4"><strong>Fecha Emisión:</strong>
                {{ \Carbon\Carbon::parse($pedido->ped_fecha)->format('d/m/Y') }}</div>
            <div class="col-4"><strong>Condición:</strong> {{ $pedido->condicion }}</div>
        </div>
        <div class="row mb-1">
            <div class="col-4"><strong>Cliente:</strong> {{ $pedido->cliente }}</div>
            <div class="col-4"><strong>CI / RUC:</strong> {{ $pedido->cli_ci }}</div>
            <div class="col-4"><strong>Teléfono:</strong> {{ $pedido->cli_telefono }}</div>
        </div>
        <div class="row mb-1">
            <div class="col-6"><strong>Dirección:</strong> {{ $pedido->cli_direccion }}</div>
        </div>
        @if ($pedido->condicion == 'CREDITO')
            <div class="row mb-1">
                <div class="col-4"><strong>Intervalo:</strong> {{ $pedido->intervalo }} días</div>
                <div class="col-4"><strong>Cantidad de Cuotas:</strong> {{ $pedido->cant_cuotas }}</div>
            </div>
        @endif
    </div>

    <!-- DETALLE DEL PEDIDO -->
    <h5 class="mt-4">Detalle de Artículos</h5>
    <table class="table table-bordered table-hover">
        <thead class="text-center">
            <tr>
                <th style="width: 80px;">Código</th>
                <th>Descripción</th>
                <th style="width: 90px;">Cantidad</th>
                <th style="width: 120px;">Precio Unit.</th>
                <th style="width: 120px;">Subtotal</th>
                @if ($hayDescuento)
                    <th style="width: 120px;">Subtotal c/ Descuento</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($detalle as $d)
                @php
                    $precioUnitario =
                        $d->det_subtotal / ($hayDescuento ? 1 - $d->det_descuento / 100 : 1) / $d->det_cantidad;
                @endphp
                <tr>
                    <td class="text-center">{{ $d->art_codigo }}</td>
                    <td>{{ $d->art_descripcion }}</td>
                    <td class="text-center">{{ $d->det_cantidad }}</td>
                    <td class="text-right">{{ number_format($precioUnitario, 0, ',', '.') }} Gs.</td>
                    <td class="text-right">
                        {{ number_format($d->det_subtotal / ($hayDescuento ? 1 - $d->det_descuento / 100 : 1), 0, ',', '.') }}
                        Gs.</td>
                    @if ($hayDescuento)
                        <td class="text-right">{{ number_format($d->det_subtotal, 0, ',', '.') }} Gs.</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTAL -->
    <table class="table table-bordered totales-table w-50 ml-auto">
        <tbody>
            <tr>
                <th>Total Sin Descuento</th>
                <td class="text-right">
                    <strong>{{ number_format($detalle->sum(function ($d) use ($hayDescuento) {return $d->det_subtotal / ($hayDescuento ? 1 - $d->det_descuento / 100 : 1);}),0,',','.') }}
                        Gs.</strong></td>
            </tr>
            @if ($hayDescuento)
                <tr>
                    <th>Total Descuento {{ $d->det_descuento }}%</th>
                    <td class="text-right">
                        <strong>{{ number_format($detalle->sum(function ($d) {return $d->det_subtotal / (1 - $d->det_descuento / 100) - $d->det_subtotal;}),0,',','.') }}
                            Gs.</strong></td>
                </tr>
            @endif
            <tr>
                <th>Total Pedido</th>
                <td class="text-right"><strong>{{ number_format($pedido->ped_total, 0, ',', '.') }} Gs.</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="text-center mt-3">
        <button onclick="window.print()" class="btn btn-primary btn-print">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>

</body>

</html>
