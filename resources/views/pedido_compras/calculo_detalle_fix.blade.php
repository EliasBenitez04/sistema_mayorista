@push('page_scripts')
<script>
(function () {
    function normalizarNumeroSeguro(valor) {
        if (typeof valor === 'number') {
            return Number.isFinite(valor) ? valor : 0;
        }

        let texto = String(valor ?? '')
            .trim()
            .replace(/\s/g, '')
            .replace(/[^0-9,.-]/g, '');

        if (!texto) return 0;

        // Valor numérico crudo típico de PostgreSQL/Blade: 35000 o 35000.00
        if (/^-?\d+(\.\d+)?$/.test(texto)) {
            const directo = Number(texto);
            return Number.isFinite(directo) ? directo : 0;
        }

        // Formato paraguayo: 35.000 / 35.000,50
        if (texto.includes(',')) {
            texto = texto.replace(/\./g, '').replace(',', '.');
        } else {
            texto = texto.replace(/\./g, '');
        }

        const numero = Number(texto);
        return Number.isFinite(numero) ? numero : 0;
    }

    function obtenerPrecioFila(row) {
        const precioRaw = row.querySelector('.precio_raw');
        const subtotalInput = row.querySelector('.subtotal');
        const cantidadInput = row.querySelector('.cantidad');

        let precio = 0;

        if (precioRaw) {
            precio = normalizarNumeroSeguro(precioRaw.dataset.precio || precioRaw.value);
        }

        if (precio <= 0 && subtotalInput && cantidadInput) {
            const subtotalAnterior = normalizarNumeroSeguro(
                subtotalInput.dataset.value || subtotalInput.value
            );
            const cantidadOriginal = parseInt(cantidadInput.defaultValue, 10) || 1;

            if (subtotalAnterior > 0 && cantidadOriginal > 0) {
                precio = subtotalAnterior / cantidadOriginal;
            }
        }

        if (precioRaw && precio > 0) {
            precioRaw.value = precio;
            precioRaw.dataset.precio = precio;
        }

        return precio;
    }

    function recalcularFilaSeguro(row) {
        if (!row) return;

        const cantidadInput = row.querySelector('.cantidad');
        const subtotalInput = row.querySelector('.subtotal');

        if (!cantidadInput || !subtotalInput) return;

        let cantidad = parseInt(cantidadInput.value, 10) || 0;
        if (cantidad < 1) {
            cantidad = 1;
            cantidadInput.value = 1;
        }

        const precio = obtenerPrecioFila(row);
        const subtotal = cantidad * precio;

        subtotalInput.dataset.value = String(subtotal);
        subtotalInput.value = formatearMiles(subtotal);
    }

    function recalcularTotalesSeguro() {
        let totalCantidad = 0;
        let total = 0;

        document.querySelectorAll('#selectedProducts tr').forEach(function (row) {
            const cantidadInput = row.querySelector('.cantidad');
            const subtotalInput = row.querySelector('.subtotal');

            if (!cantidadInput || !subtotalInput) return;

            let cantidad = parseInt(cantidadInput.value, 10) || 0;
            if (cantidad < 1) {
                cantidad = 1;
                cantidadInput.value = 1;
            }

            totalCantidad += cantidad;
            recalcularFilaSeguro(row);
            total += normalizarNumeroSeguro(subtotalInput.dataset.value || subtotalInput.value);
        });

        if ($('#descuento_si').is(':checked')) {
            let descuento = normalizarNumeroSeguro($('#descuento_input').val());
            descuento = Math.max(0, Math.min(100, descuento));
            total -= total * (descuento / 100);
        }

        const totalCantidadElement = document.getElementById('totalCantidad');
        if (totalCantidadElement) totalCantidadElement.innerText = totalCantidad;

        const modalCantidad = document.getElementById('modalCantidadProductos');
        if (modalCantidad) modalCantidad.innerText = totalCantidad;

        const totalPedido = document.getElementById('ped_total');
        if (totalPedido) totalPedido.value = formatearMiles(total);

        const modalTotal = document.getElementById('modalTotalPedido');
        if (modalTotal) modalTotal.innerText = formatearMiles(total);
    }

    // Dejamos estas funciones como las oficiales para cualquier llamada del módulo.
    window.normalizarNumeroPedido = normalizarNumeroSeguro;
    window.recalcularFila = recalcularFilaSeguro;
    window.calcularTodo = recalcularTotalesSeguro;
    window.calcularTotal = recalcularTotalesSeguro;

    function manejarCantidad(event) {
        if (!event.target || !event.target.classList.contains('cantidad')) return;

        // Evita que el listener antiguo de fields.blade.php vuelva a ejecutar
        // otro cálculo después y pise el subtotal correcto con 0.
        event.stopImmediatePropagation();

        event.target.value = event.target.value.replace(/[^0-9]/g, '');
        recalcularFilaSeguro(event.target.closest('tr'));
        recalcularTotalesSeguro();
    }

    // Captura = true: este handler corre antes de los listeners antiguos.
    document.addEventListener('input', manejarCantidad, true);
    document.addEventListener('change', manejarCantidad, true);
})();
</script>
@endpush
