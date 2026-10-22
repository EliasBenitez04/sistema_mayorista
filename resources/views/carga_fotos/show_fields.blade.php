@php
    $rutaFoto = $foto->fot_img ? 'fotos/' . basename($foto->fot_img) : null;
    $fotoDisponible = $rutaFoto && \Illuminate\Support\Facades\Storage::disk('public')->exists($rutaFoto);
    $fotoUrl = $fotoDisponible ? \Illuminate\Support\Facades\Storage::disk('public')->url($rutaFoto) : null;
@endphp
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card h-100 shadow-sm border-0 foto-detail-card">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Datos del artículo</h5></div>
            <div class="card-body">
                <dl class="foto-data-grid">
                    <dt>N.º OT</dt><dd><strong>{{ $foto->fot_ot }}</strong></dd>
                    <dt>Fecha de carga</dt><dd>{{ $foto->fot_fecha ? \Carbon\Carbon::parse($foto->fot_fecha)->format('d/m/Y') : 'Sin fecha' }}</dd>
                    <dt>Descripción</dt><dd>{{ $foto->fot_desc ?: 'Sin descripción' }}</dd>
                    <dt>Línea</dt><dd>{{ optional($foto->linea)->linea_desc ?? $foto->linea_cod ?? 'No asignada' }}</dd>
                    <dt>Precio costo</dt><dd>{{ number_format($foto->fot_costo ?? 0, 0, ',', '.') }} Gs.</dd>
                    <dt>Precio venta</dt><dd>{{ number_format($foto->fot_venta ?? 0, 0, ',', '.') }} Gs.</dd>
                    <dt>Registrado por</dt><dd>{{ optional($foto->user)->name ?? 'Usuario no disponible' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card h-100 shadow-sm border-0 foto-detail-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-image text-primary mr-2"></i>Fotografía del artículo</h5>
                @if($fotoDisponible)<span class="text-muted small">Pulsá la imagen para ampliarla</span>@endif
            </div>
            <div class="card-body d-flex align-items-center justify-content-center foto-preview-area">
                @if($fotoDisponible)
                    <button type="button" class="foto-open-btn" data-toggle="modal" data-target="#fotoAmpliada" aria-label="Ampliar imagen">
                        <img src="{{ $fotoUrl }}" alt="Imagen de OT {{ $foto->fot_ot }}" class="foto-detail-image">
                    </button>
                @else
                    <div class="text-center text-muted p-4">
                        <i class="far fa-image fa-3x mb-3"></i>
                        <p class="mb-1">No se encontró la imagen de este registro.</p>
                        <small>Verificá que el archivo exista en storage/app/public/fotos y que storage esté enlazado.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@if($fotoDisponible)
<div class="modal fade" id="fotoAmpliada" tabindex="-1" role="dialog" aria-labelledby="fotoAmpliadaTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotoAmpliadaTitulo">Fotografía de OT {{ $foto->fot_ot }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-center bg-light">
                <img src="{{ $fotoUrl }}" class="img-fluid" style="max-height:75vh;object-fit:contain" alt="Fotografía ampliada de OT {{ $foto->fot_ot }}">
            </div>
            <div class="modal-footer"><a href="{{ $fotoUrl }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="fas fa-external-link-alt mr-1"></i> Abrir imagen</a><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>
@endif
<style>
.foto-detail-card{border-radius:14px;overflow:hidden}.foto-detail-card .card-header{padding:18px 20px;border-bottom:1px solid #e7ecf3}.foto-detail-card h5{font-size:16px;font-weight:700}.foto-data-grid{display:grid;grid-template-columns:135px minmax(0,1fr);gap:13px 15px;margin:0;font-size:13px}.foto-data-grid dt{color:#687789;font-weight:600}.foto-data-grid dd{color:#243449;margin:0;overflow-wrap:anywhere}.foto-preview-area{min-height:330px;background:#f8fafc}.foto-open-btn{padding:0;border:0;background:transparent;cursor:zoom-in;width:100%}.foto-detail-image{display:block;margin:auto;max-width:100%;max-height:420px;object-fit:contain;border-radius:9px;box-shadow:0 6px 20px rgba(28,46,70,.12)}@media(max-width:576px){.foto-data-grid{grid-template-columns:110px minmax(0,1fr)}.foto-preview-area{min-height:230px}}
</style>