<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Detalle Pedido</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row mb-2">
            <!-- Botón para abrir el modal -->
            <div class="col-12 text-right">
                <button type="button" class="btn btn-primary" id="buscar" data-toggle="modal"
                    data-target="#productSearchModalPed">
                    <i class="fas fa-search"></i> Buscar Productos
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped text-nowrap item-table">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th style="width:15%;">Código</th>
                        <th style="width:50%; min-width:200px;">Producto</th>
                        <th style="width:10%;">Cantidad</th>
                        <th style="width:10%;">Prec. Unit</th>
                        <th style="width:10%;">SubTotal</th>
                        <th style="width:5%;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="selectedProducts">
                    @foreach ($detalles as $value)
                        <tr>

                            <td class="text-center">
                                <input class="form-control text-center" type="text" name="codigo[]" readonly
                                    value="{{ $value->art_codigo }}">
                            </td>

                            <td>
                                <input type="text" class="form-control" name="producto[]" readonly
                                    value="{{ $value->art_descripcion }}">
                            </td>

                            <td class="text-center">
                                <input class="form-control text-center cantidad" type="number" min="1"
                                    name="cantidad[]" value="{{ $value->det_cantidad }}">
                            </td>

                            <td class="text-center">
                                <input type="hidden" class="precio_raw" value="{{ $value->det_precio }}">

                                <input class="form-control text-center" type="text"
                                    value="{{ number_format($value->det_precio, 0, ',', '.') }}" readonly>
                            </td>

                            <td class="text-center">
                                <input class="form-control text-center subtotal" type="text" name="subtotal[]"
                                    readonly data-value="{{ $value->det_subtotal }}"
                                    value="{{ number_format($value->det_subtotal, 0, ',', '.') }}">
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm" onclick="borrarFila(this)">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </td>

                            <input type="hidden" name="id_det_pedido[]" value="{{ $value->id_det_pedido }}">
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="2" class="text-right">TOTAL ARTÍCULOS:</td>
                        <td id="totalCantidad" class="text-center">0</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>