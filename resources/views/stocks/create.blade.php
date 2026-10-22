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


            <div class="card ref-create-card">
                {!! Form::open(['route' => 'stocks.store']) !!}
                <div class="ref-create-card-header">
                    <span class="ref-create-section-icon"><i class="fas fa-boxes mr-2"></i></span>
                    <div><h2>Información de registro</h2><small>Completá los campos solicitados para guardar el registro.</small></div>
                </div>

                <div class="card-body">
                    <div class="row">
                        @include('stocks.fields')
                    </div>
                </div>

                <div class="card-footer">
                    <span class="ref-create-footer-note"><i class="fas fa-shield-alt"></i> Revisá los datos antes de guardar.</span>
                    <div class="ref-create-footer-actions">
                        <a href="{{ route('stocks.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancel</a>
                        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @include('partials.ref_create_validation')
@endsection
