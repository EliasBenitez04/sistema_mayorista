<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sólo formularios create con la clase ref-create: no modifica Pedidos.
    document.querySelectorAll('.ref-create form').forEach(function (form) {
        form.classList.remove('confirm-submit');
        form.noValidate = true;
        let sending = false;

        const mark = function (field, invalid) {
            if (!field || !field.classList || field.type === 'hidden') return;
            field.classList.toggle('is-invalid', invalid);
            field.setAttribute('aria-invalid', invalid ? 'true' : 'false');
        };

        form.addEventListener('input', function (event) {
            if (event.target.matches('input,select,textarea')) mark(event.target, false);
        });
        form.addEventListener('change', function (event) {
            if (event.target.matches('input,select,textarea')) mark(event.target, false);
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (sending) return;

            const fields = Array.from(form.elements).filter(function (field) {
                return field.willValidate && !field.disabled;
            });
            const invalid = fields.filter(function (field) {
                // También rechazar texto compuesto sólo de espacios en campos obligatorios.
                const blank = field.required && typeof field.value === 'string' &&
                    !['checkbox', 'radio', 'file'].includes(field.type) &&
                    !field.value.trim();
                const fails = blank || !field.checkValidity();
                mark(field, fails);
                return fails;
            });
            if (invalid.length) {
                const first = invalid[0];
                await Swal.fire({
                    icon: 'info',
                    title: 'Verificá los datos',
                    text: 'Completá los campos obligatorios y corregí los datos señalados antes de guardar.',
                    confirmButtonText: 'Revisar',
                    confirmButtonColor: '#2563eb'
                });
                first.focus();
                first.scrollIntoView({behavior: 'smooth', block: 'center'});
                return;
            }
            const confirmation = await Swal.fire({
                icon: 'question',
                title: '¿Desea guardar el registro?',
                text: 'Confirmá que los datos ingresados sean correctos.',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#21845a',
                reverseButtons: true
            });
            if (!confirmation.isConfirmed) return;
            sending = true;
            const submitButton = form.querySelector('[type="submit"]');
            if (submitButton) submitButton.disabled = true;
            // Envío tradicional: el servidor sigue siendo responsable de la validación final.
            HTMLFormElement.prototype.submit.call(form);
        });
    });

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Verificá los datos',
            text: @json(implode(' ', $errors->all())),
            confirmButtonText: 'Corregir datos',
            confirmButtonColor: '#2563eb'
        });
    @endif
});
</script>