@extends('layouts.app')

@section('content')
    @include('partials.ref_create_style')

    <div class="content px-3">
        <div class="ref-create">
            <div class="ref-create-heading">
                <span class="ref-create-icon"><i class="fas fa-boxes"></i></span>
                <div>
                    <h1>Nuevo registro de stock</h1>
                    <p>Completá los datos del stock</p>
                </div>
            </div>

            @include('adminlte-templates::common.errors')
            @include('sweetalert::alert')

            <div class="card ref-create-card">
                {!! Form::open(['route' => 'stocks.store']) !!}
                <div class="ref-create-card-header">
                    <h2><i class="fas fa-boxes mr-2"></i>Información de registro</h2>
                    <small>Completá los campos solicitados para guardar el registro.</small>
                </div>

                <div class="card-body">
                    <div class="row">
                        @include('stocks.fields')
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('stocks.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancel</a>
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
