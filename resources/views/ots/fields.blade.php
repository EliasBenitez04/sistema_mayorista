<!-- Nro Ot Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nro_ot', 'Nro Ot:') !!}
    {!! Form::number('nro_ot', null, ['class' => 'form-control']) !!}
</div>

<!-- Codigo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('codigo', 'Codigo:') !!}
    {!! Form::text('codigo', null, ['class' => 'form-control']) !!}
</div>

<!-- Descripcion Field -->
<div class="form-group col-sm-6">
    {!! Form::label('descripcion', 'Descripcion:') !!}
    {!! Form::text('descripcion', null, ['class' => 'form-control']) !!}
</div>

<!-- Cantidad Orden Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cantidad_orden', 'Cantidad Orden:') !!}
    {!! Form::number('cantidad_orden', null, ['class' => 'form-control']) !!}
</div>