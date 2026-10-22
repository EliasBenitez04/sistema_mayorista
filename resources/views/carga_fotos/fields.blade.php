<div class="row g-3">

    <!-- Fecha de Recepción -->
    <div class="form-group col-md-6">
        {!! Form::label('fot_fecha', 'Fecha de Recepción', ['class' => 'form-label fw-semibold']) !!}
        {!! Form::date('fot_fecha', old('fot_fecha', isset($foto) && $foto->fot_fecha ? \Carbon\Carbon::parse($foto->fot_fecha)->format('Y-m-d') : now()->format('Y-m-d')), [
            'class' => 'form-control',
            'required',
            'disabled' => true,
            'style' => 'background-color: #f5f5f5; cursor: not-allowed;',
        ]) !!}
    </div>

    <!-- Fot OT -->
    <div class="form-group col-md-6">
        {!! Form::label('fot_ot', 'OT', ['class' => 'form-label fw-semibold']) !!}
        {!! Form::text('fot_ot', old('fot_ot') ?? ($foto->fot_ot ?? ''), [
            'class' => 'form-control persist',
            'required',
            'minlength' => 5,
            'maxlength' => 5,
            'pattern' => '[0-9]{5,5}',
            'title' => 'Ingrese solo números, máximo 5 dígitos',
            'placeholder' => 'Ej: 29120',
            'data-key' => 'fot_ot',
        ]) !!}
    </div>

    <!-- Fot Costo -->
    <div class="form-group col-md-6">
        {!! Form::label('fot_costo', 'Costo', ['class' => 'form-label fw-semibold']) !!}
        {!! Form::number('fot_costo', old('fot_costo') ?? ($foto->fot_costo ?? ''), [
            'class' => 'form-control persist',
            'required',
            'min' => 0,
            'placeholder' => 'Ej: 8912',
            'data-key' => 'fot_costo',
        ]) !!}
    </div>

    <!-- Fot Venta -->
    <div class="form-group col-md-6">
        {!! Form::label('fot_venta', 'Venta', ['class' => 'form-label fw-semibold']) !!}
        {!! Form::number('fot_venta', old('fot_venta') ?? ($foto->fot_venta ?? ''), [
            'class' => 'form-control persist',
            'required',
            'min' => 0,
            'placeholder' => 'Ej: 25000',
            'data-key' => 'fot_venta',
        ]) !!}
    </div>

    <!-- Fot Descripción -->
    <div class="form-group col-md-6">
        {!! Form::label('fot_desc', 'Descripción', ['class' => 'form-label fw-semibold']) !!}
        {!! Form::text('fot_desc', old('fot_desc') ?? ($foto->fot_desc ?? ''), [
            'class' => 'form-control persist',
            'required',
            'maxlength' => 120,
            'placeholder' => 'Descripción Del Producto',
            'data-key' => 'fot_desc',
        ]) !!}
    </div>

    <!-- Línea (FK) -->
    <div class="form-group col-md-6">
        {!! Form::label('linea_cod', 'Línea', ['class' => 'form-label fw-semibold']) !!}
        {!! Form::select(
            'linea_cod',
            $lineas->pluck('linea_desc', 'linea_cod'), // opciones
            old('linea_cod') ?? ($foto->linea_cod ?? null), // valor seleccionado
            [
                'class' => 'form-control select2 persist',
                'placeholder' => 'Seleccione Línea',
                'required',
                'data-key' => 'linea_cod',
                'id' => 'linea_cod', // agregamos un id para JS
            ],
        ) !!}
    </div>

    <!-- Imagen -->
    <div class="form-group col-md-6">
        {!! Form::label('fot_img', 'Imagen', ['class' => 'form-label fw-semibold']) !!}
        {!! Form::file('fot_img', [
            'class' => 'form-control',
            'required' => !isset($foto),
            'accept' => 'image/*',
            'id' => 'fot_img_input',
            'data-current' => isset($foto) ? $foto->foto_url : '',
        ]) !!}

        <div class="mt-2 text-left" id="imagePreview">
            @if (isset($foto) && $foto->foto_disponible)
                <img src="{{ $foto->foto_url }}" alt="Imagen"
                    style="width: 120px; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.15); display:block; margin-bottom:5px;">
                <small class="d-block text-muted">Imagen guardada. Seleccioná otra para reemplazarla.</small>
            @endif
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('fot_img_input');
    const preview = document.getElementById('imagePreview');
    if (!fileInput || !preview) return;
    const initialHtml = preview.innerHTML;
    fileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) { preview.innerHTML = initialHtml; return; }
        if (!file.type.startsWith('image/')) { preview.innerHTML = ''; return; }
        const objectUrl = URL.createObjectURL(file);
        preview.innerHTML = '';
        const img = document.createElement('img');
        img.src = objectUrl;
        img.alt = 'Vista previa de la nueva fotografía';
        img.style.cssText = 'width:180px;max-height:200px;object-fit:contain;border-radius:10px;border:1px solid #dce5ef';
        img.onload = function () { URL.revokeObjectURL(objectUrl); };
        preview.appendChild(img);
        const message = document.createElement('small');
        message.className = 'd-block text-muted mt-1';
        message.textContent = 'Vista previa de la imagen seleccionada';
        preview.appendChild(message);
    });
});
</script>
