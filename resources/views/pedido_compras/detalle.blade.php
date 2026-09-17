<div class="pedido-detail-card">
    <div class="pedido-detail-header">
        <div class="pedido-detail-heading">
            <div class="pedido-detail-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <h3 class="pedido-detail-title">Detalle del pedido</h3>
                <p class="pedido-detail-subtitle">Artículos incluidos, cantidades y valores del pedido.</p>
            </div>
        </div>

        <button type="button"
            class="btn pedido-add-product-btn"
            id="buscar"
            data-toggle="modal"
            data-target="#productSearchModalPed">
            <i class="fas fa-search mr-2"></i>
            Buscar productos
        </button>
    </div>

    <div class="pedido-detail-body">
        <div class="pedido-detail-table-wrap">
            <table class="table pedido-detail-table item-table mb-0">
                <colgroup>
                    <col class="pedido-col-codigo">
                    <col class="pedido-col-producto">
                    <col class="pedido-col-cantidad">
                    <col class="pedido-col-precio">
                    <col class="pedido-col-subtotal">
                    <col class="pedido-col-acciones">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">Código</th>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-right">Precio unit.</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody id="selectedProducts"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="pedido-detail-total-label">
                            <i class="fas fa-layer-group mr-2"></i>Total de unidades
                        </td>
                        <td id="totalCantidad" class="pedido-detail-total-value text-center">0</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="pedido-detail-hint">
            <div class="pedido-detail-hint-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <strong>Detalle editable</strong>
                <span>Podés modificar la cantidad directamente en la tabla. Los subtotales y el total del pedido se recalculan automáticamente.</span>
            </div>
        </div>
    </div>
</div>

<style>
    .pedido-detail-card {
        width: 100%;
        border: 1px solid #e4eaf1;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 8px 24px rgba(31, 45, 61, .06);
    }

    .pedido-detail-header {
        min-height: 76px;
        padding: 15px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid #e8edf3;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
    }

    .pedido-detail-heading {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pedido-detail-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        color: #2563eb;
        background: #edf4ff;
        font-size: 16px;
    }

    .pedido-detail-title {
        margin: 0;
        color: #243447;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
    }

    .pedido-detail-subtitle {
        margin: 4px 0 0;
        color: #7a8797;
        font-size: 12px;
        line-height: 1.35;
    }

    .pedido-add-product-btn {
        min-height: 38px;
        padding: 8px 15px;
        flex: 0 0 auto;
        border: 1px solid #2563eb;
        border-radius: 9px;
        color: #fff;
        background: #2563eb;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 5px 12px rgba(37, 99, 235, .18);
        transition: all .18s ease;
    }

    .pedido-add-product-btn:hover,
    .pedido-add-product-btn:focus {
        color: #fff;
        background: #1d4ed8;
        border-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 7px 16px rgba(37, 99, 235, .24);
    }

    .pedido-detail-body {
        padding: 0;
    }

    .pedido-detail-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .pedido-detail-table {
        width: 100%;
        min-width: 860px;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        color: #344256;
        font-size: 12px;
    }

    .pedido-detail-table .pedido-col-codigo { width: 15%; }
    .pedido-detail-table .pedido-col-producto { width: 35%; }
    .pedido-detail-table .pedido-col-cantidad { width: 12%; }
    .pedido-detail-table .pedido-col-precio { width: 15%; }
    .pedido-detail-table .pedido-col-subtotal { width: 16%; }
    .pedido-detail-table .pedido-col-acciones { width: 7%; }

    .pedido-detail-table thead th {
        height: 44px;
        padding: 9px 10px;
        vertical-align: middle;
        border-top: 0;
        border-bottom: 1px solid #dfe6ee;
        color: #5b6879;
        background: #f6f8fb;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .045em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .pedido-detail-table tbody td {
        padding: 8px 9px;
        vertical-align: middle;
        border-top: 0;
        border-bottom: 1px solid #edf1f5;
        background: #fff;
    }

    .pedido-detail-table tbody tr:last-child td {
        border-bottom-color: #e4eaf1;
    }

    .pedido-detail-table tbody tr:hover td {
        background: #f9fbff;
    }

    .pedido-detail-table .form-control {
        height: 34px;
        padding: 5px 8px;
        border: 1px solid #dce3eb;
        border-radius: 7px;
        color: #344256;
        background: #fff;
        font-size: 12px;
        box-shadow: none;
    }

    .pedido-detail-table .form-control:focus {
        border-color: #80aaf8;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .08);
    }

    .pedido-detail-table .form-control[readonly] {
        color: #526173;
        background: #f8fafc;
    }

    .pedido-detail-table .cantidad {
        max-width: 90px;
        margin: 0 auto;
        font-weight: 700;
    }

    .pedido-detail-table .subtotal {
        font-weight: 700;
        color: #1f4f8f;
    }

    .pedido-detail-table .btn-danger {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border-color: #fecaca;
        color: #dc2626;
        background: #fff5f5;
        box-shadow: none;
    }

    .pedido-detail-table .btn-danger:hover,
    .pedido-detail-table .btn-danger:focus {
        border-color: #dc2626;
        color: #fff;
        background: #dc2626;
    }

    .pedido-detail-table tfoot td {
        height: 48px;
        padding: 10px;
        vertical-align: middle;
        border-top: 0;
        background: #f8fafc;
    }

    .pedido-detail-total-label {
        text-align: right;
        color: #667587;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .025em;
        text-transform: uppercase;
    }

    .pedido-detail-total-value {
        color: #1d4ed8;
        font-size: 16px;
        font-weight: 800;
    }

    .pedido-detail-hint {
        margin: 14px 16px 16px;
        padding: 10px 12px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        border: 1px solid #e2eaf3;
        border-radius: 9px;
        color: #617184;
        background: #f8fbff;
        font-size: 11px;
    }

    .pedido-detail-hint-icon {
        flex: 0 0 auto;
        margin-top: 1px;
        color: #3b82f6;
    }

    .pedido-detail-hint strong {
        display: block;
        margin-bottom: 2px;
        color: #405267;
        font-size: 11px;
    }

    .pedido-detail-hint span {
        display: block;
        line-height: 1.4;
    }

    @media (max-width: 767.98px) {
        .pedido-detail-header {
            align-items: stretch;
            flex-direction: column;
        }

        .pedido-add-product-btn {
            width: 100%;
        }

        .pedido-detail-hint {
            margin: 12px;
        }
    }
</style>
