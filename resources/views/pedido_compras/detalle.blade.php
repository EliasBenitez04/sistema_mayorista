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
        <div class="row">
            <!-- Botón para abrir el modal -->
            <div class="col-12 col-sm-12">
                <button type="button" class="btn btn-primary" id="buscar" style="float: right" data-toggle="modal"
                    data-target="#productSearchModalPed">
                    <i class="fa fa-search"> </i> Buscar Productos
                </button>
                {{-- <a href="{{ route('articulos.create', ['return_to' => 'pedido_compras']) }}" class="btn btn-success">
                    <i class="fa fa-plus"> </i> Nuevo Artículo
                </a> --}}
            </div>

            <div class="table-responsive">
                <br>
                <table class="table item-table">
                    <thead>
                        <tr>
                            <th style="width:15%;" class="text-center">C&oacute;digo</th>
                            <th style="width:60%;min-width:240px;">Producto</th>
                            <th style="width:15%;" class="text-center">Cantidad</th>
                            <th style="width:10%;" class="text-center">Acciones</th>
                            {{-- <th style="width:10%;" class="text-center">Prec. Unit</th>
                            <th style="width:10%;">Subtotal</th> --}}
                        </tr>
                    </thead>
                    <tbody id="selectedProducts">
                        @foreach ($detalles as $value)
                            <tr>
                                <td class="text-center">
                                    <input class="form-control text-center" type="text" name="codigo[]" readonly
                                        style="text-align: center" value="{{ $value->art_codigo }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="producto[]" readonly
                                        placeholder="Buscar productos" value="{{ $value->art_descripcion }}">
                                </td>

                                <td class="text-center">
                                    <input class="form-control text-center" type="number" min="1"
                                        name="cantidad[]" value="{{ $value->det_cantidad }}">
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-danger" onclick="borrarPed(this)">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </td>

                                <input type="hidden" name="id_det_pedido[]" value="{{ $value->id_det_pedido }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
