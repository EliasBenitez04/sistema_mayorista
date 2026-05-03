<div class="row">

    <!-- Nro de CI -->
    <div class="col-md-12 mb-3">
        <label for="cli_ci" class="form-label fw-bold">Nro de CI / R.U.C.</label>

        <div class="input-group shadow-sm">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-id-card"></i>
                </span>
            </div>

            <input type="text" name="cli_ci" class="form-control" required pattern="\d+(-\d+)?" maxlength="10"
                placeholder="Ej: 1234567-8" title="Ingrese solo números o números con guion"
                value="{{ old('cli_ci', $cliente->cli_ci ?? '') }}">
        </div>

    </div>


    <!-- Nombre -->
    <div class="col-md-6 mb-3">
        <label for="cli_nombre" class="form-label fw-bold">Nombres o Razón Social:</label>

        <div class="input-group shadow-sm">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-user"></i>
                </span>
            </div>

            <input type="text" name="cli_nombre" id="cli_nombre" class="form-control" required
                placeholder="Ingrese nombres o razón social"
                value="{{ old('cli_nombre', $cliente->cli_nombre ?? '') }}">
        </div>

    </div>


    <!-- Apellido -->
    <div class="col-md-6 mb-3">
        <label for="cli_apellido" class="form-label fw-bold">Apellidos:</label>

        <div class="input-group shadow-sm">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-user-tag"></i>
                </span>
            </div>

            <input type="text" name="cli_apellido" id="cli_apellido" class="form-control"
                placeholder="Ingrese apellidos" value="{{ old('cli_apellido', $cliente->cli_apellido ?? '') }}">
        </div>

    </div>


    <!-- Dirección -->
    <div class="col-md-6 mb-3">
        <label for="cli_direccion" class="form-label fw-bold">Dirección:</label>

        <div class="input-group shadow-sm">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
            </div>

            <input type="text" name="cli_direccion" id="cli_direccion" class="form-control"
                placeholder="Ingrese dirección" value="{{ old('cli_direccion', $cliente->cli_direccion ?? '') }}">
        </div>

    </div>


    <!-- Teléfono -->
    <div class="col-md-6 mb-3">
        <label for="cli_telefono" class="form-label fw-bold">Teléfono:</label>

        <div class="input-group shadow-sm">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-phone"></i>
                </span>
            </div>

            <input type="text" name="cli_telefono" id="cli_telefono" class="form-control"
                placeholder="Ej: 0981 123456" value="{{ old('cli_telefono', $cliente->cli_telefono ?? '') }}">
        </div>

    </div>


    <!-- Departamento -->
    <div class="col-md-6 mb-3">
        <label for="departamento_id" class="form-label fw-bold">Departamento:</label>

        <div class="input-group shadow-sm">

            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-map"></i>
                </span>
            </div>

            {!! Form::select('id_departamento', $departamento, null, [
                'class' => 'form-control select2',
                'placeholder' => 'Seleccione un departamento',
                'id' => 'departamento_id',
            ]) !!}

        </div>

    </div>


    <!-- Ciudad -->
    <div class="col-md-6 mb-3">
        <label for="ciudad_id" class="form-label fw-bold">Ciudad:</label>

        <div class="input-group shadow-sm">

            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-city"></i>
                </span>
            </div>

            {!! Form::select('id_ciudad', $ciudad, null, [
                'class' => 'form-control select2',
                'placeholder' => 'Seleccione una ciudad',
                'id' => 'ciudad_id',
            ]) !!}

        </div>

    </div>

</div>

<script>
    document.querySelector('[name="cli_ci"]').addEventListener('input', function() {
        if (this.value.length > 9) {
            this.value = this.value.slice(0, 9);
        }
    });
</script>
