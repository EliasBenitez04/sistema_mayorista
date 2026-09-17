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

            <div class="modal-body cliente-rapido-body">
                <div id="clienteRapidoAlert" class="alert alert-danger d-none mb-4" role="alert"></div>

                <div class="cliente-section mb-4">
                    <div class="cliente-section-title">
                        <span class="cliente-section-icon"><i class="fas fa-id-card"></i></span>
                        Datos del cliente
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_cli_ci" class="cliente-label">Nro. de CI / R.U.C. <span class="text-danger">*</span></label>
                            <div class="input-group cliente-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_ci" class="form-control" maxlength="12"
                                    placeholder="Ej: 1234567-8" autocomplete="off" inputmode="numeric">
                            </div>
                            <div class="invalid-feedback d-block" id="error_cliente_cli_ci"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cliente_cli_nombre" class="cliente-label">Nombres o Razón Social <span class="text-danger">*</span></label>
                            <div class="input-group cliente-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_nombre" class="form-control" maxlength="45"
                                    placeholder="Ingrese nombres o razón social" autocomplete="off">
                            </div>
                            <div class="invalid-feedback d-block" id="error_cliente_cli_nombre"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cliente_cli_apellido" class="cliente-label">Apellidos</label>
                            <div class="input-group cliente-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_apellido" class="form-control" maxlength="45"
                                    placeholder="Ingrese apellidos" autocomplete="off">
                            </div>
                            <div class="invalid-feedback d-block" id="error_cliente_cli_apellido"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cliente_cli_telefono" class="cliente-label">Teléfono <span class="text-danger">*</span></label>
                            <div class="input-group cliente-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_telefono" class="form-control" maxlength="12"
                                    placeholder="Ej: 0981123456" autocomplete="off">
                            </div>
                            <div class="invalid-feedback d-block" id="error_cliente_cli_telefono"></div>
                        </div>
                    </div>
                </div>

                <div class="cliente-section">
                    <div class="cliente-section-title">
                        <span class="cliente-section-icon"><i class="fas fa-map-marker-alt"></i></span>
                        Ubicación
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="cliente_cli_direccion" class="cliente-label">Dirección <span class="text-danger">*</span></label>
                            <div class="input-group cliente-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <input type="text" id="cliente_cli_direccion" class="form-control" maxlength="100"
                                    placeholder="Ingrese la dirección del cliente" autocomplete="off">
                            </div>
                            <div class="invalid-feedback d-block" id="error_cliente_cli_direccion"></div>
                        </div>

                        <div class="col-md-6 mb-3 cliente-select-wrap" id="wrap_cliente_id_departamento">
                            <label for="cliente_id_departamento" class="cliente-label">Departamento <span class="text-danger">*</span></label>
                            <select id="cliente_id_departamento" class="form-control cliente-modal-select">
                                <option value="">Seleccione un departamento</option>
                                @forelse ($departamentosClienteRapido as $idDepartamento => $nombreDepartamento)
                                    <option value="{{ $idDepartamento }}">{{ $nombreDepartamento }}</option>
                                @empty
                                    <option value="" disabled>No hay departamentos registrados</option>
                                @endforelse
                            </select>
                            <div class="invalid-feedback d-block" id="error_cliente_id_departamento"></div>
                        </div>

                        <div class="col-md-6 mb-3 cliente-select-wrap" id="wrap_cliente_id_ciudad">
                            <label for="cliente_id_ciudad" class="cliente-label">Ciudad <span class="text-danger">*</span></label>
                            <select id="cliente_id_ciudad" class="form-control cliente-modal-select">
                                <option value="">Seleccione una ciudad</option>
                                @forelse ($ciudadesClienteRapido as $idCiudad => $nombreCiudad)
                                    <option value="{{ $idCiudad }}">{{ $nombreCiudad }}</option>
                                @empty
                                    <option value="" disabled>No hay ciudades registradas</option>
                                @endforelse
                            </select>
                            <div class="invalid-feedback d-block" id="error_cliente_id_ciudad"></div>
                        </div>
                    </div>

                    <div class="cliente-info-box">
                        <i class="fas fa-info-circle mr-2"></i>
                        Departamento y ciudad se seleccionan de forma independiente.
                    </div>
                </div>
            </div>

            <div class="modal-footer cliente-rapido-footer">
                <button type="button" class="btn btn-light cliente-btn-cancelar" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success cliente-btn-guardar" id="btnGuardarClienteRapido">
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
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 25px 70px rgba(31, 45, 61, .28);
    }

    .cliente-rapido-header {
        border: 0;
        padding: 22px 26px;
        color: #fff;
        background: linear-gradient(135deg, #1769aa 0%, #168aad 55%, #17a2b8 100%);
    }

    .cliente-rapido-header .close {
        color: #fff;
        opacity: .9;
        text-shadow: none;
        outline: none;
        font-size: 28px;
    }

    .cliente-rapido-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .17);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .15);
        font-size: 21px;
    }

    .cliente-rapido-subtitle {
        color: rgba(255, 255, 255, .86);
    }

    .cliente-rapido-body {
        padding: 24px 26px 18px;
        background: #f7f9fc;
    }

    .cliente-section {
        background: #fff;
        border: 1px solid #e4eaf0;
        border-radius: 14px;
        padding: 20px 20px 8px;
        box-shadow: 0 3px 12px rgba(31, 45, 61, .04);
    }

    .cliente-section-title {
        display: flex;
        align-items: center;
        color: #34495e;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .45px;
        margin-bottom: 18px;
    }

    .cliente-section-icon {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 9px;
        border-radius: 8px;
        color: #168aad;
        background: #eaf7fb;
    }

    .cliente-label {
        display: block;
        margin-bottom: 7px;
        color: #455a64;
        font-size: 13px;
        font-weight: 700;
    }

    .cliente-input-group {
        border-radius: 9px;
        box-shadow: 0 2px 8px rgba(31, 45, 61, .06);
    }

    .cliente-input-group .input-group-text {
        min-width: 43px;
        justify-content: center;
        color: #607d8b;
        background: #f8fafc;
        border-color: #dce3ea;
        border-radius: 9px 0 0 9px;
    }

    .cliente-input-group .form-control {
        height: 42px;
        border-color: #dce3ea;
        border-left: 0;
        border-radius: 0 9px 9px 0;
    }

    .cliente-input-group .form-control:focus {
        border-color: #80bdff;
        box-shadow: none;
    }

    #clienteRapidoModal .select2-container {
        width: 100% !important;
    }

    #clienteRapidoModal .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #dce3ea;
        border-radius: 9px;
        box-shadow: 0 2px 8px rgba(31, 45, 61, .06);
    }

    #clienteRapidoModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 14px;
        padding-right: 36px;
        color: #495057;
    }

    #clienteRapidoModal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px;
    }

    #clienteRapidoModal .select2-container--default.select2-container--focus .select2-selection--single,
    #clienteRapidoModal .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #80bdff;
    }

    .cliente-select-wrap.is-invalid-select .select2-selection--single {
        border-color: #dc3545 !important;
    }

    .select2-container--open .select2-dropdown {
        border-color: #80bdff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(31, 45, 61, .15);
    }

    .cliente-info-box {
        margin-bottom: 12px;
        padding: 10px 12px;
        border-radius: 9px;
        color: #607d8b;
        background: #f5f9fc;
        border: 1px solid #e5edf3;
        font-size: 12px;
    }

    .cliente-rapido-footer {
        border-top: 1px solid #e9eef3;
        padding: 16px 26px;
        background: #fff;
    }

    .cliente-btn-cancelar,
    .cliente-btn-guardar {
        min-width: 135px;
        border-radius: 9px;
        font-weight: 600;
    }

    #clienteRapidoModal .invalid-feedback {
        min-height: 17px;
        margin-top: 4px;
        font-size: 12px;
    }

    @media (max-width: 767.98px) {
        .cliente-rapido-body {
            padding: 18px;
        }

        .cliente-section {
            padding: 16px 16px 5px;
        }

        .cliente-rapido-subtitle {
            display: none;
        }
    }
