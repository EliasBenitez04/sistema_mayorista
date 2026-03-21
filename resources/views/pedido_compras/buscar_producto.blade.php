<table class="table">
    <thead>
        <tr>
            <th>Código de Producto</th>
            <th>Producto</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        @forelse($productos as $product)
            <tr
                onclick="seleccionarProductoPed('{{ $product->art_codigo }}', '{{ addslashes($product->art_descripcion) }}', '{{ $product->cantidad }}')">
                <td>{{ $product->art_codigo }}</td>
                <td>{{ $product->art_descripcion }}</td>
                <td>{{ $product->cantidad }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">No se encontraron productos.</td>
            </tr>
        @endforelse
    </tbody>
</table>
