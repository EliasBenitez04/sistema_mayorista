<script>
document.addEventListener('DOMContentLoaded', function () {
    const drawer = document.getElementById('ec-cart-drawer');
    const items = document.getElementById('ec-cart-items');
    const overlay = document.getElementById('ec-cart-overlay');
    if (!drawer || !items || !overlay) return;

    const base = @json(url('/ecommerce/carrito'));
    const token = @json(csrf_token());
    const money = number => 'Gs. ' + Number(number || 0).toLocaleString('es-PY', { maximumFractionDigits: 0 });
    const countBadges = () => document.querySelectorAll('.js-ec-cart-count');
    const feedback = document.getElementById('ec-cart-feedback');
    const continueButton = document.getElementById('ec-cart-continue');
    let busy = false;
    let lastFocus = null;

    function showError(message) {
        feedback.textContent = message || 'No se pudo actualizar el carrito.';
        feedback.hidden = false;
    }
    function clearError() {
        feedback.textContent = '';
        feedback.hidden = true;
    }
    function el(tag, className, content) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (content !== undefined) node.textContent = String(content);
        return node;
    }
    function render(data) {
        const list = Array.isArray(data.items) ? data.items : [];
        countBadges().forEach(x => { x.textContent = String(data.unidades || 0); });
        document.getElementById('ec-cart-units').textContent = String(data.unidades || 0);
        document.getElementById('ec-cart-total').textContent = money(data.total || 0);
        continueButton.disabled = !list.length;
        items.replaceChildren();
        if (!list.length) {
            const empty = el('div', 'ec-cart-empty');
            const icon = el('i', 'fas fa-shopping-basket');
            const title = el('strong', '', 'El carrito está vacío');
            const p = el('p', '', 'Elegí un producto, seleccioná un talle y agregá la cantidad que necesitás.');
            empty.append(icon, title, p);
            items.appendChild(empty);
            return;
        }
        list.forEach(function (item) {
            const row = el('div', 'ec-cart-line');
            const image = el('div', 'ec-cart-line-icon');
            image.append(el('i', 'fas fa-tshirt'));
            const info = el('div', 'ec-cart-line-info');
            info.append(el('h3', '', item.descripcion || item.codigo));
            info.append(el('small', '', 'Código: ' + item.codigo));
            const line = el('div', 'ec-cart-line-meta');
            const controls = el('div', 'ec-cart-controls');
            const minus = el('button', 'js-cart-minus', '−');
            minus.type = 'button'; minus.dataset.codigo = item.codigo; minus.setAttribute('aria-label', 'Restar una unidad');
            const qty = el('input', 'js-cart-quantity');
            qty.type = 'number'; qty.min = '1'; qty.max = '9999';
            qty.value = String(item.cantidad); qty.dataset.codigo = item.codigo;
            qty.setAttribute('aria-label', 'Cantidad de ' + item.descripcion);
            const plus = el('button', 'js-cart-plus', '+');
            plus.type = 'button'; plus.dataset.codigo = item.codigo; plus.setAttribute('aria-label', 'Sumar una unidad');
            controls.append(minus, qty, plus);
            line.append(controls, el('strong', '', money(item.subtotal)));
            const remove = el('button', 'ec-cart-remove js-cart-remove', 'Quitar del carrito');
            remove.type = 'button'; remove.dataset.codigo = item.codigo;
            info.append(line, remove);
            row.append(image, info);
            items.appendChild(row);
        });
    }

    async function api(method, url, payload = null) {
        if (busy) return null;
        busy = true;
        try {
            const response = await fetch(url, {
                method,
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: payload ? JSON.stringify(payload) : undefined
            });
            const data = await response.json().catch(() => null);
            if (!response.ok) {
                const validation = data && data.errors ? Object.values(data.errors).flat().join(' ') : null;
                throw new Error(validation || (response.status === 419
                    ? 'La sesión expiró. Actualizá la página y volvé a intentarlo.'
                    : 'No se pudo actualizar el carrito. Error HTTP ' + response.status));
            }
            clearError();
            if (data) render(data);
            return data;
        } catch (err) {
            showError(err.message);
            return null;
        } finally {
            busy = false;
        }
    }

    function openCart() {
        const productModal = document.getElementById('ec-detail-dialog');
        if (productModal && productModal.open) productModal.close();
        lastFocus = document.activeElement;
        overlay.hidden = false;
        drawer.classList.add('ec-cart-open');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        drawer.focus();
    }
    function closeCart() {
        overlay.hidden = true;
        drawer.classList.remove('ec-cart-open');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lastFocus && lastFocus.isConnected && typeof lastFocus.focus === 'function') lastFocus.focus();
    }

    // Delegación: el botón "Ver carrito" vive dentro de un <template>
    // y se incorpora al DOM solo cuando se abre el detalle del producto.
    document.addEventListener('click', function (event) {
        if (event.target.closest('.js-ec-cart-open')) openCart();
    });
    document.getElementById('ec-cart-close').addEventListener('click', closeCart);
    document.getElementById('ec-cart-keep').addEventListener('click', closeCart);
    overlay.addEventListener('click', closeCart);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && drawer.classList.contains('ec-cart-open')) {
            e.preventDefault(); closeCart();
        }
    });

    document.addEventListener('click', async function (event) {
        const button = event.target.closest('.js-ec-cart-add');
        if (!button) return;
        const parent = button.closest('.ec-variant-order');
        const input = parent ? parent.querySelector('input.ec-variant-quantity') : null;
        const number = input ? Number(input.value) : 1;
        if (!Number.isInteger(number) || number < 1 || number > 9999) {
            showError('La cantidad debe ser un número entero entre 1 y 9.999.');
            openCart();
            return;
        }
        button.disabled = true;
        try {
            const result = await api('POST', base, { codigo: button.dataset.codigo, cantidad: number });
            if (result) {
                const original = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Agregado';
                setTimeout(() => { if (button.isConnected) button.innerHTML = original; }, 1400);
            } else {
                openCart();
            }
        } finally {
            button.disabled = false;
        }
    });

    items.addEventListener('click', async function (event) {
        const button = event.target.closest('button[data-codigo]');
        if (!button || busy) return;
        const code = button.dataset.codigo;
        const row = button.closest('.ec-cart-line');
        const input = row.querySelector('.js-cart-quantity');
        if (button.classList.contains('js-cart-remove')) {
            await api('DELETE', base + '/' + encodeURIComponent(code));
            return;
        }
        const current = Number(input.value);
        const next = current + (button.classList.contains('js-cart-plus') ? 1 : -1);
        if (!Number.isInteger(next) || next < 1 || next > 9999) {
            showError('Para quitar un producto usá la opción "Quitar del carrito".');
            return;
        }
        await api('PATCH', base + '/' + encodeURIComponent(code), { cantidad: next });
    });
    items.addEventListener('change', async function (event) {
        const input = event.target.closest('.js-cart-quantity');
        if (!input || busy) return;
        const qty = Number(input.value);
        if (!Number.isInteger(qty) || qty < 1 || qty > 9999) {
            showError('Ingresá una cantidad entera entre 1 y 9.999.');
            input.focus();
            return;
        }
        await api('PATCH', base + '/' + encodeURIComponent(input.dataset.codigo), { cantidad: qty });
    });
    document.getElementById('ec-cart-clear').addEventListener('click', async function () {
        if (!confirm('¿Vaciar todos los productos del carrito?')) return;
        await api('DELETE', base);
    });

    // Se lee la sesión al entrar al catálogo, así los artículos persisten entre páginas.
    api('GET', base);
});
</script>