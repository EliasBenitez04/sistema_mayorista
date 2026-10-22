@extends('layouts.app')

@section('content')
    @include('partials.ref_create_style')

    <div class="content px-3">
        <div class="ref-create">
            <div class="ref-create-heading">
                <span class="ref-create-icon"><i class="fas fa-city"></i></span>
                <div>
                    <h1>Nueva ciudad</h1>
                    <p>Agregá una ciudad al catálogo</p>
                </div>
            </div>

            @include('adminlte-templates::common.errors')
            @include('sweetalert::alert')

            <div class="card ref-create-card">
                {!! Form::open(['route' => 'ciudades.store', 'class' => 'confirm-submit']) !!}
                <div class="ref-create-card-header">
                    <h2><i class="fas fa-city mr-2"></i>Información de registro</h2>
                    <small>Completá los campos solicitados para guardar el registro.</small>
                </div>

                <div class="card-body">
                    <div class="row">
                        @include('ciudads.fields')
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('ciudades.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancelar</a>
                    {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
