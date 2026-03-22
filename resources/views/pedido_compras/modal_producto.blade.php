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
                        </thead>
                        <tbody>
                            @include('pedido_compras.buscar_producto')
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

        function seleccionarProductoPed(codigo, producto, stock, precio) {
            let tabla = document.getElementById('selectedProducts');
            if (!tabla) return;

            // Evitar duplicados
            let filas = tabla.getElementsByTagName('tr');
            for (let i = 0; i < filas.length; i++) {
                let codigoExistente = filas[i].querySelector('input[name="codigo[]"]').value;
                if (codigoExistente === codigo) {
                    alert('El producto ya fue agregado.');
                    return;
                }
            }

            // Crear fila con código, producto, cantidad, precio y subtotal
            let row = document.createElement('tr');
            row.innerHTML = `
        <td class="text-center">
            <input type="text" name="codigo[]" class="form-control text-center" value="${codigo}" readonly>
        </td>
        <td>
            <input type="text" name="producto[]" class="form-control" value="${producto}" readonly>
        </td>
        <td>
            <input type="number" name="cantidad[]" class="form-control text-center cantidad" value="1" min="1" max="${stock}">
        </td>
        <td class="text-center">
            <input type="number" name="precio[]" class="form-control text-center precio" value="${precio}">
        </td>
        <td class="text-center">
            <input type="text" name="subtotal[]" class="form-control text-center subtotal" value="${precio}" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger" onclick="borrarFila(this)">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
    `;
            tabla.appendChild(row);

            // Cerrar modal
            $('#productSearchModalPed').modal('hide');

            // Recalcular total
            calcularTotal();
        }

        // Función para recalcular subtotal cuando cambian cantidad o precio
        $(document).on("keyup change", ".cantidad, .precio", function() {
            let fila = $(this).closest("tr");
            let cant = parseFloat(fila.find(".cantidad").val()) || 0;
            let precio = parseFloat(fila.find(".precio").val()) || 0;
            let subtotal = cant * precio;
            fila.find(".subtotal").val(subtotal);

            calcularTotal();
        });

        // Función para calcular el total general
        function calcularTotal() {
            let total = 0;
            $(".subtotal").each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $("#ped_total").val(total.toLocaleString('es-PY'));
        }

        // Función para borrar fila
        function borrarFila(btn) {
            let fila = btn.closest('tr');
            if (fila) fila.remove();
            calcularTotal();
        }

        function borrarPed(button) {
            let row = button.closest('tr');
            row.remove();
        }

        function calcularTotal() {

            let total = 0;

            $(".subtotal").each(function() {
                let valor = parseFloat($(this).val()) || 0;
                total += valor;
            });

            $("#ped_total").val(total.toLocaleString('es-PY'));

        }

        $(document).on("keyup change", ".cantidad, .precio", function() {

            let fila = $(this).closest("tr");

            let cant = parseFloat(fila.find(".cantidad").val()) || 0;
            let precio = parseFloat(fila.find(".precio").val()) || 0;

            let subtotal = cant * precio;

            fila.find(".subtotal").val(subtotal);

            calcularTotal();
        });
    </script>
@endpush
