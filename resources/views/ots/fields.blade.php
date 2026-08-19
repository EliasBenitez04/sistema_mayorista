<!-- CABECERA -->

<div class="form-group col-sm-6">
    {!! Form::label('id_ot', 'Seleccionar OT:') !!}
    {!! Form::select('id_ot', $ots, null, [
        'class' => 'form-control select2',
        'placeholder' => 'Seleccione una OT',
        'id' => 'id_ot_select',
        'required',
    ]) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('nro_ot', 'Nro OT:') !!}
    {!! Form::number('nro_ot', null, [
        'class' => 'form-control',
        'id' => 'nro_ot',
        'readonly',
    ]) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('codigo', 'Código:') !!}
    {!! Form::text('codigo', null, [
        'class' => 'form-control',
        'id' => 'codigo',
        'readonly',
    ]) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('descripcion', 'Descripción:') !!}
    {!! Form::text('descripcion', null, [
        'class' => 'form-control',
        'id' => 'descripcion',
        'readonly',
    ]) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('cantidad_orden', 'Cantidad Orden:') !!}
    {!! Form::number('cantidad_orden', null, [
        'class' => 'form-control',
        'id' => 'cantidad_orden',
        'readonly',
    ]) !!}
</div>


<div class="col-sm-12">
    <hr>
    <h4>
        <b>Detalle de Procesos</b>
    </h4>
</div>

<div class="col-sm-12 mb-3">
    <button type="button" class="btn btn-success" id="agregarProceso">
        Agregar Proceso
    </button>
</div>

<div class="col-sm-12">

    <table class="table table-bordered" id="tablaProcesos">

        <thead>
            <tr>
                <th>Proceso</th>
                <th>Resultado</th>
                <th>Fecha</th>
                <th width="10%">Acción</th>
            </tr>
        </thead>

        <tbody>

            <tr>

                <td>
                    <input type="text" name="proceso[]" class="form-control" value="LOGISTICA - LOGISTICA Y DISTRIBUCION"
                        readonly>
                </td>

                <td>
                    <input type="number" name="resultado[]" class="form-control">
                </td>

                <td>
                    <input type="date" name="fecha_proceso[]" value="{{ date('Y-m-d') }}" class="form-control"
                        required>
                </td>

                <td align="center">
                    <button type="button" class="btn btn-danger eliminar">
                        X
                    </button>
                </td>

            </tr>

        </tbody>

    </table>

</div>
@push('page_scripts')
    <script>
        $(document).ready(function() {

            $('#id_ot_select').select2({
                placeholder: 'Seleccione una OT',
                allowClear: true
            });

            $('#id_ot_select').change(function() {

                let id_ot = $(this).val();

                if (id_ot) {

                    $.ajax({

                        url: '/get-ot-details/' + id_ot,
                        type: 'GET',

                        success: function(data) {

                            $('#nro_ot').val(data.nro_ot);
                            $('#codigo').val(data.codigo);
                            $('#descripcion').val(data.descripcion);
                            $('#cantidad_orden').val(data.cantidad_orden);

                        },

                        error: function() {

                            alert('No se pudo obtener la información de la OT');

                            $('#nro_ot').val('');
                            $('#codigo').val('');
                            $('#descripcion').val('');
                            $('#cantidad_orden').val('');

                        }

                    });

                } else {

                    $('#nro_ot').val('');
                    $('#codigo').val('');
                    $('#descripcion').val('');
                    $('#cantidad_orden').val('');

                }

            });

        });
    </script>
@endpush
