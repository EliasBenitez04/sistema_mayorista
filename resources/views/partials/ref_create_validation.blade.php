<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sólo formularios create con la clase ref-create: no modifica Pedidos.
    document.querySelectorAll('.ref-create form').forEach(function (form) {
        form.classList.remove('confirm-submit');
        form.noValidate = true;
        let sending = false;
        // Requisitos de cada pantalla. No afectamos los formularios de Pedidos.
        const requiredByModule = {
            clientes: ['cli_ci','cli_nombre','cli_direccion','cli_telefono','id_departamento','id_ciudad'],
            ciudads: ['ciu_descripcion'],
            sucursals: ['suc_descri','suc_direccion','suc_telefono'],
            departamentos: ['dep_descripcion'],
            lineas: ['linea_desc'],
            articulos: ['art_codigo','art_descripcion','art_precio','art_iva','prec_vent'],
            usuarios: ['name','email','ci','password'],
            permissions: ['name'],
            roles: ['name'],
            stocks: ['sucursal','codigo','descripcion','cantidad'],
            carga_fotos: ['fot_ot','fot_costo','fot_venta','fot_desc','linea_cod','fot_img']
        };
        const moduleName = Object.keys(requiredByModule).find(function (name) {
            return form.closest('.ref-create') && document.querySelector('input[name="_token"]') &&
                (form.action.includes('/' + ({
                  ciudads:'ciudades',sucursals:'sucursal',departamentos:'Departamentos'
                }[name] || name));
        });
        (requiredByModule[moduleName] || []).forEach(function (name) {
            const field = Array.from(form.elements).find(function (element) { return element.name === name; });
            if (field && !field.disabled) field.required = true;
        });
        if (moduleName === 'roles') {
            // Un rol sin permisos no debe registrarse.
            form.dataset.mustChoosePermission = '1';
        }


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
            if (form.dataset.mustChoosePermission === '1' && !form.querySelector('input[name="permission[]"]:checked')) {
                await Swal.fire({icon:'info',title:'Verificá los datos',text:'Seleccioná al menos un permiso para el rol.',confirmButtonText:'Revisar'});
                return;
            }
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