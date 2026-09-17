<style>
    .pedido-enterprise {
        --pedido-border: #e6ebf1;
        --pedido-text: #263445;
        --pedido-muted: #748094;
        --pedido-head: #f7f9fc;
        --pedido-hover: #f8fbff;
        --pedido-primary: #2563eb;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
    }

    .pedido-table-toolbar {
        min-height: 58px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid var(--pedido-border);
        background: #fff;
    }

    .pedido-table-title {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .pedido-table-title-icon {
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        background: #eef4ff;
        font-size: 15px;
    }

    .pedido-table-title strong {
        display: block;
        color: var(--pedido-text);
        font-size: 14px;
        line-height: 1.2;
    }

    .pedido-table-title small {
        display: block;
        margin-top: 3px;
        color: var(--pedido-muted);
        font-size: 12px;
    }

    .pedido-record-count {
        flex: 0 0 auto;
        padding: 6px 10px;
        border: 1px solid #dbe5f0;
        border-radius: 999px;
        color: #526173;
        background: #f8fafc;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .pedido-table-scroll {
        width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }

    .pedido-enterprise-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
        color: var(--pedido-text);
        font-size: 13px;
    }

    .pedido-enterprise-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        height: 46px;
        padding: 10px 10px;
        vertical-align: middle;
        border-top: 0;
        border-bottom: 1px solid #dfe6ee;
        color: #526173;
        background: var(--pedido-head);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .035em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .pedido-enterprise-table thead th.sortable {
        cursor: pointer;
        user-select: none;
        transition: background-color .15s ease, color .15s ease;
    }

    .pedido-enterprise-table thead th.sortable:hover {
        color: #1d4ed8;
        background: #eef4ff;
    }

    .pedido-sort-icon {
        display: inline-block;
        width: 12px;
        margin-left: 4px;
        color: #a2adba;
        font-size: 10px;
        text-align: center;
    }

    .pedido-enterprise-table tbody td {
        height: 60px;
        padding: 9px 10px;
        vertical-align: middle;
        border-top: 0;
        border-bottom: 1px solid #edf1f5;
        background: #fff;
    }

    .pedido-enterprise-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .pedido-enterprise-table tbody tr {
        transition: background-color .15s ease, box-shadow .15s ease;
    }

    .pedido-enterprise-table tbody tr:hover td {
        background: var(--pedido-hover);
    }

    .pedido-code-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #1d4ed8;
        font-weight: 700;
        white-space: nowrap;
    }

    .pedido-code-link:hover {
        color: #1e40af;
        text-decoration: none;
    }

    .pedido-code-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        background: #eef4ff;
        font-size: 11px;
    }

    .pedido-date {
        color: #475569;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .pedido-client,
    .pedido-user,
    .pedido-obs {
        display: block;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pedido-client {
        color: #1f2937;
        font-weight: 600;
    }

    .pedido-user,
    .pedido-obs {
        color: #64748b;
    }

    .pedido-qty {
        display: inline-flex;
        min-width: 38px;
        height: 28px;
        padding: 0 8px;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #334155;
        background: #f1f5f9;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .pedido-total {
        color: #0f172a;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .pedido-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .035em;
        white-space: nowrap;
    }

    .pedido-status::before {
        content: '';
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .pedido-status-pendiente {
        color: #b45309;
        background: #fff7ed;
        border: 1px solid #fed7aa;
    }

    .pedido-status-confirmado {
        color: #15803d;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    .pedido-status-anulado {
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .pedido-status-default {
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .pedido-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        white-space: nowrap;
    }

    .pedido-actions form {
        margin: 0;
        display: inline-flex;
    }

    .pedido-action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        box-shadow: none !important;
        font-size: 12px;
        transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
    }

    .pedido-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(15, 23, 42, .12) !important;
    }

    .pedido-action-view {
        color: #0369a1;
        background: #f0f9ff;
        border-color: #bae6fd;
    }

    .pedido-action-edit {
        color: #1d4ed8;
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .pedido-action-confirm {
        color: #15803d;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .pedido-action-print {
        color: #a16207;
        background: #fefce8;
        border-color: #fde68a;
    }

    .pedido-action-excel {
        color: #047857;
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .pedido-action-delete {
        color: #b91c1c;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .pedido-empty {
        padding: 50px 20px !important;
        text-align: center;
        color: #64748b;
        background: #fff !important;
    }

    .pedido-empty-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 10px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        background: #f1f5f9;
        font-size: 18px;
    }

    .pedido-table-footer {
        min-height: 58px;
        padding: 10px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-top: 1px solid var(--pedido-border);
        background: #fbfcfe;
    }

    .pedido-table-footer-info {
        color: #64748b;
        font-size: 12px;
        white-space: nowrap;
    }

    .pedido-table-footer .pagination {
        margin-bottom: 0;
    }

    .pedido-table-footer .page-link {
        min-width: 34px;
        text-align: center;
        border-color: #e2e8f0;
        color: #475569;
        font-size: 12px;
    }

    .pedido-table-footer .page-item.active .page-link {
        border-color: #2563eb;
        background: #2563eb;
    }

    @media (max-width: 1399.98px) {
        .pedido-enterprise-table {
            font-size: 12px;
        }

        .pedido-enterprise-table thead th,
        .pedido-enterprise-table tbody td {
            padding-left: 7px;
            padding-right: 7px;
        }

        .pedido-action-btn {
            width: 30px;
            height: 30px;
        }
    }

    @media (max-width: 1199.98px) {
        .pedido-enterprise-table {
            min-width: 1080px;
            table-layout: fixed;
        }
    }

    @media (max-width: 767.98px) {
        .pedido-table-toolbar,
        .pedido-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .pedido-table-footer {
            gap: 10px;
        }

        .pedido-table-footer-info {
            white-space: normal;
        }
    }
</style>

<div class="pedido-enterprise">
    <div class="pedido-table-toolbar">
        <div class="pedido-table-title">
            <span class="pedido-table-title-icon">
                <i class="fas fa-clipboard-list"></i>
            </span>
            <div>
                <strong>Listado de pedidos</strong>
                <small>Seguimiento, estado y operaciones de pedidos de clientes</small>
            </div>
        </div>

        <span class="pedido-record-count">
            <i class="fas fa-database mr-1"></i>
            {{ number_format($pedido_compras->total(), 0, ',', '.') }} registros
        </span>
    </div>

    <div class="pedido-table-scroll">
        <table class="table pedido-enterprise-table" id="pedido_compras-table">
            <colgroup>
                <col style="width: 9%;">
                <col style="width: 9%;">
                <col style="width: 17%;">
                <col style="width: 8%;">
                <col style="width: 11%;">
                <col style="width: 12%;">
                <col style="width: 10%;">
                <col style="width: 11%;">
                <col style="width: 13%;">
            </colgroup>
            <thead>
                <tr>
                    <th class="sortable text-left" data-type="text">
                        Pedido <span class="pedido-sort-icon">↕</span>
                    </th>
                    <th class="sortable text-center" data-type="date">
                        Fecha <span class="pedido-sort-icon">↕</span>
                    </th>
                    <th class="sortable text-left" data-type="text">
                        Cliente <span class="pedido-sort-icon">↕</span>
                    </th>
                    <th class="sortable text-center" data-type="number">
                        Artículos <span class="pedido-sort-icon">↕</span>
                    </th>
                    <th class="sortable text-right" data-type="number">
                        Total <span class="pedido-sort-icon">↕</span>
                    </th>
                    <th class="sortable text-left" data-type="text">
                        Realizado por <span class="pedido-sort-icon">↕</span>
                    </th>
                    <th class="sortable text-center" data-type="text">
                        Estado <span class="pedido-sort-icon">↕</span>
                    </th>
                    <th class="sortable text-left" data-type="text">
                        Observación <span class="pedido-sort-icon">↕</span>
                    </th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pedido_compras as $pedido)
                    @php
                        $estado = trim((string) $pedido->ped_estado);
                        $estadoClase = match ($estado) {
                            'CONFIRMADO' => 'pedido-status-confirmado',
                            'ANULADO' => 'pedido-status-anulado',
                            'PENDIENTE' => 'pedido-status-pendiente',
                            default => 'pedido-status-default',
                        };
                    @endphp

                    <tr>
                        <td class="text-left">
                            <a href="{{ route('pedido_compras.show', [$pedido->id_pedido]) }}"
                                class="pedido-code-link"
                                title="Ver {{ $pedido->nro_pedido }}">
                                <span class="pedido-code-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </span>
                                <span>{{ $pedido->nro_pedido }}</span>
                            </a>
                        </td>

                        <td class="text-center" data-sort-value="{{ \Carbon\Carbon::parse($pedido->ped_fecha)->format('Y-m-d') }}">
                            <span class="pedido-date">
                                {{ \Carbon\Carbon::parse($pedido->ped_fecha)->format('d/m/Y') }}
                            </span>
                        </td>

                        <td class="text-left">
                            <span class="pedido-client" title="{{ $pedido->cliente ?: 'Sin cliente' }}">
                                {{ $pedido->cliente ?: 'Sin cliente' }}
                            </span>
                        </td>

                        <td class="text-center" data-sort-value="{{ (int) $pedido->total_cantidad }}">
                            <span class="pedido-qty">
                                {{ number_format($pedido->total_cantidad, 0, ',', '.') }}
                            </span>
                        </td>

                        <td class="text-right" data-sort-value="{{ (float) $pedido->ped_total }}">
                            <span class="pedido-total">
                                Gs. {{ number_format($pedido->ped_total, 0, ',', '.') }}
                            </span>
                        </td>

                        <td class="text-left">
                            <span class="pedido-user" title="{{ $pedido->usuario }}">
                                <i class="far fa-user mr-1 text-muted"></i>{{ $pedido->usuario }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="pedido-status {{ $estadoClase }}">
                                {{ $estado }}
                            </span>
                        </td>

                        <td class="text-left">
                            <span class="pedido-obs" title="{{ $pedido->obs ?? 'Sin observación' }}">
                                {{ $pedido->obs ?: '—' }}
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="pedido-actions">
                                @if ($estado === 'PENDIENTE')
                                    {!! Form::open([
                                        'route' => ['pedido_compras.confirm', $pedido->id_pedido],
                                        'method' => 'patch',
                                        'id' => 'confirm-form-' . $pedido->id_pedido,
                                        'class' => 'd-inline',
                                    ]) !!}
                                        {!! Form::button('<i class="fas fa-check"></i>', [
                                            'type' => 'button',
                                            'class' => 'pedido-action-btn pedido-action-confirm alert-confirm',
                                            'data-id' => $pedido->id_pedido,
                                            'title' => 'Confirmar pedido',
                                            'aria-label' => 'Confirmar pedido',
                                        ]) !!}
                                    {!! Form::close() !!}
                                @endif

                                @if ($estado === 'CONFIRMADO')
                                    <a href="{{ route('pedido_compras.imprimir', [$pedido->id_pedido]) }}"
                                        class="pedido-action-btn pedido-action-print"
                                        title="Imprimir pedido"
                                        aria-label="Imprimir pedido">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <a href="{{ route('pedido.export', [$pedido->id_pedido]) }}"
                                        class="pedido-action-btn pedido-action-excel"
                                        title="Exportar Excel"
                                        aria-label="Exportar Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                @endif

                                @if (!in_array($estado, ['CONFIRMADO', 'ANULADO']))
                                    <a href="{{ route('pedido_compras.edit', [$pedido->id_pedido]) }}"
                                        class="pedido-action-btn pedido-action-edit"
                                        title="Editar pedido"
                                        aria-label="Editar pedido">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endif

                                <a href="{{ route('pedido_compras.show', [$pedido->id_pedido]) }}"
                                    class="pedido-action-btn pedido-action-view"
                                    title="Ver detalles"
                                    aria-label="Ver detalles">
                                    <i class="far fa-eye"></i>
                                </a>

                                @if ($estado !== 'ANULADO')
                                    {!! Form::open([
                                        'route' => ['pedido_compras.destroy', $pedido->id_pedido],
                                        'method' => 'delete',
                                        'class' => 'd-inline',
                                        'id' => 'delete-form-' . $pedido->id_pedido,
                                    ]) !!}
                                        {!! Form::button('<i class="fas fa-ban"></i>', [
                                            'type' => 'button',
                                            'class' => 'pedido-action-btn pedido-action-delete alert-delete',
                                            'data-id' => $pedido->id_pedido,
                                            'title' => 'Anular pedido',
                                            'aria-label' => 'Anular pedido',
                                        ]) !!}
                                    {!! Form::close() !!}
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="pedido-empty">
                            <div class="pedido-empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <strong>No hay pedidos registrados</strong>
                            <div class="mt-1">Cuando se genere un pedido aparecerá en este listado.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pedido-table-footer">
        <div class="pedido-table-footer-info">
            @if ($pedido_compras->total() > 0)
                Mostrando <strong>{{ $pedido_compras->firstItem() }}</strong>–<strong>{{ $pedido_compras->lastItem() }}</strong>
                de <strong>{{ number_format($pedido_compras->total(), 0, ',', '.') }}</strong> registros
            @else
                Sin registros para mostrar
            @endif
        </div>

        <div>
            {{ $pedido_compras->links() }}
        </div>
    </div>
</div>

@push('page_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('pedido_compras-table');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        const headers = Array.from(table.querySelectorAll('thead th.sortable'));
        let currentSort = { index: null, direction: 'asc' };

        function normalizarTexto(value) {
            return (value || '')
                .toString()
                .trim()
                .toLocaleLowerCase('es');
        }

        function valorCelda(row, index, type) {
            const cell = row.children[index];
            if (!cell) return '';

            const explicitValue = cell.getAttribute('data-sort-value');
            const raw = explicitValue !== null ? explicitValue : cell.innerText.trim();

            if (type === 'number') {
                if (explicitValue !== null) {
                    return Number(explicitValue) || 0;
                }

                return Number(
                    raw.replace(/[^\d,.-]/g, '')
                        .replace(/\./g, '')
                        .replace(',', '.')
                ) || 0;
            }

            if (type === 'date') {
                if (explicitValue) {
                    return new Date(explicitValue + 'T00:00:00').getTime();
                }

                const parts = raw.split('/');
                if (parts.length === 3) {
                    return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
                }
                return 0;
            }

            return normalizarTexto(raw);
        }

        function actualizarIconos(activeIndex, direction) {
            headers.forEach(function(th) {
                const icon = th.querySelector('.pedido-sort-icon');
                const index = th.cellIndex;
                if (!icon) return;

                icon.textContent = index === activeIndex
                    ? (direction === 'asc' ? '▲' : '▼')
                    : '↕';
            });
        }

        headers.forEach(function(th) {
            th.addEventListener('click', function() {
                const columnIndex = th.cellIndex;
                const type = th.getAttribute('data-type') || 'text';

                if (currentSort.index === columnIndex) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.index = columnIndex;
                    currentSort.direction = 'asc';
                }

                const rows = Array.from(tbody.querySelectorAll('tr'))
                    .filter(function(row) {
                        return !row.querySelector('.pedido-empty');
                    });

                rows.sort(function(a, b) {
                    const valueA = valorCelda(a, columnIndex, type);
                    const valueB = valorCelda(b, columnIndex, type);

                    if (valueA < valueB) return currentSort.direction === 'asc' ? -1 : 1;
                    if (valueA > valueB) return currentSort.direction === 'asc' ? 1 : -1;
                    return 0;
                });

                rows.forEach(function(row) {
                    tbody.appendChild(row);
                });

                actualizarIconos(columnIndex, currentSort.direction);
            });
        });
    });
</script>
@endpush
