<!-- Pedido Fecha Field -->
<div class="form-group col-md-4">
    {!! Form::label('nro_pedido', 'N° Pedido:') !!}
    {!! Form::text('nro_pedido', $pedido->nro_pedido ?? $nroPedidoPreview, [
        'class' => 'form-control',
        'readonly' => true,
    ]) !!}
</div>

<div class="form-group col-sm-4">
    {!! Form::label('ped_fecha', 'Fecha:') !!}
    {!! Form::date('ped_fecha', \Carbon\Carbon::now()->format('Y-m-d'), [
        'class' => 'form-control',
        'id' => 'ped_fecha',
    ]) !!}
</div>

<div class="form-group col-sm-4">
    {!! Form::label('user_id', 'Usuario:') !!}
    {!! Form::text('user_id', Auth::user()->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
</div>

<!-- Cod Suc Field -->
<div class="form-group col-sm-4">
    {!! Form::label('cod_suc', 'Sucursal:') !!}
    {!! Form::select('cod_suc', $sucursal, Auth::user()->cod_suc, [
        'class' => 'form-control',
        'id' => 'cod_suc',
        'disabled' => true,
    ]) !!}
    {!! Form::hidden('cod_suc', Auth::user()->cod_suc) !!}
</div>

<div class="form-group col-md-4">
    {!! Form::label('id_cliente', 'Cliente') !!}

    <div class="input-group input-group-sm">
        <div class="input-group-prepend" style="flex:1;">
            {!! Form::select('id_cliente', $clientes, $pedido->id_cliente ?? null, [
                'class' => 'form-control select2',
                'placeholder' => 'Seleccione un cliente',
                'required',
                'style' => 'width:100%;',
            ]) !!}
        </div>
    </div>
</div>

<div class="form-group col-sm-4">
    {!! Form::label('aplica_descuento', '¿Aplicar Descuento?', ['class' => 'font-weight-bold d-block mb-2']) !!}
    <div class="d-flex align-items-center"">
        <div class="custom-control custom-radio mt-2">
            {!! Form::radio('aplica_descuento', 'SI', false, ['id' => 'descuento_si', 'class' => 'custom-control-input']) !!}
            <label class="custom-control-label" for="descuento_si">Sí</label>
        </div>
        <div class="custom-control custom-radio mt-2 ml-4">
            {!! Form::radio('aplica_descuento', 'NO', true, ['id' => 'descuento_no', 'class' => 'custom-control-input']) !!}
            <label class="custom-control-label" for="descuento_no">No</label>
        </div>
    </div>
</div>

<div class="form-group col-sm-4" id="div-descuento" style="display: none;">
    {!! Form::label('descuento', 'Descuento (%)') !!}
    {!! Form::number('descuento', null, [
        'class' => 'form-control',
        'min' => 0,
        'max' => 100,
        'step' => '0.01',
        'id' => 'descuento_input',
    ]) !!}
</div>

<div class="form-group col-sm-4">
    {!! Form::label('condicion', 'Condición Pedido:') !!}
    {!! Form::select('condicion', $condicion, null, [
        'class' => 'form-control',
        'id' => 'condicion',
        'onchange' => 'condicion(this)',
    ]) !!}
</div>

<div class="form-group col-sm-2" id="div-intervalo" style="display: none">
    {!! Form::label('intervalo', 'Intervalo (días):') !!}
    {!! Form::number('intervalo', null, [
        'class' => 'form-control',
        'min' => 1,
        'id' => 'intervalo',
    ]) !!}
</div>

<div class="form-group col-sm-2" id="div-cantcuotas" style="display: none">
    {!! Form::label('cant_cuotas', 'Cantidad Cuotas:') !!}
    {!! Form::number('cant_cuotas', null, [
        'class' => 'form-control',
        'min' => 1,
        'id' => 'cant_cuotas',
    ]) !!}
</div>

<!-- DETALLE COMPRAS -->
<div class="form-group col-sm-12">
    <hr>
</div>

<div class="form-group col-sm-12">
    @include('pedido_compras.detalle')
</div>

@include('pedido_compras.modal_producto')

{{-- <div class="form-group col-sm-6">
    {!! Form::label('ped_total', 'Total General:') !!}
    {!! Form::text('ped_total', isset($pedido_compras) ? number_format($pedido_compras->ped_total, 0, ',', '.') : null, [
        'class' => 'form-control',
        'readonly' => true,
        'id' => 'ped_total',
    ]) !!}
</div> --}}



@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            console.log("prueba de script::::::");

            // Evitar submit con Enter
            $("form").keypress(function(e) {
                if (e.which == 13) {
                    return false;
                }
            });

            // Llenar modal con AJAX al abrir
            $('#productSearchModal').on('show.bs.modal', function() {
                let cod_suc = $("#cod_suc").val();
                fetch('{{ url('buscar-productos-ped') }}?cod_suc=' + cod_suc)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('modalResults').innerHTML = html;
                    });
            });

            // Buscar productos al escribir en el input
            $('#productSearchQuery').on('keyup', function() {
                let query = $(this).val();
                let cod_suc = $("#cod_suc").val();
                fetch('{{ url('buscar-productos-ped') }}?query=' + query + '&cod_suc=' + cod_suc)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('modalResults').innerHTML = html;
                    });
            });
        });

        // Función para agregar producto a la tabla de pedidos
        function seleccionarProductoPed(codigo, producto) {
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

            // Crear fila solo con código, producto y cantidad
            let row = document.createElement('tr');
            row.innerHTML = `
        <td class="text-center">
            <input type="text" name="codigo[]" class="form-control text-center" value="${codigo}" readonly>
        </td>
        <td>
            <input type="text" name="producto[]" class="form-control" value="${producto}" readonly>
        </td>
        <td class="text-center">
            <input type="number" name="cantidad[]" class="form-control text-center" value="1" min="1">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger" onclick="borrarFila(this)">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
        <input type="hidden" name="id_det_pedido[]" value="">
    `;
            tabla.appendChild(row);

            // Cerrar modal
            $('#productSearchModalPed').modal('hide');
        }

        // Función para borrar cualquier fila de productos (existente o nueva)
        function borrarFila(btn) {
            let fila = btn.closest('tr');
            if (fila) fila.remove();
        }

        // Función para borrar fila de la tabla
        function borrarFila(button) {
            let row = button.closest('tr');
            row.remove();
        }

        $(document).ready(function() {

            // ejecutar al cargar la página
            toggleCondicion();

            // ejecutar cuando cambie el select
            $("#condicion").change(function() {
                toggleCondicion();
            });

        });

        function toggleCondicion() {

            let valor = $("#condicion").val();

            if (valor === "CREDITO") {
                $("#div-intervalo").show();
                $("#div-cantcuotas").show();

                $("#intervalo").prop("required", true);
                $("#cant_cuotas").prop("required", true);
            } else {
                $("#div-intervalo").hide();
                $("#div-cantcuotas").hide();

                $("#intervalo").prop("required", false).val("");
                $("#cant_cuotas").prop("required", false).val("");
            }

        }

        function toggleDescuento() {
            if ($('#descuento_si').is(':checked')) {
                $('#div-descuento').show();
                $('#descuento_input').prop('required', true).val(0);
            } else {
                $('#div-descuento').hide();
                $('#descuento_input').prop('required', false).val(0);
            }
        }

        $(document).ready(function() {
            toggleDescuento();

            $('#descuento_si, #descuento_no').change(function() {
                toggleDescuento();
            });
        });
    </script>
@endpush
