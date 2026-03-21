<div class="table-responsive">
    <!-- Tabla de Detalles Generales -->
    <table class="table table-bordered" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
        <thead style="background-color: #0051a3; color: #fff;">
            <tr>
                <th colspan="6" style="padding: 12px; text-align: center; font-size: 18px; font-weight: bold;">
                    Detalles Generales
                </th>
            </tr>
        </thead>
        <tbody style="background-color: #f1f1f1;">
            <tr>
                <td style="padding: 10px; font-weight: bold;">Fecha Pedido</td>
                <td style="padding: 10px;">{{ \Carbon\Carbon::parse($pedido->ped_fecha)->format('d/m/Y') }}</td>
                <td style="padding: 10px; font-weight: bold;">Usuario</td>
                <td style="padding: 10px;">{{ $pedido->usuario }}</td>
                <td style="padding: 10px; font-weight: bold;">Sucursal</td>
                <td style="padding: 10px;">{{ $pedido->sucursal }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold;">Estado</td>
                <td style="padding: 10px;">{{ $pedido->ped_estado }}</td>
                <td style="padding: 10px; font-weight: bold;">Condición</td>
                <td style="padding: 10px;">{{ $pedido->condicion }}</td>
                <td style="padding: 10px; font-weight: bold;">Cliente</td>
                <td style="padding: 10px;">{{ $pedido->cliente }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Detalle de Productos -->
    <table class="table table-bordered"
        style="border-collapse: collapse; width: 100%; margin-top: 20px; font-family: Arial, sans-serif;">
        <thead style="background-color: #0051a3; color: #fff;">
            <tr>
                <th style="width: 10%; text-align: center; padding: 10px;">Código</th>
                <th style="width: 40%; text-align: left; padding: 10px;">Producto</th>
                <th style="width: 10%; text-align: center; padding: 10px;">Cantidad</th>
                <th style="width: 15%; text-align: center; padding: 10px;">Precio Unit.</th>
                <th style="width: 12%; text-align: center; padding: 10px;">Subtotal</th>
                @if ($detalle->first()->det_descuento > 0)
                    <th style="width: 6%; text-align: center; padding: 10px;">Desc. (%)</th>
                    <th style="width: 18%; text-align: center; padding: 10px;">Subtotal c/ Desc.</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($detalle as $det)
                @php
                    $precioUnitario = $det->det_subtotal / ($det->det_cantidad * (1 - $det->det_descuento / 100));
                    $subtotalSinDesc = $precioUnitario * $det->det_cantidad;
                    $subtotalConDesc = $det->det_subtotal;
                @endphp
                <tr style="background-color: #f9f9f9;">
                    <td style="padding: 10px; text-align: center; font-weight: bold;">{{ $det->art_codigo }}</td>
                    <td style="padding: 10px; text-align: left;">{{ $det->art_descripcion }}</td>
                    <td style="padding: 10px; text-align: center;">{{ $det->det_cantidad }}</td>
                    <td style="padding: 10px; text-align: center;">{{ number_format($precioUnitario, 0, ',', '.') }} Gs.
                    </td>
                    <td style="padding: 10px; text-align: center;">{{ number_format($subtotalSinDesc, 0, ',', '.') }}
                        Gs.</td>
                    @if ($det->det_descuento > 0)
                        <td style="padding: 10px; text-align: center;">{{ $det->det_descuento }}%</td>
                        <td style="padding: 10px; text-align: center;">
                            {{ number_format($subtotalConDesc, 0, ',', '.') }} Gs.</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 15px; text-align: center; font-style: italic; color: #666;">
                        No hay detalles disponibles
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            @if ($detalle->count() > 0)
                @php
                    $totalCantidad = $detalle->sum('det_cantidad');
                    $totalSinDesc = $detalle->sum(function ($d) {
                        return $d->det_subtotal / (1 - $d->det_descuento / 100);
                    });
                    $totalDescuento = $detalle->sum(function ($d) {
                        return $d->det_subtotal / (1 - $d->det_descuento / 100) - $d->det_subtotal;
                    });
                    $totalConDesc = $detalle->sum('det_subtotal');
                @endphp
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="2" style="padding: 10px; text-align: center;">Total Cantidad</td>
                    <td style="padding: 10px; text-align: center;">{{ $totalCantidad }}</td>
                    <td style="padding: 10px; text-align: center;">-</td>
                    <td style="padding: 10px; text-align: center;">{{ number_format($totalSinDesc, 0, ',', '.') }} Gs.
                    </td>
                    @if ($detalle->first()->det_descuento > 0)
                        <td style="padding: 10px; text-align: center;">-</td>
                        <td style="padding: 10px; text-align: center;">{{ number_format($totalConDesc, 0, ',', '.') }}
                            Gs.</td>
                    @endif
                </tr>
            @endif
        </tfoot>
    </table>
</div>
