<table class="table">
    <thead>
        <tr>
            <th>Código de Producto</th>
            <th>Producto</th>
            <th>Precio</th>
        </tr>
    </thead>
    <tbody>
        @forelse($productos as $product)
            <tr onclick="seleccionarProductoPed('{{ $product->art_codigo }}', '{{ $product->art_descripcion }}', {{ $product->prec_vent }})">
                <td>{{ $product->art_codigo }}</td>
                <td>{{ $product->art_descripcion }}</td>
                <td>{{ number_format($product->prec_vent, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">No se encontraron productos.</td>
            </tr>
        @endforelse
    </tbody>
</table>
