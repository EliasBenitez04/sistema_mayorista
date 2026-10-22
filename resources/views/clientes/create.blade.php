@extends('layouts.app')

@section('content')
    @include('partials.ref_create_style')

    <div class="content px-3">
        <div class="ref-create">
            <div class="ref-create-heading">
                <span class="ref-create-icon"><i class="fas fa-user-plus"></i></span>
                <div>
                    <h1>Nuevo cliente</h1>
                    <p>Registrá los datos del cliente</p>
                </div>
            </div>

            @include('adminlte-templates::common.errors')
            @include('sweetalert::alert')

            <div class="card ref-create-card">
                {!! Form::open([
                'route' => 'clientes.store',
                'class' => 'confirm-submit',
                'files' => true,
            ]) !!}
                <div class="ref-create-card-header">
                    <span class="ref-create-section-icon"><i class="fas fa-user-plus mr-2"></i></span>
                    <div><h2>Información de registro</h2><small>Completá los campos solicitados para guardar el registro.</small></div>
                </div>

                <div class="card-body">
                    <div class="row">
                        @include('clientes.fields')
                    </div>
                </div>

                <div class="card-footer">
                    <span class="ref-create-footer-note"><i class="fas fa-shield-alt"></i> Revisá los datos antes de guardar.</span>
                    <div class="ref-create-footer-actions">
                        <a href="{{ route('clientes.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancelar</a>
                        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
