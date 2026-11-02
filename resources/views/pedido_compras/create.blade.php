@extends('layouts.app')

@section('content')
    {{-- <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                    Crear Nuevo Pedido
                    </h1>
                </div>
            </div>
        </div>
    </section> --}}

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        @if(isset($carritoEcommerce) && $carritoEcommerce->isNotEmpty())
            <div class="alert alert-info d-flex align-items-center justify-content-between flex-wrap"
                style="gap:10px;border-radius:10px;margin-top:18px">
                <div><i class="fas fa-shopping-cart mr-2"></i>
                    <strong>Productos del E-commerce listos para el detalle.</strong>
                    {{ $carritoEcommerce->count() }} variante(s) seleccionadas.
                    Ahora podés completar el cliente, la condición y confirmar el pedido.
                </div>
                <a href="{{ route('ecommerce.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-left mr-1"></i>Volver al catálogo
                </a>
            </div>
        @endif

        <div class="card">

            {!! Form::open(['route' => 'pedido_compras.store', 'class' => 'confirm-submit']) !!}

            <div class="card-body">

                @include('sweetalert::alert')

                {{-- <div class="d-flex justify-content-end mb-3">
                    <button type="button"
                        id="btnNuevoClientePedido"
                        class="btn btn-success btn-sm px-3"
                        data-toggle="modal"
                        data-target="#clienteRapidoModal">
                        <i class="fas fa-user-plus mr-1"></i>
                        Nuevo cliente
                    </button>
                </div> --}}

                <div class="row">
                    @include('pedido_compras.fields')
                </div>

                @include('pedido_compras.borrador')

            </div>

            <div class="card-footer">
                {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
                <a href="{{ route('pedido_compras.index') }}" class="btn btn-primary btn-cancelar-pedido"> Cancelar </a>
            </div>

            {!! Form::close() !!}

        </div>

        {{-- Modal V2: catálogos renderizados directamente, sin AJAX de ciudades/departamentos. --}}
        @include('pedido_compras.modal_cliente_v2')
    </div>
@endsection


@push('page_scripts')
<script>
(function () {
    const transferencia = @json($carritoEcommerce ?? []);
    if (!Array.isArray(transferencia) || !transferencia.length) return;
    let aplicado = false;

    function escapar(valor) {
        return String(valor ?? '').replace(/[&<>"']/g, function (s) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[s];
        });
    }

    function crearFila(item) {
        const codigo = escapar(item.codigo);
        const descripcion = escapar(item.producto);
        const cantidad = Number(item.cantidad);
        const precio = Number(item.precio);
        const subtotal = cantidad * precio;
        const miles = typeof formatearMiles === 'function'
            ? formatearMiles : n => Number(n || 0).toLocaleString('es-PY');
        const row = document.createElement('tr');
        row.innerHTML =
            '<td class="text-center"><input name="codigo[]" value="' + codigo + '" readonly class="form-control form-control-sm text-center"></td>' +
            '<td><input name="producto[]" value="' + descripcion + '" readonly class="form-control form-control-sm"></td>' +
            '<td class="text-center"><input name="cantidad[]" value="' + cantidad + '" class="form-control form-control-sm text-center cantidad" inputmode="numeric" autocomplete="off"></td>' +
            '<td class="text-center"><input type="hidden" class="precio_raw" value="' + precio + '" data-precio="' + precio + '"><input value="' + miles(precio) + '" readonly class="form-control form-control-sm text-right"></td>' +
            '<td class="text-center"><input class="form-control form-control-sm text-right subtotal" value="' + miles(subtotal) + '" data-value="' + subtotal + '" readonly></td>' +
            '<td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="confirmarBorrado(this)" title="Eliminar producto"><i class="far fa-trash-alt"></i></button></td>';
        return row;
    }

    function aplicarCarritoAlDetalle() {
        if (aplicado) return;
        const tbody = document.getElementById('selectedProducts');
        if (!tbody || typeof calcularTodo !== 'function') return;
        aplicado = true;

        const existentes = new Map();
        tbody.querySelectorAll('tr').forEach(row => {
            const codigo = row.querySelector('input[name="codigo[]"]');
            if (codigo) existentes.set(codigo.value, row);
        });

        // Se fusiona con el borrador; si un código ya existe, se utiliza la
        // cantidad elegida en el carrito y NO se duplica la línea.
        transferencia.forEach(function (item) {
            const cantidad = Number(item.cantidad);
            const precio = Number(item.precio);
            if (!item.codigo || !Number.isInteger(cantidad) || cantidad < 1
                || !Number.isFinite(precio) || precio < 0) return;
            const anterior = existentes.get(item.codigo);
            if (anterior) {
                const q = anterior.querySelector('.cantidad');
                const p = anterior.querySelector('.precio_raw');
                const visible = p ? p.nextElementSibling : null;
                if (q) q.value = cantidad;
                if (p) { p.value = precio; p.dataset.precio = precio; }
                if (visible) visible.value = formatearMiles(precio);
                if (typeof recalcularFila === 'function') recalcularFila(anterior, true);
            } else {
                const nuevo = crearFila(item);
                tbody.appendChild(nuevo);
                existentes.set(item.codigo, nuevo);
            }
        });
        calcularTodo(true);
        const message = document.getElementById('pedido-traspaso-indicador');
        if (message) message.textContent = 'Carrito incorporado al detalle. Completá el cliente para guardar el pedido.';
    }

    // El borrador restaura primero sus datos, luego se fusiona el carrito.
    document.addEventListener('pedido:borrador-restaurado', aplicarCarritoAlDetalle, { once: true });
    // Respaldo si el navegador bloquea localStorage o el borrador no se inicializa.
    window.addEventListener('load', function () {
        window.setTimeout(aplicarCarritoAlDetalle, 500);
    });
})();
</script>
@endpush
