@extends('layouts.app')

@section('content')
    @include('partials.ref_create_style')

    <div class="content px-3">
        <div class="ref-create">
            <div class="ref-create-heading">
                <span class="ref-create-icon"><i class="fas fa-store"></i></span>
                <div>
                    <h1>Nueva sucursal</h1>
                    <p>Completá la información del local</p>
                </div>
            </div>

            @include('adminlte-templates::common.errors')
            @include('sweetalert::alert')

            <div class="card ref-create-card">
                {!! Form::open(['route' => 'sucursal.store', 'class' => 'confirm-submit']) !!}
                <div class="ref-create-card-header">
                    <h2><i class="fas fa-store mr-2"></i>Información de registro</h2>
                    <small>Completá los campos solicitados para guardar el registro.</small>
                </div>

                <div class="card-body">
                    <div class="row">
                        @include('sucursals.fields')
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('sucursal.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancelar</a>
                    {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
