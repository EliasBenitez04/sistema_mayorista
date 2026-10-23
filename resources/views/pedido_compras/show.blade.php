@extends('layouts.app')
@section('content')
@php
    $cantidad = $detalle->sum('det_cantidad');
    $lineas = $detalle->count();
    $descuento = max(0, (float)$totalDescuento);
    $total = (float)$totalConDescuento;
    $estado = strtoupper(trim((string)($pedido->ped_estado ?? '')));
    $colorEstado = ['CONFIRMADO'=>'success','PENDIENTE'=>'warning','ANULADO'=>'danger'][$estado] ?? 'secondary';
    $formato = fn($v) => number_format((float)$v, 0, ',', '.');
    $descuentosAplicados = $detalle->contains(fn($d) => (float)$d->det_descuento > 0);
@endphp
<style>
.pv{background:#f3f7fc;min-height:calc(100vh - 57px);padding:23px;color:#27374c}
.pv-hero{background:linear-gradient(110deg,#122746,#1d54a6);padding:23px 26px;border-radius:16px;color:white;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;box-shadow:0 10px 24px #142f5520}
.pv-hero h1{font-size:24px;font-weight:800;margin:5px 0}.pv-hero p{color:#cddff8;font-size:12px;margin:0}
.pv-actions{display:flex;gap:8px;flex-wrap:wrap}.pv-actions .btn{border-radius:9px;font-size:12px;font-weight:700}
.pv-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:18px 0}
.pv-metric,.pv-card{background:white;border:1px solid #e2eaf3;border-radius:13px;box-shadow:0 5px 18px #253b5d0b}
.pv-metric{padding:18px}.pv-metric-label{font-size:11px;font-weight:700;color:#71829a;display:flex;justify-content:space-between;gap:8px}
.pv-metric-label i{color:#397ac8;font-size:17px}.pv-metric-value{font-size:25px;font-weight:800;color:#1f3450;margin-top:8px;overflow-wrap:anywhere}
.pv-metric-hint{font-size:11px;color:#8998ab;margin-top:3px}
.pv-columns{display:grid;grid-template-columns:1.3fr 1fr;gap:15px;margin-bottom:16px}
.pv-card{overflow:hidden;margin-bottom:16px}.pv-card-header{display:flex;align-items:center;gap:10px;padding:17px 20px;border-bottom:1px solid #edf1f6;background:#fcfdff}
.pv-card-header i{color:#3172c8}.pv-card-header h2{font-size:15px;font-weight:800;margin:0}.pv-card-header small{color:#8090a4;font-size:11px}
.pv-card-body{padding:18px 20px}
.pv-details{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px 18px}.pv-details .wide{grid-column:1/-1}
.pv-label{display:block;color:#8491a4;font-size:11px;font-weight:700;margin-bottom:4px}.pv-info{font-size:13px;font-weight:650;overflow-wrap:anywhere}
.pv-table{margin:0;width:100%;font-size:12px;min-width:820px}.pv-table th{padding:13px 12px!important;background:#f6f9fd;color:#60748c;border-top:0!important;font-size:11px;white-space:nowrap}
.pv-table td{padding:12px!important;border-top:1px solid #edf2f8;vertical-align:middle!important}.pv-table tbody tr:hover{background:#f9fbff}
.pv-code{font-weight:750;color:#1e569f}.pv-table .num{text-align:right;white-space:nowrap}.pv-table .center{text-align:center}
.pv-table tfoot td{background:#f4f8fe;font-weight:800}
.pv-summary{margin-left:auto;max-width:360px;padding:19px 21px}.pv-summary-row{display:flex;justify-content:space-between;gap:16px;font-size:13px;padding:7px 0}
.pv-summary-row.final{border-top:1px solid #dfe7f1;margin-top:7px;padding-top:15px;font-weight:800;color:#1b4d94;font-size:17px}
.pv-note{white-space:pre-line;overflow-wrap:anywhere}
@media(max-width:1060px){.pv-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.pv-columns{grid-template-columns:1fr}}
@media(max-width:560px){.pv{padding:12px}.pv-hero{padding:19px}.pv-hero h1{font-size:20px}.pv-grid{gap:9px}.pv-metric{padding:13px}.pv-metric-value{font-size:20px}.pv-details{grid-template-columns:1fr}}
@media print{.main-sidebar,.main-header,.pv-actions{display:none!important}.content-wrapper{margin-left:0!important}.pv{padding:0;background:white}.pv-card,.pv-metric,.pv-hero{box-shadow:none!important}.pv-table{min-width:0;font-size:10px}.pv-hero{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
<div class="pv">
    <div class="pv-hero">
        <div>
            <div style="font-size:10px;letter-spacing:1.3px;font-weight:750;color:#b8d4f9">SISTEMA MAYORISTA · CONSULTA DE PEDIDOS</div>
            <h1><i class="fas fa-file-invoice mr-2"></i>Pedido {{ $pedido->nro_pedido }}</h1>
            <p>Fecha: {{ $pedido->ped_fecha ? \Carbon\Carbon::parse($pedido->ped_fecha)->format('d/m/Y') : 'No registrada' }} · <span class="badge badge-{{ $colorEstado }}">{{ $pedido->ped_estado ?: 'SIN ESTADO' }}</span></p>
        </div>
        <div class="pv-actions">
            <a href="{{ route('pedido_compras.index') }}" class="btn btn-light"><i class="fas fa-arrow-left mr-1"></i> Volver</a>
            <a href="{{ route('pedido_compras.imprimir', $pedido->id_pedido) }}" class="btn btn-outline-light"><i class="fas fa-print mr-1"></i> Imprimir</a>
            <a href="{{ route('pedido.export', $pedido->id_pedido) }}" class="btn btn-outline-light"><i class="fas fa-file-excel mr-1"></i> Exportar</a>
            @can('pedido_compras update')
            @if($estado === 'PENDIENTE')
            <a href="{{ route('pedido_compras.edit', $pedido->id_pedido) }}" class="btn btn-warning"><i class="fas fa-pen mr-1"></i> Editar</a>
            @endif
            @endcan
        </div>
    </div>
    <div class="pv-grid">
        <div class="pv-metric"><div class="pv-metric-label">Total del pedido<i class="fas fa-coins"></i></div><div class="pv-metric-value" style="font-size:22px">Gs {{ $formato($total) }}</div><div class="pv-metric-hint">Importe final registrado en detalle</div></div>
        <div class="pv-metric"><div class="pv-metric-label">Unidades solicitadas<i class="fas fa-boxes"></i></div><div class="pv-metric-value">{{ $formato($cantidad) }}</div><div class="pv-metric-hint">Suma de cantidades</div></div>
        <div class="pv-metric"><div class="pv-metric-label">Artículos / líneas<i class="fas fa-tags"></i></div><div class="pv-metric-value">{{ $formato($lineas) }}</div><div class="pv-metric-hint">Renglones del pedido</div></div>
        <div class="pv-metric"><div class="pv-metric-label">Descuento calculado<i class="fas fa-percent"></i></div><div class="pv-metric-value" style="font-size:22px">Gs {{ $formato($descuento) }}</div><div class="pv-metric-hint">Según descuentos de los detalles</div></div>
    </div>
    <div class="pv-columns">
        <section class="pv-card">
            <div class="pv-card-header"><i class="fas fa-user-circle"></i><div><h2>Datos del cliente</h2><small>Información de contacto registrada</small></div></div>
            <div class="pv-card-body pv-details">
                <div class="wide"><span class="pv-label">Nombre completo</span><div class="pv-info">{{ $pedido->cliente ?: 'No registrado' }}</div></div>
                <div><span class="pv-label">CI / RUC</span><div class="pv-info">{{ $pedido->cli_ci ?: 'No registrado' }}</div></div>
                <div><span class="pv-label">Teléfono</span><div class="pv-info">{{ $pedido->cli_telefono ?: 'No registrado' }}</div></div>
                <div class="wide"><span class="pv-label">Dirección</span><div class="pv-info">{{ $pedido->cli_direccion ?: 'No registrada' }}</div></div>
            </div>
        </section>
        <section class="pv-card">
            <div class="pv-card-header"><i class="fas fa-clipboard-list"></i><div><h2>Información del pedido</h2><small>Datos de registro y condición comercial</small></div></div>
            <div class="pv-card-body pv-details">
                <div><span class="pv-label">N.º interno</span><div class="pv-info">#{{ $pedido->id_pedido }}</div></div>
                <div><span class="pv-label">Estado</span><div class="pv-info"><span class="badge badge-{{ $colorEstado }}">{{ $pedido->ped_estado ?: 'SIN ESTADO' }}</span></div></div>
                <div><span class="pv-label">Sucursal</span><div class="pv-info">{{ $pedido->sucursal ?: 'No registrada' }}</div></div>
                <div><span class="pv-label">Registrado por</span><div class="pv-info">{{ $pedido->usuario ?: 'No registrado' }}</div></div>
                <div><span class="pv-label">Condición</span><div class="pv-info">{{ $pedido->condicion ?: 'No registrada' }}</div></div>
                <div><span class="pv-label">Fecha</span><div class="pv-info">{{ $pedido->ped_fecha ? \Carbon\Carbon::parse($pedido->ped_fecha)->format('d/m/Y') : 'No registrada' }}</div></div>
                @if(strtoupper((string)$pedido->condicion) === 'CREDITO')
                <div><span class="pv-label">Intervalo</span><div class="pv-info">{{ $pedido->intervalo ?: 'No definido' }}</div></div>
                <div><span class="pv-label">Cuotas</span><div class="pv-info">{{ $pedido->cant_cuotas ?: 'No definidas' }}</div></div>
                @endif
                @if(!empty($pedido->obs))
                <div class="wide"><span class="pv-label">Observaciones</span><div class="pv-info pv-note">{{ $pedido->obs }}</div></div>
                @endif
            </div>
        </section>
    </div>
    <section class="pv-card">
        <div class="pv-card-header"><i class="fas fa-box-open"></i><div><h2>Detalle de artículos</h2><small>Códigos, cantidades y valores correspondientes al pedido</small></div></div>
        <div class="table-responsive">
            <table class="table pv-table">
                <thead><tr><th class="center">#</th><th>Código</th><th>Descripción del artículo</th><th class="center">Cantidad</th><th class="num">Precio unit. (Gs)</th><th class="num">Subtotal (Gs)</th><th class="center">Desc.</th><th class="num">Total (Gs)</th></tr></thead>
                <tbody>
                @forelse($detalle as $det)
                    @php
                        $d = (float)$det->det_descuento;
                        $neto = (float)$det->det_subtotal;
                        $factor = 1 - $d / 100;
                        $bruto = $factor > 0 ? $neto / $factor : 0;
                        $unitario = $det->det_cantidad > 0 ? $bruto / $det->det_cantidad : 0;
                    @endphp
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="pv-code">{{ $det->art_codigo }}</td>
                        <td>{{ $det->art_descripcion }}</td>
                        <td class="center font-weight-bold">{{ $formato($det->det_cantidad) }}</td>
                        <td class="num">{{ $formato($unitario) }}</td>
                        <td class="num">{{ $formato($bruto) }}</td>
                        <td class="center">{{ $d > 0 ? number_format($d, 2, ',', '.') . '%' : '—' }}</td>
                        <td class="num font-weight-bold">{{ $formato($neto) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">Este pedido no tiene artículos en el detalle.</td></tr>
                @endforelse
                </tbody>
                <tfoot><tr><td colspan="3">TOTAL</td><td class="center">{{ $formato($cantidad) }}</td><td></td><td class="num">{{ $formato($totalSinDescuento) }}</td><td></td><td class="num">{{ $formato($total) }}</td></tr></tfoot>
            </table>
        </div>
        <div class="pv-summary">
            <div class="pv-summary-row"><span>Subtotal sin descuento</span><strong>Gs {{ $formato($totalSinDescuento) }}</strong></div>
            <div class="pv-summary-row"><span>Descuentos</span><strong>− Gs {{ $formato($descuento) }}</strong></div>
            <div class="pv-summary-row final"><span>Total final</span><strong>Gs {{ $formato($total) }}</strong></div>
        </div>
    </section>
</div>
@endsection
