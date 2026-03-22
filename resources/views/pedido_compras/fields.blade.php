<!-- Pedido Fecha Field -->
<div class="form-group col-md-4">
    {!! Form::label('nro_pedido', 'N° Pedido:') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
        </div>
        {!! Form::text('nro_pedido', $pedido->nro_pedido ?? $nroPedidoPreview, [
            'class' => 'form-control',
            'readonly' => true,
        ]) !!}
    </div>
</div>

<div class="form-group col-sm-4">
    {!! Form::label('ped_fecha', 'Fecha:') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
        </div>
        {!! Form::date('ped_fecha', \Carbon\Carbon::now()->format('Y-m-d'), [
            'class' => 'form-control',
            'id' => 'ped_fecha',
        ]) !!}
    </div>
</div>

<div class="form-group col-sm-4">
    {!! Form::label('user_id', 'Usuario:') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
        </div>
        {!! Form::text('user_id', Auth::user()->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
    </div>
</div>

<!-- Cod Suc Field -->
<div class="form-group col-sm-4">
    {!! Form::label('cod_suc', 'Sucursal:') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-store"></i></span>
        </div>
        {!! Form::select('cod_suc', $sucursal, Auth::user()->cod_suc, [
            'class' => 'form-control',
            'id' => 'cod_suc',
            'disabled' => true,
        ]) !!}
    </div>
    {!! Form::hidden('cod_suc', Auth::user()->cod_suc) !!}
</div>

<div class="form-group col-md-4">
    {!! Form::label('id_cliente', 'Cliente') !!}

    <div class="input-group input-group-sm">

        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
        </div>

        <div style="flex:1;">
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

    <div class="d-flex align-items-center">

        <div class="custom-control custom-radio mt-2">

            {!! Form::radio('aplica_descuento', 'SI', false, [
                'id' => 'descuento_si',
                'class' => 'custom-control-input',
            ]) !!}

            <label class="custom-control-label" for="descuento_si">
                Sí
            </label>

        </div>

        <div class="custom-control custom-radio mt-2 ml-4">

            {!! Form::radio('aplica_descuento', 'NO', true, [
                'id' => 'descuento_no',
                'class' => 'custom-control-input',
            ]) !!}

            <label class="custom-control-label" for="descuento_no">
                No
            </label>

        </div>

    </div>
</div>

<div class="form-group col-sm-4" id="div-descuento" style="display:none;">

    {!! Form::label('descuento', 'Descuento (%)') !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-percent"></i></span>
        </div>

        {!! Form::number('descuento', null, [
            'class' => 'form-control',
            'min' => 0,
            'max' => 100,
            'step' => '0.01',
            'id' => 'descuento_input',
        ]) !!}
    </div>

</div>

<div class="form-group col-sm-4">

    {!! Form::label('condicion', 'Condición Pedido:') !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
        </div>

        {!! Form::select('condicion', $condicion, null, [
            'class' => 'form-control',
            'id' => 'condicion',
        ]) !!}
    </div>

</div>

<div class="form-group col-sm-2" id="div-intervalo" style="display:none;">

    {!! Form::label('intervalo', 'Intervalo (días):') !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-clock"></i></span>
        </div>

        {!! Form::number('intervalo', null, [
            'class' => 'form-control',
            'min' => 1,
            'id' => 'intervalo',
        ]) !!}
    </div>

</div>

<div class="form-group col-sm-2" id="div-cantcuotas" style="display:none;">

    {!! Form::label('cant_cuotas', 'Cantidad Cuotas:') !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
        </div>

        {!! Form::number('cant_cuotas', null, [
            'class' => 'form-control',
            'min' => 1,
            'id' => 'cant_cuotas',
        ]) !!}
    </div>

</div>

<!-- DETALLE COMPRAS -->
<div class="form-group col-sm-12">
    <hr>
</div>

<div class="form-group col-sm-12">
    @include('pedido_compras.detalle')
</div>

<!-- Compra Total Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ped_total', 'Total:') !!}
    {!! Form::text('ped_total', isset($pedido) ? number_format($pedido->ped_total, 0, ',', '.') : null, [
        'class' => 'form-control',
        'readonly' => 'readonly',
    ]) !!}
</div>

@include('pedido_compras.modal_producto')

<!-- Agregar SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            // Evitar submit con Enter
            $("form").keypress(function(e) {
                if (e.which == 13) return false;
            });

            // Abrir modal y cargar productos
            $('#productSearchModalPed').on('show.bs.modal', function() {
                let cod_suc = $("#cod_suc").val();
                fetch('{{ url('buscar-productos-ped') }}?cod_suc=' + cod_suc)
                    .then(response => response.text())
                    .then(html => document.getElementById('modalResultsPed').innerHTML = html);
            });

            // Buscar productos
            $('#productSearchQueryPed').on('keyup', function() {
                let query = $(this).val();
                let cod_suc = $("#cod_suc").val();
                fetch('{{ url('buscar-productos-ped') }}?query=' + query + '&cod_suc=' + cod_suc)
                    .then(response => response.text())
                    .then(html => document.getElementById('modalResultsPed').innerHTML = html);
            });

            // Detectar cambios en descuento y condición
            $('#descuento_si, #descuento_no').on('change', toggleDescuento);
            $('#condicion').on('change', toggleCondicion);
            $('#descuento_input').on('keyup change', calcularTotal);

            // Inicializar al cargar
            toggleDescuento();
            toggleCondicion();
        });

        // ================= FUNCIONES =================

        function formatearMiles(numero) {
            return parseFloat(numero).toLocaleString('es-PY', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function limpiarMiles(valor) {
            return parseFloat(valor.toString().replace(/\./g, '').replace(',', '.')) || 0;
        }


        // Seleccionar producto
        function seleccionarProductoPed(codigo, producto, precio) {
            let tabla = document.getElementById('selectedProducts');
            if (!tabla) return;

            // Evitar duplicados con SweetAlert
            for (let fila of tabla.getElementsByTagName('tr')) {
                if (fila.querySelector('input[name="codigo[]"]').value === codigo) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'El producto ya fue agregado.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
            }

            let precioFormateado = formatearMiles(precio);

            let row = document.createElement('tr');
            row.innerHTML = `
        <td class="text-center">
            <input type="text" name="codigo[]" class="form-control text-center" value="${codigo}" readonly>
        </td>

        <td>
            <input type="text" name="producto[]" class="form-control" value="${producto}" readonly>
        </td>

        <td class="text-center">
            <input type="number" name="cantidad[]" class="form-control text-center cantidad" value="1" min="1">
        </td>

        <td class="text-center">
            <input type="text" name="precio[]" class="form-control text-center precio" value="${precioFormateado}">
        </td>

        <td class="text-center">
            <input type="text" name="subtotal[]" class="form-control text-center subtotal" value="${precioFormateado}" readonly>
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-danger" onclick="borrarFila(this)">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
    `;

            tabla.appendChild(row);

            $('#productSearchModalPed').modal('hide');

            calcularTotal();
        }

        document.addEventListener('input', function(e) {

            if (e.target.classList.contains('cantidad') || e.target.classList.contains('precio')) {

                let fila = e.target.closest('tr');

                let cantidadInput = fila.querySelector('.cantidad');
                let precioInput = fila.querySelector('.precio');
                let subtotalInput = fila.querySelector('.subtotal');

                let cantidad = limpiarMiles(cantidadInput.value);
                let precio = limpiarMiles(precioInput.value);

                if (!cantidad) cantidad = 0;
                if (!precio) precio = 0;

                let subtotal = cantidad * precio;

                // FORZAR FORMATO
                subtotalInput.value = formatearMiles(subtotal);

                // volver a formatear precio también
                precioInput.value = formatearMiles(precio);

                calcularTotal();
            }

        });

        // Borrar fila
        function borrarFila(btn) {
            let fila = btn.closest('tr');
            if (fila) fila.remove();
            calcularTotal();
        }

        // Recalcular subtotal al cambiar cantidad o precio
        $(document).on("keyup change", ".cantidad, .precio", function() {

            let fila = $(this).closest("tr");

            let cant = limpiarMiles(fila.find(".cantidad").val());
            let precio = limpiarMiles(fila.find(".precio").val());

            if (!cant) cant = 0;
            if (!precio) precio = 0;

            let subtotal = cant * precio;

            fila.find(".subtotal").val(formatearMiles(subtotal));

            calcularTotal();
        });
        // Calcular total general
        function calcularTotal() {

            let total = 0;

            $(".subtotal").each(function() {

                total += limpiarMiles($(this).val());

            });

            // aplicar descuento
            if ($('#descuento_si').is(':checked')) {
                let desc = parseFloat($("#descuento_input").val()) || 0;
                if (desc > 0) {
                    total -= total * (desc / 100);
                }
            }

            $("#ped_total").val(formatearMiles(total));

        }
        // Toggle descuento
        function toggleDescuento() {
            if ($('#descuento_si').is(':checked')) {
                $('#div-descuento').show();
                $('#descuento_input').prop('required', true);
            } else {
                $('#div-descuento').hide();
                $('#descuento_input').prop('required', false).val(0);
            }
            calcularTotal();
        }

        // Toggle condición CREDITO
        function toggleCondicion() {
            let valor = $("#condicion").val();
            if (valor === "CREDITO") {
                $("#div-intervalo, #div-cantcuotas").show();
                $("#intervalo, #cant_cuotas").prop('required', true);
            } else {
                $("#div-intervalo, #div-cantcuotas").hide();
                $("#intervalo, #cant_cuotas").prop('required', false).val('');
            }
        }
    </script>
@endpush
