<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class PedidoExport implements WithMultipleSheets
{
    protected $id_pedido;

    protected $sucursalesExcluidas = [
        1,   // Sucursal 01
        25,  // Sucursal 25
    ];

    protected $maxSugerencias = 5;

    protected $pedidosPorSucursal = [];

    // Saldo virtual por artículo/sucursal durante esta exportación.
    protected $saldosExportacion = [];

    public function __construct($id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }

    public function sheets(): array
    {

        $resultado = $this->procesarPedido();

        $sheets = [];

        $sheets[] = new PedidoResumenSheet(
            $resultado['resumen']
        );

        foreach (
            $this->pedidosPorSucursal
            as $codigoSucursal => $sucursal
        ) {

            $sheets[] = new PedidoSucursalSheet(
                $sucursal['nombre'],
                $sucursal['items']
            );
        }

        return $sheets;
    }

    protected function procesarPedido(): array
    {

        $items = DB::table('detalle_pedido as d')
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
            ->where(
                'p.id_pedido',
                $this->id_pedido
            )
            ->select(
                'p.nro_pedido',
                'p.cod_suc',
                'a.art_codigo',
                'a.art_descripcion',
                'd.det_cantidad'
            )
            ->get();

        $resumen = [];

        foreach ($items as $item) {

            $cantidadSolicitada =
                (int) $item->det_cantidad;

            $stocks = DB::table('stock_sucursales')
                ->where(
                    'codigo',
                    $item->art_codigo
                )
                ->where(
                    'cantidad',
                    '>',
                    0
                )
                ->get();

            if ($stocks->isEmpty()) {

                $resumen[] = [

                    'nro_pedido' =>
                    $item->nro_pedido,

                    'codigo' =>
                    $item->art_codigo,

                    'articulo' =>
                    $item->art_descripcion,

                    'necesita' =>
                    $cantidadSolicitada,

                    'total_retirar' =>
                    0,

                    'pendiente' =>
                    $cantidadSolicitada,

                    'situacion' =>
                    'SIN STOCK',

                    'observacion' =>
                    'No hay unidades disponibles.',
                ];

                continue;
            }

            $stocksProcesados = $stocks
                ->map(function ($stock) {

                    preg_match(
                        '/Sucursal:\s*([0-9]+)/',
                        $stock->sucursal ?? '',
                        $matches
                    );

                    $stock->codigo_sucursal =
                        isset($matches[1])
                        ? (int) $matches[1]
                        : null;

                    $stock->cantidad =
                        (int) $stock->cantidad;

                    return $stock;
                })
                ->filter(function ($stock) {

                    return
                        $stock->codigo_sucursal !== null &&
                        $stock->cantidad > 0;
                })
                ->values();

            $stocksProcesados = $stocksProcesados
                ->reject(function ($stock) {
                    return in_array(
                        $stock->codigo_sucursal,
                        $this->sucursalesExcluidas
                    );
                })
                ->values();

            if ($stocksProcesados->isEmpty()) {

                $resumen[] = [

                    'nro_pedido' =>
                    $item->nro_pedido,

                    'codigo' =>
                    $item->art_codigo,

                    'articulo' =>
                    $item->art_descripcion,

                    'necesita' =>
                    $cantidadSolicitada,

                    'total_retirar' =>
                    0,

                    'pendiente' =>
                    $cantidadSolicitada,

                    'situacion' =>
                    'SIN STOCK',

                    'observacion' =>
                    'No se pudo identificar la sucursal.',
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | DISTRIBUCIÓN EQUILIBRADA SEGÚN STOCK REAL
            |--------------------------------------------------------------------------
            | No hay prioridad fija por número de sucursal.
            | Se usan hasta cinco locales con más existencia por artículo.
            | Cada unidad se asigna al local con mayor saldo restante;
            | los empates mantienen el orden inicial por stock y código.
            | En la primera pasada se conserva una unidad por local cuando
            | es posible. La segunda permite usar esa última unidad si
            | es necesaria para completar el pedido.
            |--------------------------------------------------------------------------
            */
            $codigoArticulo = (string) $item->art_codigo;
            if (!isset($this->saldosExportacion[$codigoArticulo])) {
                $this->saldosExportacion[$codigoArticulo] = [];
            }

            foreach ($stocksProcesados as $stock) {
                $codigoLocal = (int) $stock->codigo_sucursal;
                if (!array_key_exists($codigoLocal, $this->saldosExportacion[$codigoArticulo])) {
                    $this->saldosExportacion[$codigoArticulo][$codigoLocal] = (int) $stock->cantidad;
                }
            }

            $candidatas = $stocksProcesados
                ->sort(function ($a, $b) use ($codigoArticulo) {
                    $saldoA = $this->saldosExportacion[$codigoArticulo][$a->codigo_sucursal] ?? 0;
                    $saldoB = $this->saldosExportacion[$codigoArticulo][$b->codigo_sucursal] ?? 0;
                    return ($saldoB <=> $saldoA)
                        ?: ($a->codigo_sucursal <=> $b->codigo_sucursal);
                })
                ->filter(function ($stock) use ($codigoArticulo) {
                    return ($this->saldosExportacion[$codigoArticulo][$stock->codigo_sucursal] ?? 0) > 0;
                })
                ->take($this->maxSugerencias)
                ->values();

            $cantidadesAsignadas = [];
            $restante = $cantidadSolicitada;

            foreach ([1, 0] as $minimoEnOrigen) {
                while ($restante > 0) {
                    $mejor = null;
                    $mayorSaldo = $minimoEnOrigen;

                    foreach ($candidatas as $stock) {
                        $codigoLocal = (int) $stock->codigo_sucursal;
                        $saldo = $this->saldosExportacion[$codigoArticulo][$codigoLocal] ?? 0;
                        if ($saldo > $mayorSaldo) {
                            $mayorSaldo = $saldo;
                            $mejor = $stock;
                        }
                    }

                    if ($mejor === null) {
                        break;
                    }

                    $codigoLocal = (int) $mejor->codigo_sucursal;
                    $cantidadesAsignadas[$codigoLocal] = ($cantidadesAsignadas[$codigoLocal] ?? 0) + 1;
                    $this->saldosExportacion[$codigoArticulo][$codigoLocal]--;
                    $restante--;
                }
            }

            $asignaciones = [];
            foreach ($candidatas as $stock) {
                $codigoLocal = (int) $stock->codigo_sucursal;
                $cantidad = $cantidadesAsignadas[$codigoLocal] ?? 0;
                if ($cantidad > 0) {
                    $asignaciones[] = [
                        'sucursal' => $stock->sucursal,
                        'codigo_sucursal' => $codigoLocal,
                        'cantidad' => $cantidad,
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL RETIRADO
            |--------------------------------------------------------------------------
            */

            $totalRetirar = 0;

            foreach (
                $asignaciones
                as $asignacion
            ) {

                $totalRetirar +=
                    (int) $asignacion['cantidad'];
            }

            /*
            |--------------------------------------------------------------------------
            | PENDIENTE
            |--------------------------------------------------------------------------
            */

            $pendiente =
                max(
                    0,
                    $cantidadSolicitada -
                        $totalRetirar
                );

            /*
            |--------------------------------------------------------------------------
            | SITUACIÓN
            |--------------------------------------------------------------------------
            */

            if ($pendiente === 0) {

                $situacion =
                    'COMPLETO';

                $observacion =
                    'Pedido completo.';
            } elseif ($totalRetirar > 0) {

                $situacion =
                    'PARCIAL';

                $observacion =
                    'Faltan ' .
                    $pendiente .
                    ' unidad(es).';
            } else {

                $situacion =
                    'SIN STOCK';

                $observacion =
                    'No hay unidades disponibles.';
            }

            /*
            |--------------------------------------------------------------------------
            | AGREGAR AL RESUMEN
            |--------------------------------------------------------------------------
            */

            $resumen[] = [

                'nro_pedido' =>
                $item->nro_pedido,

                'codigo' =>
                $item->art_codigo,

                'articulo' =>
                $item->art_descripcion,

                'necesita' =>
                $cantidadSolicitada,

                'total_retirar' =>
                $totalRetirar,

                'pendiente' =>
                $pendiente,

                'situacion' =>
                $situacion,

                'observacion' =>
                $observacion,
            ];

            /*
            |--------------------------------------------------------------------------
            | AGRUPAR POR SUCURSAL
            |--------------------------------------------------------------------------
            */

            foreach (
                $asignaciones
                as $asignacion
            ) {

                $codigoSucursal =
                    $asignacion['codigo_sucursal'];

                $nombreSucursal =
                    $asignacion['sucursal'];

                /*
                |--------------------------------------------------------------------------
                | CREAR SUCURSAL
                |--------------------------------------------------------------------------
                */

                if (
                    !isset(
                        $this->pedidosPorSucursal[$codigoSucursal]
                    )
                ) {

                    $this->pedidosPorSucursal[$codigoSucursal] = [

                        'nombre' =>
                        $this->limpiarNombreHoja(
                            $nombreSucursal
                        ),

                        'items' => [],
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | CLAVE ÚNICA DEL ARTÍCULO
                |--------------------------------------------------------------------------
                */

                $clave =
                    $item->nro_pedido .
                    '|' .
                    $item->art_codigo;

                /*
                |--------------------------------------------------------------------------
                | BUSCAR SI YA EXISTE
                |--------------------------------------------------------------------------
                */

                $indiceExistente = null;

                foreach (
                    $this->pedidosPorSucursal[$codigoSucursal]['items']
                    as $indice => $existente
                ) {

                    if (
                        $existente['clave'] ===
                        $clave
                    ) {

                        $indiceExistente =
                            $indice;

                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | SUMAR SI YA EXISTE
                |--------------------------------------------------------------------------
                */

                if (
                    $indiceExistente !== null
                ) {

                    $this->pedidosPorSucursal[$codigoSucursal]['items'][$indiceExistente]['cantidad'] +=
                        $asignacion['cantidad'];
                }

                /*
                |--------------------------------------------------------------------------
                | NUEVO ARTÍCULO
                |--------------------------------------------------------------------------
                */ else {

                    $this->pedidosPorSucursal[$codigoSucursal]['items'][] = [

                        'clave' =>
                        $clave,

                        'nro_pedido' =>
                        $item->nro_pedido,

                        'codigo' =>
                        $item->art_codigo,

                        'articulo' =>
                        $item->art_descripcion,

                        'cantidad' =>
                        $asignacion['cantidad'],
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RETORNAR RESULTADO
        |--------------------------------------------------------------------------
        */

        return [
            'resumen' =>
            $resumen,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LIMPIAR NOMBRE DE HOJA
    |--------------------------------------------------------------------------
    */

    private function limpiarNombreHoja(
        $nombre
    ): string {

        $nombre = preg_replace(
            '/[\/\\\\\?\*\[\]:]/',
            '',
            $nombre
        );

        $nombre = mb_substr(
            trim($nombre),
            0,
            31
        );

        if ($nombre === '') {
            $nombre = 'SUCURSAL';
        }

        return $nombre;
    }
}


/*
|--------------------------------------------------------------------------
| HOJA RESUMEN
|--------------------------------------------------------------------------
*/

class PedidoResumenSheet implements
    FromArray,
    WithHeadings,
    WithTitle,
    ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'RESUMEN';
    }

    public function headings(): array
    {
        return [

            'N° Pedido',
            'Código',
            'Artículo',
            'Se necesitan',
            'Total a retirar',
            'Queda pendiente',
            'Situación',
            'Observación',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->data as $item) {

            $data[] = [

                $item['nro_pedido'],

                $item['codigo'],

                $item['articulo'],

                $item['necesita'],

                $item['total_retirar'],

                $item['pendiente'],

                $item['situacion'],

                $item['observacion'],
            ];
        }

        return $data;
    }
}


/*
|--------------------------------------------------------------------------
| HOJA DE CADA SUCURSAL
|--------------------------------------------------------------------------
*/

class PedidoSucursalSheet implements
    FromArray,
    WithHeadings,
    WithTitle,
    ShouldAutoSize
{
    protected $nombreSucursal;
    protected $items;

    public function __construct(
        $nombreSucursal,
        array $items
    ) {

        $this->nombreSucursal =
            $nombreSucursal;

        $this->items =
            $items;
    }

    public function title(): string
    {
        return $this->nombreSucursal;
    }

    public function headings(): array
    {
        return [

            'N° Pedido',
            'Código',
            'Artículo',
            'Cantidad a retirar',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->items as $item) {

            $data[] = [

                $item['nro_pedido'],

                $item['codigo'],

                $item['articulo'],

                $item['cantidad'],
            ];
        }

        return $data;
    }
}
