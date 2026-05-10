<!-- Modal Buscar Productos Pedidos -->
<div class="modal fade" id="productSearchModalPed" tabindex="-1" role="dialog" aria-labelledby="productSearchModalPedLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">

        <div class="modal-content shadow-lg border-0 rounded-lg">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">

                <div>
                    <h5 class="modal-title font-weight-bold mb-0" id="productSearchModalPedLabel">
                        <i class="fas fa-search mr-2"></i>
                        Buscar Productos
                    </h5>

                    <small class="text-light">
                        Seleccione un producto para agregar al pedido
                    </small>
                </div>

                <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Cerrar">

                    <span aria-hidden="true">&times;</span>

                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body bg-light">

                <div class="row">

                    <!-- IZQUIERDA -->
                    <div class="col-md-8">

                        <!-- BUSCADOR -->
                        <div class="card shadow-sm border-0 mb-3">

                            <div class="card-body p-3">

                                <div class="input-group input-group-lg">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0">
                                            <i class="fas fa-search text-primary"></i>
                                        </span>
                                    </div>

                                    <input type="text" id="productSearchQueryPed" class="form-control border-left-0"
                                        placeholder="Buscar por código o descripción...">

                                </div>

                            </div>

                        </div>

                        <!-- TABLA -->
                        <div class="card shadow-sm border-0">

                            <div class="card-body p-0">

                                <div id="modalResultsPed" class="table-responsive">

                                    <table class="table table-hover table-striped mb-0">

                                        <thead class="bg-dark text-white">

                                        </thead>

                                        <tbody>
                                            @include('pedido_compras.buscar_producto')
                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- DERECHA -->
                    <div class="col-md-4">

                        <div class="card shadow border-0">

                            <div class="card-header bg-primary text-white">

                                <h5 class="mb-0">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Resumen
                                </h5>

                            </div>

                            <div class="card-body">

                                <!-- CANTIDAD -->
                                <div class="info-box mb-3">

                                    <span class="info-box-icon bg-info elevation-1">
                                        <i class="fas fa-boxes"></i>
                                    </span>

                                    <div class="info-box-content">

                                        <span class="info-box-text">
                                            Cantidad Productos
                                        </span>

                                        <span class="info-box-number" id="modalCantidadProductos">
                                            0
                                        </span>

                                    </div>

                                </div>

                                <!-- TOTAL -->
                                <div class="info-box">

                                    <span class="info-box-icon bg-success elevation-1">
                                        <i class="fas fa-dollar-sign"></i>
                                    </span>

                                    <div class="info-box-content">

                                        <span class="info-box-text">
                                            Total Pedido
                                        </span>

                                        <span class="info-box-number" id="modalTotalPedido">
                                            0
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <style>
                #productSearchModalPed .modal-content {
                    border-radius: 14px;
                    overflow: hidden;
                }

                #productSearchModalPed .table td,
                #productSearchModalPed .table th {
                    vertical-align: middle;
                    font-size: 14px;
                }

                #productSearchModalPed .table tbody tr {
                    transition: all .15s ease;
                }

                #productSearchModalPed .table tbody tr:hover {
                    transform: scale(1.002);
                    background: #eef5ff;
                }

                #productSearchQueryPed:focus {
                    box-shadow: none;
                    border-color: #80bdff;
                }

                #modalResultsPed {
                    max-height: 550px;
                    overflow-y: auto;
                }
            </style>

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
