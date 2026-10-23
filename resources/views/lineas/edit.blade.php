@extends('layouts.app')

@section('content')
    @include('partials.ref_create_style')
    <div class="content px-3">
        <div class="ref-create ref-create--simple">
            <div class="ref-create-heading">
                <span class="ref-create-icon"><i class="fas fa-layer-group"></i></span>
                <div><h1>Editar línea</h1><p>Actualizá los datos del registro</p></div>
            </div>
            <div class="card ref-create-card">
                {!! Form::model($linea, ['route' => ['lineas.update', $linea->linea_cod], 'method' => 'patch', 'class' => 'ref-validated-form']) !!}
                <div class="ref-create-card-header">
                    <span class="ref-create-section-icon"><i class="fas fa-layer-group mr-2"></i></span>
                    <div><h2>Información del registro</h2><small>Revisá los campos antes de actualizar.</small></div>
                </div>
                <div class="card-body">
                    @include('sweetalert::alert')
                    <div class="row">
                        @include('lineas.fields')

                    </div>
                </div>
                <div class="card-footer">
                    <span class="ref-create-footer-note"><i class="fas fa-shield-alt"></i> Revisá los datos antes de guardar.</span>
                    <div class="ref-create-footer-actions">
                        <a href="{{ route('lineas.index') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Cancelar</a>
                        {!! Form::submit('Actualizar', ['class' => 'btn btn-success']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @include('partials.ref_create_validation')
@endsection
