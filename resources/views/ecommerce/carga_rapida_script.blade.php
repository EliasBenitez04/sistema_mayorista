<script>
document.addEventListener('DOMContentLoaded', function () {
    const cantidad = document.getElementById('ec-insert-qty');
    if (!cantidad) return;

    const presets = Array.from(document.querySelectorAll('.js-ec-qty-preset'));
    const dockTotal = document.getElementById('ec-quick-dock-total');
    const toast = document.getElementById('ec-quick-toast');
    let toastTimer = null;
    let ultimoResumen = null;
    const peticiones = new Map();

    function validarCantidad(valor) {
        const numero = Number(valor);
        return Number.isSafeInteger(numero) && numero >= 1 && numero <= 9999 ? numero : null;
    }

    function sincronizarPresets(numero) {
        presets.forEach(function (boton) {
            const activo = Number(boton.dataset.qty) === numero;
            boton.classList.toggle('is-active', activo);
            boton.setAttribute('aria-pressed', String(activo));
        });
    }

    function establecerCantidad(numero) {
        cantidad.value = String(numero);
        sincronizarPresets(numero);
        try { sessionStorage.setItem('ecommerce:pedido-rapido:cantidad', String(numero)); } catch (e) {}
    }

    try {
        const anterior = validarCantidad(sessionStorage.getItem('ecommerce:pedido-rapido:cantidad'));
        if (anterior) establecerCantidad(anterior);
    } catch (e) {}

    presets.forEach(function (boton) {
        boton.addEventListener('click', function () { establecerCantidad(Number(boton.dataset.qty)); });
    });
    document.querySelectorAll('[data-ec-qty-step]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const actual = validarCantidad(cantidad.value) || 1;
            const proximo = Math.max(1, Math.min(9999, actual + Number(boton.dataset.ecQtyStep)));
            establecerCantidad(proximo);
        });
    });
    cantidad.addEventListener('change', function () {
        const actual = validarCantidad(cantidad.value);
        if (actual) establecerCantidad(actual);
        else {
            mostrarToast('Ingresá una cantidad entre 1 y 9.999.', true);
            establecerCantidad(1);
        }
    });
    cantidad.addEventListener('input', function () { sincronizarPresets(validarCantidad(cantidad.value)); });

    function mostrarToast(mensaje, esError = false) {
        if (!toast) return;
        clearTimeout(toastTimer);
        toast.textContent = mensaje;
        toast.classList.toggle('is-error', esError);
        toast.hidden = false;
        toastTimer = setTimeout(function () { toast.hidden = true; }, esError ? 3900 : 1800);
    }

    function actualizarBotones(resumen) {
        if (!resumen || !Array.isArray(resumen.items)) return;
        ultimoResumen = resumen;
        const cantidades = new Map(resumen.items.map(function (item) {
            return [String(item.codigo).toUpperCase(), Number(item.cantidad)];
        }));
        document.querySelectorAll('.js-ec-quick-add').forEach(function (boton) {
            const actual = cantidades.get(String(boton.dataset.codigo).toUpperCase()) || 0;
            const chip = boton.querySelector('.ec-size-in-cart');
            if (chip) {
                chip.hidden = actual < 1;
                chip.textContent = String(actual);
            }
            boton.classList.toggle('ec-size-selected', actual > 0);
            if (actual) boton.setAttribute('aria-label', 'Talle ' + boton.dataset.talle +
                '. ' + actual + ' unidades en carrito. Agregar más');
            else boton.setAttribute('aria-label', 'Agregar talle ' + boton.dataset.talle);
        });
        if (dockTotal) {
            dockTotal.textContent = 'Gs. ' + Number(resumen.total || 0).toLocaleString('es-PY', {
                maximumFractionDigits: 0
            });
        }
    }
    document.addEventListener('ecommerce:carrito-actualizado', function (evento) {
        actualizarBotones(evento.detail);
    });

    document.addEventListener('click', function (evento) {
        const boton = evento.target.closest('.js-ec-quick-add');
        if (!boton || boton.disabled) return;
        evento.preventDefault();
        const numero = validarCantidad(cantidad.value);
        if (!numero) {
            mostrarToast('Elegí una cantidad válida para insertar.', true);
            cantidad.focus();
            return;
        }
        if (typeof window.ecommerceCarritoAgregar !== 'function') {
            mostrarToast('No se pudo iniciar el carrito. Actualizá la página.', true);
            return;
        }
        const codigo = boton.dataset.codigo;
        const talle = boton.dataset.talle;
        const pendientes = (peticiones.get(codigo) || 0) + 1;
        peticiones.set(codigo, pendientes);
        boton.classList.add('ec-size-requesting');

        // No bloqueamos el botón: pueden elegirse talles rápidamente y cada
        // selección quedará en la cola de peticiones al servidor.
        window.ecommerceCarritoAgregar(codigo, numero).then(function (respuesta) {
            if (respuesta) mostrarToast('Talle ' + talle + ': +' + numero + ' unidades agregadas al carrito.');
            else mostrarToast('No se pudo agregar el talle ' + talle + '. Revisá el carrito.', true);
        }).catch(function () {
            mostrarToast('No se pudo agregar el talle ' + talle + '.', true);
        }).finally(function () {
            const restantes = Math.max(0, (peticiones.get(codigo) || 1) - 1);
            if (restantes) peticiones.set(codigo, restantes);
            else peticiones.delete(codigo);
            if (!restantes) boton.classList.remove('ec-size-requesting');
        });
    });

    // El carrito se sincroniza desde la sesión al abrir el catálogo.
    // En ningún momento se guardan códigos/precios falsos en el navegador.
});
</script>