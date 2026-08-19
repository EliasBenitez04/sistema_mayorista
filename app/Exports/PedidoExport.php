<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PedidoExport implements FromArray, WithHeadings
{
    protected $id_pedido;

    /*
    |--------------------------------------------------------------------------
    | SUCURSALES PRIORITARIAS
    |--------------------------------------------------------------------------
    |
    | Orden en el que se buscan las sucursales.
    |
    | 14 -> 8 -> 2 -> 9 -> 5 -> 1
    |
    */

    protected $sucursalesPrioritarias = [
        14,
        8,
        2,
        9,
        5,
        1,
    ];

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
            'Local',
            'Motivo'
        ];
    }

    public function array(): array
    {
        $data = DB::table('detalle_pedido as d')
            ->join(
                'pedido_compras as p',
                'p.id_pedido',
                '=',
                'd.id_pedido_compras'
            )
            ->join(
                'articulos as a',
                'a.id_articulo',
                '=',
                'd.id_articulo'
            )
            ->where('p.id_pedido', $this->id_pedido)
            ->select(
                'p.nro_pedido',
                'p.cod_suc',
                'a.art_codigo',
                'a.art_descripcion',
                'd.det_cantidad'
            )
            ->get();

        return $data->map(function ($item) {

            $cantidadSolicitada = (float) $item->det_cantidad;

            /*
            |--------------------------------------------------------------------------
            | TRAER TODO EL STOCK DEL ARTÍCULO
            |--------------------------------------------------------------------------
            */

            $stocks = DB::table('stock_sucursales')
                ->where('codigo', $item->art_codigo)
                ->where('cantidad', '>', 0)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | NO HAY STOCK
            |--------------------------------------------------------------------------
            */

            if ($stocks->isEmpty()) {
                return [
                    $item->nro_pedido,
                    $item->art_codigo,
                    $item->art_descripcion,
                    $item->det_cantidad,
                    'SIN STOCK',
                    'No hay stock disponible en ninguna sucursal.'
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | OBTENER CÓDIGO DE SUCURSAL
            |--------------------------------------------------------------------------
            */

            $stocksProcesados = $stocks->map(function ($stock) {

                preg_match(
                    '/Sucursal:\s*([0-9]+)/',
                    $stock->sucursal ?? '',
                    $matches
                );

                $stock->codigo_sucursal = isset($matches[1])
                    ? (int) $matches[1]
                    : null;

                $stock->cantidad = (float) $stock->cantidad;

                return $stock;
            });

            /*
            |--------------------------------------------------------------------------
            | 1. BUSCAR CANTIDAD COMPLETA EN LAS SUCURSALES PRIORITARIAS
            |--------------------------------------------------------------------------
            |
            | Ejemplo:
            |
            | Pedido = 3
            |
            | Sucursal 14 = 5
            |
            | Entonces se pide directamente de 14.
            |
            */

            foreach ($this->sucursalesPrioritarias as $codigoSucursal) {

                $stockCompleto = $stocksProcesados->first(
                    function ($stock) use (
                        $codigoSucursal,
                        $cantidadSolicitada
                    ) {

                        return
                            $stock->codigo_sucursal !== null &&
                            $stock->codigo_sucursal == $codigoSucursal &&
                            $stock->cantidad >= $cantidadSolicitada;
                    }
                );

                if ($stockCompleto) {

                    return [
                        $item->nro_pedido,
                        $item->art_codigo,
                        $item->art_descripcion,
                        $item->det_cantidad,
                        $stockCompleto->sucursal,
                        ''
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 2. BUSCAR STOCK PARCIAL EN LAS SUCURSALES PRIORITARIAS
            |--------------------------------------------------------------------------
            |
            | IMPORTANTE:
            |
            | Si pedido = 3
            | Sucursal 7 = 2
            |
            | Debe mostrar:
            |
            | Cantidad disponible: 2 de 3
            | Falta: 1
            |
            */

            foreach ($this->sucursalesPrioritarias as $codigoSucursal) {

                $stockParcial = $stocksProcesados->first(
                    function ($stock) use (
                        $codigoSucursal,
                        $cantidadSolicitada
                    ) {

                        return
                            $stock->codigo_sucursal !== null &&
                            $stock->codigo_sucursal == $codigoSucursal &&
                            $stock->cantidad > 0 &&
                            $stock->cantidad < $cantidadSolicitada;
                    }
                );

                if (!$stockParcial) {
                    continue;
                }

                $cantidadDisponible = (float) $stockParcial->cantidad;

                $faltante = $cantidadSolicitada - $cantidadDisponible;

                /*
                |--------------------------------------------------------------------------
                | BUSCAR OTRAS SUCURSALES QUE PUEDAN COMPLETAR
                |--------------------------------------------------------------------------
                */

                $sucursalesParaCompletar = $stocksProcesados
                    ->filter(function ($stock) use (
                        $stockParcial,
                        $cantidadSolicitada
                    ) {

                        return
                            $stock->id != $stockParcial->id &&
                            $stock->codigo_sucursal !== null &&
                            $stock->cantidad > 0 &&
                            $stock->cantidad < $cantidadSolicitada;
                    })
                    ->sortByDesc('cantidad')
                    ->values();

                /*
                |--------------------------------------------------------------------------
                | ARMAR SUGERENCIAS
                |--------------------------------------------------------------------------
                */

                $sugerencias = [];

                foreach ($sucursalesParaCompletar as $otraSucursal) {

                    $sugerencias[] =
                        $otraSucursal->sucursal .
                        ' (' .
                        (float) $otraSucursal->cantidad .
                        ' disponible)';
                }

                /*
                |--------------------------------------------------------------------------
                | ARMAR MOTIVO
                |--------------------------------------------------------------------------
                */

                $motivo =
                    'Cantidad disponible: ' .
                    $cantidadDisponible .
                    ' de ' .
                    $cantidadSolicitada .
                    '. Falta: ' .
                    $faltante .
                    '.';

                /*
                |--------------------------------------------------------------------------
                | SI HAY OTRAS SUCURSALES
                |--------------------------------------------------------------------------
                */

                if (!empty($sugerencias)) {

                    $motivo .=
                        ' Sugerir completar desde: ' .
                        implode(', ', $sugerencias);
                } else {

                    $motivo .=
                        ' No se encontró otra sucursal con stock para completar.';
                }

                return [
                    $item->nro_pedido,
                    $item->art_codigo,
                    $item->art_descripcion,
                    $item->det_cantidad,
                    $stockParcial->sucursal,
                    $motivo
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | 3. SI NO HAY EN LAS PRIORITARIAS
            |--------------------------------------------------------------------------
            |
            | Buscar una sucursal cualquiera que tenga TODO el pedido.
            |
            | Se elige la que tenga MAYOR STOCK.
            |
            */

            $stockAlternativo = $stocksProcesados
                ->filter(function ($stock) use ($cantidadSolicitada) {

                    return
                        $stock->codigo_sucursal !== null &&
                        !in_array(
                            $stock->codigo_sucursal,
                            $this->sucursalesPrioritarias
                        ) &&
                        $stock->cantidad >= $cantidadSolicitada;
                })
                ->sortByDesc('cantidad')
                ->first();

            if ($stockAlternativo) {

                return [
                    $item->nro_pedido,
                    $item->art_codigo,
                    $item->art_descripcion,
                    $item->det_cantidad,
                    $stockAlternativo->sucursal,
                    'No hay disponible en sucursales cercanas. ' .
                        'Se recomienda solicitar a esta sucursal por tener ' .
                        (float) $stockAlternativo->cantidad .
                        ' unidades disponibles.'
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | 4. BUSCAR EL MAYOR STOCK PARCIAL EN OTRAS SUCURSALES
            |--------------------------------------------------------------------------
            */

            $stockParcialAlternativo = $stocksProcesados
                ->filter(function ($stock) use ($cantidadSolicitada) {

                    return
                        $stock->codigo_sucursal !== null &&
                        !in_array(
                            $stock->codigo_sucursal,
                            $this->sucursalesPrioritarias
                        ) &&
                        $stock->cantidad > 0 &&
                        $stock->cantidad < $cantidadSolicitada;
                })
                ->sortByDesc('cantidad')
                ->first();

            if ($stockParcialAlternativo) {

                $cantidadDisponible =
                    (float) $stockParcialAlternativo->cantidad;

                $faltante =
                    $cantidadSolicitada - $cantidadDisponible;

                /*
                |--------------------------------------------------------------------------
                | BUSCAR OTRAS SUCURSALES PARA COMPLETAR
                |--------------------------------------------------------------------------
                */

                $sugerencias = $stocksProcesados
                    ->filter(function ($stock) use (
                        $stockParcialAlternativo
                    ) {

                        return
                            $stock->id != $stockParcialAlternativo->id &&
                            $stock->codigo_sucursal !== null &&
                            $stock->cantidad > 0;
                    })
                    ->sortByDesc('cantidad')
                    ->values();

                $listaSugerencias = [];

                foreach ($sugerencias as $otraSucursal) {

                    $listaSugerencias[] =
                        $otraSucursal->sucursal .
                        ' (' .
                        (float) $otraSucursal->cantidad .
                        ' disponible)';
                }

                /*
                |--------------------------------------------------------------------------
                | MOTIVO
                |--------------------------------------------------------------------------
                */

                $motivo =
                    'No hay disponible en sucursales cercanas. ' .
                    'Cantidad disponible: ' .
                    $cantidadDisponible .
                    ' de ' .
                    $cantidadSolicitada .
                    '. Falta: ' .
                    $faltante .
                    '.';

                if (!empty($listaSugerencias)) {

                    $motivo .=
                        ' Sugerir completar desde: ' .
                        implode(', ', $listaSugerencias);
                } else {

                    $motivo .=
                        ' No se encontró otra sucursal con stock disponible.';
                }

                return [
                    $item->nro_pedido,
                    $item->art_codigo,
                    $item->art_descripcion,
                    $item->det_cantidad,
                    $stockParcialAlternativo->sucursal,
                    $motivo
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | 5. SIN STOCK
            |--------------------------------------------------------------------------
            */

            return [
                $item->nro_pedido,
                $item->art_codigo,
                $item->art_descripcion,
                $item->det_cantidad,
                'SIN STOCK',
                'No hay stock disponible en ninguna sucursal.'
            ];
        })->toArray();
    }
}
