@extends('layouts.app')
@section('content')
<style>
.gts-home{padding:24px 22px 34px;min-height:calc(100vh - 57px);background:linear-gradient(180deg,#f3f7fc 0%,#f7f9fc 100%);font-family:inherit}
.gts-home .gts-hero{position:relative;overflow:hidden;border-radius:20px;padding:27px 32px;color:#fff;background:linear-gradient(112deg,#142b52,#1854a7 58%,#348dd3);box-shadow:0 14px 32px rgba(19,58,105,.17);margin-bottom:25px}
.gts-home .gts-hero:before{content:"";position:absolute;width:270px;height:270px;border:1px solid rgba(255,255,255,.15);border-radius:50%;right:-55px;top:-132px}
.gts-home .gts-hero:after{content:"";position:absolute;width:170px;height:170px;background:rgba(255,255,255,.045);border-radius:50%;right:160px;bottom:-110px}
.gts-home .gts-eyebrow{font-size:11px;letter-spacing:1.4px;font-weight:750;color:#b9d9ff;text-transform:uppercase;margin-bottom:8px}
.gts-home .gts-hero h1{font-size:30px;line-height:1.2;letter-spacing:-.6px;font-weight:800;color:white;margin:0 0 9px}
.gts-home .gts-hero p{color:#e0eeff;font-size:13px;margin:0;max-width:560px}
.gts-home .gts-hero-user{display:inline-flex;align-items:center;gap:9px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.14);border-radius:30px;padding:8px 13px;font-size:12px;margin-top:17px}
.gts-home .gts-section-heading{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px}
.gts-home .gts-section-title{font-size:17px;font-weight:800;color:#223852;margin:0;letter-spacing:-.2px}
.gts-home .gts-section-sub{font-size:12px;color:#78889c;margin:4px 0 0}
.gts-home .gts-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin-bottom:23px}
.gts-home .gts-stat{display:flex;flex-direction:column;overflow:hidden;background:white;border:1px solid #e6ecf4;border-radius:15px;box-shadow:0 7px 23px rgba(24,42,70,.055);transition:transform .2s,box-shadow .2s}
.gts-home .gts-stat:hover{transform:translateY(-4px);box-shadow:0 13px 30px rgba(24,42,70,.12)}
.gts-home .gts-stat-body{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:20px 17px 15px;flex:1}
.gts-home .gts-stat-name{color:#63748b;font-weight:650;font-size:12px}
.gts-home .gts-stat-value{color:#21364f;font-weight:800;font-size:30px;line-height:1.25;margin-top:7px}
.gts-home .gts-stat-desc{color:#91a0b0;font-size:11px;margin-top:3px}
.gts-home .gts-stat-icon{flex-shrink:0;width:54px;height:54px;display:flex;align-items:center;justify-content:center;border-radius:15px;font-size:22px}
.gts-home .stat-clients{background:#e8f0ff;color:#2a65d2}
.gts-home .stat-orders{background:#e7f8f4;color:#16856b}
.gts-home .stat-cities{background:#eef0ff;color:#6464ca}
.gts-home .stat-depts{background:#fff3e5;color:#c88c34}
.gts-home .gts-stat-link{display:flex;align-items:center;justify-content:space-between;padding:11px 17px;background:#f9fbfe;border-top:1px solid #eef2f7;color:#315b90;font-weight:700;font-size:12px;text-decoration:none!important}
.gts-home .gts-stat-link:hover{background:#edf4ff;color:#174b9a}
.gts-home .gts-bottom{display:grid;grid-template-columns:minmax(0,1.7fr) minmax(260px,1fr);gap:17px}
.gts-home .gts-panel{background:white;border:1px solid #e6ecf4;border-radius:16px;padding:22px;box-shadow:0 7px 23px rgba(24,42,70,.055)}
.gts-home .gts-quick-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:19px}
.gts-home .gts-quick-link{display:flex;gap:12px;align-items:center;background:#f7faff;border:1px solid #e9f0f8;border-radius:12px;padding:14px;text-decoration:none!important;transition:background .2s,transform .2s,border-color .2s}
.gts-home .gts-quick-link:hover{background:#edf5ff;transform:translateY(-2px);border-color:#cfdff3}
.gts-home .gts-quick-icon{display:flex;align-items:center;justify-content:center;width:42px;height:42px;flex-shrink:0;border-radius:12px;background:#e5efff;color:#2865ba;font-size:17px}
.gts-home .gts-quick-name{font-size:13px;font-weight:750;color:#29405d}
.gts-home .gts-quick-detail{font-size:11px;color:#7d8da0;margin-top:3px}
.gts-home .gts-info-row{display:flex;align-items:center;gap:12px;padding:13px 0;border-bottom:1px solid #eef2f7}
.gts-home .gts-info-row:last-child{border-bottom:0}
.gts-home .gts-info-icon{width:37px;height:37px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:10px;color:#4070af;background:#edf4ff}
.gts-home .gts-info-label{color:#7b899b;font-size:11px}
.gts-home .gts-info-value{color:#29405d;font-size:13px;font-weight:700;margin-top:3px;overflow-wrap:anywhere}
.gts-home .gts-empty{color:#78889c;font-size:13px;margin:8px 0}
@media(max-width:1150px){.gts-home .gts-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:850px){.gts-home .gts-bottom{grid-template-columns:1fr}}
@media(max-width:600px){.gts-home{padding:15px 12px 25px}.gts-home .gts-hero{padding:23px 20px;border-radius:16px}.gts-home .gts-hero h1{font-size:24px}.gts-home .gts-stats{gap:10px}.gts-home .gts-stat-body{padding:14px 12px}.gts-home .gts-stat-value{font-size:25px}.gts-home .gts-stat-icon{width:39px;height:39px;font-size:17px}.gts-home .gts-stat-link{padding:10px 12px}.gts-home .gts-panel{padding:17px}.gts-home .gts-quick-grid{grid-template-columns:1fr}}
</style>
<div class="gts-home">
    <section class="gts-hero">
        <div class="gts-eyebrow">GTS Mayorista · Panel principal</div>
        <h1>Bienvenido al sistema</h1>
        <p>Accedé a tus herramientas de gestión y consultá la información más importante desde un solo lugar.</p>
        <div class="gts-hero-user"><i class="far fa-user-circle"></i>{{ Auth::user()->name }}</div>
    </section>

    <div class="gts-section-heading">
        <div><h2 class="gts-section-title">Resumen general</h2><p class="gts-section-sub">Información actualizada de los módulos disponibles</p></div>
    </div>
    <section class="gts-stats" aria-label="Resumen de módulos">
        @can('clientes index')
            <article class="gts-stat">
                <div class="gts-stat-body"><div><div class="gts-stat-name">Clientes</div><div class="gts-stat-value">{{ number_format($resumen['clientes'] ?? 0, 0, ',', '.') }}</div><div class="gts-stat-desc">Clientes registrados</div></div><span class="gts-stat-icon stat-clients"><i class="fas fa-user-friends"></i></span></div>
                <a class="gts-stat-link" href="{{ route('clientes.index') }}">Gestionar clientes <i class="fas fa-arrow-right"></i></a>
            </article>
        @endcan
        @can('pedido_compras index')
            <article class="gts-stat">
                <div class="gts-stat-body"><div><div class="gts-stat-name">Pedidos</div><div class="gts-stat-value">{{ number_format($resumen['pedidos'] ?? 0, 0, ',', '.') }}</div><div class="gts-stat-desc">Pedidos registrados</div></div><span class="gts-stat-icon stat-orders"><i class="fas fa-file-invoice"></i></span></div>
                <a class="gts-stat-link" href="{{ route('pedido_compras.index') }}">Gestionar pedidos <i class="fas fa-arrow-right"></i></a>
            </article>
        @endcan
        @can('ciudades index')
            <article class="gts-stat">
                <div class="gts-stat-body"><div><div class="gts-stat-name">Ciudades</div><div class="gts-stat-value">{{ number_format($resumen['ciudades'] ?? 0, 0, ',', '.') }}</div><div class="gts-stat-desc">Ciudades registradas</div></div><span class="gts-stat-icon stat-cities"><i class="fas fa-city"></i></span></div>
                <a class="gts-stat-link" href="{{ route('ciudades.index') }}">Gestionar ciudades <i class="fas fa-arrow-right"></i></a>
            </article>
        @endcan
        @can('departamentos index')
            <article class="gts-stat">
                <div class="gts-stat-body"><div><div class="gts-stat-name">Departamentos</div><div class="gts-stat-value">{{ number_format($resumen['departamentos'] ?? 0, 0, ',', '.') }}</div><div class="gts-stat-desc">Departamentos registrados</div></div><span class="gts-stat-icon stat-depts"><i class="fas fa-map-marked-alt"></i></span></div>
                <a class="gts-stat-link" href="{{ route('Departamentos.index') }}">Gestionar departamentos <i class="fas fa-arrow-right"></i></a>
            </article>
        @endcan
    </section>

    <div class="gts-bottom">
        <section class="gts-panel">
            <h2 class="gts-section-title">Accesos rápidos</h2>
            <p class="gts-section-sub">Entrá directamente a tus módulos habituales</p>
            <div class="gts-quick-grid">
                @can('clientes index')
                    <a href="{{ route('clientes.index') }}" class="gts-quick-link"><span class="gts-quick-icon"><i class="fas fa-users"></i></span><span><span class="gts-quick-name d-block">Clientes</span><span class="gts-quick-detail d-block">Consultar y administrar registros</span></span></a>
                @endcan
                @can('pedido_compras index')
                    <a href="{{ route('pedido_compras.index') }}" class="gts-quick-link"><span class="gts-quick-icon"><i class="fas fa-file-alt"></i></span><span><span class="gts-quick-name d-block">Pedidos</span><span class="gts-quick-detail d-block">Consultar y gestionar pedidos</span></span></a>
                @endcan
                @can('ciudades index')
                    <a href="{{ route('ciudades.index') }}" class="gts-quick-link"><span class="gts-quick-icon"><i class="fas fa-city"></i></span><span><span class="gts-quick-name d-block">Ciudades</span><span class="gts-quick-detail d-block">Administrar ciudades registradas</span></span></a>
                @endcan
                @can('departamentos index')
                    <a href="{{ route('Departamentos.index') }}" class="gts-quick-link"><span class="gts-quick-icon"><i class="fas fa-map"></i></span><span><span class="gts-quick-name d-block">Departamentos</span><span class="gts-quick-detail d-block">Administrar departamentos</span></span></a>
                @endcan
            </div>
            @if (!auth()->user()->canAny(['clientes index', 'pedido_compras index', 'ciudades index', 'departamentos index']))
                <p class="gts-empty">No tenés módulos habilitados en este panel. Consultá con el administrador.</p>
            @endif
        </section>
        <aside class="gts-panel">
            <h2 class="gts-section-title">Tu espacio de trabajo</h2>
            <p class="gts-section-sub">Información de tu sesión actual</p>
            <div class="gts-info-row"><span class="gts-info-icon"><i class="far fa-user"></i></span><div><div class="gts-info-label">Usuario conectado</div><div class="gts-info-value">{{ Auth::user()->name }}</div></div></div>
            <div class="gts-info-row"><span class="gts-info-icon"><i class="far fa-calendar-alt"></i></span><div><div class="gts-info-label">Fecha de hoy</div><div class="gts-info-value">{{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</div></div></div>
            <div class="gts-info-row"><span class="gts-info-icon"><i class="fas fa-shield-alt"></i></span><div><div class="gts-info-label">Acceso</div><div class="gts-info-value">Sesión iniciada</div></div></div>
        </aside>
    </div>
</div>
@endsection
