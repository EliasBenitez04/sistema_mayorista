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

        if (/^-?\d+(\.\d+)?$/.test(texto)) {
            const directo = Number(texto);
            return Number.isFinite(directo) ? directo : 0;
        }

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

    function obtenerCantidad(input, completarVacio) {
        const texto = String(input.value ?? '').trim();

        // Mientras el usuario escribe permitimos que el campo quede vacío.
        if (texto === '') {
            if (completarVacio) {
                input.value = 1;
                return 1;
            }
            return null;
        }

        let cantidad = parseInt(texto, 10) || 0;

        if (cantidad < 1) {
            if (completarVacio) {
                input.value = 1;
                return 1;
            }
            return null;
        }

        return cantidad;
    }

    function recalcularFilaSeguro(row, completarVacio = true) {
        if (!row) return;

        const cantidadInput = row.querySelector('.cantidad');
        const subtotalInput = row.querySelector('.subtotal');

        if (!cantidadInput || !subtotalInput) return;

        const cantidad = obtenerCantidad(cantidadInput, completarVacio);

        // Si está vacío mientras se edita, no pisamos el subtotal anterior.
        if (cantidad === null) return;

        const precio = obtenerPrecioFila(row);
        const subtotal = cantidad * precio;

        subtotalInput.dataset.value = String(subtotal);
        subtotalInput.value = formatearMiles(subtotal);
    }

    function recalcularTotalesSeguro(completarVacios = true) {
        let totalCantidad = 0;
        let total = 0;

        document.querySelectorAll('#selectedProducts tr').forEach(function (row) {
            const cantidadInput = row.querySelector('.cantidad');
            const subtotalInput = row.querySelector('.subtotal');

            if (!cantidadInput || !subtotalInput) return;

            const cantidad = obtenerCantidad(cantidadInput, completarVacios);

            // Durante la edición de un campo vacío conservamos temporalmente
            // el subtotal anterior y no forzamos el valor a 1 todavía.
            if (cantidad === null) {
                total += normalizarNumeroSeguro(subtotalInput.dataset.value || subtotalInput.value);
                return;
            }

            totalCantidad += cantidad;
            recalcularFilaSeguro(row, completarVacios);
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

    window.normalizarNumeroPedido = normalizarNumeroSeguro;
    window.recalcularFila = function (row) {
        recalcularFilaSeguro(row, true);
    };
    window.calcularTodo = function () {
        recalcularTotalesSeguro(true);
    };
    window.calcularTotal = function () {
        recalcularTotalesSeguro(true);
    };

    function manejarInputCantidad(event) {
        if (!event.target || !event.target.classList.contains('cantidad')) return;

        // Impide que el listener antiguo fuerce 1 mientras el usuario está borrando.
        event.stopImmediatePropagation();

        event.target.value = event.target.value.replace(/[^0-9]/g, '');

        // Vacío es válido mientras se está escribiendo.
        if (event.target.value === '') {
            recalcularTotalesSeguro(false);
            return;
        }

        recalcularFilaSeguro(event.target.closest('tr'), false);
        recalcularTotalesSeguro(false);
    }

    function finalizarEdicionCantidad(event) {
        if (!event.target || !event.target.classList.contains('cantidad')) return;

        event.stopImmediatePropagation();

        // Al abandonar el campo: vacío, cero o inválido => 1.
        const cantidad = parseInt(event.target.value, 10) || 0;
        if (cantidad < 1) {
            event.target.value = 1;
        }

        recalcularFilaSeguro(event.target.closest('tr'), true);
        recalcularTotalesSeguro(true);
    }

    // Captura = true para ejecutarnos antes del listener antiguo de fields.blade.php.
    document.addEventListener('input', manejarInputCantidad, true);
    document.addEventListener('change', finalizarEdicionCantidad, true);
    document.addEventListener('blur', finalizarEdicionCantidad, true);
})();
</script>
@endpush
