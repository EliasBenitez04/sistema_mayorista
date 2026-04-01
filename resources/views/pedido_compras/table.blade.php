<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered text-nowrap" id="pedido_compras-table">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th>Nro Pedido</th>
                    <th>Fecha Pedido</th>
                    <th>Cliente</th>
                    <th>Cant. Artículos</th>
                    <th>Total Pedido</th>
                    <th>Realizado Por</th>
                    <th>Estado</th>
                    <th>Confirmado Por</th>
                    <th colspan="3">Operaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedido_compras as $pedido)
                    <tr class="text-center">
                        <td>{{ $pedido->nro_pedido }}</td>
                        <td>{{ \Carbon\Carbon::parse($pedido->ped_fecha)->format('d/m/Y') }}</td>
                        <td>{{ $pedido->cliente }}</td>
                        <td>{{ $pedido->total_cantidad }}</td>
                        <td>GS. {{ number_format($pedido->ped_total, 0, ',', '.') }}</td>
                        <td>{{ $pedido->usuario }}</td>
                        <td>
                            <span
                                class="badge badge-{{ $pedido->ped_estado == 'CONFIRMADO' ? 'success' : ($pedido->ped_estado == 'ANULADO' ? 'danger' : 'warning') }}">
                                {{ $pedido->ped_estado }}
                            </span>
                        </td>
                        <td>{{ $pedido->confirmado_por ?? '—' }}</td>
                        <td class="text-center" style="min-width: 180px; white-space: nowrap;">
                            <div class="btn-group" role="group">
                                @if ($pedido->ped_estado === 'PENDIENTE')
                                    {!! Form::open([
                                        'route' => ['pedido_compras.confirm', $pedido->id_pedido],
                                        'method' => 'patch',
                                        'id' => 'confirm-form-' . $pedido->id_pedido,
                                        'class' => 'd-inline',
                                    ]) !!}

                                    {!! Form::button('<i class="far fa-check-circle"></i>', [
                                        'type' => 'button',
                                        'class' => 'btn btn-success btn-sg alert-confirm',
                                        'data-id' => $pedido->id_pedido,
                                        'title' => 'Confirmar Pedido',
                                    ]) !!}

                                    {!! Form::close() !!}
                                @endif

                                @if ($pedido->ped_estado === 'CONFIRMADO')
                                    <a href="{{ route('pedido_compras.imprimir', [$pedido->id_pedido]) }}"
                                        class="btn btn-warning btn-sg" title="Imprimir pedido">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('pedido.export', [$pedido->id_pedido]) }}"
                                        class="btn btn-success btn-sg" title="Exportar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                @endif

                                <a href="{{ route('pedido_compras.show', [$pedido->id_pedido]) }}"
                                    class="btn btn-info btn-sg" title="Ver detalles">
                                    <i class="far fa-eye"></i>
                                </a>

                                @if ($pedido->ped_estado !== 'ANULADO')
                                    {!! Form::open([
                                        'route' => ['pedido_compras.destroy', $pedido->id_pedido],
                                        'method' => 'delete',
                                        'class' => 'd-inline',
                                        'id' => 'delete-form-' . $pedido->id_pedido,
                                    ]) !!}
                                    {!! Form::button('<i class="fas fa-trash-alt"></i>', [
                                        'type' => 'button',
                                        'class' => 'btn btn-danger btn-sg alert-delete',
                                        'data-id' => $pedido->id_pedido,
                                        'title' => 'Anular',
                                    ]) !!}
                                    {!! Form::close() !!}
                                @endif

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix bg-light">
        <div class="float-left text-muted">
            Mostrando {{ $pedido_compras->firstItem() }} - {{ $pedido_compras->lastItem() }} de
            {{ $pedido_compras->total() }} registros
        </div>
        <div class="float-right">
            {{ $pedido_compras->links() }}
        </div>
    </div>
</div>
