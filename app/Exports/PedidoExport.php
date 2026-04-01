<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PedidoExport implements FromArray, WithHeadings
{
    protected $id_pedido;

    public function __construct($id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }

    public function headings(): array
    {
        return [
            'N° Pedido',
            'Código Artículo',
            'Descripción',
            'Cantidad',
            'Local'
        ];
    }

    public function array(): array
    {
        $data = DB::table('detalle_pedido as d')
            ->join('pedido_compras as p', 'p.id_pedido', '=', 'd.id_pedido_compras')
            ->join('articulos as a', 'a.id_articulo', '=', 'd.id_articulo')
            ->where('p.id_pedido', $this->id_pedido)
            ->select(
                'p.nro_pedido',
                'a.art_codigo',
                'a.art_descripcion',
                'd.det_cantidad'
            )
            ->get();

        return $data->map(function ($item) {

            // buscar sucursal con mayor stock del código
            $stock = DB::table('stock_sucursales')
                ->where('codigo', $item->art_codigo)
                ->orderBy('cantidad', 'desc')
                ->first();

            return [
                $item->nro_pedido,
                $item->art_codigo,
                $item->art_descripcion,
                $item->det_cantidad,
                $stock->sucursal ?? 'SIN STOCK'
            ];
        })->toArray();
    }
}