</style>

@push('page_scripts')
<script>
    $(document).ready(function() {
        const $modalCliente = $('#clienteRapidoModal');
        const $btnGuardar = $('#btnGuardarClienteRapido');

        function inicializarSelectsCliente() {
            if (typeof $.fn.select2 === 'undefined') {
                return;
            }

            $modalCliente.find('.cliente-modal-select').each(function() {
                const $select = $(this);

                if ($select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                $select.select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: $select.find('option:first').text(),
                    dropdownParent: $modalCliente
                });
            });
        }

        function limpiarErroresCliente() {
            $modalCliente.find('.is-invalid').removeClass('is-invalid');
            $modalCliente.find('.is-invalid-select').removeClass('is-invalid-select');
            $modalCliente.find('.invalid-feedback').text('');
            $('#clienteRapidoAlert').addClass('d-none').text('');
        }

        function mostrarAlertaCliente(mensaje) {
            $('#clienteRapidoAlert')
                .removeClass('d-none')
                .text(mensaje || 'No se pudo procesar la solicitud.');
        }

        function mostrarErroresCliente(errores, mensaje) {
            limpiarErroresCliente();
            let encontroCampo = false;

            if (errores) {
                Object.keys(errores).forEach(function(campo) {
                    const $campo = $('#cliente_' + campo);
                    const $error = $('#error_cliente_' + campo);
                    const mensajes = Array.isArray(errores[campo]) ? errores[campo] : [errores[campo]];

                    if ($campo.length) {
                        encontroCampo = true;

                        if ($campo.is('select')) {
                            $('#wrap_cliente_' + campo).addClass('is-invalid-select');
                        } else {
                            $campo.addClass('is-invalid');
                        }
                    }

                    if ($error.length) {
                        $error.text(mensajes[0] || 'Dato inválido.');
                    }
                });
            }

            if (!encontroCampo) {
                mostrarAlertaCliente(mensaje || 'No se pudo registrar el cliente.');
            }
        }

        function estadoGuardando(activo) {
            $btnGuardar.prop('disabled', activo);
            $btnGuardar.find('.cliente-save-normal').toggleClass('d-none', activo);
            $btnGuardar.find('.cliente-save-loading').toggleClass('d-none', !activo);
        }

        function limpiarFormularioCliente() {
            limpiarErroresCliente();
            $('#cliente_cli_ci, #cliente_cli_nombre, #cliente_cli_apellido, #cliente_cli_telefono, #cliente_cli_direccion').val('');
            $('#cliente_id_departamento, #cliente_id_ciudad').val(null).trigger('change');
        }

        $modalCliente.on('shown.bs.modal', function() {
            inicializarSelectsCliente();
            limpiarErroresCliente();
            $('#cliente_cli_ci').trigger('focus');
        });

        $modalCliente.on('hidden.bs.modal', function() {
            limpiarFormularioCliente();
        });

        $('#cliente_cli_ci').on('input', function() {
            this.value = this.value.replace(/[^0-9-]/g, '').substring(0, 12);
        });

        $modalCliente.on('input change', 'input, select', function() {
            const id = $(this).attr('id');
            $(this).removeClass('is-invalid');
            $(this).closest('.cliente-select-wrap').removeClass('is-invalid-select');
            if (id) {
                $('#error_' + id).text('');
            }
        });

        $btnGuardar.on('click', function() {
            limpiarErroresCliente();

            const payload = {
                _token: '{{ csrf_token() }}',
                cli_ci: $('#cliente_cli_ci').val().trim(),
                cli_nombre: $('#cliente_cli_nombre').val().trim(),
                cli_apellido: $('#cliente_cli_apellido').val().trim(),
                cli_telefono: $('#cliente_cli_telefono').val().trim(),
                cli_direccion: $('#cliente_cli_direccion').val().trim(),
                id_departamento: $('#cliente_id_departamento').val(),
                id_ciudad: $('#cliente_id_ciudad').val()
            };

            estadoGuardando(true);

            $.ajax({
                url: '{{ route('pedido_compras.clientes.store') }}',
                type: 'POST',
                data: payload,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(data) {
                    if (!data || !data.cliente) {
                        mostrarAlertaCliente('El servidor respondió, pero no devolvió los datos del cliente creado.');
                        return;
                    }

                    const cliente = data.cliente;
                    const $selectCliente = $('select[name="id_cliente"]');

                    $selectCliente.find('option[value="' + cliente.id + '"]').remove();
                    $selectCliente.append(new Option(cliente.texto, cliente.id, true, true));
                    $selectCliente.val(String(cliente.id)).trigger('change');

                    $modalCliente.modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Cliente creado',
                        text: cliente.texto + ' quedó seleccionado en el pedido.',
                        timer: 2200,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                },
                error: function(xhr) {
                    const data = xhr.responseJSON || {};

                    if (xhr.status === 422) {
                        mostrarErroresCliente(data.errors || {}, data.message);
                        return;
                    }

                    let mensaje = data.message || 'No se pudo registrar el cliente.';

                    if (data.debug) {
                        mensaje += ' Detalle: ' + data.debug;
                    }

                    mostrarAlertaCliente(mensaje);
                },
                complete: function() {
                    estadoGuardando(false);
                }
            });
        });
    });
</script>
@endpush
