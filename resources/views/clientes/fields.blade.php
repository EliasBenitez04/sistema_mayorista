<div class="row">

    <!-- Nro de CI -->
    <div class="col-md-12 mb-3">
        <label for="cli_ci" class="form-label fw-bold">Nro de CI / R.U.C.</label>
        <input type="text" name="cli_ci" class="form-control shadow-sm" required pattern="\d+(-\d+)?"
            title="Ingrese solo números o números con guion" value="{{ old('cli_ci', $cliente->cli_ci ?? '') }}">
    </div>

    <!-- Nombre -->
    <div class="col-md-6 mb-3">
        <label for="cli_nombre" class="form-label fw-bold">Nombres</label>
        <input type="text" name="cli_nombre" id="cli_nombre" class="form-control shadow-sm" required
            value="{{ old('cli_nombre', $cliente->cli_nombre ?? '') }}">
    </div>

    <!-- Apellido -->
    <div class="col-md-6 mb-3">
        <label for="cli_apellido" class="form-label fw-bold">Apellidos</label>
        <input type="text" name="cli_apellido" id="cli_apellido" class="form-control shadow-sm" required
            value="{{ old('cli_apellido', $cliente->cli_apellido ?? '') }}">
    </div>

    <!-- Dirección -->
    <div class="col-md-6 mb-3">
        <label for="cli_direccion" class="form-label fw-bold">Dirección</label>
        <input type="text" name="cli_direccion" id="cli_direccion" class="form-control shadow-sm"
            value="{{ old('cli_direccion', $cliente->cli_direccion ?? '') }}">
    </div>

    <!-- Teléfono -->
    <div class="col-md-6 mb-3">
        <label for="cli_telefono" class="form-label fw-bold">Teléfono</label>
        <input type="text" name="cli_telefono" id="cli_telefono" class="form-control shadow-sm"
            value="{{ old('cli_telefono', $cliente->cli_telefono ?? '') }}">
    </div>

    <!-- Departamento -->
    <div class="col-md-6 mb-3">
        <label for="departamento_id" class="form-label fw-bold">Departamento</label>
        {!! Form::select('id_departamento', $departamento, null, [
            'class' => 'form-control select2 shadow-sm',
            'placeholder' => 'Seleccione...',
            'id' => 'departamento_id',
        ]) !!}
    </div>

    <!-- Ciudad -->
    <div class="col-md-6 mb-3">
        <label for="ciudad_id" class="form-label fw-bold">Ciudad</label>
        {!! Form::select('id_ciudad', $ciudad, null, [
            'class' => 'form-control select2 shadow-sm',
            'placeholder' => 'Seleccione...',
            'id' => 'ciudad_id',
        ]) !!}
    </div>

</div>
