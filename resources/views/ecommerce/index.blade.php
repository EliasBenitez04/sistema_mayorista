@extends('layouts.app')

@section('content')
<div class="ec-storefront" id="ecommerce-storefront">
    <div class="ec-service-bar">
        <span><i class="fas fa-circle ec-service-dot"></i> CATÁLOGO DIGITAL GOTITAS</span>
        <span class="ec-service-note">Explorá nuestros artículos, variantes y precios en guaraníes</span>
        <a href="{{ route('home') }}" class="ec-back-admin"><i class="fas fa-arrow-left"></i> Volver al sistema</a>
    </div>

    <header class="ec-shop-header">
        <a href="{{ route('ecommerce.index') }}" class="ec-brand" aria-label="Ir al inicio del catálogo">
            <span class="ec-brand-mark">G<span>.</span></span>
            <span class="ec-brand-word">GOTITAS<small>CATÁLOGO DE PRODUCTOS</small></span>
        </a>
        <form action="{{ route('ecommerce.index') }}" method="GET" class="ec-search-form" role="search">
            @foreach(request()->except(['buscar', 'page']) as $nombre => $valor)
                @if(is_scalar($valor))
                    <input type="hidden" name="{{ $nombre }}" value="{{ $valor }}">
                @endif
            @endforeach
            <label class="sr-only" for="ec-main-search">Buscar productos</label>
            <input id="ec-main-search" type="search" name="buscar" value="{{ request('buscar') }}"
                placeholder="Buscar productos, códigos, descripción u OT..." autocomplete="off">
            <button type="submit" aria-label="Buscar"><i class="fas fa-search"></i><span>Buscar</span></button>
        </form>
        <div class="ec-header-actions">
            @can('pedido_compras create')
                <button type="button" class="ec-header-cart js-ec-cart-open" aria-label="Abrir carrito de pedido">
                    <i class="fas fa-shopping-cart"></i><span>Mi carrito</span>
                    <b class="ec-cart-count js-ec-cart-count" aria-live="polite">0</b>
                </button>
            @endcan
            @can('articulos create')
                <a href="{{ route('ecommerce.importar.form') }}" class="ec-header-import">
                    <i class="fas fa-cloud-upload-alt"></i><span>Importar fotos</span>
                </a>
            @endcan
            <button type="button" class="ec-header-screen" id="ec-screen-toggle" aria-pressed="false"
                title="Mostrar catálogo como página completa">
                <i class="fas fa-expand-arrows-alt"></i><span>Vista tienda</span>
            </button>
        </div>
    </header>

    <nav class="ec-category-bar" aria-label="Categorías del catálogo">
        <span class="ec-category-label">LÍNEAS <i class="fas fa-angle-right"></i></span>
        <div class="ec-category-scroll">
            <a href="{{ route('ecommerce.index', request()->except(['page','linea'])) }}"
                class="ec-category-link {{ !request('linea') ? 'is-current' : '' }}">
                Todas las líneas
            </a>
            @foreach($lineas as $lineaItem)
                <a href="{{ route('ecommerce.index', array_merge(request()->except(['page','linea']), ['linea' => $lineaItem->linea_cod])) }}"
                    class="ec-category-link {{ (string)request('linea') === (string)$lineaItem->linea_cod ? 'is-current' : '' }}">
                    {{ $lineaItem->linea_desc }}
                </a>
            @endforeach
            <a href="{{ route('ecommerce.index', array_merge(request()->except(['page','linea']), ['linea' => 'sin-linea'])) }}"
                class="ec-category-link {{ request('linea') === 'sin-linea' ? 'is-current' : '' }}">Sin clasificar</a>
        </div>
    </nav>

    <section class="ec-hero" aria-labelledby="ec-hero-title">
        <div class="ec-hero-copy">
            <span class="ec-hero-eyebrow"><span></span> NUEVA EXPERIENCIA DE CATÁLOGO</span>
            <h1 id="ec-hero-title">Encontrá lo que <em>buscás.</em></h1>
            <p>Explorá la colección por línea, encontrá talles y consultá los precios de nuestros artículos.</p>
            <a href="#ec-productos" class="ec-hero-btn">Explorar productos <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="ec-hero-aside" aria-hidden="true">
            <div class="ec-hero-figure ec-figure-one"></div>
            <div class="ec-hero-figure ec-figure-two"></div>
            <div class="ec-hero-initial">G.</div>
            <div class="ec-hero-tag">COLECCIÓN <strong>GOTITAS</strong></div>
        </div>
    </section>

    <div class="ec-info-strip" aria-label="Características del catálogo">
        <span><i class="fas fa-th-large"></i> Categorías por línea</span>
        <span><i class="fas fa-ruler-horizontal"></i> Talles y variantes</span>
        <span><i class="fas fa-tags"></i> Precios en guaraníes</span>
        <span><i class="fas fa-search"></i> Búsqueda rápida</span>
    </div>

    <main id="ec-productos" class="ec-shop-body">
        <div class="ec-breadcrumb"><a href="{{ route('ecommerce.index') }}">Catálogo</a><i class="fas fa-chevron-right"></i>
            <span>
                @if(request('linea') === 'sin-linea') Sin clasificar
                @elseif(request('linea')) {{ optional($lineas->firstWhere('linea_cod', request('linea')))->linea_desc ?? 'Línea' }}
                @else Todos los productos @endif
            </span>
        </div>

        @if(session('ecommerce_mensaje'))
            <div class="ec-success"><i class="fas fa-check-circle"></i> {{ session('ecommerce_mensaje') }}</div>
        @endif

        <div class="ec-shop-columns">
            <aside class="ec-filters" id="ec-filters" aria-label="Filtros de productos">
                <form method="GET" action="{{ route('ecommerce.index') }}" id="ec-filter-form">
                    @if(request('buscar')) <input type="hidden" name="buscar" value="{{ request('buscar') }}"> @endif
                    @if(request('linea')) <input type="hidden" name="linea" value="{{ request('linea') }}"> @endif
                    <div class="ec-filters-heading">
                        <strong>Filtrar productos</strong>
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div class="ec-filter-group">
                        <label class="ec-filter-title" for="ec-sort">ORDENAR POR</label>
                        <select id="ec-sort" name="orden" class="ec-filter-input" data-auto-submit>
                            <option value="codigo" {{ request('orden','codigo') === 'codigo' ? 'selected' : '' }}>Código del producto</option>
                            <option value="descripcion" {{ request('orden') === 'descripcion' ? 'selected' : '' }}>Nombre A–Z</option>
                            <option value="precio_asc" {{ request('orden') === 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                            <option value="precio_desc" {{ request('orden') === 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                        </select>
                    </div>
                    <div class="ec-filter-group">
                        <div class="ec-filter-title">FOTOGRAFÍAS</div>
                        <label class="ec-radio"><input type="radio" name="imagen" value="" {{ !request('imagen') ? 'checked' : '' }}> Todas las imágenes</label>
                        <label class="ec-radio"><input type="radio" name="imagen" value="si" {{ request('imagen') === 'si' ? 'checked' : '' }}> Solo con fotografía</label>
                        <label class="ec-radio"><input type="radio" name="imagen" value="no" {{ request('imagen') === 'no' ? 'checked' : '' }}> Sin fotografía</label>
                    </div>
                    <div class="ec-filter-group">
                        <div class="ec-filter-title">RANGO DE PRECIOS</div>
                        <div class="ec-price-range">
                            <div><label for="ec-precio-desde">Mínimo (Gs.)</label>
                                <input type="number" id="ec-precio-desde" name="precio_min" min="0" inputmode="numeric"
                                    value="{{ request('precio_min') }}" placeholder="0"></div>
                            <div><label for="ec-precio-hasta">Máximo (Gs.)</label>
                                <input type="number" id="ec-precio-hasta" name="precio_max" min="0" inputmode="numeric"
                                    value="{{ request('precio_max') }}" placeholder="Sin límite"></div>
                        </div>
                    </div>
                    <button type="submit" class="ec-apply-filters">Aplicar filtros <i class="fas fa-arrow-right"></i></button>
                    <a class="ec-clear-filters" href="{{ route('ecommerce.index') }}"><i class="fas fa-undo-alt"></i> Limpiar filtros</a>
                </form>
                <div class="ec-side-note"><i class="fas fa-info-circle"></i>
                    Disponibilidad basada en el stock registrado. Confirmá las existencias antes de vender.
                </div>
            </aside>

            <section class="ec-products-column" aria-label="Listado de productos">
                <div class="ec-listing-head">
                    <div>
                        <span class="ec-listing-eyebrow">COLECCIÓN GOTITAS</span>
                        <h2>{{ request('linea') === 'sin-linea' ? 'Sin clasificar' :
                            (request('linea') ? (optional($lineas->firstWhere('linea_cod', request('linea')))->linea_desc ?? 'Productos') : 'Todos los productos') }}</h2>
                        <p><strong>{{ number_format($productos->total(), 0, ',', '.') }}</strong> productos encontrados
                            @if(request('buscar')) para <strong>“{{ request('buscar') }}”</strong> @endif
                        </p>
                    </div>
                    <button type="button" class="ec-mobile-filter-btn" id="ec-mobile-filters" aria-expanded="false">
                        <i class="fas fa-filter"></i> Filtros
                    </button>
                </div>

                @can('pedido_compras create')
                    <div class="ec-fastbar" aria-label="Selector rápido de cantidades">
                        <div class="ec-fastbar-title">
                            <span class="ec-fastbar-icon"><i class="fas fa-bolt"></i></span>
                            <div><strong>Pedido rápido</strong><small>Elegí la cantidad y tocá los talles para agregarlos.</small></div>
                        </div>
                        <div class="ec-fastbar-controls">
                            <label for="ec-insert-qty">Cantidad por clic</label>
                            <div class="ec-fastbar-stepper">
                                <button type="button" data-ec-qty-step="-1" aria-label="Restar cantidad"><i class="fas fa-minus"></i></button>
                                <input type="number" min="1" max="9999" value="1" id="ec-insert-qty"
                                    inputmode="numeric" aria-label="Cantidad de inserción">
                                <button type="button" data-ec-qty-step="1" aria-label="Sumar cantidad"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="ec-fastbar-presets" aria-label="Cantidades frecuentes">
                                @foreach([1, 3, 6, 12] as $rapido)
                                    <button type="button" class="js-ec-qty-preset {{ $rapido === 1 ? 'is-active' : '' }}"
                                        data-qty="{{ $rapido }}">{{ $rapido }}</button>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="ec-fastbar-cart js-ec-cart-open">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Ver carrito</span>
                            <strong class="js-ec-cart-count">0</strong>
                        </button>
                    </div>
                @endcan

                @if(request('imagen') || request('precio_min') || request('precio_max') || request('buscar'))
                    <div class="ec-active-filters">
                        <span>Filtros activos</span>
                        @if(request('buscar')) <span class="ec-active-chip">Búsqueda: {{ request('buscar') }}</span> @endif
                        @if(request('imagen')) <span class="ec-active-chip">{{ request('imagen') === 'si' ? 'Con imagen' : 'Sin imagen' }}</span> @endif
                        @if(request('precio_min')) <span class="ec-active-chip">Desde Gs. {{ number_format((float)request('precio_min'), 0, ',', '.') }}</span> @endif
                        @if(request('precio_max')) <span class="ec-active-chip">Hasta Gs. {{ number_format((float)request('precio_max'), 0, ',', '.') }}</span> @endif
                        <a href="{{ route('ecommerce.index', request()->only('linea')) }}">Quitar filtros <i class="fas fa-times"></i></a>
                    </div>
                @endif

                <div class="ec-products-grid">
                    @forelse($productos as $producto)
                        @php
                            $opciones = $variantes->get($producto->codigo_base, collect());
                            $stockTotal = $opciones->sum('disponible');
                            $stockParaPedir = $opciones->sum('disponible_pedir');
                            $fotoUrl = $producto->imagen ? route('ecommerce.foto', ['codigo' => $producto->codigo_base]) : null;
                            $fotoCodigo = $producto->imagen_codigo ?: $producto->codigo_base;
                        @endphp
                        <article class="ec-product-card">
                            <div class="ec-image-surface {{ $fotoUrl ? '' : 'ec-image-failed' }}">
                                @if($fotoUrl)
                                    <button type="button" class="ec-image-open js-ec-open" data-template="ec-tpl-{{ $loop->index }}"
                                        aria-label="Ver detalles de {{ $producto->descripcion }}">
                                        <img src="{{ $fotoUrl }}" class="ec-product-image"
                                            alt="Fotografía de {{ $producto->descripcion }}" loading="lazy"
                                            decoding="async" onerror="this.closest('.ec-image-surface').classList.add('ec-image-failed')">
                                    </button>
                                @endif
                                <div class="ec-image-placeholder">
                                    <i class="fas fa-tshirt"></i><span>Imagen no disponible</span>
                                </div>
                                <span class="ec-photo-label">{{ $producto->linea_desc ?: 'COLECCIÓN' }}</span>
                                @if($stockParaPedir > 0)
                                    <span class="ec-stock-badge"><i class="fas fa-circle"></i> Disponible para pedir</span>
                                @else
                                    <span class="ec-stock-badge ec-stock-empty"><i class="fas fa-circle"></i> Sin disponible para pedir</span>
                                @endif
                                <button type="button" class="ec-quick-icon js-ec-open" data-template="ec-tpl-{{ $loop->index }}"
                                    aria-label="Vista rápida de {{ $producto->descripcion }}" title="Vista rápida">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                            <div class="ec-card-information">
                                <span class="ec-card-category">{{ $producto->linea_desc ?: 'Sin clasificar' }}</span>
                                <h3>{{ $producto->descripcion }}</h3>
                                <div class="ec-product-sku">Código: {{ $producto->codigo_base }}</div>
                                <div class="ec-card-price">
                                    <strong>Gs. {{ number_format($producto->precio_desde, 0, ',', '.') }}</strong>
                                    @if((float)$producto->precio_hasta != (float)$producto->precio_desde)
                                        <small>hasta Gs. {{ number_format($producto->precio_hasta, 0, ',', '.') }}</small>
                                    @endif
                                </div>
                                <div class="ec-card-sizes ec-card-sizes-quick">
                                    <div class="ec-sizes-heading">
                                        <strong>Talles</strong>
                                        @can('pedido_compras create')
                                            <span>Un clic para agregar <i class="fas fa-plus-circle"></i></span>
                                        @endcan
                                    </div>
                                    <div class="ec-sizes-buttons">
                                        @foreach($opciones->take(12) as $opcion)
                                            @php($talle = preg_match('/^[0-9]{9}[A-Z]{2}[A-Z0-9]{2}$/i', $opcion->art_codigo) ? substr($opcion->art_codigo, -2) : 'Único')
                                            @can('pedido_compras create')
                                                <button type="button"
                                                    class="ec-quick-size js-ec-quick-add {{ (float)$opcion->disponible_pedir > 0 ? '' : 'is-unavailable' }}"
                                                    data-codigo="{{ $opcion->art_codigo }}"
                                                    data-talle="{{ $talle }}"
                                                    {{ (float)$opcion->disponible_pedir <= 0 ? 'disabled' : '' }}
                                                    title="{{ $opcion->art_codigo }} · {{ number_format($opcion->disponible_pedir, 0, ',', '.') }} unidades disponibles para pedido">
                                                    <span>{{ $talle }}</span>
                                                    <small>{{ (float)$opcion->disponible_pedir > 0 ? number_format($opcion->disponible_pedir, 0, ',', '.') . ' disp.' : 'Sin disp.' }}</small>
                                                    <em class="ec-size-in-cart" hidden></em>
                                                </button>
                                            @else
                                                <span class="ec-quick-size {{ (float)$opcion->disponible_pedir > 0 ? '' : 'is-unavailable' }}">
                                                    <span>{{ $talle }}</span>
                                                    <small>{{ number_format($opcion->disponible_pedir, 0, ',', '.') }} disp.</small>
                                                </span>
                                            @endcan
                                        @endforeach
                                        @if($opciones->count() > 12)
                                            <button type="button" class="ec-quick-more js-ec-open"
                                                data-template="ec-tpl-{{ $loop->index }}" title="Ver los demás talles">
                                                +{{ $opciones->count()-12 }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="ec-view-product js-ec-open" data-template="ec-tpl-{{ $loop->index }}">
                                    <i class="far fa-eye"></i> Ver información y más talles
                                </button>
                            </div>
                        </article>

                        <template id="ec-tpl-{{ $loop->index }}">
                            <div class="ec-detail-grid">
                                <div class="ec-detail-image {{ $fotoUrl ? '' : 'ec-image-failed' }}">
                                    @if($fotoUrl)
                                        <img src="{{ $fotoUrl }}" alt="Fotografía de {{ $producto->descripcion }}"
                                            onerror="this.parentElement.classList.add('ec-image-failed')">
                                    @endif
                                    <div class="ec-detail-fallback"><i class="fas fa-tshirt"></i><span>Imagen no disponible</span></div>
                                </div>
                                <div class="ec-detail-content">
                                    <span class="ec-detail-kicker">{{ $producto->linea_desc ?: 'Colección Gotitas' }}</span>
                                    <h2 id="ec-dialog-title">{{ $producto->descripcion }}</h2>
                                    <div class="ec-detail-sku">Código base: <strong>{{ $producto->codigo_base }}</strong></div>
                                    <div class="ec-detail-price">Gs. {{ number_format($producto->precio_desde, 0, ',', '.') }}
                                        @if((float)$producto->precio_hasta != (float)$producto->precio_desde)
                                            <small>– Gs. {{ number_format($producto->precio_hasta, 0, ',', '.') }}</small>
                                        @endif
                                    </div>
                                    <div class="ec-detail-rule"></div>
                                    <h3>Talles y disponibilidad para pedidos</h3>
                                    <div class="ec-detail-variants">
                                        @forelse($opciones as $opcion)
                                            @php($talle = preg_match('/^[0-9]{9}[A-Z]{2}[A-Z0-9]{2}$/i', $opcion->art_codigo) ? substr($opcion->art_codigo, -2) : 'Único')
                                            <div class="ec-detail-variant">
                                                <div class="ec-variant-main">
                                                    <b>Talle {{ $talle }}</b>
                                                    <span>{{ $opcion->art_codigo }}</span>
                                                    <small>{{ number_format($opcion->disponible_pedir, 0, ',', '.') }} disponibles para pedido</small>
                                                </div>
                                                @can('pedido_compras create')
                                                    <div class="ec-variant-order">
                                                        <label for="ec-qty-{{ $producto->codigo_base }}-{{ $loop->index }}">Cantidad</label>
                                                        <input id="ec-qty-{{ $producto->codigo_base }}-{{ $loop->index }}"
                                                            class="ec-variant-quantity" type="number" min="1" max="9999"
                                                            value="1" inputmode="numeric" aria-label="Cantidad para el talle {{ $talle }}"
                                                            {{ (float)$opcion->disponible_pedir <= 0 ? 'disabled' : '' }}>
                                                        <button type="button" class="js-ec-cart-add"
                                                            data-codigo="{{ $opcion->art_codigo }}"
                                                            {{ (float)$opcion->disponible_pedir <= 0 ? 'disabled' : '' }}
                                                            aria-label="Agregar talle {{ $talle }} al carrito">
                                                            <i class="fas fa-plus"></i> Agregar
                                                        </button>
                                                    </div>
                                                @endcan
                                            </div>
                                        @empty
                                            <p>Sin variantes registradas.</p>
                                        @endforelse
                                    </div>
                                    @can('pedido_compras create')
                                        <div class="ec-detail-cart-actions">
                                            <span>Podés agregar varios talles y luego continuar al pedido.</span>
                                            <button type="button" class="js-ec-cart-open">
                                                <i class="fas fa-shopping-cart"></i> Ver carrito
                                                (<span class="js-ec-cart-count">0</span>)
                                            </button>
                                        </div>
                                    @endcan
                                    <div class="ec-detail-total"><span>Total registrado</span><strong>{{ number_format($stockTotal, 0, ',', '.') }} unidades</strong></div>
                                    @if($producto->ot || $producto->fecha_archivo || $producto->valor_v || $producto->valor_c)
                                        <div class="ec-detail-metadata">
                                            @if($producto->ot) <span>OT <strong>{{ $producto->ot }}</strong></span> @endif
                                            @if($producto->fecha_archivo) <span>Fecha de foto <strong>{{ \Carbon\Carbon::parse($producto->fecha_archivo)->format('d/m/Y') }}</strong></span> @endif
                                            @if($producto->valor_v) <span>Archivo <strong>{{ $producto->valor_v }}</strong></span> @endif
                                            @if($producto->valor_c) <span>Archivo <strong>{{ $producto->valor_c }}</strong></span> @endif
                                        </div>
                                    @endif
                                    @can('articulos edit')
                                        <div class="ec-detail-manage">
                                            <h3><i class="fas fa-cog"></i> Administrar producto</h3>
                                            <form method="POST" action="{{ route('ecommerce.linea', $producto->codigo_base) }}">
                                                @csrf @method('PATCH')
                                                <label for="ec-modal-linea-{{ $loop->index }}">Clasificar en línea</label>
                                                <div class="ec-detail-inline">
                                                    <select name="linea_cod" id="ec-modal-linea-{{ $loop->index }}">
                                                        <option value="">Sin clasificar</option>
                                                        @foreach($lineas as $lineaItem)
                                                            <option value="{{ $lineaItem->linea_cod }}"
                                                                {{ (string)$producto->linea_cod === (string)$lineaItem->linea_cod ? 'selected' : '' }}>
                                                                {{ $lineaItem->linea_desc }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit">Guardar</button>
                                                </div>
                                            </form>
                                            @can('articulos destroy')
                                                @if($producto->imagen)
                                                    <form method="POST" action="{{ route('ecommerce.imagen.eliminar', $fotoCodigo) }}"
                                                        class="ec-detail-remove" onsubmit="return confirm('¿Retirar esta imagen? {{ strlen($fotoCodigo) === 9 ? 'Es una imagen general y afectará a todos los colores que la utilizan.' : 'No se eliminará el artículo.' }}');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"><i class="fas fa-trash-alt"></i>
                                                            {{ strlen($fotoCodigo) === 9 ? 'Retirar fotografía general' : 'Retirar fotografía' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </template>
                    @empty
                        <div class="ec-no-results">
                            <i class="fas fa-search"></i>
                            <h3>No encontramos productos</h3>
                            <p>Probá con otras palabras, un código o quitá algunos filtros.</p>
                            <a href="{{ route('ecommerce.index') }}">Ver todos los artículos</a>
                        </div>
                    @endforelse
                </div>

                <div class="ec-pagination-bar">
                    <span>Mostrando {{ $productos->firstItem() ?? 0 }} – {{ $productos->lastItem() ?? 0 }}
                        de {{ number_format($productos->total(), 0, ',', '.') }} productos</span>
                    @if($productos->hasPages()) <div>{{ $productos->links() }}</div> @endif
                </div>
            </section>
        </div>
    </main>
    @can('pedido_compras create')
        <div class="ec-quick-dock" id="ec-quick-dock" aria-live="polite">
            <div class="ec-quick-dock-info">
                <i class="fas fa-shopping-cart"></i>
                <div><strong><span class="js-ec-cart-count">0</span> unidades</strong>
                    <small id="ec-quick-dock-total">Gs. 0</small>
                </div>
            </div>
            <button type="button" class="js-ec-cart-open ec-quick-review">Revisar</button>
            <form method="POST" action="{{ route('ecommerce.carrito.continuar') }}" class="ec-quick-continue-form">
                @csrf
                <button type="submit" class="ec-quick-continue js-ec-cart-continue" disabled>
                    Continuar al pedido <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
        <div id="ec-quick-toast" class="ec-quick-toast" role="status" aria-live="polite" hidden></div>
    @endcan
    <footer class="ec-store-footer">
        <div><span class="ec-footer-logo">G.</span><strong>GOTITAS</strong><small>Catálogo digital interno</small></div>
        <span>Precios y stock según la información registrada en el sistema.</span>
        <a href="{{ route('home') }}">Volver al panel <i class="fas fa-arrow-right"></i></a>
    </footer>
</div>

@can('pedido_compras create')
<div id="ec-cart-overlay" class="ec-cart-overlay" hidden></div>
<aside id="ec-cart-drawer" class="ec-cart-drawer" role="dialog" aria-modal="true"
    aria-label="Carrito para el pedido" aria-hidden="true" tabindex="-1">
    <div class="ec-cart-drawer-head">
        <div><small>TU SELECCIÓN</small><h2><i class="fas fa-shopping-cart"></i> Carrito del pedido</h2></div>
        <button type="button" class="ec-cart-close" id="ec-cart-close" aria-label="Cerrar carrito"><i class="fas fa-times"></i></button>
    </div>
    <div class="ec-cart-drawer-desc">Seleccioná productos y cantidades. El cliente y las condiciones se completan en el siguiente paso.</div>
    <div id="ec-cart-feedback" class="ec-cart-feedback" role="alert" hidden></div>
    <div id="ec-cart-items" class="ec-cart-items">
        <p class="ec-cart-loading">Cargando carrito...</p>
    </div>
    <div class="ec-cart-drawer-foot">
        <div class="ec-cart-total-row"><span>Unidades</span><strong id="ec-cart-units">0</strong></div>
        <div class="ec-cart-total-row ec-cart-amount"><span>Subtotal estimado</span><strong id="ec-cart-total">Gs. 0</strong></div>
        <p>El precio definitivo se calcula en el formulario del pedido, con los descuentos que correspondan.</p>
        <form method="POST" action="{{ route('ecommerce.carrito.continuar') }}">
            @csrf
            <button type="submit" id="ec-cart-continue" class="ec-cart-continue" disabled>
                Continuar y completar cliente <i class="fas fa-arrow-right"></i>
            </button>
        </form>
        <div class="ec-cart-secondary-actions">
            <button type="button" class="ec-cart-keep" id="ec-cart-keep">Seguir comprando</button>
            <button type="button" class="ec-cart-clear" id="ec-cart-clear">Vaciar carrito</button>
        </div>
    </div>
</aside>
@endcan

<dialog id="ec-detail-dialog" class="ec-product-dialog" aria-labelledby="ec-dialog-title">
    <div class="ec-dialog-header">
        <span><i class="fas fa-shopping-bag"></i> DETALLE DEL PRODUCTO</span>
        <button type="button" class="ec-dialog-close" id="ec-dialog-close" aria-label="Cerrar detalle">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div id="ec-dialog-body"></div>
</dialog>

@include('ecommerce.styles')
@include('ecommerce.carrito_style')
@include('ecommerce.scripts')
@include('ecommerce.carrito_script')
@include('ecommerce.carga_rapida_script')
@include('ecommerce.carga_rapida_style')
@endsection
