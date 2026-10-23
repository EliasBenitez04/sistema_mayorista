<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nota de pedido {{ $pedido->nro_pedido }}</title>
<style>
*{box-sizing:border-box}
body{font-family:Arial,Helvetica,sans-serif;background:#edf2f8;color:#243449;margin:0;font-size:12px}
.documento{background:#fff;width:210mm;max-width:100%;min-height:297mm;margin:22px auto;padding:16mm 15mm;box-shadow:0 8px 30px rgba(18,39,70,.12)}
.cabecera{display:flex;align-items:center;justify-content:space-between;gap:18px;padding-bottom:14px;border-bottom:3px solid #1e5aa9}
.marca img{max-width:155px;max-height:78px;object-fit:contain}
.empresa{text-align:right;font-size:11px;line-height:1.55;color:#4b5870}
.empresa strong{display:block;font-size:14px;color:#203958}
.titulo{display:flex;align-items:flex-end;justify-content:space-between;margin:20px 0;gap:10px}
.titulo h1{font-size:20px;color:#1b4d91;margin:0 0 5px}
.muted{color:#748298;font-size:11px}.numero{font-size:15px;color:#1c4e91;font-weight:800}
.datos{border:1px solid #dce4ef;border-radius:9px;margin-bottom:18px;overflow:hidden}
.seccion{padding:10px 14px;background:#f2f6fc;border-bottom:1px solid #dce4ef;font-weight:800;font-size:12px;color:#264a7a}
.datos-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px 12px;padding:14px}
.dato.wide{grid-column:1/-1}.etiqueta{display:block;font-size:10px;color:#748399;margin-bottom:3px;font-weight:700}
.valor{font-size:12px;overflow-wrap:anywhere;white-space:pre-line}
.detalle-title{font-weight:800;font-size:14px;margin:20px 0 10px;color:#203958}
table{width:100%;border-collapse:collapse;font-size:11px}
th{background:#eaf1fa;color:#203e66;font-size:10px;text-align:left;padding:9px 7px;border-bottom:1px solid #cad7e7}
td{padding:9px 7px;border-bottom:1px solid #e3e9f2;vertical-align:top}
.num{text-align:right;white-space:nowrap}.center{text-align:center}.codigo{font-weight:700;color:#184b8d}
tfoot td{font-weight:800;background:#f6f9fd;border-top:1px solid #d4deeb}
.totales{width:310px;max-width:100%;margin:16px 0 0 auto}
.totales div{display:flex;justify-content:space-between;gap:12px;padding:7px 0;font-size:12px}
.totales .final{padding-top:12px;border-top:2px solid #c9d8ea;color:#184a8c;font-weight:800;font-size:15px}
.nota{border-top:1px solid #e2e9f2;margin-top:22px;padding-top:12px;white-space:pre-line;overflow-wrap:anywhere}
.firmas{display:flex;gap:45px;margin-top:62px;justify-content:space-between}
.firma{width:46%;border-top:1px solid #607086;text-align:center;padding-top:7px;color:#617189;font-size:11px}
.aviso{margin-top:18px;color:#7c8796;font-size:10px;text-align:center}
.acciones{text-align:center;padding:16px}
.acciones button,.acciones a{display:inline-block;border:0;border-radius:8px;background:#195bc0;color:white;padding:11px 18px;text-decoration:none;font-size:12px;font-weight:700;cursor:pointer;margin:0 4px}
.acciones a{background:#55677f}
@media(max-width:650px){.documento{margin:0;padding:20px 13px;min-height:0}.cabecera{align-items:flex-start}.marca img{max-width:100px}.empresa{font-size:9px}.datos-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.tabla-wrap{overflow-x:auto}.tabla-wrap table{min-width:650px}}
@media print{
@page{size:A4 portrait;margin:12mm}
html,body{background:#fff;width:auto;margin:0;padding:0}
.documento{width:100%;max-width:none;min-height:0;padding:0;margin:0;box-shadow:none}
.acciones{display:none!important}
.cabecera,.datos,.totales,.firmas{break-inside:avoid;page-break-inside:avoid}
thead{display:table-header-group}tfoot{display:table-footer-group}
tr{break-inside:avoid;page-break-inside:avoid}
th,td{padding:6px 5px}
table{font-size:10px}
.firmas{margin-top:42px}
}
</style>
</head>
<body>
@php
    $totalUnidades = $detalle->sum('det_cantidad');
    $totalNeto = (float)$detalle->sum('det_subtotal');
    $totalBruto = $detalle->sum(function($d){
        $descuento = (float)($d->det_descuento ?? 0);
        $factor = 1 - $descuento / 100;
        return $factor > 0 ? (float)$d->det_subtotal / $factor : 0;
    });
    $totalDescuentos = max(0, $totalBruto - $totalNeto);
    $tieneDescuento = $detalle->contains(fn($d) => (float)($d->det_descuento ?? 0) > 0);
    $gs = fn($importe) => number_format((float)$importe, 0, ',', '.');
@endphp
<div class="documento">
    <header class="cabecera">
        <div class="marca"><img src="{{ asset('storage/logos/logo_gts.jpeg') }}" alt="Gotitas"></div>
        <div class="empresa">
            <strong>SEDAMA S.A.</strong>
            Lomas Valentina casi Sargento González<br>
            R.U.C. 80093399-0<br>
            021 513 824 · 0984-261-267
        </div>
    </header>
    <div class="titulo">
        <div><h1>NOTA DE PEDIDO</h1><span class="muted">Comprobante de solicitud comercial · No es factura</span></div>
        <div style="text-align:right"><span class="etiqueta">NÚMERO</span><span class="numero">{{ $pedido->nro_pedido }}</span></div>
    </div>
    <section class="datos">
        <div class="seccion">Información del pedido y cliente</div>
        <div class="datos-grid">
            <div class="dato"><span class="etiqueta">Fecha del pedido</span><span class="valor">{{ $pedido->ped_fecha ? \Carbon\Carbon::parse($pedido->ped_fecha)->format('d/m/Y') : 'No registrada' }}</span></div>
            <div class="dato"><span class="etiqueta">Condición</span><span class="valor">{{ $pedido->condicion ?: 'No registrada' }}</span></div>
            <div class="dato"><span class="etiqueta">Sucursal</span><span class="valor">{{ $pedido->suc_descri ?: 'No registrada' }}</span></div>
            <div class="dato"><span class="etiqueta">Cliente</span><span class="valor">{{ $pedido->cliente ?: 'No registrado' }}</span></div>
            <div class="dato"><span class="etiqueta">CI / RUC</span><span class="valor">{{ $pedido->cli_ci ?: 'No registrado' }}</span></div>
            <div class="dato"><span class="etiqueta">Teléfono</span><span class="valor">{{ $pedido->cli_telefono ?: 'No registrado' }}</span></div>
            <div class="dato wide"><span class="etiqueta">Dirección</span><span class="valor">{{ $pedido->cli_direccion ?: 'No registrada' }}</span></div>
            @if(strtoupper((string)$pedido->condicion) === 'CREDITO')
            <div class="dato"><span class="etiqueta">Intervalo de cuotas</span><span class="valor">{{ $pedido->intervalo ?: 'No definido' }} días</span></div>
            <div class="dato"><span class="etiqueta">Cantidad de cuotas</span><span class="valor">{{ $pedido->cant_cuotas ?: 'No definida' }}</span></div>
            @endif
        </div>
    </section>
    <div class="detalle-title">Detalle de artículos</div>
    <div class="tabla-wrap">
        <table>
            <thead>
                <tr><th>Código</th><th>Descripción</th><th class="center">Cant.</th><th class="num">Precio unit.</th><th class="num">Subtotal</th>@if($tieneDescuento)<th class="center">Desc.</th><th class="num">Total</th>@endif</tr>
            </thead>
            <tbody>
                @forelse($detalle as $d)
                @php
                    $porcentaje = (float)($d->det_descuento ?? 0);
                    $factor = 1 - $porcentaje / 100;
                    $neto = (float)$d->det_subtotal;
                    $bruto = $factor > 0 ? $neto / $factor : 0;
                    $unitario = $d->det_cantidad > 0 ? $bruto / $d->det_cantidad : 0;
                @endphp
                <tr>
                    <td class="codigo">{{ $d->art_codigo }}</td>
                    <td>{{ $d->art_descripcion }}</td>
                    <td class="center">{{ $gs($d->det_cantidad) }}</td>
                    <td class="num">{{ $gs($unitario) }}</td>
                    <td class="num">{{ $gs($bruto) }}</td>
                    @if($tieneDescuento)
                    <td class="center">{{ $porcentaje > 0 ? number_format($porcentaje, 2, ',', '.') . '%' : '—' }}</td>
                    <td class="num">{{ $gs($neto) }}</td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="{{ $tieneDescuento ? 7 : 5 }}" class="center">No hay artículos en este pedido.</td></tr>
                @endforelse
            </tbody>
            <tfoot><tr><td colspan="2">TOTAL UNIDADES</td><td class="center">{{ $gs($totalUnidades) }}</td><td></td><td class="num">{{ $gs($totalBruto) }}</td>@if($tieneDescuento)<td></td><td class="num">{{ $gs($totalNeto) }}</td>@endif</tr></tfoot>
        </table>
    </div>
    <div class="totales">
        @if($tieneDescuento)
        <div><span>Subtotal sin descuento</span><strong>Gs {{ $gs($totalBruto) }}</strong></div>
        <div><span>Descuento total</span><strong>− Gs {{ $gs($totalDescuentos) }}</strong></div>
        @endif
        <div class="final"><span>TOTAL PEDIDO</span><span>Gs {{ $gs($totalNeto) }}</span></div>
    </div>
    @if(!empty($pedido->obs))
    <div class="nota"><strong>Observaciones:</strong><br>{{ $pedido->obs }}</div>
    @endif
    <div class="firmas"><div class="firma">Firma de responsable</div><div class="firma">Firma / conformidad del cliente</div></div>
    <p class="aviso">Documento de pedido generado por el Sistema Mayorista.</p>
</div>
<div class="acciones"><button type="button" onclick="window.print()">Imprimir / Guardar como PDF</button><a href="{{ route('pedido_compras.show', $pedido->id_pedido) }}">Volver al pedido</a></div>
</body>
</html>
