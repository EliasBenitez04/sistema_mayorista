<!-- Art Descripcion Field -->
<!-- Pantalla de carga -->
<div id="loader"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#00000080; justify-content:center; align-items:center; flex-direction:column; z-index:9999;">
    <div class="spinner-border text-light"></div>
    <div id="percent" style="color:white; font-size:20px; margin-top:10px;">0%</div>
</div>

<div class="form-group col-sm-12">
    {!! Form::label('art_codigo', 'Código Articulo:', ['class' => 'form-label']) !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-barcode"></i>
            </span>
        </div>

        {!! Form::text('art_codigo', null, [
            'class' => 'form-control',
            'autofocus' => 'autofocus',
            'required' => 'required',
            'placeholder' => 'Ingrese el código del producto',
        ]) !!}
    </div>
</div>


<!-- Art Descripcion Field -->
<div class="form-group col-sm-12">
    {!! Form::label('art_descripcion', 'Descripción Articulo:', ['class' => 'form-label']) !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-box-open"></i>
            </span>
        </div>

        {!! Form::text('art_descripcion', null, [
            'class' => 'form-control',
            'autofocus' => 'autofocus',
            'required' => 'required',
            'placeholder' => 'Ingrese la descripción del producto',
        ]) !!}
    </div>
</div>


<!-- Art Precio Field -->
<div class="form-group col-sm-4">
    {!! Form::label('art_precio', 'Precio Costo:', ['class' => 'form-label']) !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-coins"></i>
            </span>
        </div>

        {!! Form::text('art_precio', null, [
            'class' => 'form-control',
            'required' => 'required',
            'onkeyup' => 'format(this)',
            'placeholder' => 'Ingrese el precio del artículo',
        ]) !!}
    </div>
</div>


<!-- Art Precio Venta Field -->
<div class="form-group col-sm-4">
    {!! Form::label('prec_vent', 'Precio Venta:', ['class' => 'form-label']) !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-hand-holding-usd"></i>
            </span>
        </div>

        {!! Form::text('prec_vent', null, [
            'class' => 'form-control',
            'required' => 'required',
            'onkeyup' => 'format(this)',
            'placeholder' => 'Ingrese el precio de venta del artículo',
        ]) !!}
    </div>
</div>


<!-- Art IVA Field -->
<div class="form-group col-sm-4">
    {!! Form::label('art_iva', 'Impuestos:', ['class' => 'form-label']) !!}

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-percent"></i>
            </span>
        </div>

        {!! Form::select('art_iva', $iva, null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione IVA',
            'required' => 'required',
        ]) !!}
    </div>
</div>

<style>
    #loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        z-index: 9999;
    }

    #progress-bar {
        width: 0%;
        height: 20px;
        background: #4caf50;
        transition: width 0.3s;
    }

    #percent {
        margin-top: 10px;
        font-size: 18px;
        font-weight: bold;
    }
</style>

<script>
    const btnImport = document.getElementById('btn-import');
    const fileInput = document.getElementById('file');
    const loader = document.getElementById('loader');
    const percentText = document.getElementById('percent');

    btnImport.addEventListener('click', async () => {

        if (!fileInput.files.length) {
            alert('Seleccione un archivo');
            return;
        }

        loader.style.display = 'flex';
        percentText.innerText = '0%';

        let formData = new FormData();
        formData.append('archivo', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        await fetch("{{ route('articulos.importar') }}", {
            method: 'POST',
            body: formData
        });

        const interval = setInterval(async () => {

            const res = await fetch("{{ route('import.progress') }}");
            const data = await res.json();

            let progress = data.progress ?? 0;

            percentText.innerText = progress + '%';

            if (progress >= 100) {

                clearInterval(interval);

                setTimeout(() => {
                    loader.style.display = 'none';
                    location.reload();
                }, 500);

            }

        }, 500);

    });
</script>
