<!-- Estilos personalizados -->
<style>
    .table {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 12px 15px;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table-bordered {
        border: 1px solid #ddd;
    }

    .btn-group .btn {
        margin: 0 5px;
        transition: all 0.3s ease;
    }

    .btn-group .btn:hover {
        transform: translateY(-2px);
    }

    .card-footer {
        border-top: 1px solid #ddd;
        background-color: #f7f7f7;
    }

    .card-footer .float-right {
        margin-top: 5px;
    }

    .btn-info,
    .btn-danger {
        transition: background-color 0.3s, transform 0.2s ease-in-out;
    }

    .btn-info:hover,
    .btn-danger:hover {
        transform: scale(1.1);
    }

    .thead-dark {
        background-color: #343a40;
        color: white;
    }

    /* Opcional: mejorar la apariencia de la tabla al pasar el ratón sobre las celdas */
    .table td,
    .table th {
        border-top: 1px solid #e3e3e3;
    }
</style>
<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered mb-0" id="clientes-table">
            <thead class="thead-dark text-center">
                <tr>
                    <th>#</th>
                    <th style="width: 10%">Nro. Documento</th>
                    <th>Cliente</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Departamento</th>
                    <th>Ciudad</th>
                    <th colspan="3" class="text-center">Operaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <td class="text-center" style="width: 2%;">
                            {{ $cliente->id_cliente }}
                        </td>

                        <td class="text-center" style="width: 10%;">
                            {{ $cliente->cli_ci }}
                        </td>

                        <td style="width: 24%;">
                            {{ $cliente->cli_nombre }} {{ $cliente->cli_apellido }}
                        </td>

                        <td style="width: 27%;">
                            {{ $cliente->cli_direccion }}
                        </td>

                        <td class="text-center" style="width: 8%;">
                            {{ $cliente->cli_telefono }}
                        </td>

                        <td class="text-center" style="width: 10%;">
                            {{ $cliente->dep_descripcion }}
                        </td>

                        <td class="text-center" style="width: 19%;">
                            {{ $cliente->ciu_descripcion }}
                        </td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['clientes.destroy', $cliente->id_cliente], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                @can('clientes edit')
                                    <a href="{{ route('clientes.edit', [$cliente->id_cliente]) }}"
                                        class='btn btn-primary btn-sg' data-toggle="tooltip" title="Editar">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('clientes destroy')
                                    {!! Form::button('<i class="far fa-trash-alt"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-danger btn-sg alert-delete',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Eliminar',
                                    ]) !!}
                                @endcan
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix bg-light">
        <div class="float-left text-muted">
            Mostrando {{ $clientes->firstItem() }} -
            {{ $clientes->lastItem() }} de
            {{ $clientes->total() }} registros
        </div>
        <div class="float-right">
            {{ $clientes->links() }}
        </div>
    </div>
</div>
