@php
    $pedidoBorradorTipo = isset($pedido) ? 'edit_' . $pedido->id_pedido : 'create';
    $pedidoBorradorKey = 'sistema_mayorista:pedido_compras:borrador:' . Auth::id() . ':' . $pedidoBorradorTipo;
@endphp

@push('page_scripts')
<script type="text/javascript">
(function () {
    const BORRADOR_KEY = @json($pedidoBorradorKey);
    const PENDIENTE_KEY = 'sistema_mayorista:pedido_compras:borrador-pendiente';
    let restaurandoBorrador = false;
    let timerBorrador = null;

    function storageDisponible() {
        try {
            const testKey = '__pedido_borrador_test__';
            localStorage.setItem(testKey, '1');
            localStorage.removeItem(testKey);
            return true;
        } catch (e) {
            return false;
        }
    }

    if (!storageDisponible()) return;

    function escaparHtml(valor) {
        return String(valor ?? '').replace(/[&<>'"]/g, function (c) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#039;',
                '"': '&quot;'
            }[c];
        });
    }

    function valorCampo(selector) {
        const el = document.querySelector(selector);
        return el ? el.value : '';
    }

    function obtenerProductosBorrador() {
        return Array.from(document.querySelectorAll('#selectedProducts tr')).map(function (row) {
            const codigo = row.querySelector("input[name='codigo[]']");
            const producto = row.querySelector("input[name='producto[]']");
            const cantidad = row.querySelector('.cantidad');
            const precioRaw = row.querySelector('.precio_raw');
            const subtotal = row.querySelector('.subtotal');

            return {
                codigo: codigo ? codigo.value : '',
                producto: producto ? producto.value : '',
                cantidad: cantidad ? cantidad.value : '',
                precio: precioRaw ? (precioRaw.dataset.precio || precioRaw.value || '0') : '0',
                subtotal: subtotal ? (subtotal.dataset.value || subtotal.value || '0') : '0'
            };
        }).filter(function (item) {
            return item.codigo !== '';
        });
    }

    function construirBorrador() {
        const clienteSelect = document.getElementById('id_cliente');
        const clienteOption = clienteSelect && clienteSelect.selectedIndex >= 0
            ? clienteSelect.options[clienteSelect.selectedIndex]
            : null;

        return {
            version: 1,
            guardado_en: new Date().toISOString(),
            cabecera: {
                ped_fecha: valorCampo('#ped_fecha'),
                id_cliente: valorCampo('#id_cliente'),
                cliente_texto: clienteOption ? clienteOption.text : '',
                condicion: valorCampo('#condicion'),
                aplica_descuento: document.getElementById('descuento_si')?.checked ? 'SI' : 'NO',
                descuento: valorCampo('#descuento_input'),
                intervalo: valorCampo('#intervalo'),
                cant_cuotas: valorCampo('#cant_cuotas'),
                obs: valorCampo("textarea[name='obs']")
            },
            productos: obtenerProductosBorrador()
        };
    }

    function guardarBorrador() {
        if (restaurandoBorrador) return;
        try {
            localStorage.setItem(BORRADOR_KEY, JSON.stringify(construirBorrador()));
        } catch (e) {
            console.warn('No se pudo guardar el borrador del pedido.', e);
        }
    }

    function programarGuardadoBorrador() {
        if (restaurandoBorrador) return;
        clearTimeout(timerBorrador);
        timerBorrador = setTimeout(guardarBorrador, 180);
    }

    function crearFilaDesdeBorrador(item) {
        const precio = typeof normalizarNumeroPedido === 'function'
            ? normalizarNumeroPedido(item.precio)
            : (parseFloat(item.precio) || 0);
        const cantidadTexto = String(item.cantidad ?? '');
        const cantidadNumero = parseInt(cantidadTexto, 10) || 0;
        const subtotalGuardado = typeof normalizarNumeroPedido === 'function'
            ? normalizarNumeroPedido(item.subtotal)
            : (parseFloat(item.subtotal) || 0);
        const subtotal = subtotalGuardado || (precio * cantidadNumero);
        const row = document.createElement('tr');

        row.innerHTML = `
            <td class="text-center">
                <input name="codigo[]" value="${escaparHtml(item.codigo)}" readonly class="form-control form-control-sm text-center">
            </td>
            <td>
                <input name="producto[]" value="${escaparHtml(item.producto)}" readonly class="form-control form-control-sm">
            </td>
            <td class="text-center">
                <input name="cantidad[]" value="${escaparHtml(cantidadTexto)}" class="form-control form-control-sm text-center cantidad" inputmode="numeric" autocomplete="off">
            </td>
            <td class="text-center">
                <input type="hidden" class="precio_raw" value="${precio}" data-precio="${precio}">
                <input value="${typeof formatearMiles === 'function' ? formatearMiles(precio) : precio}" readonly class="form-control form-control-sm text-right">
            </td>
            <td class="text-center">
                <input class="form-control form-control-sm text-right subtotal" value="${typeof formatearMiles === 'function' ? formatearMiles(subtotal) : subtotal}" data-value="${subtotal}" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmarBorrado(this)" title="Eliminar producto">
                    <i class="far fa-trash-alt"></i>
                </button>
            </td>`;

        return row;
    }

    function restaurarCabecera(cabecera) {
        if (!cabecera) return;

        if (cabecera.ped_fecha !== undefined) $('#ped_fecha').val(cabecera.ped_fecha);

        if (cabecera.id_cliente !== undefined && cabecera.id_cliente !== '') {
            const selectCliente = document.getElementById('id_cliente');
            if (selectCliente) {
                let option = Array.from(selectCliente.options).find(function (op) {
                    return String(op.value) === String(cabecera.id_cliente);
                });

                if (!option && cabecera.cliente_texto) {
                    option = new Option(cabecera.cliente_texto, cabecera.id_cliente, true, true);
                    selectCliente.add(option);
                }

                $('#id_cliente').val(String(cabecera.id_cliente)).trigger('change.select2');
            }
        }

        if (cabecera.condicion !== undefined) $('#condicion').val(cabecera.condicion);
        if (cabecera.descuento !== undefined) $('#descuento_input').val(cabecera.descuento);
        if (cabecera.intervalo !== undefined) $('#intervalo').val(cabecera.intervalo);
        if (cabecera.cant_cuotas !== undefined) $('#cant_cuotas').val(cabecera.cant_cuotas);
        if (cabecera.obs !== undefined) $("textarea[name='obs']").val(cabecera.obs);

        if (cabecera.aplica_descuento === 'SI') {
            $('#descuento_si').prop('checked', true);
            $('#descuento_no').prop('checked', false);
        } else {
            $('#descuento_no').prop('checked', true);
            $('#descuento_si').prop('checked', false);
        }
    }

    function restaurarBorrador() {
        let raw = null;
        try {
            raw = localStorage.getItem(BORRADOR_KEY);
        } catch (e) {
            return;
        }

        if (!raw) return;

        let borrador = null;
        try {
            borrador = JSON.parse(raw);
        } catch (e) {
            localStorage.removeItem(BORRADOR_KEY);
            return;
        }

        if (!borrador || borrador.version !== 1) return;

        restaurandoBorrador = true;

        restaurarCabecera(borrador.cabecera || {});

        const tabla = document.getElementById('selectedProducts');
        if (tabla && Array.isArray(borrador.productos)) {
            tabla.innerHTML = '';
            borrador.productos.forEach(function (item) {
                tabla.appendChild(crearFilaDesdeBorrador(item));
            });
        }

        if (typeof toggleDescuento === 'function') toggleDescuento();
        if (typeof toggleCondicion === 'function') toggleCondicion();
        if (typeof calcularTodo === 'function') calcularTodo(false);

        restaurandoBorrador = false;
    }

    function limpiarBorrador() {
        try {
            localStorage.removeItem(BORRADOR_KEY);
            if (sessionStorage.getItem(PENDIENTE_KEY) === BORRADOR_KEY) {
                sessionStorage.removeItem(PENDIENTE_KEY);
            }
        } catch (e) {}
    }

    function inicializarBorrador() {
        // Si el servidor devolvió el mismo formulario por una validación o error,
        // no se considera guardado exitoso: conservamos el borrador.
        try {
            if (sessionStorage.getItem(PENDIENTE_KEY) === BORRADOR_KEY) {
                sessionStorage.removeItem(PENDIENTE_KEY);
            }
        } catch (e) {}

        // Los handlers de fields.blade.php se registran antes que este include.
        // Dejamos que terminen de cargar el detalle original y luego restauramos.
        setTimeout(function () {
            restaurarBorrador();

            const tabla = document.getElementById('selectedProducts');
            if (tabla && window.MutationObserver) {
                const observer = new MutationObserver(function () {
                    programarGuardadoBorrador();
                });
                observer.observe(tabla, { childList: true, subtree: false });
            }
        }, 0);

        document.addEventListener('input', function (e) {
            if (e.target.closest('form.confirm-submit')) {
                programarGuardadoBorrador();
            }
        }, true);

        document.addEventListener('change', function (e) {
            if (e.target.closest('form.confirm-submit')) {
                programarGuardadoBorrador();
            }
        }, true);

        const form = document.querySelector('form.confirm-submit');
        if (form) {
            form.addEventListener('submit', function () {
                guardarBorrador();
                try {
                    sessionStorage.setItem(PENDIENTE_KEY, BORRADOR_KEY);
                } catch (e) {}
            }, true);
        }

        document.querySelectorAll('.btn-cancelar-pedido').forEach(function (btn) {
            btn.addEventListener('click', function () {
                limpiarBorrador();
            });
        });

        window.addEventListener('beforeunload', function () {
            guardarBorrador();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarBorrador);
    } else {
        setTimeout(inicializarBorrador, 0);
    }
})();
</script>
@endpush
