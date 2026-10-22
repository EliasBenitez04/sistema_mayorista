@push('page_scripts')
<script>
(function () {
    'use strict';
    // Delegación global: no depende del momento en que se renderice el formulario.
    // Limitado a las pantallas create que utilizan ref-validated-form. Pedidos no se modifica.
    const requiredByRoute = {
        clientes: ['cli_ci', 'cli_nombre', 'cli_direccion', 'cli_telefono', 'id_departamento', 'id_ciudad'],
        ciudades: ['ciu_descripcion'],
        sucursal: ['suc_descri', 'suc_direccion', 'suc_telefono'],
        Departamentos: ['dep_descripcion'],
        lineas: ['linea_desc'],
        articulos: ['art_codigo', 'art_descripcion', 'art_precio', 'art_iva', 'prec_vent'],
        usuarios: ['name', 'email', 'ci', 'password'],
        permissions: ['name'],
        roles: ['name'],
        stocks: ['sucursal', 'codigo', 'descripcion', 'cantidad'],
        carga_fotos: ['fot_ot', 'fot_costo', 'fot_venta', 'fot_desc', 'linea_cod', 'fot_img']
    };

    function showInfo() {
        return Swal.fire({
            icon: 'info',
            title: 'Verificá los datos',
            text: 'Completá los datos correctamente para poder guardar.',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#2563eb'
        });
    }

    function validateForm(form) {
        const routePath = new URL(form.action, window.location.href).pathname.split('/').filter(Boolean);
        const route = routePath[routePath.length - 1] === 'store'
            ? routePath[routePath.length - 2] : routePath[routePath.length - 1];
        const required = requiredByRoute[route] || [];
        let firstInvalid = null;

        // HTML checkValidity() evalúa los atributos required, pattern, tipo y longitud.
        Array.from(form.elements).forEach(function (field) {
            if (!field.name || field.disabled || field.type === 'hidden' || field.type === 'submit') return;
            const blank = required.includes(field.name) &&
                (field.type === 'file' ? !field.files.length : !String(field.value || '').trim());
            const invalid = blank || (field.willValidate && !field.checkValidity());
            if (invalid && !firstInvalid) firstInvalid = field;
        });

        if (route === 'roles' && !form.querySelector('input[name="permission[]"]:checked')) {
            if (!firstInvalid) firstInvalid = form.querySelector('input[name="permission[]"]');
        }
        return firstInvalid;
    }

    // Capture evita que handlers anteriores envíen un formulario incompleto.
    document.addEventListener('submit', async function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.matches('.ref-create form.ref-validated-form')) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (form.dataset.sending === 'true') return;
        if (typeof Swal === 'undefined') {
            window.alert('Completá los datos correctamente para poder guardar.');
            return;
        }

        const invalid = validateForm(form);
        if (invalid) {
            await showInfo();
            invalid.focus();
            return;
        }

        const decision = await Swal.fire({
            icon: 'question',
            title: '¿Desea guardar el registro?',
            text: 'Confirmá que los datos ingresados sean correctos.',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#21845a',
            reverseButtons: true
        });
        if (!decision.isConfirmed) return;
        form.dataset.sending = 'true';
        const button = form.querySelector('[type="submit"]');
        if (button) button.disabled = true;
        HTMLFormElement.prototype.submit.call(form);
    }, true);

    // Si Laravel devuelve errores tras POST, mostrarlos sin un bloque rojo.
    @if ($errors->any())
        if (typeof Swal !== 'undefined') showInfo();
    @endif
})();
</script>
@endpush
