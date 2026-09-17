@push('page_scripts')
<script>
(function () {
    /**
     * Convierte valores numéricos provenientes de PostgreSQL/Blade o con
     * formato paraguayo (35.000 / 35.000,50) a un Number confiable.
     */
    window.normalizarNumeroPedido = function (valor) {
        if (typeof valor === 'number') {
            return Number.isFinite(valor) ? valor : 0;
        }

        let texto = String(valor ?? '')
            .trim()
            .replace(/\s/g, '')
            .replace(/[^0-9,.-]/g, '');

        if (!texto) return 0;

        const tieneComa = texto.indexOf(',') !== -1;
        const tienePunto = texto.indexOf('.') !== -1;

        if (tieneComa && tienePunto) {
            // 35.000,50 => 35000.50 | 35,000.50 => 35000.50
            if (texto.lastIndexOf(',') > texto.lastIndexOf('.')) {
                texto = texto.replace(/\./g, '').replace(',', '.');
            } else {
                texto = texto.replace(/,/g, '');
            }
        } else if (tieneComa) {
            const partes = texto.split(',');
            if (partes.length === 2 && partes[1].length <= 2) {
                texto = partes[0].replace(/\./g, '') + '.' + partes[1];
            } else {
                texto = texto.replace(/,/g, '');
            }
        } else if (tienePunto) {
            const partes = texto.split('.');
            if (partes.length > 2) {
                texto = texto.replace(/\./g, '');
            } else if (partes.length === 2 && partes[1].length === 3) {
                // Formato de miles usado en Paraguay: 35.000
                texto = partes[0] + partes[1];
            }
        }

        const numero = Number(texto);
        return Number.isFinite(numero) ? numero : 0;
    };

    window.recalcularFila = function (row) {
        if (!row) return;

        const cantidadInput = row.querySelector('.cantidad');
        const precioRaw = row.querySelector('.precio_raw');
        const subtotalInput = row.querySelector('.subtotal');

        if (!cantidadInput || !subtotalInput) return;

        let cantidad = parseInt(cantidadInput.value, 10) || 0;
        if (cantidad < 1) {
            cantidad = 1;
            cantidadInput.value = 1;
        }

        let precio = precioRaw
            ? normalizarNumeroPedido(precioRaw.dataset.precio || precioRaw.value)
            : 0;

        // Respaldo: si el precio oculto vino vacío/incorrecto, reconstruirlo
        // usando el subtotal que ya mostraba la fila y su cantidad original.
        if (precio <= 0) {
            const subtotalAnterior = normalizarNumeroPedido(
                subtotalInput.dataset.value || subtotalInput.value
            );
            const cantidadOriginal = parseInt(cantidadInput.defaultValue, 10) || cantidad;

            if (subtotalAnterior > 0 && cantidadOriginal > 0) {
                precio = subtotalAnterior / cantidadOriginal;
            }
        }

        if (precioRaw) {
            precioRaw.value = precio;
            precioRaw.dataset.precio = precio;
        }

        const subtotal = cantidad * precio;
        subtotalInput.dataset.value = subtotal;
        subtotalInput.value = formatearMiles(subtotal);
    };

    window.calcularTodo = function () {
        let totalCantidad = 0;

        document.querySelectorAll('#selectedProducts tr').forEach(function (row) {
            const input = row.querySelector('.cantidad');
            if (!input) return;

            let cantidad = parseInt(input.value, 10) || 0;
            if (cantidad < 1) {
                cantidad = 1;
                input.value = 1;
            }

            totalCantidad += cantidad;
            recalcularFila(row);
        });

        const totalCantidadElement = document.getElementById('totalCantidad');
        if (totalCantidadElement) totalCantidadElement.innerText = totalCantidad;

        const modalCantidad = document.getElementById('modalCantidadProductos');
        if (modalCantidad) modalCantidad.innerText = totalCantidad;

        calcularTotal();
    };

    window.calcularTotal = function () {
        let total = 0;

        document.querySelectorAll('#selectedProducts .subtotal').forEach(function (input) {
            total += normalizarNumeroPedido(input.dataset.value || input.value);
        });

        if ($('#descuento_si').is(':checked')) {
            let descuento = normalizarNumeroPedido($('#descuento_input').val());
            descuento = Math.max(0, Math.min(100, descuento));
            total -= total * (descuento / 100);
        }

        const totalPedido = document.getElementById('ped_total');
        if (totalPedido) totalPedido.value = formatearMiles(total);

        const modalTotal = document.getElementById('modalTotalPedido');
        if (modalTotal) modalTotal.innerText = formatearMiles(total);
    };

    // Recalcular inmediatamente la fila modificada. El listener existente
    // puede seguir ejecutándose; ambos usan estas funciones corregidas.
    document.addEventListener('input', function (event) {
        if (!event.target.classList.contains('cantidad')) return;

        event.target.value = event.target.value.replace(/[^0-9]/g, '');
        const row = event.target.closest('tr');
        recalcularFila(row);
        calcularTodo();
    });
})();
</script>
@endpush
