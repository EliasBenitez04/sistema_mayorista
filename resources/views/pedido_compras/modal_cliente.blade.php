@php
    $departamentosClienteRapido = \Illuminate\Support\Facades\DB::table('departamento')
        ->select('id_departamento', 'dep_descripcion')
        ->orderBy('dep_descripcion')
        ->get();

    $ciudadesClienteRapido = \Illuminate\Support\Facades\DB::table('ciudad')
        ->select('id_ciudad', 'ciu_descripcion')
        ->orderBy('ciu_descripcion')
        ->get();
@endphp

<div class="modal fade" id="clienteRapidoModal" tabindex="-1" role="dialog" aria-labelledby="clienteRapidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content cliente-rapido-content">
            <div class="modal-header cliente-rapido-header">
                <div class="d-flex align-items-center">
                    <div class="cliente-rapido-icon mr-3">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="clienteRapidoModalLabel">Registrar nuevo cliente</h5>
                        <small class="cliente-rapido-subtitle">Complete los datos y continúe con el pedido sin salir de esta pantalla.</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <div id="clienteRapidoAlert" class="alert alert-danger d-none mb-4" role="alert"></div>

                <div class="cliente-rapido-section mb-4">
                    <div class="cliente-rapido-section-title">
                        <i class="fas fa-id-card mr-2"></i>Identificación
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_cli_ci" class="font-weight-bold">Nro. de CI / R.U.C. <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_ci" class="form-control" maxlength="12"
                                    placeholder="Ej: 1234567-8" autocomplete="off" inputmode="numeric">
                                <div class="invalid-feedback" id="error_cliente_cli_ci"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cliente_cli_nombre" class="font-weight-bold">Nombres o Razón Social <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_nombre" class="form-control" maxlength="45"
                                    placeholder="Ingrese nombres o razón social" autocomplete="off">
                                <div class="invalid-feedback" id="error_cliente_cli_nombre"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cliente_cli_apellido" class="font-weight-bold">Apellidos</label>
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_apellido" class="form-control" maxlength="45"
                                    placeholder="Ingrese apellidos" autocomplete="off">
                                <div class="invalid-feedback" id="error_cliente_cli_apellido"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cliente_cli_telefono" class="font-weight-bold">Teléfono <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_telefono" class="form-control" maxlength="12"
                                    placeholder="Ej: 0981123456" autocomplete="off">
                                <div class="invalid-feedback" id="error_cliente_cli_telefono"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cliente-rapido-section">
                    <div class="cliente-rapido-section-title">
                        <i class="fas fa-map-marked-alt mr-2"></i>Ubicación y contacto
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="cliente_cli_direccion" class="font-weight-bold">Dirección <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_direccion" class="form-control" maxlength="100"
                                    placeholder="Ingrese la dirección del cliente" autocomplete="off">
                                <div class="invalid-feedback" id="error_cliente_cli_direccion"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cliente_id_departamento" class="font-weight-bold">Departamento <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map"></i></span>
                                </div>
                                <select id="cliente_id_departamento" class="form-control">
                                    <option value="">Seleccione un departamento</option>
                                    @foreach ($departamentosClienteRapido as $departamento)
                                        <option value="{{ $departamento->id_departamento }}">
                                            {{ $departamento->dep_descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error_cliente_id_departamento"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cliente_id_ciudad" class="font-weight-bold">Ciudad <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select id="cliente_id_ciudad" class="form-control">
                                    <option value="">Seleccione una ciudad</option>
                                    @foreach ($ciudadesClienteRapido as $ciudad)
                                        <option value="{{ $ciudad->id_ciudad }}">
                                            {{ $ciudad->ciu_descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error_cliente_id_ciudad"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cliente-rapido-help mt-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Departamento y ciudad se seleccionan de forma independiente.
                </div>
            </div>

            <div class="modal-footer cliente-rapido-footer">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success px-4" id="btnGuardarClienteRapido">
                    <span class="cliente-save-normal"><i class="fas fa-save mr-1"></i> Guardar cliente</span>
                    <span class="cliente-save-loading d-none"><i class="fas fa-spinner fa-spin mr-1"></i> Guardando...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .cliente-rapido-content {
        border: 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(31, 45, 61, .25);
    }

    .cliente-rapido-header {
        border-bottom: 0;
        padding: 20px 24px;
        color: #fff;
        background: linear-gradient(135deg, #1f6fb2 0%, #17a2b8 100%);
    }

    .cliente-rapido-header .close {
        opacity: .9;
        text-shadow: none;
        outline: none;
    }

    .cliente-rapido-icon {
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .18);
        font-size: 20px;
    }

    .cliente-rapido-subtitle {
        color: rgba(255, 255, 255, .88);
    }

    .cliente-rapido-section {
        border: 1px solid #e7edf3;
        border-radius: 12px;
        padding: 18px 18px 4px;
        background: #fbfcfe;
    }

    .cliente-rapido-section-title {
        color: #34495e;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 16px;
    }

    .cliente-rapido-section .input-group-text {
        min-width: 42px;
        justify-content: center;
        color: #5b6b7c;
        background: #fff;
        border-right: 0;
    }

    .cliente-rapido-section .form-control {
        min-height: 40px;
    }

    .cliente-rapido-section .input-group .form-control,
    .cliente-rapido-section select.form-control {
        border-left: 0;
    }

    .cliente-rapido-section .form-control:focus {
        box-shadow: none;
        border-color: #80bdff;
    }

    .cliente-rapido-help {
        color: #66788a;
        font-size: 13px;
        padding: 10px 2px 0;
    }

    .cliente-rapido-footer {
        border-top: 1px solid #eef1f4;
        background: #fff;
        padding: 16px 24px;
    }

    #btnNuevoClientePedido {
        border-radius: 8px;
        font-weight: 600;
    }

    #clienteRapidoModal .invalid-feedback {
        width: 100%;
    }

    @media (max-width: 767.98px) {
        .cliente-rapido-header {
            padding: 16px;
        }

        .cliente-rapido-subtitle {
            display: none;
        }

        .cliente-rapido-section {
            padding: 14px 14px 2px;
        }
    }
</style>

<script>
    (function() {
        function escaparHtml(texto) {
            return $('<div>').text(texto == null ? '' : texto).html();
        }

        function limpiarErroresCliente() {
            $('#clienteRapidoModal .is-invalid').removeClass('is-invalid');
            $('#clienteRapidoModal .invalid-feedback').text('');
            $('#clienteRapidoAlert').addClass('d-none').text('');
        }

        function mostrarAlertaCliente(mensaje) {
            $('#clienteRapidoAlert').removeClass('d-none').text(mensaje || 'Ocurrió un error al procesar la solicitud.');
        }

        function mostrarErroresCliente(errores, mensaje) {
            limpiarErroresCliente();

            let tieneErroresDeCampo = false;

            if (errores) {
                Object.keys(errores).forEach(function(campo) {
                    const $campo = $('#cliente_' + campo);
                    const $error = $('#error_cliente_' + campo);
                    const mensajes = Array.isArray(errores[campo]) ? errores[campo] : [errores[campo]];

                    if ($campo.length) {
                        $campo.addClass('is-invalid');
                        tieneErroresDeCampo = true;
                    }

                    if ($error.length) {
                        $error.text(mensajes[0] || 'Dato inválido.');
                    }
                });
            }

            if (!tieneErroresDeCampo && mensaje) {
                mostrarAlertaCliente(mensaje);
            }
        }

        function limpiarClienteRapido() {
            limpiarErroresCliente();
            $('#cliente_cli_ci, #cliente_cli_nombre, #cliente_cli_apellido, #cliente_cli_direccion, #cliente_cli_telefono').val('');
            $('#cliente_id_departamento').val('');
            $('#cliente_id_ciudad').val('');
        }

        $(document).ready(function() {
            $('#clienteRapidoModal').on('shown.bs.modal', function() {
                limpiarErroresCliente();
                $('#cliente_cli_ci').trigger('focus');
            });

            $('#clienteRapidoModal').on('hidden.bs.modal', function() {
                limpiarClienteRapido();
            });

            $('#cliente_cli_ci').on('input', function() {
                let valor = $(this).val().replace(/[^0-9-]/g, '');
                $(this).val(valor.substring(0, 12));
            });

            $('#clienteRapidoModal input, #clienteRapidoModal select').on('input change', function() {
                $(this).removeClass('is-invalid');
                $('#error_' + $(this).attr('id')).text('');
            });

            $('#btnGuardarClienteRapido').on('click', async function() {
                limpiarErroresCliente();

                const $boton = $(this);
                const payload = {
                    cli_ci: $('#cliente_cli_ci').val().trim(),
                    cli_nombre: $('#cliente_cli_nombre').val().trim(),
                    cli_apellido: $('#cliente_cli_apellido').val().trim(),
                    cli_direccion: $('#cliente_cli_direccion').val().trim(),
                    cli_telefono: $('#cliente_cli_telefono').val().trim(),
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

                    const data = await response.json().catch(function() {
                        return {};
                    });

                    if (!response.ok) {
                        mostrarErroresCliente(data.errors || null, data.message || 'No se pudo registrar el cliente.');
                        return;
                    }

                    const cliente = data.cliente;
                    const $selectCliente = $('select[name="id_cliente"]');

                    $selectCliente.find('option[value="' + cliente.id + '"]').remove();

                    const nuevaOpcion = new Option(cliente.texto, cliente.id, true, true);
                    $selectCliente.append(nuevaOpcion).val(String(cliente.id)).trigger('change');

                    $('#clienteRapidoModal').modal('hide');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cliente creado',
                            html: '<strong>' + escaparHtml(cliente.texto) + '</strong><br>Ya quedó seleccionado en el pedido.',
                            timer: 2200,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                } catch (error) {
                    mostrarAlertaCliente('No se pudo conectar con el servidor. Verifique la conexión e intente nuevamente.');
                } finally {
                    $boton.prop('disabled', false);
                    $boton.find('.cliente-save-normal').removeClass('d-none');
                    $boton.find('.cliente-save-loading').addClass('d-none');
                }
            });
        });
    })();
</script>
