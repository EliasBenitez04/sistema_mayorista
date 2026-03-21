<!-- Modal Buscar Productos Pedidos -->
<div class="modal fade" id="productSearchModalPed" tabindex="-1" role="dialog" aria-labelledby="productSearchModalPedLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productSearchModalPedLabel">Buscar Producto (Pedidos)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="productSearchQueryPed" class="form-control" placeholder="Buscar...">
                <br>
                <div id="modalResultsPed">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos as $product)
                                <tr
                                    onclick="seleccionarProductoPed('{{ $product->art_codigo }}', '{{ $product->art_descripcion }}', '{{ $product->cantidad }}')">
                                    <td>{{ $product->art_codigo }}</td>
                                    <td>{{ $product->art_descripcion }}</td>
                                    <td>{{ $product->cantidad }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No se encontraron productos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page_scripts')
    <script>
        document.getElementById('productSearchQueryPed').addEventListener('keyup', function() {
            let query = this.value;
            fetch('{{ url('buscar-productos-ped') }}?query=' + query + '&cod_suc=' + $("#cod_suc").val())
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalResultsPed').innerHTML = html;
                });
        });

        function seleccionarProductoPed(codigo, producto, stock) {
            let tabla = document.getElementById('selectedProducts');
            if (!tabla) return;

            let filas = tabla.getElementsByTagName('tr');
            for (let i = 0; i < filas.length; i++) {
                let codigoExistente = filas[i].querySelector('input[name="codigo[]"]').value;
                if (codigoExistente === codigo) {
                    alert('El producto ya fue agregado.');
                    return;
                }
            }

            let row = document.createElement('tr');
            row.innerHTML = `
            <td class="text-center">
                <input type="text" name="codigo[]" class="form-control text-center" value="${codigo}" readonly>
            </td>
            <td>
                <input type="text" name="producto[]" class="form-control" value="${producto}" readonly>
            </td>
            <td>
                <input type="number" name="cantidad[]" class="form-control text-center" value="1" min="1" max="${stock}">
            </td>
            <td class="text-center">
                <input type="text" name="stock[]" class="form-control text-center" value="${stock}" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger" onclick="borrarPed(this)">
                    <i class="far fa-trash-alt"></i>
                </button>
            </td>
        `;
            tabla.appendChild(row);

            $('#productSearchModalPed').modal('hide');
        }

        function borrarPed(button) {
            let row = button.closest('tr');
            row.remove();
        }
    </script>
@endpush
