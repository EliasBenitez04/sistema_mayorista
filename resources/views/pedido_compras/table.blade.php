<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered text-nowrap" id="pedido_compras-table">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th class="sortable">Nro Pedido</th>
                    <th class="sortable">Fecha Pedido</th>
                    <th class="sortable">Cliente</th>
                    <th class="sortable">Cant. Artículos</th>
                    <th class="sortable">Total Pedido</th>
                    <th class="sortable">Realizado Por</th>
                    <th class="sortable">Estado</th>
                    <th class="sortable">Obs</th>
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
                        <td>{{ $pedido->obs ?? '—' }}</td>
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

                                @if (!in_array(trim($pedido->ped_estado), ['CONFIRMADO', 'ANULADO']))
                                    <a href="{{ route('pedido_compras.edit', [$pedido->id_pedido]) }}"
                                        class="btn btn-primary btn-sg" title="Editar pedido">
                                        <i class="far fa-edit"></i>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const table = document.getElementById("pedido_compras-table");
        const headers = table.querySelectorAll("thead th.sortable");

        let currentSort = {
            index: null,
            direction: "asc"
        };

        function toNumber(value) {
            return parseFloat(
                (value || "")
                .toString()
                .replace(/[^\d,.-]/g, '') // 🔥 elimina todo menos números
                .replace(/\./g, '') // quita miles
                .replace(',', '.') // decimal
            ) || 0;
        }

        function toDate(value) {
            if (!value) return 0;
            const parts = value.split('/');
            if (parts.length !== 3) return 0;
            return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
        }

        headers.forEach((th, index) => {

            if (th.getAttribute("colspan")) return;

            th.style.cursor = "pointer";

            const icon = document.createElement("span");
            icon.innerHTML = " ↕";
            icon.style.fontSize = "12px";
            th.appendChild(icon);

            th.addEventListener("click", function() {

                if (currentSort.index === index) {
                    currentSort.direction = currentSort.direction === "asc" ? "desc" : "asc";
                } else {
                    currentSort.index = index;
                    currentSort.direction = "asc";
                }

                sortTable(index, currentSort.direction);
                updateIcons(headers, index, currentSort.direction);
            });
        });

        function sortTable(columnIndex, direction) {

            const tbody = table.querySelector("tbody");
            const rows = Array.from(tbody.querySelectorAll("tr"));

            rows.sort((a, b) => {

                let aText = a.children[columnIndex]?.innerText.trim() || "";
                let bText = b.children[columnIndex]?.innerText.trim() || "";

                // 🔥 FECHA (columna 1 normalmente)
                if (columnIndex === 1) {
                    aText = toDate(aText);
                    bText = toDate(bText);
                }

                // 🔥 NUMEROS (TOTAL + CANTIDADES)
                else if (
                    columnIndex === 3 || // cantidad
                    columnIndex === 4 // total
                ) {
                    aText = toNumber(aText);
                    bText = toNumber(bText);
                }

                if (aText < bText) return direction === "asc" ? -1 : 1;
                if (aText > bText) return direction === "asc" ? 1 : -1;

                return 0;
            });

            rows.forEach(r => tbody.appendChild(r));
        }

        function updateIcons(headers, activeIndex, direction) {

            headers.forEach((th, i) => {

                let icon = th.querySelector("span");

                if (!icon) return;

                icon.innerHTML =
                    i === activeIndex ?
                    (direction === "asc" ? " 🔼" : " 🔽") :
                    " ↕";
            });
        }

    });
</script>
