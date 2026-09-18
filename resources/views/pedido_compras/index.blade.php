@extends('layouts.app')

@section('content')
    {{-- <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pedidos Realizados</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right" href="{{ route('pedido_compras.create') }}">
                        Nuevo
                    </a>
                </div>
            </div>
        </div>
    </section> --}}

    <div class="content px-3">

        @include('sweetalert::alert')

        <div class="clearfix"></div>

        <div class="card">
            @include('pedido_compras.table')
        </div>
    </div>
@endsection

@push('page_scripts')
<script type="text/javascript">
(function () {
    const PENDIENTE_KEY = 'sistema_mayorista:pedido_compras:borrador-pendiente';

    try {
        const borradorGuardado = sessionStorage.getItem(PENDIENTE_KEY);
        if (borradorGuardado) {
            localStorage.removeItem(borradorGuardado);
            sessionStorage.removeItem(PENDIENTE_KEY);
        }
    } catch (e) {}
})();
</script>
@endpush
