@extends('layouts.app')

@section('content')
    @include('partials.ref_create_style')
    <div class="content px-3">
        <div class="ref-create">
            <div class="ref-create-heading">
                <span class="ref-create-icon"><i class="fas fa-camera"></i></span>
                <div><h1>Nueva carga de fotos</h1><p>Completá los datos de la carga</p></div>
            </div>
            @include('adminlte-templates::common.errors')
            @include('sweetalert::alert')
            <div class="card ref-create-card">
                {!! Form::open([
                'route' => 'carga_fotos.store',
                'class' => 'confirm-submit',
                'files' => true,
            ]) !!}
                <div class="ref-create-card-header">
                    <h2><i class="fas fa-camera mr-2"></i>Información de registro</h2>
                    <small>Completá los campos solicitados para guardar el registro.</small>
                </div>
                <div class="card-body">
                    <div class="row">@include('carga_fotos.fields')</div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('carga_fotos.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancelar</a>
                    {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
