@extends('layouts.app')

@section('content')
    @include('partials.ref_create_style')
    <div class="content px-3">
        <div class="ref-create ref-create--simple">
            <div class="ref-create-heading">
                <span class="ref-create-icon"><i class="fas fa-key"></i></span>
                <div><h1>Editar permiso</h1><p>Actualizá los datos del registro</p></div>
            </div>
            <div class="card ref-create-card">
                {!! Form::model($permissions, ['route' => ['permissions.update', $permissions->id], 'method' => 'patch', 'class' => 'ref-validated-form']) !!}
                <div class="ref-create-card-header">
                    <span class="ref-create-section-icon"><i class="fas fa-key mr-2"></i></span>
                    <div><h2>Información del registro</h2><small>Revisá los campos antes de actualizar.</small></div>
                </div>
                <div class="card-body">
                    @include('sweetalert::alert')
                    <div class="row">
                        @include('permissions.fields')

                    </div>
                </div>
                <div class="card-footer">
                    <span class="ref-create-footer-note"><i class="fas fa-shield-alt"></i> Revisá los datos antes de guardar.</span>
                    <div class="ref-create-footer-actions">
                        <a href="{{ route('permissions.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancelar</a>
                        {!! Form::submit('Actualizar', ['class' => 'btn btn-success']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @include('partials.ref_create_validation')
@endsection
