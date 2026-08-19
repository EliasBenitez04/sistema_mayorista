<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Lote {{ $lote->numero_lote }}</title>

    <style>
        @page {
            margin: 35px 35px 45px 35px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #334155;
            margin: 0;
            padding: 0;
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .header {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 65%;
            vertical-align: middle;
        }

        .header-right {
            width: 35%;
            text-align: right;
            vertical-align: middle;
        }

        .system-name {
            font-size: 17px;
            font-weight: bold;
            color: #172033;
            margin-bottom: 3px;
        }

        .document-title {
            font-size: 10px;
            color: #64748b;
        }

        .document-number {
            font-size: 11px;
            font-weight: bold;
            color: #2563eb;
        }

        .document-date {
            margin-top: 3px;
            font-size: 8px;
            color: #64748b;
        }

        /* =====================================================
           RESUMEN
        ===================================================== */

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #172033;
            border-left: 4px solid #2563eb;
            padding-left: 7px;
            margin: 15px 0 8px 0;
        }

        .summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-left: -6px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 9px;
            vertical-align: top;
        }

        .summary-label {
            display: block;
            color: #64748b;
            font-size: 7px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .summary-value {
            display: block;
            color: #172033;
            font-size: 11px;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 4px 8px;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            font-size: 8px;
            font-weight: bold;
        }

        /* =====================================================
           TABLA
        ===================================================== */

        .table-wrapper {
            width: 100%;
            margin-top: 8px;
        }

        table.detalles {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.detalles thead {
            display: table-header-group;
        }

        table.detalles tr {
            page-break-inside: avoid;
        }

        table.detalles th {
            background: #172033;
            color: #ffffff;
            padding: 7px 6px;
            text-align: left;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #172033;
        }

        table.detalles td {
            border: 1px solid #e2e8f0;
            padding: 6px;
            font-size: 8px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.detalles tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .center {
            text-align: center !important;
        }

        .codigo {
            font-weight: bold;
            color: #172033;
        }

        .cantidad {
            font-weight: bold;
            color: #2563eb;
        }

        .estado {
            font-size: 7px;
            font-weight: bold;
        }

        /* =====================================================
           TOTALES
        ===================================================== */

        .totales {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
        }

        .totales td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            background: #f8fafc;
        }

        .total-label {
            text-align: right;
            color: #64748b;
            font-weight: bold;
        }

        .total-value {
            width: 100px;
            text-align: center;
            color: #172033;
            font-weight: bold;
            font-size: 10px;
        }

        /* =====================================================
           FOOTER
        ===================================================== */

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
        }

        .footer strong {
            color: #64748b;
        }
    </style>
</head>

<body>

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-left">

                    <div class="system-name">
                        GESTIÓN DE REDISTRIBUCIÓN
                    </div>

                    <div class="document-title">
                        Comprobante de movimiento de redistribución
                    </div>

                </td>

                <td class="header-right">

                    <div class="document-number">
                        {{ $lote->numero_lote }}
                    </div>

                    <div class="document-date">
                        Generado:
                        {{ \Carbon\Carbon::parse($lote->fecha_generacion)->format('d/m/Y H:i') }}
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- =====================================================
         INFORMACIÓN DEL LOTE
    ====================================================== --}}

    <div class="section-title">
        INFORMACIÓN DEL LOTE
    </div>

    <table class="summary">

        <tr>

            <td>

                <span class="summary-label">
                    Número de lote
                </span>

                <span class="summary-value">
                    {{ $lote->numero_lote }}
                </span>

            </td>

            <td>

                <span class="summary-label">
                    Estado
                </span>

                <span class="status">
                    {{ $lote->estado }}
                </span>

            </td>

            <td>

                <span class="summary-label">
                    Total transferencias
                </span>

                <span class="summary-value">
                    {{ $lote->total_transferencias }}
                </span>

            </td>

            <td>

                <span class="summary-label">
                    Total productos
                </span>

                <span class="summary-value">
                    {{ $lote->total_productos }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         DETALLE
    ====================================================== --}}

    <div class="section-title">
        DETALLE DE TRANSFERENCIAS
    </div>

    <div class="table-wrapper">

        <table class="detalles">

            <thead>

                <tr>

                    <th width="5%" class="center">
                        #
                    </th>

                    <th width="17%">
                        Código
                    </th>

                    <th width="25%">
                        Origen
                    </th>

                    <th width="25%">
                        Destino
                    </th>

                    <th width="10%" class="center">
                        Cantidad
                    </th>

                    <th width="18%">
                        Estado
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($lote->detalles as $detalle)
                    <tr>

                        <td class="center">
                            {{ $loop->iteration }}
                        </td>

                        <td class="codigo">
                            {{ $detalle->codigo ?? '-' }}
                        </td>

                        <td>
                            {{ $detalle->origen->suc_descri ?? '-' }}
                        </td>

                        <td>
                            {{ $detalle->destino->suc_descri ?? '-' }}
                        </td>

                        <td class="center cantidad">
                            {{ $detalle->cantidad ?? 0 }}
                        </td>

                        <td class="estado">
                            {{ $detalle->estado }}
                        </td>

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>


    {{-- =====================================================
         TOTALES
    ====================================================== --}}

    <table class="totales">

        <tr>

            <td class="total-label">
                Total de transferencias
            </td>

            <td class="total-value">
                {{ $lote->detalles->count() }}
            </td>

            <td class="total-label">
                Total unidades
            </td>

            <td class="total-value">
                {{ $lote->detalles->sum('cantidad') }}
            </td>

        </tr>

    </table>


    {{-- =====================================================
         FOOTER
    ====================================================== --}}

    <div class="footer">

        Documento generado automáticamente por el sistema de
        <strong>Gestión de Redistribución</strong>.

    </div>

</body>

</html>
