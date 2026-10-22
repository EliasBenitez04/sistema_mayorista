@extends('layouts.app')
@section('content')
<div class="content px-3 py-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3" style="gap:12px">
        <div>
            <h1 style="font-size:24px;font-weight:750;color:#253a56;margin:0">Detalle de fotografía</h1>
            <p class="text-muted mb-0">Información registrada para la OT {{ $foto->fot_ot }}</p>
        </div>
        <div class="d-flex" style="gap:8px">
            <a href="{{ route('carga_fotos.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Volver</a>
            @can('carga_fotos edit')
                <a href="{{ route('carga_fotos.edit', $foto->fot_cod) }}" class="btn btn-primary"><i class="far fa-edit mr-1"></i>Editar</a>
            @endcan
        </div>
    </div>
    @include('carga_fotos.show_fields')
</div>
@endsection
