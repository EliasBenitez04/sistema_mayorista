@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1><i class="fas fa-cloud-upload-alt mr-2"></i>Importar imágenes al E-commerce</h1>
            <p class="text-muted mb-0 mt-2">Vinculá fotografías con artículos existentes usando el código del nombre del archivo.</p>
        </div>
        <a href="{{ route('ecommerce.index') }}" class="btn btn-outline-primary mt-2">
            <i class="fas fa-arrow-left mr-1"></i>Volver al catálogo
        </a>
    </div>
</section>

<section class="content px-3 pb-4">
    <div class="ec-import-container">
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>No se pudo iniciar la importación.</strong>
                <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @if(session('resultado_importacion'))
            @php($res = session('resultado_importacion'))
            <div class="alert alert-info ec-import-results">
                <strong><i class="fas fa-clipboard-check mr-2"></i>Resultado de la importación</strong>
                <div class="d-flex flex-wrap mt-2" style="gap:18px">
                    <span><strong>{{ $res['nuevas'] }}</strong> nuevas</span>
                    <span><strong>{{ $res['reemplazadas'] }}</strong> reemplazadas</span>
                    <span><strong>{{ $res['omitidas'] }}</strong> omitidas</span>
                </div>
                @if(session('errores_importacion'))
                    <details class="mt-2">
                        <summary>Ver motivos de las imágenes omitidas</summary>
                        <ul class="mt-2 mb-0">
                            @foreach(session('errores_importacion') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card ec-import-card">
                    <div class="card-body">
                        <div class="ec-step"><span>01</span> Elegir imágenes</div>
                        <form method="POST" action="{{ route('ecommerce.importar') }}" enctype="multipart/form-data" id="ec-import-form">
                            @csrf
                            <div class="form-group">
                                <label for="ec-archivos">Imágenes individuales</label>
                                <label class="ec-file-area" for="ec-archivos">
                                    <i class="fas fa-images"></i>
                                    <strong>Seleccionar imágenes JPG, PNG o WEBP</strong>
                                    <span>Hasta 20 por carga · 30 MB por imagen</span>
                                    <input id="ec-archivos" name="archivos[]" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        multiple style="position:absolute;opacity:0;width:1px;height:1px">
                                </label>
                            </div>

                            <div class="ec-divider"><span>O bien</span></div>
                            <div class="form-group">
                                <label for="ec-zip">Paquete ZIP (importación masiva)</label>
                                <input type="file" class="form-control-file" id="ec-zip" name="paquete_zip" accept=".zip,application/zip">
                                <small class="text-muted">Hasta 500 archivos en un ZIP de 200 MB. No es necesario descomprimirlo.</small>
                            </div>

                            <div class="ec-step mt-4"><span>02</span> Clasificar las fotografías</div>
                            <div class="form-group">
                                <label for="ec-linea-import">Línea para las imágenes cargadas</label>
                                <select name="linea_cod" id="ec-linea-import" class="form-control">
                                    <option value="">Automática por OT (si existe), o conservar clasificación</option>
                                    @foreach($lineas as $linea)
                                        <option value="{{ $linea->linea_cod }}" {{ old('linea_cod') == $linea->linea_cod ? 'selected' : '' }}>
                                            {{ $linea->linea_desc }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Si seleccionás una línea, se aplicará a todas las imágenes del lote. Si no, se conserva la clasificación previa o se busca la OT en Carga de Fotos; las restantes quedan sin clasificar.</small>
                            </div>

                            <div id="ec-file-summary" class="ec-file-summary" aria-live="polite">Todavía no seleccionaste imágenes.</div>
                            <div id="ec-file-preview" class="ec-file-preview"></div>
                            <div class="ec-submit-row">
                                <button type="submit" class="btn btn-primary btn-lg" id="ec-import-btn">
                                    <i class="fas fa-upload mr-2"></i>Importar al catálogo
                                </button>
                                <small class="text-muted">La importación no modifica artículos, stock ni precios.</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card ec-import-card ec-help">
                    <div class="card-body">
                        <div class="ec-step"><span><i class="fas fa-magic"></i></span> Lectura automática del nombre</div>
                        <p>Ejemplos reales de nombres admitidos:</p>
                        <div class="ec-example">060615677 V39 C18727 OT28696 25-07-25 NENA REMERA MC COLECCION.jpg</div>
                        <div class="ec-example">060616168 V49 C23142 11-11-25.jpeg</div>
                        <div class="ec-example">060616680 V39 C19959 OT29699 NENA REMERA MC COL.jpg</div>
                        <div class="ec-example">100613678CV V35 C13.388 OT26697 BERMUDA ECO VARÓN 08-10-26.jpg</div>
                        <div class="ec-parsed">
                            <div><span>Código base</span><strong>100613678CV</strong></div>
                            <div><span>Valor V</span><strong>V35</strong></div>
                            <div><span>Valor C</span><strong>C13.388</strong></div>
                            <div><span>OT</span><strong>26697</strong></div>
                            <div><span>Descripción</span><strong>BERMUDA ECO VARÓN</strong></div>
                            <div><span>Fecha del nombre</span><strong>08/10/2026</strong></div>
                        </div>
                        <div class="ec-info-box">
                            <i class="fas fa-info-circle mr-1"></i>
                            El código base identifica todas las variantes de talle del producto. Se muestra el precio real de la base de datos; V y C son referencias del nombre del archivo.
                        </div>
                        <h4 class="mt-4">Reglas de importación</h4>
                        <p>Se aceptan códigos de <strong>9 dígitos</strong> (ej. <strong>060615677</strong>) y códigos con color de <strong>11 caracteres</strong> (ej. <strong>100613678CV</strong>). También se aceptan códigos completos de 13 caracteres.</p>
                        <p>La fecha puede estar antes o después de la descripción. OT, descripción y fecha son opcionales; también se admite <strong>V 89</strong> o un valor numérico sin V. Se puede importar <strong>060615677.png</strong>.</p>
                        <p>Si no seleccionás línea, el importador intenta usar la OT y, en su defecto, el comienzo de la descripción (por ejemplo, NENA).</p>
                        <p>Si la imagen corresponde a un código que todavía no existe en Artículos, se omite y se informa el motivo. Una imagen nueva del mismo código sustituye la anterior sin duplicar el producto.</p>
                        <p class="mb-0">Para ver fotos en el navegador, el proyecto necesita tener habilitado el enlace público de almacenamiento con <code>php artisan storage:link</code>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.ec-import-container{max-width:1500px;margin:0 auto}.ec-import-card{border-radius:18px!important;border:1px solid #dfe8f3!important;box-shadow:0 9px 32px rgba(26,49,77,.06)!important}
.ec-import-card .card-body{padding:26px}.ec-step{display:flex;align-items:center;gap:12px;font-weight:800;font-size:16px;color:#253e62;margin-bottom:20px}
.ec-step span{display:inline-flex;align-items:center;justify-content:center;width:35px;height:35px;border-radius:11px;background:#e8f1ff;color:#2666c1;font-size:12px}
.ec-file-area{position:relative;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:10px;min-height:195px;border:2px dashed #a9c6ed;border-radius:15px;background:#f7faff;cursor:pointer;padding:18px;text-align:center;transition:.2s}
.ec-file-area:hover{background:#edf5ff;border-color:#4d89df}.ec-file-area i{font-size:34px;color:#5d8fd1}.ec-file-area strong{font-size:15px;color:#2d527e}.ec-file-area span{font-size:12px;color:#7891b0}
.ec-divider{display:flex;align-items:center;gap:14px;color:#9aabc0;font-size:11px;text-transform:uppercase;font-weight:800;margin:20px 0}.ec-divider:before,.ec-divider:after{content:"";height:1px;flex:1;background:#e7ecf3}
.ec-file-summary{font-size:12px;color:#687f9d;background:#f2f6fc;border:1px solid #e0e9f5;border-radius:10px;padding:13px 15px;margin-top:18px}
.ec-file-preview{max-height:190px;overflow-y:auto;margin-top:9px}.ec-file-preview>div{border-bottom:1px solid #ecf0f5;padding:7px 4px;font-size:11px;display:flex;justify-content:space-between;gap:10px;word-break:break-word}
.ec-file-preview strong{color:#2267bc;white-space:nowrap}.ec-file-preview strong.invalid{color:#bd5a4b}
.ec-submit-row{display:flex;flex-wrap:wrap;align-items:center;gap:13px;margin-top:18px}.ec-submit-row small{max-width:250px}.ec-example{background:#182b46;color:#dce9fd;padding:18px;border-radius:12px;font-family:monospace;font-size:13px;overflow-wrap:anywhere;margin:10px 0 18px}
.ec-parsed>div{display:flex;justify-content:space-between;gap:10px;border-bottom:1px solid #e9eef5;padding:12px 0;font-size:12px}.ec-parsed span{color:#7088a2}.ec-parsed strong{text-align:right;color:#293f60}
.ec-info-box{border-radius:11px;background:#eaf2fd;color:#355b8c;padding:14px;font-size:12px;line-height:1.6;margin-top:19px}.ec-help h4{font-size:14px;font-weight:800;color:#284263}.ec-help p{font-size:12px;line-height:1.7;color:#687d95}
@media(max-width:575px){.ec-import-card .card-body{padding:16px}}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('ec-archivos');
    const zip = document.getElementById('ec-zip');
    const summary = document.getElementById('ec-file-summary');
    const preview = document.getElementById('ec-file-preview');
    const button = document.getElementById('ec-import-btn');
    const form = document.getElementById('ec-import-form');
    function nombreValido(name) {
        const base = name.replace(/\.(jpg|jpeg|png|webp)$/i, '');
        return /^([0-9]{9}(?:[A-Z]{2}(?:[A-Z0-9]{2})?)?)(?:\s|$)/i.test(base);
    }
    function update() {
        const files = Array.from(input.files || []);
        const hasZip = !!(zip.files && zip.files.length);
        summary.textContent = files.length + ' imagen(es) seleccionadas' + (hasZip ? ' · ZIP: ' + zip.files[0].name : '') +
            (files.length > 20 ? ' · Supera el límite de 20 imágenes.' : '');
        if (!files.length && !hasZip) summary.textContent = 'Todavía no seleccionaste imágenes.';
        preview.innerHTML = '';
        files.slice(0, 20).forEach(function (file) {
            const row = document.createElement('div');
            const name = document.createElement('span');
            const state = document.createElement('strong');
            name.textContent = file.name;
            const valid = nombreValido(file.name);
            state.textContent = valid ? 'Nombre válido' : 'Revisar nombre';
            if (!valid) state.className = 'invalid';
            row.appendChild(name); row.appendChild(state); preview.appendChild(row);
        });
    }
    input.addEventListener('change', update);
    zip.addEventListener('change', update);
    form.addEventListener('submit', function (event) {
        if ((!input.files || input.files.length === 0) && (!zip.files || zip.files.length === 0)) {
            event.preventDefault();
            window.alert('Seleccioná imágenes o un ZIP.');
            return;
        }
        if (input.files.length > 20) {
            event.preventDefault();
            window.alert('Seleccioná hasta 20 imágenes o utilizá un ZIP.');
            return;
        }
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importando imágenes...';
    });
});
</script>
@endsection
