@php
    $departamentosClienteRapido = \Illuminate\Support\Facades\DB::table('departamento')
        ->orderBy('dep_descripcion')
        ->pluck('dep_descripcion', 'id_departamento');

    $ciudadesClienteRapido = \Illuminate\Support\Facades\DB::table('ciudad')
        ->orderBy('ciu_descripcion')
        ->pluck('ciu_descripcion', 'id_ciudad');
@endphp

<div class="modal fade" id="clienteRapidoModal" tabindex="-1" role="dialog" aria-labelledby="clienteRapidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content cliente-rapido-content">
            <div class="modal-header cliente-rapido-header">
                <div class="d-flex align-items-center">
                    <div class="cliente-rapido-icon mr-3"><i class="fas fa-user-plus"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="clienteRapidoModalLabel">Registrar nuevo cliente</h5>
                        <small class="cliente-rapido-subtitle">Guarde el cliente sin salir del pedido.</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <div id="clienteRapidoAlert" class="alert alert-danger d-none mb-3" role="alert"></div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cliente_cli_ci" class="font-weight-bold">Nro. de CI / R.U.C. <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-id-card"></i></span></div>
                            <input type="text" id="cliente_cli_ci" class="form-control" maxlength="12" placeholder="Ej: 1234567-8" autocomplete="off">
                            <div class="invalid-feedback" id="error_cliente_cli_ci"></div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cliente_cli_nombre" class="font-weight-bold">Nombres o Razón Social <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                            <input type="text" id="cliente_cli_nombre" class="form-control" maxlength="45" placeholder="Ingrese nombres o razón social" autocomplete="off">
                            <div class="invalid-feedback" id="error_cliente_cli_nombre"></div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cliente_cli_apellido" class="font-weight-bold">Apellidos</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-tag"></i></span></div>
                            <input type="text" id="cliente_cli_apellido" class="form-control" maxlength="45" placeholder="Ingrese apellidos" autocomplete="off">
                            <div class="invalid-feedback" id="error_cliente_cli_apellido"></div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cliente_cli_telefono" class="font-weight-bold">Teléfono <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                            <input type="text" id="cliente_cli_telefono" class="form-control" maxlength="12" placeholder="Ej: 0981123456" autocomplete="off">
                            <div class="invalid-feedback" id="error_cliente_cli_telefono"></div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="cliente_cli_direccion" class="font-weight-bold">Dirección <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                            <input type="text" id="cliente_cli_direccion" class="form-control" maxlength="100" placeholder="Ingrese la dirección" autocomplete="off">
                            <div class="invalid-feedback" id="error_cliente_cli_direccion"></div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cliente_id_departamento" class="font-weight-bold">Departamento <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map"></i></span></div>
                            <select id="cliente_id_departamento" class="form-control">
                                <option value="">Seleccione un departamento</option>
                                @forelse ($departamentosClienteRapido as $idDepartamento => $nombreDepartamento)
                                    <option value="{{ $idDepartamento }}">{{ $nombreDepartamento }}</option>
                                @empty
                                    <option value="" disabled>No hay departamentos registrados</option>
                                @endforelse
                            </select>
                            <div class="invalid-feedback" id="error_cliente_id_departamento"></div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cliente_id_ciudad" class="font-weight-bold">Ciudad <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-city"></i></span></div>
                            <select id="cliente_id_ciudad" class="form-control">
                                <option value="">Seleccione una ciudad</option>
                                @forelse ($ciudadesClienteRapido as $idCiudad => $nombreCiudad)
                                    <option value="{{ $idCiudad }}">{{ $nombreCiudad }}</option>
                                @empty
                                    <option value="" disabled>No hay ciudades registradas</option>
                                @endforelse
                            </select>
                            <div class="invalid-feedback" id="error_cliente_id_ciudad"></div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light border mb-0 py-2">
                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Departamento y ciudad son independientes.</small>
                </div>
            </div>

            <div class="modal-footer cliente-rapido-footer">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Cancelar</button>
                <button type="button" class="btn btn-success px-4" id="btnGuardarClienteRapido">
                    <span class="cliente-save-normal"><i class="fas fa-save mr-1"></i> Guardar cliente</span>
                    <span class="cliente-save-loading d-none"><i class="fas fa-spinner fa-spin mr-1"></i> Guardando...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .cliente-rapido-content { border: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(31,45,61,.25); }
    .cliente-rapido-header { border-bottom: 0; padding: 20px 24px; color: #fff; background: linear-gradient(135deg,#1f6fb2 0%,#17a2b8 100%); }
    .cliente-rapido-icon { width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.18); font-size: 20px; }
    .cliente-rapido-subtitle { color: rgba(255,255,255,.88); }
    .cliente-rapido-header .close { opacity: .9; text-shadow: none; }
    .cliente-rapido-footer { border-top: 1px solid #eef1f4; padding: 16px 24px; }
    #clienteRapidoModal .input-group-text { min-width: 42px; justify-content: center; }
    #clienteRapidoModal .invalid-feedback { width: 100%; }
</style>

<script>
    $(function () {
        function limpiarErroresCliente() {
            $('#clienteRapidoModal .is-invalid').removeClass('is-invalid');
            $('#clienteRapidoModal .invalid-feedback').text('');
            $('#clienteRapidoAlert').addClass('d-none').text('');
        }

        function mostrarAlertaCliente(mensaje) {
            $('#clienteRapidoAlert').removeClass('d-none').text(mensaje || 'No se pudo procesar la solicitud.');
        }

        function mostrarErroresCliente(errores, mensaje) {
            limpiarErroresCliente();
            let encontroCampo = false;

            if (errores) {
                Object.keys(errores).forEach(function (campo) {
                    const $campo = $('#cliente_' + campo);
                    const $error = $('#error_cliente_' + campo);
                    const mensajes = Array.isArray(errores[campo]) ? errores[campo] : [errores[campo]];
                    if ($campo.length) {
                        $campo.addClass('is-invalid');
                        encontroCampo = true;
                    }
                    if ($error.length) {
                        $error.text(mensajes[0] || 'Dato inválido.');
                    }
                });
            }

            if (!encontroCampo && mensaje) {
                mostrarAlertaCliente(mensaje);
            }
        }

        $('#clienteRapidoModal').on('shown.bs.modal', function () {
            limpiarErroresCliente();
            $('#cliente_cli_ci').trigger('focus');
        });

        $('#clienteRapidoModal').on('hidden.bs.modal', function () {
            limpiarErroresCliente();
            $('#cliente_cli_ci, #cliente_cli_nombre, #cliente_cli_apellido, #cliente_cli_telefono, #cliente_cli_direccion').val('');
            $('#cliente_id_departamento, #cliente_id_ciudad').val('');
        });

        $('#cliente_cli_ci').on('input', function () {
            this.value = this.value.replace(/[^0-9-]/g, '').substring(0, 12);
        });

        $('#clienteRapidoModal input, #clienteRapidoModal select').on('input change', function () {
            $(this).removeClass('is-invalid');
            $('#error_' + $(this).attr('id')).text('');
        });

        $('#btnGuardarClienteRapido').on('click', async function () {
            limpiarErroresCliente();

            const $boton = $(this);
            const payload = {
                cli_ci: $('#cliente_cli_ci').val().trim(),
                cli_nombre: $('#cliente_cli_nombre').val().trim(),
                cli_apellido: $('#cliente_cli_apellido').val().trim(),
                cli_telefono: $('#cliente_cli_telefono').val().trim(),
                cli_direccion: $('#cliente_cli_direccion').val().trim(),
                id_departamento: $('#cliente_id_departamento').val(),
                id_ciudad: $('#cliente_id_ciudad').val()
            };

            $boton.prop('disabled', true);
            $boton.find('.cliente-save-normal').addClass('d-none');
            $boton.find('.cliente-save-loading').removeClass('d-none');

            try {
                const response = await fetch('{{ route('pedido_compras.clientes.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json().catch(function () { return {}; });

                if (!response.ok) {
                    mostrarErroresCliente(data.errors || null, data.message || 'No se pudo registrar el cliente.');
                    return;
                }

                const cliente = data.cliente;
                const $selectCliente = $('select[name="id_cliente"]');
                $selectCliente.find('option[value="' + cliente.id + '"]').remove();
                $selectCliente.append(new Option(cliente.texto, cliente.id, true, true));
                $selectCliente.val(String(cliente.id)).trigger('change');

                $('#clienteRapidoModal').modal('hide');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cliente creado',
                        text: 'El cliente quedó seleccionado en el pedido.',
                        timer: 1800,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } catch (error) {
                mostrarAlertaCliente('No se pudo conectar con el servidor.');
            } finally {
                $boton.prop('disabled', false);
                $boton.find('.cliente-save-normal').removeClass('d-none');
                $boton.find('.cliente-save-loading').addClass('d-none');
            }
        });
    });
</script>
