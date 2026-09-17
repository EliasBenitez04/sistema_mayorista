@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                    Crear Nuevo Pedido
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::open(['route' => 'pedido_compras.store', 'class' => 'confirm-submit']) !!}

            <div class="card-body">

                @include('sweetalert::alert')

                <div class="d-flex justify-content-end mb-3">
                    <button type="button"
                        id="btnNuevoClientePedido"
                        class="btn btn-success btn-sm px-3"
                        data-toggle="modal"
                        data-target="#clienteRapidoModal">
                        <i class="fas fa-user-plus mr-1"></i>
                        Nuevo cliente
                    </button>
                </div>

                <div class="row">
                    @include('pedido_compras.fields')
                </div>

            </div>

            <div class="card-footer">
                {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
                <a href="{{ route('pedido_compras.index') }}" class="btn btn-primary"> Cancelar </a>
            </div>

            {!! Form::close() !!}

        </div>

        {{-- Modal V2: catálogos renderizados directamente, sin AJAX de ciudades/departamentos. --}}
        @include('pedido_compras.modal_cliente_v2')
    </div>
@endsection
