@extends('layouts.app')

@section('content')
<section class="content-header ecommerce-heading">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap:12px">
            <div>
                <div class="ec-eyebrow">CATÁLOGO DE PRODUCTOS</div>
                <h1><i class="fas fa-shopping-bag mr-2"></i>E-commerce</h1>
                <p class="text-muted mb-0">Fotos, talles, precios y artículos organizados por línea.</p>
            </div>
            @can('articulos create')
                <a href="{{ route('ecommerce.importar.form') }}" class="btn btn-primary ec-upload-btn">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Importar imágenes
                </a>
            @endcan
        </div>
    </div>
</section>

<section class="content px-3 ecommerce-content pb-4">
    @if(session('ecommerce_mensaje'))
        <div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>{{ session('ecommerce_mensaje') }}</div>
    @endif
    <div class="ec-line-wrap">
        <div class="ec-nav-label"><i class="fas fa-layer-group mr-2"></i>Explorar líneas</div>
        <nav class="ec-line-nav" aria-label="Líneas del catálogo">
            <a href="{{ route('ecommerce.index', request()->except(['page', 'linea'])) }}"
                class="ec-line-tab {{ !request('linea') ? 'selected' : '' }}">
                <i class="fas fa-th-large mr-1"></i>Todas
            </a>
            @foreach($lineas as $linea)
                <a href="{{ route('ecommerce.index', array_merge(request()->except(['page', 'linea']), ['linea' => $linea->linea_cod])) }}"
                    class="ec-line-tab {{ (string)request('linea') === (string)$linea->linea_cod ? 'selected' : '' }}">
                    {{ $linea->linea_desc }}
                </a>
            @endforeach
            <a href="{{ route('ecommerce.index', array_merge(request()->except(['page', 'linea']), ['linea' => 'sin-linea'])) }}"
                class="ec-line-tab {{ request('linea') === 'sin-linea' ? 'selected' : '' }}">Sin clasificar</a>
        </nav>
    </div>

    <div class="ec-filter-box">
        <form method="GET" action="{{ route('ecommerce.index') }}">
            @if(request('linea'))
                <input type="hidden" name="linea" value="{{ request('linea') }}">
            @endif
            <div class="row align-items-end">
                <div class="col-xl-4 col-md-6 mb-3">
                    <label for="ec-buscar">Buscar artículo, código u OT</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                        <input type="search" id="ec-buscar" name="buscar" class="form-control"
                            placeholder="Ej. bermuda, 100613678CV, OT26697" value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-xl-2 col-md-3 col-6 mb-3">
                    <label for="ec-imagen">Fotografía</label>
                    <select id="ec-imagen" name="imagen" class="form-control">
                        <option value="">Todas</option>
                        <option value="si" {{ request('imagen') === 'si' ? 'selected' : '' }}>Con imagen</option>
                        <option value="no" {{ request('imagen') === 'no' ? 'selected' : '' }}>Sin imagen</option>
                    </select>
                </div>
                <div class="col-xl-2 col-md-3 col-6 mb-3">
                    <label for="ec-orden">Ordenar por</label>
                    <select id="ec-orden" name="orden" class="form-control">
                        <option value="codigo" {{ request('orden', 'codigo') === 'codigo' ? 'selected' : '' }}>Código</option>
                        <option value="descripcion" {{ request('orden') === 'descripcion' ? 'selected' : '' }}>Descripción</option>
                        <option value="precio_asc" {{ request('orden') === 'precio_asc' ? 'selected' : '' }}>Menor precio</option>
                        <option value="precio_desc" {{ request('orden') === 'precio_desc' ? 'selected' : '' }}>Mayor precio</option>
                    </select>
                </div>
                <div class="col-xl-2 col-md-3 col-6 mb-3">
                    <label for="ec-min">Precio mín. Gs.</label>
                    <input id="ec-min" type="number" min="0" name="precio_min" class="form-control"
                        placeholder="0" value="{{ request('precio_min') }}">
                </div>
                <div class="col-xl-2 col-md-3 col-6 mb-3">
                    <label for="ec-max">Precio máx. Gs.</label>
                    <input id="ec-max" type="number" min="0" name="precio_max" class="form-control"
                        placeholder="Sin límite" value="{{ request('precio_max') }}">
                </div>
            </div>
            <div class="d-flex justify-content-end align-items-center ec-filter-actions">
                <a href="{{ route('ecommerce.index') }}" class="btn btn-light mr-2">
                    <i class="fas fa-eraser mr-1"></i>Limpiar
                </a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i>Aplicar filtros</button>
            </div>
        </form>
    </div>

    <div class="ec-results-bar d-flex justify-content-between align-items-center flex-wrap">
        <div><strong>{{ number_format($productos->total(), 0, ',', '.') }}</strong>
            {{ $productos->total() === 1 ? 'producto' : 'productos' }} encontrados
            <span class="text-muted ml-2">Agrupados por código base y color</span>
        </div>
        <div class="ec-results-page">Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</div>
    </div>

    <div class="ec-grid">
        @forelse($productos as $producto)
            @php
                $opciones = $variantes->get($producto->codigo_base, collect());
                $stockTotal = $opciones->sum('disponible');
                $imagenUrl = $producto->imagen ? asset('storage/' . $producto->imagen) : null;
            @endphp
            <article class="ec-card">
                <div class="ec-photo-wrap">
                    @if($imagenUrl)
                        <img src="{{ $imagenUrl }}" alt="Imagen de {{ $producto->descripcion }}"
                            class="ec-photo" loading="lazy">
                    @else
                        <div class="ec-no-photo">
                            <i class="far fa-image"></i>
                            <span>Sin fotografía</span>
                        </div>
                    @endif
                    <span class="ec-status {{ $stockTotal > 0 ? 'is-available' : 'is-unavailable' }}">
                        <span class="ec-status-dot"></span>
                        {{ $stockTotal > 0 ? 'Con stock' : 'Sin stock registrado' }}
                    </span>
                </div>
                <div class="ec-card-body">
                    <div class="ec-line-badge">{{ $producto->linea_desc ?: 'Sin clasificar' }}</div>
                    <h2 class="ec-product-name" title="{{ $producto->descripcion }}">{{ $producto->descripcion }}</h2>
                    <div class="ec-product-code">Código base: <strong>{{ $producto->codigo_base }}</strong></div>
                    <div class="ec-price">
                        Gs. {{ number_format($producto->precio_desde, 0, ',', '.') }}
                        @if((float)$producto->precio_hasta !== (float)$producto->precio_desde)
                            <small>– {{ number_format($producto->precio_hasta, 0, ',', '.') }}</small>
                        @endif
                    </div>
                    <div class="ec-size-title">Talles / variantes
                        <span>{{ $producto->variantes }}</span>
                    </div>
                    <div class="ec-sizes">
                        @foreach($opciones->take(12) as $opcion)
                            @php
                                $esVariante = preg_match('/^[0-9]{9}[A-Z]{2}[A-Z0-9]{2}$/i', $opcion->art_codigo);
                                $talle = $esVariante ? substr($opcion->art_codigo, -2) : 'Único';
                            @endphp
                            <span class="ec-size {{ (float)$opcion->disponible > 0 ? '' : 'no-stock' }}"
                                title="{{ $opcion->art_codigo }}: {{ number_format($opcion->disponible, 0, ',', '.') }} unidades">
                                {{ $talle }}
                            </span>
                        @endforeach
                        @if($opciones->count() > 12)
                            <span class="ec-size">+{{ $opciones->count() - 12 }}</span>
                        @endif
                    </div>
                    <div class="ec-meta">
                        <span><i class="fas fa-boxes mr-1"></i>{{ number_format($stockTotal, 0, ',', '.') }} unidades en stock total</span>
                        @if($producto->ot)
                            <span><i class="fas fa-hashtag mr-1"></i>OT {{ $producto->ot }}</span>
                        @endif
                        @if($producto->valor_v || $producto->valor_c)
                            <span title="Valores extraídos del nombre del archivo; no modifican precios del artículo">
                                Archivo: {{ $producto->valor_v }} {{ $producto->valor_c }}
                            </span>
                        @endif
                    </div>
                    @can('articulos edit')
                        <form method="POST" action="{{ route('ecommerce.linea', $producto->codigo_base) }}" class="ec-classify">
                            @csrf
                            @method('PATCH')
                            <label for="linea-{{ $loop->index }}">Clasificar línea</label>
                            <div class="d-flex">
                                <select id="linea-{{ $loop->index }}" name="linea_cod" class="form-control form-control-sm">
                                    <option value="">Sin clasificar</option>
                                    @foreach($lineas as $linea)
                                        <option value="{{ $linea->linea_cod }}" {{ (string)$producto->linea_cod === (string)$linea->linea_cod ? 'selected' : '' }}>
                                            {{ $linea->linea_desc }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-outline-primary btn-sm ml-1" title="Guardar línea">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                        </form>
                    @endcan
                    @can('articulos destroy')
                        @if($producto->imagen)
                            <form method="POST" action="{{ route('ecommerce.imagen.eliminar', $producto->codigo_base) }}"
                                class="ec-remove-photo" onsubmit="return confirm('¿Retirar la foto de este producto? El artículo no se eliminará.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"><i class="far fa-trash-alt mr-1"></i>Retirar fotografía</button>
                            </form>
                        @endif
                    @endcan
                </div>
            </article>
        @empty
            <div class="ec-empty">
                <i class="fas fa-search"></i>
                <h3>No encontramos productos</h3>
                <p>Probá con otro código, línea o filtro. También podés cargar imágenes desde el importador.</p>
                <a href="{{ route('ecommerce.index') }}" class="btn btn-primary">Ver todos los artículos</a>
            </div>
        @endforelse
    </div>

    @if($productos->hasPages())
        <div class="ec-pagination d-flex justify-content-center mt-4">{{ $productos->links() }}</div>
    @endif
</section>

<style>
.ecommerce-heading{padding-bottom:14px!important}.ec-eyebrow{font-size:11px;font-weight:800;letter-spacing:1.5px;color:#3b72ce;margin-bottom:4px}
.ecommerce-heading h1{font-size:27px!important;letter-spacing:-.7px}.ec-upload-btn{padding:11px 17px;font-weight:700}
.ecommerce-content{max-width:1700px;margin:0 auto}.ec-line-wrap,.ec-filter-box{background:#fff;border:1px solid #e3eaf3;border-radius:17px;padding:17px 19px;box-shadow:0 8px 26px rgba(28,48,77,.04);margin-bottom:17px}
.ec-nav-label{font-weight:800;color:#253d61;font-size:13px;margin-bottom:12px}.ec-line-nav{display:flex;align-items:center;gap:9px;overflow-x:auto;padding:1px 0 8px;scrollbar-width:thin}
.ec-line-tab{display:inline-flex;align-items:center;white-space:nowrap;border:1px solid #dce5f2;background:#f8faff;color:#4b6280;border-radius:100px;padding:9px 15px;font-size:12px;font-weight:700;text-decoration:none!important;transition:.15s}
.ec-line-tab:hover{background:#eaf2ff;color:#2359b6}.ec-line-tab.selected{background:#2465d3;border-color:#2465d3;color:#fff;box-shadow:0 4px 10px rgba(36,101,211,.18)}
.ec-filter-box label{font-size:11px!important;font-weight:800!important;color:#61758f!important;letter-spacing:.2px}.ec-filter-box .form-control{height:41px!important}.ec-filter-actions{border-top:1px solid #eff3f8;padding-top:11px}
.ec-results-bar{gap:8px;margin:21px 1px 15px;font-size:14px;color:#324a68}.ec-results-bar strong{font-size:19px;color:#1c375a}.ec-results-page{font-size:12px;color:#788ba3}
.ec-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:17px}
.ec-card{background:#fff;border:1px solid #e3eaf3;border-radius:17px;box-shadow:0 6px 20px rgba(24,44,76,.045);overflow:hidden;display:flex;flex-direction:column;min-width:0;transition:transform .18s ease,box-shadow .18s ease}
.ec-card:hover{transform:translateY(-3px);box-shadow:0 15px 30px rgba(27,47,79,.10)}.ec-photo-wrap{height:220px;position:relative;background:linear-gradient(145deg,#fff,#f5f8fc);display:flex;align-items:center;justify-content:center;padding:14px;border-bottom:1px solid #f0f3f7}
.ec-photo{width:100%;height:100%;object-fit:contain;mix-blend-mode:multiply}.ec-no-photo{display:flex;flex-direction:column;align-items:center;gap:11px;color:#a4b3c8;font-size:12px;font-weight:650}
.ec-no-photo i{font-size:47px;color:#d0dbe9}.ec-status{position:absolute;left:12px;bottom:12px;display:inline-flex;align-items:center;gap:7px;font-size:10px;font-weight:800;padding:6px 10px;background:#fff;border:1px solid #e8edf5;border-radius:100px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.ec-status-dot{width:7px;height:7px;border-radius:50%;background:#c6cbd4}.ec-status.is-available{color:#287e56}.ec-status.is-available .ec-status-dot{background:#29a76b}.ec-status.is-unavailable{color:#78859a}
.ec-card-body{padding:17px;display:flex;flex-direction:column;flex:1}.ec-line-badge{font-size:10px;letter-spacing:.3px;text-transform:uppercase;color:#3170bf;font-weight:800;margin-bottom:7px}
.ec-product-name{font-size:15px!important;line-height:1.35;font-weight:800;color:#223a5a;min-height:41px;margin:0 0 5px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.ec-product-code{font-size:11px;color:#7889a0;margin-bottom:9px}.ec-product-code strong{color:#3c536f}.ec-price{color:#172f50;font-size:20px;font-weight:900;line-height:1.3;margin-bottom:15px;letter-spacing:-.5px}.ec-price small{font-size:12px;color:#70839b;font-weight:650}
.ec-size-title{font-size:11px;font-weight:800;color:#687d97;display:flex;justify-content:space-between;margin-bottom:8px}.ec-size-title span{color:#98a9bd}
.ec-sizes{display:flex;gap:5px;flex-wrap:wrap;margin-bottom:13px;min-height:30px}.ec-size{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:27px;border:1px solid #dce6f3;background:#f7faff;border-radius:6px;font-size:10px;font-weight:800;color:#355b87;padding:0 7px}
.ec-size.no-stock{background:#f8f9fc;color:#aab6c6;border-color:#edf1f7;text-decoration:line-through}
.ec-meta{border-top:1px solid #eef2f7;padding-top:11px;font-size:11px;color:#72849d;display:flex;flex-direction:column;gap:5px;margin-top:auto}
.ec-classify{margin-top:12px;padding-top:10px;border-top:1px solid #edf2f7}.ec-classify label{font-size:10px!important;color:#7a8da4!important;margin-bottom:4px}
.ec-classify .form-control{min-width:0;height:32px!important;font-size:11px}.ec-classify button{width:38px;flex:0 0 38px}
.ec-remove-photo{text-align:right;margin-top:7px}.ec-remove-photo button{background:none;border:0;color:#a66b72;font-size:10px;cursor:pointer;padding:4px}.ec-remove-photo button:hover{color:#bb2537}
.ec-empty{grid-column:1/-1;text-align:center;padding:65px 15px;background:#fff;border:1px dashed #cad7e9;border-radius:16px}
.ec-empty>i{font-size:38px;color:#a6b9d4;margin-bottom:12px}.ec-empty h3{font-size:19px;font-weight:800}.ec-empty p{color:#70829b}
@media(max-width:1390px){.ec-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:1000px){.ec-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:575px){.ec-grid{grid-template-columns:1fr}.ec-photo-wrap{height:250px}.ec-line-wrap,.ec-filter-box{padding:14px}.ec-results-bar{font-size:12px}}
</style>
@endsection
