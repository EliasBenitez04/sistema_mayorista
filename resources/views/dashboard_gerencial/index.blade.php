@extends('layouts.app')
@section('content')
<style>
.dg{padding:24px;background:#f4f7fc;min-height:calc(100vh - 57px);color:#24354d}
.dg-hero{background:linear-gradient(110deg,#14294d,#245cc0);border-radius:16px;color:white;padding:24px 28px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.dg-hero h1{font-size:24px;font-weight:800;margin:4px 0 5px}.dg-hero p{font-size:12px;color:#d7e7ff;margin:0}
.dg-filter{display:flex;gap:8px;align-items:end;flex-wrap:wrap}.dg-filter label{font-size:11px;color:#dce9ff;display:block;margin:0 0 4px}
.dg-filter input{height:38px;border:0;border-radius:8px;padding:8px 12px}
.dg-filter button{height:38px;border:0;border-radius:8px;padding:0 16px;background:#fff;color:#16458b;font-weight:700}
.dg-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:19px}
.dg-kpi,.dg-panel{background:#fff;border:1px solid #e4ebf4;border-radius:14px;box-shadow:0 5px 20px rgba(15,46,90,.045)}
.dg-kpi{padding:20px}.dg-kpi-head{display:flex;align-items:center;justify-content:space-between;color:#718197;font-size:12px;font-weight:650}
.dg-kpi-head i{font-size:19px;color:#3c7bd8}.dg-kpi-value{font-weight:800;font-size:27px;overflow-wrap:anywhere;color:#203653;margin:8px 0 4px}
.dg-kpi-note{font-size:11px;color:#8090a5}
.dg-grid{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(300px,1fr);gap:17px;margin-bottom:18px}
.dg-panel{padding:20px;min-width:0}.dg-panel h2{font-size:16px;font-weight:800;margin:0 0 5px}.dg-panel .dg-sub{color:#8897a9;font-size:12px;margin-bottom:18px}
.dg-bars{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:9px;height:245px;align-items:end;border-bottom:1px solid #dce5ef;padding:10px 4px 0}
.dg-col{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:end;min-width:0}
.dg-track{flex:1;width:100%;display:flex;align-items:end;justify-content:center}.dg-bar{width:66%;min-height:3px;border-radius:7px 7px 0 0;background:linear-gradient(#4b96f1,#245ac4)}
.dg-month{font-size:10px;color:#6c7f99;text-align:center;margin-top:9px;white-space:nowrap}
.dg-table{width:100%;border-collapse:collapse;font-size:12px}.dg-table th{background:#f6f8fc;color:#62728a;font-weight:750}
.dg-table th,.dg-table td{padding:11px 9px;border-bottom:1px solid #ecf0f5;text-align:left}.dg-table .number{text-align:right;white-space:nowrap}
.dg-muted{color:#8592a4;font-size:12px}.dg-pill{background:#eef5ff;color:#245aa5;padding:5px 9px;border-radius:18px;font-size:11px;font-weight:700}
@media(max-width:1100px){.dg-kpis{grid-template-columns:repeat(2,1fr)}.dg-grid{grid-template-columns:1fr}}
@media(max-width:600px){.dg{padding:13px}.dg-hero{padding:19px}.dg-kpis{gap:9px}.dg-kpi{padding:14px}.dg-kpi-value{font-size:21px}.dg-panel{padding:13px}.dg-bars{gap:3px}.dg-month{font-size:9px;transform:rotate(-40deg);margin-bottom:8px}}
</style>
<div class="dg">
    <div class="dg-hero">
        <div>
            <div style="font-size:10px;letter-spacing:1.5px;font-weight:700;color:#bcd8ff">SISTEMA MAYORISTA · ANÁLISIS COMERCIAL</div>
            <h1><i class="fas fa-chart-line mr-2"></i>Dashboard gerencial</h1>
            <p>Indicadores reales de pedidos registrados. No representan ventas facturadas ni cobros.</p>
        </div>
        <form class="dg-filter" action="{{ route('dashboard-gerencial.index') }}" method="GET">
            <div><label for="mes">Mes de análisis</label><input id="mes" name="mes" type="month" value="{{ $mes }}" required></div>
            <button type="submit"><i class="fas fa-filter mr-1"></i> Aplicar</button>
        </form>
    </div>
    <div class="dg-kpis">
        <div class="dg-kpi"><div class="dg-kpi-head">Pedidos del mes <i class="fas fa-file-invoice"></i></div><div class="dg-kpi-value">{{ number_format($pedidosMes,0,',','.') }}</div><div class="dg-kpi-note">Mes anterior: {{ number_format($pedidosAnterior,0,',','.') }}</div></div>
        <div class="dg-kpi"><div class="dg-kpi-head">Importe de pedidos <i class="fas fa-coins"></i></div><div class="dg-kpi-value" style="font-size:23px">Gs {{ number_format($importeMes,0,',','.') }}</div><div class="dg-kpi-note">Variación frente al mes anterior: {{ is_null($variacion) ? 'Sin base comparable' : (($variacion > 0 ? '+' : '') . number_format($variacion,1,',','.') . '%') }}</div></div>
        <div class="dg-kpi"><div class="dg-kpi-head">Clientes activos <i class="fas fa-users"></i></div><div class="dg-kpi-value">{{ number_format($clientesActivos,0,',','.') }}</div><div class="dg-kpi-note">Clientes distintos con pedidos del mes</div></div>
        <div class="dg-kpi"><div class="dg-kpi-head">Unidades solicitadas <i class="fas fa-boxes"></i></div><div class="dg-kpi-value">{{ number_format($unidadesMes,0,',','.') }}</div><div class="dg-kpi-note">Suma de cantidades en detalle de pedidos</div></div>
    </div>
    <div class="dg-grid">
        <section class="dg-panel">
            <h2>Evolución mensual de importes</h2><div class="dg-sub">Últimos 12 meses hasta {{ $inicio->locale('es')->translatedFormat('F Y') }} · Pedidos no anulados</div>
            <div class="dg-bars">
                @foreach($meses as $item)
                <div class="dg-col" title="{{ $item['etiqueta'] }}: Gs {{ number_format($item['importe'],0,',','.') }} · {{ $item['pedidos'] }} pedidos">
                    <div class="dg-track"><div class="dg-bar" style="height:{{ $item['importe'] > 0 ? max(2, 100*$item['importe']/$maxImporte) : 0 }}%"></div></div>
                    <div class="dg-month">{{ $item['etiqueta'] }}</div>
                </div>
                @endforeach
            </div>
            <div class="dg-muted mt-4">El importe corresponde a la suma de <code>ped_total</code> de los pedidos, no a facturación realizada.</div>
        </section>
        <section class="dg-panel">
            <h2>Artículos más pedidos</h2><div class="dg-sub">Top 10 según unidades solicitadas en el mes</div>
            <div class="table-responsive"><table class="dg-table">
                <thead><tr><th>Artículo</th><th class="number">Unidades</th></tr></thead>
                <tbody>
                @forelse($articulos as $articulo)
                <tr><td><strong>{{ $articulo->art_codigo ?: ('ID '.$articulo->id_articulo) }}</strong><div class="dg-muted">{{ $articulo->art_descripcion ?: 'Sin descripción' }}</div></td><td class="number"><span class="dg-pill">{{ number_format($articulo->unidades,0,',','.') }}</span></td></tr>
                @empty <tr><td colspan="2" class="dg-muted text-center py-4">Sin artículos solicitados en este mes.</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </section>
    </div>
    <section class="dg-panel">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div><h2>Últimos pedidos del mes</h2><div class="dg-sub">Movimientos comerciales más recientes en el período elegido</div></div>
            <a href="{{ route('reportes.index', ['tipo'=>'pedidos','desde'=>$inicio->toDateString(),'hasta'=>$inicio->copy()->endOfMonth()->toDateString()]) }}" class="btn btn-outline-primary btn-sm">Ver reporte completo <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="table-responsive"><table class="dg-table">
            <thead><tr><th>Pedido</th><th>Fecha</th><th>Cliente</th><th>Estado</th><th class="number">Importe (Gs)</th></tr></thead>
            <tbody>
            @forelse($ultimos as $pedido)
            <tr><td>{{ $pedido->nro_pedido }}</td><td>{{ \Carbon\Carbon::parse($pedido->ped_fecha)->format('d/m/Y') }}</td><td>{{ $pedido->cliente ?: 'Sin cliente' }}</td><td>{{ $pedido->ped_estado ?: 'Sin estado' }}</td><td class="number">{{ number_format((float)$pedido->ped_total,0,',','.') }}</td></tr>
            @empty <tr><td colspan="5" class="text-center dg-muted py-4">No hay pedidos en el mes seleccionado.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </section>
</div>
@endsection
