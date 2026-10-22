@extends('layouts.app')

@section('content')
    @include('partials.ref_create_style')
    <div class="content px-3">
        <div class="ref-create">
            <div class="ref-create-heading">
                <span class="ref-create-icon"><i class="fas fa-camera"></i></span>
                <div><h1>Nueva carga de fotos</h1><p>Completá los datos de la carga</p></div>
            </div>
            <div class="card ref-create-card">
                {!! Form::open([
                'route' => 'carga_fotos.store',
                'class' => 'ref-validated-form',
                'files' => true,
            ]) !!}
                <div class="ref-create-card-header"><span class="ref-create-section-icon"><i class="fas fa-camera mr-2"></i></span><div><h2>Información de registro</h2><small>Completá los campos solicitados para guardar el registro.</small></div></div>
                <div class="card-body">
                    <div class="row">@include('carga_fotos.fields')</div>
                </div>
                <div class="card-footer"><span class="ref-create-footer-note"><i class="fas fa-shield-alt"></i> Revisá los datos antes de guardar.</span><div class="ref-create-footer-actions"><a href="{{ route('carga_fotos.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancelar</a>{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}</div></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @include('partials.ref_create_validation')
@endsection
