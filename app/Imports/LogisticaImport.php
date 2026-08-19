<?php

namespace App\Imports;

use App\Models\Ot;
use App\Models\OtTrazabilidad;
use App\Models\OtLogisticaDetalle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LogisticaImport implements ToCollection, WithHeadingRow
{
    private $fechaProceso;
    public function __construct($fechaProceso)
    {
        $this->fechaProceso = $fechaProceso;
    }

    public function collection(Collection $rows)
    {
        set_time_limit(0);

        foreach ($rows as $index => $row) {
            // DB::beginTransaction();
            try {
                // =========================
                // DATOS EXCEL
                // =========================

                $codigo = trim($row['codigo'] ?? '');
                $nroOtExcel = trim($row['nro_ot'] ?? '');
                $resultado = (int)($row['resultado'] ?? 0);

                if (empty($codigo)) {
                    continue;
                }

                // =========================
                // BUSCAR OT
                // =========================

                $ot = Ot::where('codigo', $codigo)->first();

                if (!$ot) {
                    Log::warning('CODIGO NO ENCONTRADO', [
                        'codigo' => $codigo,
                        'nro_ot' => $nroOtExcel
                    ]);

                    continue;
                }

                // =========================
                // VALIDAR PRODUCTO TERMINADO
                // =========================

                $productoTerminado = OtTrazabilidad::where('id_ot', $ot->id_ot)
                    ->where('proceso', 'TERMINACION - PRODUCTO TERMINADO')
                    ->first();

                if (!$productoTerminado) {
                    Log::warning('NO TIENE PRODUCTO TERMINADO', [
                        'codigo' => $codigo
                    ]);

                    continue;
                }

                // =========================
                // BUSCAR ULTIMA LOGISTICA
                // =========================

                $ultimoRegistro = OtTrazabilidad::where('id_ot', $ot->id_ot)
                    ->where('proceso', 'LOGISTICA - LOGISTICA Y DISTRIBUCION')
                    ->orderBy('id_trazabilidad', 'desc')
                    ->first();

                // =========================
                // EVITAR DUPLICADO
                // =========================

                if (
                    $ultimoRegistro &&
                    (int)$ultimoRegistro->resultado === $resultado
                ) {
                    Log::info('LOGISTICA YA EXISTE', [
                        'codigo' => $codigo,
                        'resultado' => $resultado
                    ]);
                    continue;
                }

                // =========================
                // CREAR TRAZABILIDAD
                // =========================

                $trazabilidad = OtTrazabilidad::create([
                    'id_ot' => $ot->id_ot,
                    'proceso' => 'LOGISTICA - LOGISTICA Y DISTRIBUCION',
                    'resultado' => $resultado,
                    'fecha_proceso' => $this->fechaProceso,
                ]);

                Log::info('TRAZABILIDAD CREADA', [
                    'id' => $trazabilidad->id_trazabilidad,
                    'exists' => $trazabilidad->exists,
                    'atributos' => $trazabilidad->getAttributes(),
                ]);

                if (!$trazabilidad || !$trazabilidad->id_trazabilidad) {

                    Log::warning('NO SE PUDO CREAR LA TRAZABILIDAD', [
                        'codigo' => $codigo,
                        'ot' => $ot->nro_ot
                    ]);

                    continue;
                }

                $sucursales = [

                    'SL' => $row['sl'] ?? 0,
                    'Bonanza' => $row['bonanza'] ?? 0,
                    'Shopp' => $row['shopp'] ?? 0,
                    'Luque' => $row['luque'] ?? 0,
                    'Rural' => $row['rural'] ?? 0,
                    'Mall' => $row['mall'] ?? 0,
                    'Ayala' => $row['ayala'] ?? 0,
                    'Mariano' => $row['mariano'] ?? 0,
                    'Ñemby' => $row['nemby'] ?? 0,
                    'Pinedo' => $row['pinedo'] ?? 0,
                    'L06' => $row['l06'] ?? 0,
                    'Multi' => $row['multi'] ?? 0,
                    'Los Jardines' => $row['los_jardines'] ?? 0,
                    'Modelo Muestra' => $row['modelo_muestra'] ?? 0,

                ];

                foreach ($sucursales as $sucursal => $cantidad) {

                    $cantidad = (int) $cantidad;

                    if ($cantidad <= 0) {
                        continue;
                    }

                    $detalle = OtLogisticaDetalle::where('id_ot', $ot->id_ot)
                        ->where('sucursal', $sucursal)
                        ->first();

                    if ($detalle) {

                        $cantidadAnterior = (int) $detalle->cantidad;

                        $detalle->cantidad = $cantidadAnterior + $cantidad;
                        $detalle->id_trazabilidad = $trazabilidad->id_trazabilidad;
                        $detalle->save();

                        Log::info('LOGISTICA SUMADA', [
                            'ot' => $ot->nro_ot,
                            'sucursal' => $sucursal,
                            'cantidad_anterior' => $cantidadAnterior,
                            'cantidad_agregada' => $cantidad,
                            'cantidad_actual' => $detalle->cantidad,
                            'trazabilidad' => $trazabilidad->id_trazabilidad,
                        ]);
                    } else {

                        $detalle = OtLogisticaDetalle::create([
                            'id_ot' => $ot->id_ot,
                            'id_trazabilidad' => $trazabilidad->id_trazabilidad,
                            'sucursal' => $sucursal,
                            'cantidad' => $cantidad,
                        ]);

                        Log::info('LOGISTICA CREADA', [
                            'ot' => $ot->nro_ot,
                            'sucursal' => $sucursal,
                            'cantidad' => $cantidad,
                            'trazabilidad' => $trazabilidad->id_trazabilidad,
                        ]);
                    }
                }

                $detalle = OtLogisticaDetalle::where('id_trazabilidad', $trazabilidad->id_trazabilidad)
                    ->get();

                Log::info('DETALLES DESPUES DEL PROCESO', [
                    'cantidad' => $detalle->count(),
                    'datos' => $detalle->toArray(),
                ]);

                Log::info('TOTAL TRAZABILIDADES', [
                    'cantidad' => DB::table('ot_trazabilidad')->count(),
                    'ultima' => DB::table('ot_trazabilidad')->max('id_trazabilidad'),
                ]);

                $traza = DB::table('ot_trazabilidad')
                    ->where('id_trazabilidad', $trazabilidad->id_trazabilidad)
                    ->first();

                $detalles = DB::table('ot_logistica_detalle')
                    ->where('id_trazabilidad', $trazabilidad->id_trazabilidad)
                    ->count();

                Log::info('POST PROCESO', [
                    'id_trazabilidad' => $trazabilidad->id_trazabilidad,
                    'trazabilidad_en_bd' => $traza ? 'SI' : 'NO',
                    'cantidad_detalles' => $detalles,
                ]);

                Log::info('LOGISTICA CARGADA', [
                    'codigo' => $codigo,
                    'ot' => $ot->nro_ot,
                    'total_movimiento' => $resultado,
                    'trazabilidad' => $trazabilidad->id_trazabilidad,
                ]);
            } catch (\Exception $e) {
                Log::error('ERROR AL PROCESAR LOGISTICA', [
                    'codigo' => $codigo,
                    'nro_ot' => $nroOtExcel,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                ]);
            }
        }
    }
}
