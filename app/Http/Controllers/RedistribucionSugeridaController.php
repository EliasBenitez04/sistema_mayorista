<?php

namespace App\Http\Controllers;

use App\Models\RedistribucionLote;
use App\Models\RedistribucionSugerida;
use App\Models\RedistribucionProceso;
use App\Models\RedistribucionProcesoDetalle;
use App\Models\StockVentasSucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LoteRedistribucionExport;
use Maatwebsite\Excel\Facades\Excel;

class RedistribucionSugeridaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // VER
        $this->middleware('permission:redistribucionsugerencia index')
            ->only([
                'index',
                'lotes',
                'proceso',
                'lote',
                'show',
            ]);

        // CREAR / GENERAR
        $this->middleware('permission:redistribucionsugerencia create')
            ->only([
                'analizar',
                'generarLote',
            ]);

        // MODIFICAR / PROCESAR
        $this->middleware('permission:redistribucionsugerencia update')
            ->only([
                'aprobar',
                'rechazar',
                'procesarLote',
                'finalizarLote',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PANTALLA PRINCIPAL
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $periodos = StockVentasSucursal::query()
            ->whereNotNull('periodo')
            ->where('periodo', '<>', '')
            ->distinct()
            ->orderByDesc('periodo')
            ->pluck('periodo');

        $gruposPlan = StockVentasSucursal::query()
            ->whereNotNull('grupo_plan')
            ->where('grupo_plan', '<>', '')
            ->distinct()
            ->orderBy('grupo_plan')
            ->pluck('grupo_plan');

        $lineas = StockVentasSucursal::query()
            ->whereNotNull('linea')
            ->where('linea', '<>', '')
            ->distinct()
            ->orderBy('linea')
            ->pluck('linea');

        $temporadas = StockVentasSucursal::query()
            ->whereNotNull('temporada')
            ->where('temporada', '<>', '')
            ->distinct()
            ->orderBy('temporada')
            ->pluck('temporada');

        /*
         * Sucursales
         */
        $sucursales = StockVentasSucursal::query()
            ->join(
                'sucursal',
                'stock_ventas_sucursales.sucursal_id',
                '=',
                'sucursal.cod_suc'
            )
            ->select(
                'sucursal.cod_suc',
                'sucursal.suc_descri'
            )
            ->distinct()
            ->orderBy('sucursal.suc_descri')
            ->get();

        /*
         * Sugerencias
         */
        $sugerencias = RedistribucionSugerida::with([
            'origen',
            'destino'
        ])
            ->whereIn('estado', [
                'PENDIENTE',
                'RECHAZADA'
            ])
            ->orderBy('codigo')
            ->orderBy('fecha_generacion', 'desc')
            ->get();

        return view(
            'redistribucion_sugeridas.index',
            compact(
                'periodos',
                'gruposPlan',
                'lineas',
                'temporadas',
                'sucursales',
                'sugerencias'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ANALIZAR REDISTRIBUCIÓN
    |--------------------------------------------------------------------------
    */

    /**
     * =========================================================
     * ANALIZAR REDISTRIBUCIÓN
     * =========================================================
     */
    /**
     * =========================================================
     * ANALIZAR REDISTRIBUCIÓN
     * =========================================================
     */
    public function analizar(Request $request)
    {
        $request->validate([
            'periodo'     => 'required',
            'grupo_plan'  => 'nullable',
            'linea'       => 'nullable',
            'temporada'   => 'nullable',
        ]);

        DB::beginTransaction();

        try {

            $fechaGeneracion = now();

            /**
             * =====================================================
             * 1. ELIMINAR SUGERENCIAS PENDIENTES GENERADAS HOY
             * =====================================================
             *
             * Esto NO elimina:
             *
             * - APROBADAS
             * - RECHAZADAS
             *
             * Las rechazadas quedan históricamente registradas,
             * pero NO bloquean futuros análisis.
             */
            $inicioDia = $fechaGeneracion->copy()->startOfDay();
            $finDia    = $fechaGeneracion->copy()->endOfDay();

            RedistribucionSugerida::whereBetween(
                'fecha_generacion',
                [$inicioDia, $finDia]
            )
                ->where('estado', 'PENDIENTE')
                ->delete();


            /**
             * =====================================================
             * 2. OBTENER STOCK Y VENTAS
             * =====================================================
             */
            $query = StockVentasSucursal::query()
                ->where('periodo', $request->periodo);

            if ($request->filled('grupo_plan')) {
                $query->where(
                    'grupo_plan',
                    $request->grupo_plan
                );
            }

            if ($request->filled('linea')) {
                $query->where(
                    'linea',
                    $request->linea
                );
            }

            if ($request->filled('temporada')) {
                $query->where(
                    'temporada',
                    $request->temporada
                );
            }

            $datos = $query->get();


            /**
             * =====================================================
             * 3. VALIDAR DATOS
             * =====================================================
             */
            if ($datos->isEmpty()) {

                DB::rollBack();

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'No existen datos para los filtros seleccionados.'
                    );
            }


            /**
             * =====================================================
             * 4. CÓDIGOS BLOQUEADOS
             * =====================================================
             *
             * REGLA DEL NEGOCIO:
             *
             * A) PENDIENTE
             *    -> BLOQUEADO
             *
             * B) EN PROCESO
             *    -> BLOQUEADO
             *
             * C) FINALIZADO MENOS DE 30 DÍAS
             *    -> BLOQUEADO
             *
             * D) FINALIZADO HACE 30 DÍAS O MÁS
             *    -> DISPONIBLE NUEVAMENTE
             *
             * E) RECHAZADA
             *    -> NUNCA BLOQUEA
             *
             * F) APROBADA
             *    -> No se utiliza directamente para bloquear.
             *       Una aprobada ya genera un detalle de proceso,
             *       por lo que el bloqueo se controla mediante
             *       RedistribucionProcesoDetalle.
             */


            /**
             * =====================================================
             * 4.1 CÓDIGOS EN PROCESOS ACTIVOS
             * =====================================================
             *
             * PENDIENTE:
             * Ya fue aprobado y está esperando lote.
             *
             * EN PROCESO:
             * Está siendo procesado.
             */
            $codigosActivos = RedistribucionProcesoDetalle::query()
                ->whereIn('estado', [
                    'PENDIENTE',
                    'EN PROCESO'
                ])
                ->pluck('codigo')
                ->unique()
                ->values();


            $fechaLimite = now()->subDays(30);

            $codigosFinalizadosRecientes =
                RedistribucionProcesoDetalle::query()
                ->join(
                    'redistribucion_lote',
                    'redistribucion_proceso_detalle.lote_id',
                    '=',
                    'redistribucion_lote.id'
                )
                ->where(
                    'redistribucion_lote.estado',
                    'FINALIZADO'
                )
                ->whereNotNull(
                    'redistribucion_lote.fecha_finalizacion'
                )
                ->where(
                    'redistribucion_lote.fecha_finalizacion',
                    '>=',
                    $fechaLimite
                )
                ->pluck(
                    'redistribucion_proceso_detalle.codigo'
                )
                ->unique()
                ->values();


            /**
             * =====================================================
             * 4.3 UNIFICAR BLOQUEADOS
             * =====================================================
             */
            $codigosBloqueados = $codigosActivos
                ->merge($codigosFinalizadosRecientes)
                ->unique()
                ->values();


            /**
             * =====================================================
             * 5. EXCLUIR CÓDIGOS BLOQUEADOS
             * =====================================================
             *
             * IMPORTANTE:
             *
             * NO buscamos RECHAZADAS.
             *
             * Por lo tanto una sugerencia rechazada queda libre
             * automáticamente para un nuevo análisis.
             */
            if ($codigosBloqueados->isNotEmpty()) {

                $datos = $datos
                    ->reject(function ($item) use ($codigosBloqueados) {

                        return $codigosBloqueados->contains(
                            $item->codigo
                        );
                    })
                    ->values();
            }


            /**
             * =====================================================
             * 6. VALIDAR SI QUEDARON PRODUCTOS
             * =====================================================
             */
            if ($datos->isEmpty()) {

                DB::rollBack();

                return redirect()
                    ->route(
                        'RedistribucionSugeridas.index'
                    )
                    ->with(
                        'warning',
                        'No existen prendas disponibles para analizar. Las prendas actualmente están pendientes, en proceso o fueron finalizadas hace menos de 30 días.'
                    );
            }


            /**
             * =====================================================
             * 7. AGRUPAR POR PRODUCTO
             * =====================================================
             */
            $productos = $datos->groupBy('codigo');


            /**
             * =====================================================
             * 8. ACUMULADORES
             * =====================================================
             */
            $insertar = [];

            $totalSugerencias = 0;


            /**
             * =====================================================
             * 9. ANALIZAR CADA PRODUCTO
             * =====================================================
             */
            foreach ($productos as $codigo => $sucursales) {

                /**
                 * Ordenar sucursales por mayor venta.
                 */
                $sucursales = $sucursales
                    ->sortByDesc('cant_vta')
                    ->values();

                $destinos = [];
                $origenes = [];


                /**
                 * =================================================
                 * 9.1 IDENTIFICAR ORÍGENES Y DESTINOS
                 * =================================================
                 */
                foreach ($sucursales as $item) {

                    $venta = (int) $item->cant_vta;
                    $stock = (int) $item->stock_actual;


                    /**
                     * Stock objetivo = venta.
                     */
                    $stockObjetivo = $venta;


                    /**
                     * Necesidad.
                     */
                    $necesidad =
                        $stockObjetivo - $stock;


                    /**
                     * Exceso.
                     */
                    $exceso =
                        $stock - $stockObjetivo;


                    /**
                     * =============================================
                     * DESTINO
                     * =============================================
                     *
                     * Solamente se consideran destinos con:
                     *
                     * venta > 2
                     * y stock menor que venta.
                     */
                    if (
                        $venta > 2 &&
                        $necesidad > 0
                    ) {

                        $destinos[] = [

                            'sucursal_id' =>
                            $item->sucursal_id,

                            'stock' =>
                            $stock,

                            'venta' =>
                            $venta,

                            'necesidad' =>
                            $necesidad,
                        ];
                    }


                    /**
                     * =============================================
                     * ORIGEN
                     * =============================================
                     */
                    if ($exceso > 0) {

                        $origenes[] = [

                            'sucursal_id' =>
                            $item->sucursal_id,

                            'stock' =>
                            $stock,

                            'venta' =>
                            $venta,

                            'exceso' =>
                            $exceso,
                        ];
                    }
                }


                /**
                 * =================================================
                 * 9.2 ORDENAR ORÍGENES
                 * =================================================
                 *
                 * Primero la sucursal con mayor exceso.
                 */
                usort(
                    $origenes,
                    function ($a, $b) {

                        return
                            $b['exceso']
                            <=>
                            $a['exceso'];
                    }
                );


                /**
                 * =================================================
                 * 9.3 ORDENAR DESTINOS
                 * =================================================
                 *
                 * Primero la sucursal con mayor venta.
                 */
                usort(
                    $destinos,
                    function ($a, $b) {

                        return
                            $b['venta']
                            <=>
                            $a['venta'];
                    }
                );


                /**
                 * =================================================
                 * 9.4 GENERAR TRANSFERENCIAS
                 * =================================================
                 */
                foreach ($destinos as &$destino) {

                    if (
                        $destino['necesidad'] <= 0
                    ) {
                        continue;
                    }


                    /**
                     * =================================================
                     * LÍMITE DE TRANSFERENCIA
                     * =================================================
                     *
                     * Máximo 50% de la necesidad.
                     */
                    $maximoTransferirDestino =
                        (int) ceil(
                            $destino['necesidad'] * 0.50
                        );


                    $pendienteTransferir =
                        $maximoTransferirDestino;


                    /**
                     * =================================================
                     * BUSCAR ORIGEN
                     * =================================================
                     */
                    foreach ($origenes as &$origen) {

                        if (
                            $origen['exceso'] <= 0
                        ) {
                            continue;
                        }


                        /**
                         * Nunca transferir a la misma sucursal.
                         */
                        if (
                            $origen['sucursal_id']
                            ==
                            $destino['sucursal_id']
                        ) {
                            continue;
                        }


                        /**
                         * Cantidad a transferir.
                         */
                        $cantidad = min(
                            $pendienteTransferir,
                            $origen['exceso']
                        );


                        if ($cantidad <= 0) {
                            continue;
                        }


                        /**
                         * Motivo.
                         */
                        $motivo =
                            'Transferencia por exceso de stock y mayor demanda.';


                        /**
                         * =================================================
                         * PREPARAR INSERT
                         * =================================================
                         */
                        $insertar[] = [

                            'codigo' =>
                            $codigo,

                            'sucursal_origen' =>
                            $origen['sucursal_id'],

                            'sucursal_destino' =>
                            $destino['sucursal_id'],

                            'cantidad' =>
                            $cantidad,

                            'stock_origen' =>
                            $origen['stock'],

                            'stock_destino' =>
                            $destino['stock'],

                            'venta_origen' =>
                            $origen['venta'],

                            'venta_destino' =>
                            $destino['venta'],

                            'motivo' =>
                            $motivo,

                            'estado' =>
                            'PENDIENTE',

                            'fecha_generacion' =>
                            $fechaGeneracion,
                        ];


                        /**
                         * =================================================
                         * ACTUALIZAR RESTANTES
                         * =================================================
                         */
                        $pendienteTransferir -=
                            $cantidad;

                        $origen['exceso'] -=
                            $cantidad;

                        $totalSugerencias++;


                        /**
                         * Ya llegó al límite del destino.
                         */
                        if (
                            $pendienteTransferir <= 0
                        ) {
                            break;
                        }
                    }

                    unset($origen);
                }

                unset($destino);
            }


            /**
             * =====================================================
             * 10. INSERT MASIVO
             * =====================================================
             */
            if (!empty($insertar)) {

                foreach (
                    array_chunk(
                        $insertar,
                        1000
                    ) as $chunk
                ) {

                    RedistribucionSugerida::insert(
                        $chunk
                    );
                }
            }


            /**
             * =====================================================
             * 11. CONFIRMAR
             * =====================================================
             */
            DB::commit();


            /**
             * =====================================================
             * 12. SIN RESULTADOS
             * =====================================================
             */
            if (
                $totalSugerencias === 0
            ) {

                return redirect()
                    ->route(
                        'RedistribucionSugeridas.index'
                    )
                    ->with(
                        'warning',
                        'El análisis terminó, pero no se encontraron redistribuciones necesarias.'
                    );
            }


            /**
             * =====================================================
             * 13. RESULTADO
             * =====================================================
             */
            return redirect()
                ->route(
                    'RedistribucionSugeridas.index'
                )
                ->with(
                    'success',
                    'Análisis realizado correctamente. Se generaron '
                        . $totalSugerencias
                        . ' sugerencias.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Error redistribucion',
                [
                    'error' =>
                    $e->getMessage(),

                    'line' =>
                    $e->getLine(),

                    'file' =>
                    $e->getFile(),

                    'trace' =>
                    $e->getTraceAsString(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Error al analizar redistribución: '
                        . $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | APROBAR
    |--------------------------------------------------------------------------
    */

    public function aprobar(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        DB::beginTransaction();

        try {

            /*
             * Bloquear registros mientras se aprueban.
             */

            $sugerencias = RedistribucionSugerida::whereIn(
                'id',
                $request->ids
            )
                ->where('estado', 'PENDIENTE')
                ->lockForUpdate()
                ->get();


            if ($sugerencias->isEmpty()) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'No existen sugerencias pendientes para aprobar.'
                );
            }


            /*
             * Usuario.
             */

            $usuario = optional(auth()->user())->name
                ?? optional(auth()->user())->email
                ?? 'SISTEMA';


            /*
             * Crear proceso.
             */

            $proceso = RedistribucionProceso::create([

                'fecha' => now(),

                'usuario' => $usuario,

                'total_productos' =>
                $sugerencias
                    ->unique('codigo')
                    ->count(),

                'total_movimientos' =>
                $sugerencias->count(),

                'observacion' =>
                'Redistribución aprobada desde sugerencias automáticas.',
            ]);


            /*
             * Detalles.
             */

            $fecha = now();

            $detallesInsertar = [];


            foreach ($sugerencias as $sugerencia) {

                $detallesInsertar[] = [

                    'proceso_id' =>
                    $proceso->id,

                    'codigo' =>
                    $sugerencia->codigo,

                    'sucursal_origen' =>
                    $sugerencia->sucursal_origen,

                    'sucursal_destino' =>
                    $sugerencia->sucursal_destino,

                    'cantidad' =>
                    $sugerencia->cantidad,

                    'estado' =>
                    'PENDIENTE',

                    'observacion' =>
                    $sugerencia->motivo,

                    'fecha' =>
                    $fecha,

                    'lote_id' =>
                    null,
                ];
            }


            /*
             * Insertar detalles.
             */

            foreach (
                array_chunk($detallesInsertar, 1000)
                as $chunk
            ) {

                RedistribucionProcesoDetalle::insert(
                    $chunk
                );
            }


            /*
             * Marcar sugerencias como aprobadas.
             */

            RedistribucionSugerida::whereIn(
                'id',
                $sugerencias->pluck('id')
            )
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado' => 'APROBADA',
                ]);


            DB::commit();


            return redirect()
                ->route('RedistribucionSugeridas.lotes')
                ->with(
                    'success',
                    'Proceso #' .
                        $proceso->id .
                        ' creado correctamente con ' .
                        $sugerencias->count() .
                        ' movimientos.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Error aprobando redistribución: ' .
                    $e->getMessage(),
                [
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]
            );

            return back()->with(
                'error',
                'Error al aprobar las redistribuciones: ' .
                    $e->getMessage()
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RECHAZAR
    |--------------------------------------------------------------------------
    */

    public function rechazar(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        DB::beginTransaction();

        try {

            $cantidad = RedistribucionSugerida::whereIn(
                'id',
                $request->ids
            )
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado' => 'RECHAZADA',
                ]);


            if ($cantidad == 0) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'No existen sugerencias pendientes para rechazar.'
                );
            }


            DB::commit();


            return redirect()
                ->route('RedistribucionSugeridas.index')
                ->with(
                    'success',
                    $cantidad .
                        ' sugerencias rechazadas correctamente.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Error rechazando redistribución: ' .
                    $e->getMessage(),
                [
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]
            );

            return back()->with(
                'error',
                'Error al rechazar las redistribuciones: ' .
                    $e->getMessage()
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VER PROCESO
    |--------------------------------------------------------------------------
    */

    public function proceso($id)
    {
        $proceso = RedistribucionProceso::with([
            'detalles.origen',
            'detalles.destino'
        ])->findOrFail($id);

        return view(
            'redistribucion_sugeridas.proceso',
            compact('proceso')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERAR LOTE
    |--------------------------------------------------------------------------
    */

    public function generarLote(Request $request)
    {
        $request->validate([
            'proceso_id' => 'required|integer'
        ]);

        DB::beginTransaction();

        try {

            $proceso = RedistribucionProceso::findOrFail(
                $request->proceso_id
            );


            /*
         * ============================================================
         * OBTENER DETALLES PENDIENTES SIN LOTE
         * ============================================================
         */

            $detalles = RedistribucionProcesoDetalle::where(
                'proceso_id',
                $proceso->id
            )
                ->where('estado', 'PENDIENTE')
                ->whereNull('lote_id')
                ->lockForUpdate()
                ->get();


            if ($detalles->isEmpty()) {

                DB::rollBack();

                return back()->with(
                    'warning',
                    'No existen transferencias pendientes para generar el lote.'
                );
            }


            /*
         * ============================================================
         * NÚMERO DE LOTE CORRELATIVO
         * ============================================================
         *
         * LOT-1
         * LOT-2
         * LOT-3
         * LOT-4
         * LOT-5
         * ...
         *
         * Busca el último lote generado y suma 1.
         */

            $ultimoLote = RedistribucionLote::orderByDesc('id')
                ->lockForUpdate()
                ->first();


            if ($ultimoLote) {

                /*
             * Extraer solamente el número.
             *
             * Ejemplo:
             *
             * LOT-2
             *
             * se convierte en:
             *
             * 2
             */

                $ultimoNumero = (int) str_replace(
                    'LOT-',
                    '',
                    $ultimoLote->numero_lote
                );

                $siguienteNumero = $ultimoNumero + 1;
            } else {

                /*
             * Si todavía no existe ningún lote.
             */

                $siguienteNumero = 1;
            }


            /*
         * Crear número definitivo.
         */

            $numeroLote = 'LOT-' . $siguienteNumero;


            /*
         * ============================================================
         * VERIFICAR QUE NO EXISTA
         * ============================================================
         *
         * Esto agrega una protección adicional.
         */

            while (
                RedistribucionLote::where(
                    'numero_lote',
                    $numeroLote
                )->exists()
            ) {

                $siguienteNumero++;

                $numeroLote = 'LOT-' . $siguienteNumero;
            }


            /*
         * ============================================================
         * USUARIO
         * ============================================================
         */

            $usuarioId = auth()->id();

            $usuarioNombre = auth()->user()->name
                ?? auth()->user()->email
                ?? 'SISTEMA';


            /*
         * ============================================================
         * TOTALES
         * ============================================================
         */

            $totalMovimientos =
                $detalles->count();

            $totalTransferencias =
                $totalMovimientos;

            $totalProductos =
                $detalles
                ->unique('codigo')
                ->count();

            $totalUnidades =
                $detalles->sum('cantidad');


            /*
         * ============================================================
         * CREAR LOTE
         * ============================================================
         */

            $lote = RedistribucionLote::create([

                'proceso_id' =>
                $proceso->id,

                'numero_lote' =>
                $numeroLote,

                'fecha_generacion' =>
                now(),

                'usuario' =>
                $usuarioNombre,

                'total_movimientos' =>
                $totalMovimientos,

                'total_transferencias' =>
                $totalTransferencias,

                'total_productos' =>
                $totalProductos,

                'total_unidades' =>
                $totalUnidades,

                'estado' =>
                'GENERADO',

                'observacion' =>
                'Lote generado desde proceso de redistribución #' .
                    $proceso->id,

                'usuario_generacion' =>
                $usuarioId,
            ]);


            /*
         * ============================================================
         * ASOCIAR DETALLES AL LOTE
         * ============================================================
         */

            RedistribucionProcesoDetalle::whereIn(
                'id',
                $detalles->pluck('id')
            )->update([
                'lote_id' => $lote->id,
            ]);


            /*
         * ============================================================
         * CONFIRMAR TRANSACCIÓN
         * ============================================================
         */

            DB::commit();


            /*
         * ============================================================
         * REDIRECCIONAR
         * ============================================================
         */

            return redirect()
                ->route('RedistribucionSugeridas.lotes')
                ->with(
                    'success',
                    'Lote ' .
                        $numeroLote .
                        ' generado correctamente con ' .
                        $totalTransferencias .
                        ' transferencias.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Error generando lote de redistribución: ' .
                    $e->getMessage(),
                [
                    'line' =>
                    $e->getLine(),

                    'file' =>
                    $e->getFile(),

                    'proceso_id' =>
                    $request->proceso_id ?? null,
                ]
            );

            return back()->with(
                'error',
                'Error al generar el lote: ' .
                    $e->getMessage()
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VER LOTE
    |--------------------------------------------------------------------------
    */

    public function lote($id)
    {
        $lote = RedistribucionLote::findOrFail($id);

        $detalles = RedistribucionProcesoDetalle::with([
            'origen',
            'destino',
            'proceso'
        ])
            ->where('lote_id', $lote->id)
            ->orderBy('codigo')
            ->get();

        return view(
            'redistribucion_sugeridas.lote',
            compact(
                'lote',
                'detalles'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESAR LOTE
    |--------------------------------------------------------------------------
    */

    public function procesarLote(Request $request, $id)
    {
        $inicioTotal = microtime(true);

        DB::beginTransaction();

        try {

            $lote = RedistribucionLote::lockForUpdate()
                ->findOrFail($id);


            Log::info('PROCESANDO LOTE', [
                'lote_id' =>
                $lote->id,

                'numero_lote' =>
                $lote->numero_lote,

                'estado_lote' =>
                $lote->estado
            ]);


            /*
             * Validar estado.
             */

            if ($lote->estado !== 'GENERADO') {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' =>
                    'El lote no puede procesarse porque actualmente se encuentra en estado: ' .
                        $lote->estado
                ], 422);
            }


            /*
             * Pasar detalles a EN PROCESO.
             */

            $totalTransferencias =
                RedistribucionProcesoDetalle::where(
                    'lote_id',
                    $lote->id
                )
                ->where(
                    'estado',
                    'PENDIENTE'
                )
                ->update([
                    'estado' =>
                    'EN PROCESO'
                ]);


            if ($totalTransferencias === 0) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' =>
                    'El lote está en estado GENERADO, pero no tiene detalles PENDIENTES.'
                ], 422);
            }


            /*
             * Actualizar lote.
             */

            $lote->update([
                'estado' =>
                'EN PROCESO'
            ]);


            DB::commit();


            Log::info(
                'LOTE PROCESADO CORRECTAMENTE',
                [
                    'lote_id' =>
                    $lote->id,

                    'total_transferencias' =>
                    $totalTransferencias,

                    'tiempo' =>
                    microtime(true) -
                        $inicioTotal
                ]
            );


            return response()->json([
                'success' => true,

                'message' =>
                'Lote ' .
                    $lote->numero_lote .
                    ' procesado correctamente.',

                'lote_id' =>
                $lote->id,

                'total_transferencias' =>
                $totalTransferencias
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'ERROR PROCESANDO LOTE',
                [
                    'lote_id' =>
                    $id,

                    'error' =>
                    $e->getMessage(),

                    'line' =>
                    $e->getLine(),

                    'file' =>
                    $e->getFile()
                ]
            );

            return response()->json([
                'success' => false,

                'message' =>
                'Error al procesar el lote: ' .
                    $e->getMessage()
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FINALIZAR LOTE
    |--------------------------------------------------------------------------
    */

    public function finalizarLote(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $lote = RedistribucionLote::lockForUpdate()
                ->findOrFail($id);


            /*
             * Validar estado.
             */

            if ($lote->estado !== 'EN PROCESO') {

                DB::rollBack();

                return response()->json([
                    'success' => false,

                    'message' =>
                    'El lote no puede finalizarse porque actualmente se encuentra en estado: ' .
                        $lote->estado
                ], 422);
            }


            /*
             * =====================================================
             * FINALIZAR DETALLES
             *
             * IMPORTANTE:
             *
             * updated_at se actualiza automáticamente.
             *
             * Esa fecha será utilizada para determinar los
             * 30 días de bloqueo.
             * =====================================================
             */

            $totalFinalizados =
                RedistribucionProcesoDetalle::where(
                    'lote_id',
                    $lote->id
                )
                ->where(
                    'estado',
                    'EN PROCESO'
                )
                ->update([
                    'estado' =>
                    'FINALIZADO',
                ]);


            /*
             * Validar detalles.
             */

            if ($totalFinalizados === 0) {

                DB::rollBack();

                return response()->json([
                    'success' => false,

                    'message' =>
                    'No existen transferencias en proceso para finalizar.'
                ], 422);
            }


            /*
             * Finalizar lote.
             */

            $lote->update([
                'estado' => 'FINALIZADO',
                'fecha_finalizacion' => now(),
            ]);


            DB::commit();


            return response()->json([
                'success' => true,

                'message' =>
                'Lote ' .
                    $lote->numero_lote .
                    ' finalizado correctamente con ' .
                    $totalFinalizados .
                    ' transferencias.',

                'lote_id' =>
                $lote->id,

                'total_transferencias' =>
                $totalFinalizados
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Error finalizando lote de redistribución: ' .
                    $e->getMessage(),
                [
                    'line' =>
                    $e->getLine(),

                    'file' =>
                    $e->getFile(),

                    'lote_id' =>
                    $id,
                ]
            );

            return response()->json([
                'success' => false,

                'message' =>
                'Error al finalizar el lote: ' .
                    $e->getMessage()
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE LOTES
    |--------------------------------------------------------------------------
    */

    public function lotes()
    {
        /*
         * Procesos pendientes.
         */

        $procesosPendientes =
            RedistribucionProceso::whereHas(
                'detalles',
                function ($query) {

                    $query
                        ->where(
                            'estado',
                            'PENDIENTE'
                        )
                        ->whereNull(
                            'lote_id'
                        );
                }
            )
            ->with([
                'detalles' => function ($query) {

                    $query
                        ->where(
                            'estado',
                            'PENDIENTE'
                        )
                        ->whereNull(
                            'lote_id'
                        );
                }
            ])
            ->orderByDesc('fecha')
            ->get();


        /*
         * Lotes generados.
         */

        $lotesGenerados =
            RedistribucionLote::where(
                'estado',
                'GENERADO'
            )
            ->with('detalles')
            ->orderByDesc(
                'fecha_generacion'
            )
            ->paginate(
                10,
                ['*'],
                'generados'
            );


        /*
         * Lotes en proceso.
         */

        $lotesEnProceso =
            RedistribucionLote::where(
                'estado',
                'EN PROCESO'
            )
            ->with('detalles')
            ->orderByDesc(
                'fecha_generacion'
            )
            ->paginate(
                10,
                ['*'],
                'en_proceso'
            );


        /*
         * Lotes finalizados.
         */

        $lotesFinalizados =
            RedistribucionLote::where(
                'estado',
                'FINALIZADO'
            )
            ->with('detalles')
            ->orderByDesc(
                'fecha_generacion'
            )
            ->paginate(
                10,
                ['*'],
                'finalizados'
            );


        return view(
            'redistribucion_sugeridas.lotes',
            compact(
                'procesosPendientes',
                'lotesGenerados',
                'lotesEnProceso',
                'lotesFinalizados'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORTAR LOTE PDF
    |--------------------------------------------------------------------------
    */

    public function exportarLotePdf($id)
    {
        try {

            $lote = RedistribucionLote::with([
                'detalles.origen',
                'detalles.destino'
            ])->findOrFail($id);


            /*
        |--------------------------------------------------------------------------
        | OBTENER CÓDIGOS DEL LOTE
        |--------------------------------------------------------------------------
        */

            $codigos = $lote->detalles
                ->pluck('codigo')
                ->filter()
                ->unique()
                ->values();


            /*
        |--------------------------------------------------------------------------
        | BUSCAR GRUPO_PLAN = DESCRIPCIÓN
        |--------------------------------------------------------------------------
        */

            $descripciones = DB::table('stock_ventas_sucursales')
                ->whereIn('codigo', $codigos)
                ->select(
                    'codigo',
                    'grupo_plan'
                )
                ->orderBy('id')
                ->get()
                ->groupBy('codigo')
                ->map(function ($items) {

                    return $items->first()->grupo_plan ?? '-';
                });


            /*
        |--------------------------------------------------------------------------
        | ASIGNAR DESCRIPCIÓN A CADA DETALLE
        |--------------------------------------------------------------------------
        */

            $lote->detalles->each(function ($detalle) use ($descripciones) {

                $detalle->descripcion =
                    $descripciones[$detalle->codigo] ?? '-';
            });


            /*
        |--------------------------------------------------------------------------
        | ORDENAR DETALLES
        |--------------------------------------------------------------------------
        */

            $lote->setRelation(
                'detalles',
                $lote->detalles
                    ->sortBy(function ($detalle) {

                        return [
                            // 1. ORIGEN
                            strtoupper(
                                trim($detalle->origen->suc_descri ?? '')
                            ),

                            // 2. DESTINO
                            strtoupper(
                                trim($detalle->destino->suc_descri ?? '')
                            ),

                            // 3. CÓDIGO
                            strtoupper(
                                trim($detalle->codigo ?? '')
                            )
                        ];
                    })
                    ->values()
            );


            /*
        |--------------------------------------------------------------------------
        | GENERAR PDF
        |--------------------------------------------------------------------------
        */

            $pdf = Pdf::loadView(
                'redistribucion_sugeridas.pdf.lote',
                compact('lote')
            );


            /*
        |--------------------------------------------------------------------------
        | CONFIGURACIÓN
        |--------------------------------------------------------------------------
        */

            $pdf->setPaper(
                'A4',
                'portrait'
            );


            /*
        |--------------------------------------------------------------------------
        | DESCARGAR
        |--------------------------------------------------------------------------
        */

            return $pdf->download(
                'Lote-' . $lote->numero_lote . '.pdf'
            );
        } catch (\Exception $e) {

            Log::error(
                'Error exportando lote a PDF',
                [
                    'lote_id' => $id,
                    'error'   => $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile()
                ]
            );


            return back()->with(
                'error',
                'No se pudo generar el PDF: ' .
                    $e->getMessage()
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORTAR LOTE EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportarLoteExcel($id)
    {
        try {

            /*
         * ============================================================
         * OBTENER LOTE
         * ============================================================
         *
         * Cargamos:
         *
         * lote
         *   └── detalles
         *         ├── origen
         *         └── destino
         */

            $lote = RedistribucionLote::with([
                'detalles.origen',
                'detalles.destino'
            ])->findOrFail($id);


            /*
         * ============================================================
         * ORDENAR DETALLES
         * ============================================================
         *
         * Primero:
         *     Sucursal Origen
         *
         * Después:
         *     Código
         */

            $lote->setRelation(
                'detalles',
                $lote->detalles
                    ->sortBy(function ($detalle) {

                        return [
                            // 1. ORIGEN
                            strtoupper(
                                trim($detalle->origen->suc_descri ?? '')
                            ),

                            // 2. DESTINO
                            strtoupper(
                                trim($detalle->destino->suc_descri ?? '')
                            ),

                            // 3. CÓDIGO
                            strtoupper(
                                trim($detalle->codigo ?? '')
                            )
                        ];
                    })
                    ->values()
            );


            /*
         * ============================================================
         * EXPORTAR EXCEL
         * ============================================================
         */

            return Excel::download(
                new LoteRedistribucionExport($lote),
                'Lote-' . $lote->numero_lote . '.xlsx'
            );
        } catch (\Exception $e) {

            /*
         * ============================================================
         * REGISTRAR ERROR
         * ============================================================
         */

            Log::error(
                'Error exportando lote a Excel',
                [
                    'lote_id' => $id,
                    'error'   => $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile()
                ]
            );


            /*
         * ============================================================
         * VOLVER CON ERROR
         * ============================================================
         */

            return back()->with(
                'error',
                'No se pudo generar el Excel: ' . $e->getMessage()
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VER DETALLE DE SUGERENCIA
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $redistribucion =
            RedistribucionSugerida::with([
                'origen',
                'destino'
            ])->findOrFail($id);


        return view(
            'redistribucion_sugeridas.show',
            compact('redistribucion')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR SUGERENCIA
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $redistribucion =
            RedistribucionSugerida::findOrFail($id);


        /*
         * No permitir eliminar una sugerencia ya aprobada.
         */

        if ($redistribucion->estado === 'APROBADA') {

            return back()->with(
                'error',
                'No se puede eliminar una sugerencia que ya fue aprobada.'
            );
        }


        $redistribucion->delete();


        return back()->with(
            'success',
            'Sugerencia eliminada correctamente.'
        );
    }
}
