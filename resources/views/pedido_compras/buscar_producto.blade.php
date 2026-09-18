@php
    $productos = $productos ?? collect();
    $mensajeBusqueda = $mensajeBusqueda ?? 'Escriba al menos 4 caracteres para buscar productos.';
@endphp

<table class="table table-hover mb-0 pedido-product-search-table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Producto</th>
            <th class="text-right">Precio</th>
            <th class="text-center">Stock general</th>
            <th class="text-center">Disponible para pedir</th>
        </tr>
    </thead>

    <tbody>
        @forelse($productos as $product)
            @php
                $stockGeneral = (float) ($product->stock_general ?? 0);
                $stockDisponible = (float) ($product->stock_disponible_pedir ?? 0);
                $precioPedido = (float) ($product->prec_vent ?? 0);
                $puedePedir = $stockDisponible > 0;
            @endphp

            <tr
                class="{{ $puedePedir ? 'producto-search-row' : 'producto-search-row producto-search-row-disabled' }}"
                @if ($puedePedir)
                    role="button"
                    tabindex="0"
                    data-codigo="{{ $product->art_codigo }}"
                    data-producto="{{ $product->art_descripcion }}"
                    data-stock="{{ $stockDisponible }}"
                    data-precio="{{ $precioPedido }}"
                    onclick="seleccionarProductoPedDesdeFila(this)"
                    onkeydown="if(event.key === 'Enter'){ this.click(); }"
                @endif>

                <td>
                    <strong class="producto-search-code">{{ $product->art_codigo }}</strong>
                </td>

                <td>
                    <span class="producto-search-description" title="{{ $product->art_descripcion }}">
                        {{ $product->art_descripcion }}
                    </span>
                </td>

                <td class="text-right producto-search-price">
                    {{ number_format($precioPedido, 0, ',', '.') }}
                </td>

                <td class="text-center">
                    <span class="badge badge-info producto-stock-badge">
                        {{ number_format($stockGeneral, 0, ',', '.') }}
                    </span>
                </td>

                <td class="text-center">
                    @if ($puedePedir)
                        <span class="badge badge-success producto-stock-badge">
                            {{ number_format($stockDisponible, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="badge badge-danger producto-stock-badge">
                            Sin stock
                        </span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="producto-search-empty">
                    <div class="producto-search-empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <strong>{{ $mensajeBusqueda }}</strong>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
