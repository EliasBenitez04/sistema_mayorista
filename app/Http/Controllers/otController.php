<?php

namespace App\Http\Controllers;

use App\Imports\LogisticaImport;
use App\Models\Ot;
use App\Imports\OtImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ot index')->only('index');
        $this->middleware('permission:ot create')->only('create', 'store');
        $this->middleware('permission:ot edit')->only('edit', 'update');
        $this->middleware('permission:ot destroy')->only('destroy');
        $this->middleware('permission:ot importar')->only('importar');
        $this->middleware('permission:ot dashboard')->only('dashboard');
        // Reutilizamos el permiso "ot edit" para agregar procesos manualmente.
        // Si tenés un permiso propio (ej. "ot proceso"), cambialo acá.
        // $this->middleware('permission:ot edit')->only('nuevoProceso', 'guardarProceso');
    }

    public function index()
    {

        $ots = Ot::paginate(20);

        return view('ots.index', compact('ots'));
    }

    public function create()
    {
        $ots = Ot::orderBy('nro_ot')
            ->get()
            ->mapWithKeys(function ($ot) {
                return [
                    $ot->id_ot => $ot->nro_ot . ' - ' . $ot->codigo
                ];
            });

        return view('ots.create', compact('ots'));
    }

    public function getOtDetails($id)
    {
        $ot = Ot::where('id_ot', $id)->first();

        if ($ot) {
            return response()->json($ot);
        }

        return response()->json(null);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            // Buscar la OT existente
            $ot = Ot::findOrFail($request->id_ot);

            //==========================
            // DETALLE DE PROCESOS
            //==========================

            if ($request->has('proceso')) {

                foreach ($request->proceso as $key => $proceso) {

                    if (empty($proceso)) {
                        continue;
                    }

                    $ot->trazabilidades()->create([

                        'id_ot'         => $ot->id_ot,
                        'proceso'       => $proceso,
                        'resultado'     => $request->resultado[$key] ?? null,
                        'fecha_proceso' => $request->fecha_proceso[$key],

                    ]);
                }
            }

            DB::commit();

            alert()->success(
                'Éxito',
                'Trazabilidad registrada correctamente.'
            );
        } catch (\Exception $e) {

            DB::rollBack();

            alert()->error(
                'Error',
                $e->getMessage()
            );

            return redirect()
                ->back()
                ->withInput();
        }

        return redirect()->route('ots.index');
    }

    public function show($id)
    {
        $ot = Ot::with('trazabilidades')->findOrFail($id);

        return view('ots.show', compact('ot'));
    }

    public function edit($id)
    {
        $ot = Ot::findOrFail($id);

        return view('ots.edit', compact('ot'));
    }

    public function update(Request $request, $id)
    {
        $ot = Ot::findOrFail($id);

        $request->validate([
            'nro_ot' => 'required|unique:ot,nro_ot,' . $id . ',id_ot',
            'codigo' => 'required',
            'descripcion' => 'required',
            'cantidad_orden' => 'required|numeric'
        ]);

        $ot->update($request->all());

        alert()->success('Éxito', 'OT actualizada correctamente');

        return redirect()->route('ots.index');
    }

    public function buscarOt($nro_ot)
    {
        $ot = Ot::where('nro_ot', $nro_ot)->first();

        if (!$ot) {
            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'codigo' => $ot->codigo,
            'descripcion' => $ot->descripcion,
            'cantidad_orden' => $ot->cantidad_orden
        ]);
    }

    public function destroy($id)
    {
        $ot = Ot::findOrFail($id);

        $ot->delete();

        alert()->success('Éxito', 'OT eliminada correctamente');

        return redirect()->route('ots.index');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(
            new OtImport,
            $request->file('archivo')
        );

        alert()->success('Éxito', 'Importación realizada correctamente');

        return redirect()->route('ots.index');
    }

    public function importarLogistica(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls',
            'fecha_proceso' => 'required|date',
        ]);

        Excel::import(
            new LogisticaImport($request->fecha_proceso),
            $request->file('archivo')
        );

        alert()->success(
            'Éxito',
            'Importación de logística realizada correctamente'
        );

        return back();
    }

    public function buscar(Request $request)
    {
        $ot = Ot::with('trazabilidades')
            ->where('nro_ot', $request->nro_ot)
            ->first();

        return view('ots.resultado', compact('ot'));
    }

    /**
     * Muestra el formulario para agregar un nuevo proceso (trazabilidad)
     * a una OT existente. Trae todos los datos de la OT ya cargados
     * (nro_ot, codigo, descripcion, cantidad_orden) para que el usuario
     * solo tenga que completar manualmente los datos del proceso
     * (proceso, resultado, fecha_proceso).
     */
    public function nuevoProceso($id)
    {
        $ot = Ot::findOrFail($id);

        return view('ots.proceso-create', compact('ot'));
    }

    /**
     * Guarda el nuevo proceso (trazabilidad) asociado a la OT.
     * El id_ot se toma de la propia OT (no lo maneja el usuario),
     * evitando así inconsistencias.
     */
    public function guardarProceso(Request $request, $id)
    {
        $ot = Ot::findOrFail($id);

        $request->validate([
            'proceso'       => 'required|string|max:100',
            'resultado'     => 'nullable|integer',
            'fecha_proceso' => 'required|date',
        ]);

        // La unique de la tabla (id_ot, proceso, fecha_proceso) evita
        // duplicados exactos; si ya existe, Laravel lanzará un
        // QueryException que se puede capturar más adelante si se
        // quiere mostrar un mensaje personalizado.
        $ot->trazabilidades()->create([
            'proceso'       => $request->proceso,
            'resultado'     => $request->resultado,
            'fecha_proceso' => $request->fecha_proceso,
        ]);

        alert()->success('Éxito', 'Proceso agregado correctamente');

        return redirect()->route('ots.show', $ot->id_ot);
    }

    /**
     * Catálogo de procesos del flujo de producción y el % de avance que
     * representa cada uno cuando es el ÚLTIMO proceso registrado en la OT.
     *
     * ⚠️ El ORDEN en que están declaradas las claves aquí es también el
     * orden en que se mostrarán los procesos en el dashboard (no el orden
     * por fecha). Esto es clave porque algunas OT pueden tener procesos
     * registrados con fechas "desordenadas" o simplemente no tener todos
     * los procesos del flujo.
     */
    private const FLUJO_PROCESOS = [
        'PEDIDO A PRODUCCION'  => 5,
        'ORDEN DE TRABAJO'     => 10,
        'MOLDERIA'             => 15,
        'PROTOTIPO'            => 20,
        'DISEÑO GRAFICO'       => 25,
        'TIZADAS'              => 30,
        'CORTE'                => 40,
        'LOTEO Y DISTRIBUCION' => 45,
        'REVELADO'             => 50,
        'SERIGRAFIA'           => 55,
        'BORDADO'              => 60,
        'COSTURA INTERNA'      => 70,
        'LAVANDERIA'           => 75,
        'PRETERMINACION'       => 80,
        'ATRAQUES'             => 85,
        'INGRESO TERMINACION'  => 90,
        'TERMINACION'          => 95,
        'PRODUCTO TERMINADO'   => 100,
        'LOGISTICA Y DISTRIBUCION'   => 100,
        'REVISION'             => 100,
        'PEDIDO SUSPENDIDO'    => -1, // Estado especial, no representa avance
    ];

    /**
     * Nombre exacto (normalizado) del proceso que representa una OT
     * suspendida. Se trata aparte porque no debe sumar avance ni marcar
     * la OT como finalizada, aunque sí debe listarse en la tabla.
     */
    private const PROCESO_SUSPENDIDO = 'PEDIDO SUSPENDIDO';

    /**
     * Días sin movimiento a partir de los cuales la OT se marca como
     * "Demorada" en el reporte (alerta visual para gerencia).
     */
    private const DIAS_PARA_ALERTA = 30;

    public function dashboard(Request $request)
    {
        $ot = null;
        $resumen = null;
        $procesos = collect();
        $labels = [];
        $duraciones = [];
        $avancesAcumulados = [];
        $mensaje = null;

        $nroOt = trim((string) $request->input('nro_ot'));

        if ($nroOt !== '') {

            // Ya no ordenamos por fecha aquí: el orden final (por flujo)
            // se calcula en construirReporte() para que el dashboard
            // muestre siempre PEDIDO A PRODUCCION -> ... -> PRODUCTO
            // TERMINADO / REVISION, sin importar el orden de las fechas.
            $ot = Ot::where('nro_ot', $nroOt)
                ->with([
                    'trazabilidades',
                    'logisticaDetalle'
                ])
                ->first();

            if (!$ot) {
                $mensaje = "No se encontró ninguna OT con el número \"{$nroOt}\".";
            } elseif ($ot->trazabilidades->isEmpty()) {
                $mensaje = "La OT N° {$ot->nro_ot} no tiene procesos registrados todavía.";
            } else {
                [$procesos, $resumen] = $this->construirReporte($ot->trazabilidades);

                $labels = $procesos->pluck('proceso')->all();
                $duraciones = $procesos->pluck('duracion_dias')->all();
                $avancesAcumulados = $procesos->pluck('avance_acumulado')->all();
            }
        }

        return view('dashboard.ot', [
            'ot'                => $ot,
            'procesos'          => $procesos,
            'resumen'           => $resumen,
            'labels'            => $labels,
            'duraciones'        => $duraciones,
            'avancesAcumulados' => $avancesAcumulados,
            'mensaje'           => $mensaje,
            'nroOtBuscada'      => $nroOt,
        ]);
    }

    /**
     * Construye la línea de tiempo enriquecida (duración real, avance
     * acumulado, etc.) y el resumen ejecutivo (KPIs) a partir de las
     * trazabilidades de una OT.
     *
     * Las filas se muestran SIEMPRE en el orden del flujo de producción
     * (FLUJO_PROCESOS), no en el orden en que fueron registradas. Si la
     * OT no tiene un proceso, simplemente no aparece esa fila (no se
     * inventan procesos faltantes).
     *
     * @param  Collection  $trazas
     * @return array{0: Collection, 1: array}
     */
    private function construirReporte(Collection $trazas): array
    {
        // 1) Ordenar por posición en el flujo real de producción y, como
        // criterio secundario (para procesos repetidos, ej. reprocesos de
        // COSTURA INTERNA), por fecha.
        $trazasOrdenadas = $trazas
            ->sortBy(function ($traza) {
                $orden = str_pad((string) $this->ordenProceso($traza->proceso), 5, '0', STR_PAD_LEFT);
                $fecha = Carbon::parse($traza->fecha_proceso)->format('YmdHis');

                return "{$orden}-{$fecha}";
            })
            ->values();

        $procesos = collect();
        $horasAcumuladas = 0.0;
        $posicion = 0;

        foreach ($trazasOrdenadas as $index => $traza) {

            $posicion++;

            $fecha = Carbon::parse($traza->fecha_proceso);

            // Obtener el siguiente proceso
            $siguienteTraza = $trazasOrdenadas->get($index + 1);

            if ($siguienteTraza) {

                $fechaSiguiente = Carbon::parse($siguienteTraza->fecha_proceso);

                // Tiempo desde este proceso hasta el siguiente
                $duracionHoras = round(
                    $fecha->diffInMinutes($fechaSiguiente) / 60,
                    2
                );
            } else {

                // Último proceso no tiene siguiente proceso
                $duracionHoras = 0.0;
            }

            $horasAcumuladas += $duracionHoras;

            $esSuspendido = $this->normalizarProceso($traza->proceso) === self::PROCESO_SUSPENDIDO;

            $procesos->push([
                'id_trazabilidad' => $traza->id_trazabilidad,
                'proceso'          => $traza->proceso,
                'resultado'        => $traza->resultado,
                'fecha'            => $fecha,
                'duracion_horas'   => $duracionHoras,
                'duracion_dias'    => round($duracionHoras / 24, 2),
                'horas_acumuladas' => round($horasAcumuladas, 2),
                'avance_acumulado' => $this->calcularAvance($traza->proceso, $posicion),
                'es_suspendido'    => $esSuspendido,
            ]);
        }

        $resumen = $this->construirResumen($procesos, $horasAcumuladas);

        return [$procesos, $resumen];
    }

    /**
     * Arma el resumen ejecutivo (KPIs) a partir de la lista de procesos
     * ya ordenada por flujo.
     */
    private function construirResumen(Collection $procesos, float $horasAcumuladas): array
    {
        $primerProceso = $procesos->first();
        $ultimoProceso = $procesos->last();

        $fechaInicio = $primerProceso['fecha'];
        $fechaUltimo = $ultimoProceso['fecha'];

        $estaSuspendida = $ultimoProceso['es_suspendido'];

        // Si el último proceso del flujo presente es "PEDIDO SUSPENDIDO",
        // el avance real de la OT es el del último proceso NO suspendido
        // (para no perder de vista en qué etapa quedó antes de suspenderse).
        if ($estaSuspendida) {
            $ultimoProcesoReal = $procesos
                ->reverse()
                ->first(fn($p) => !$p['es_suspendido']);

            $avance = $ultimoProcesoReal['avance_acumulado'] ?? 0;
            $nombreUltimoProceso = $ultimoProceso['proceso'];
        } else {
            $avance = $ultimoProceso['avance_acumulado'];
            $nombreUltimoProceso = $ultimoProceso['proceso'];
        }

        $finalizada = !$estaSuspendida && $avance >= 100;

        // Días totales que tomó la OT desde el primer registro hasta el último (con precisión decimal).
        $diasTotales = round($fechaInicio->diffInMinutes($fechaUltimo) / 60 / 24, 2);

        // Días transcurridos desde el último movimiento hasta HOY (clave para detectar OTs estancadas).
        $diasDesdeUltimoProceso = round($fechaUltimo->diffInMinutes(Carbon::now()) / 60 / 24, 2);

        // Para promedios/cuellos de botella se descarta el primer registro (su duración siempre es 0).
        $duracionesValidas = $procesos->skip(1)->pluck('duracion_horas');
        $duracionPromedio = $duracionesValidas->isNotEmpty() ? round($duracionesValidas->avg(), 2) : 0.0;

        $procesoMasLento = $procesos->skip(1)->sortByDesc('duracion_horas')->first();
        $procesoMasRapido = $procesos->skip(1)->sortBy('duracion_horas')->first();

        $estaDemorada = !$finalizada && !$estaSuspendida && $diasDesdeUltimoProceso >= self::DIAS_PARA_ALERTA;

        if ($estaSuspendida) {
            $estadoTexto = 'Suspendido';
            $estadoColor = 'secondary';
        } elseif ($finalizada) {
            $estadoTexto = 'Finalizado';
            $estadoColor = 'success';
        } elseif ($estaDemorada) {
            $estadoTexto = 'Demorado';
            $estadoColor = 'danger';
        } else {
            $estadoTexto = 'En Proceso';
            $estadoColor = 'info';
        }

        $avanceColor = $avance >= 100 ? 'success' : ($avance >= 50 ? 'info' : 'warning');

        return [
            'cantidad_procesos'         => $procesos->count(),
            'fecha_inicio'              => $fechaInicio,
            'fecha_ultimo_proceso'      => $fechaUltimo,
            'ultimo_proceso'            => $nombreUltimoProceso,
            'avance'                    => $avance,
            'avance_color'              => $avanceColor,
            'finalizada'                => $finalizada,
            'esta_suspendida'           => $estaSuspendida,
            'estado_texto'              => $estadoTexto,
            'estado_color'              => $estadoColor,
            'tiempo_total_horas'        => round($horasAcumuladas, 2),
            'tiempo_total_dias'         => $diasTotales,
            'dias_desde_ultimo_proceso' => $diasDesdeUltimoProceso,
            'esta_demorada'             => $estaDemorada,
            'duracion_promedio_horas'   => $duracionPromedio,
            'proceso_mas_lento'         => $procesoMasLento,
            'proceso_mas_rapido'        => $procesoMasRapido,
            'fecha_generacion_reporte'  => Carbon::now(),
        ];
    }

    /**
     * Normaliza el nombre de un proceso (mayúsculas + trim) para poder
     * compararlo de forma consistente contra el catálogo FLUJO_PROCESOS.
     */
    private function normalizarProceso(?string $proceso): string
    {
        $valor = (string) $proceso;

        // 1) Reemplazar espacios "especiales" que a veces vienen pegados
        // desde Excel/copiar-pegar y que NO son detectados por trim() ni
        // por \s normal: espacio no separable (\xC2\xA0) y espacio de
        // ancho cero (\xE2\x80\x8B), además de tabs/saltos de línea.
        $valor = str_replace(
            ["\xC2\xA0", "\xE2\x80\x8B", "\t", "\n", "\r"],
            ' ',
            $valor
        );

        $valor = mb_strtoupper($valor, 'UTF-8');

        // 2) Los datos reales vienen con el formato "CATEGORIA - PROCESO
        // REAL", por ejemplo "TERMINACION - PRODUCTO TERMINADO" o
        // "PRODUCCION - CORTE". El catálogo FLUJO_PROCESOS solo conoce el
        // nombre del proceso real (la parte después del último guion), así
        // que nos quedamos con esa parte para poder matchear.
        if (str_contains($valor, '-')) {
            $partes = explode('-', $valor);
            $valor = trim(end($partes));
        }

        // 3) Sacar CUALQUIER carácter que no sea letra (incluye Ñ) o
        // espacio: puntos, comas, paréntesis, números, etc. Esto es lo que
        // resuelve casos como "PRODUCTO TERMINADO.", "PRODUCTO TERMINADO
        // (OK)", etc.
        $valor = preg_replace('/[^A-ZÁÉÍÓÚÑ\s]/u', '', $valor);

        // 4) Colapsar espacios múltiples y quitar los de los extremos.
        $valor = preg_replace('/\s+/u', ' ', $valor);

        return trim($valor);
    }

    /**
     * Devuelve la posición del proceso dentro del flujo de producción
     * (según el orden de declaración de FLUJO_PROCESOS). Los procesos no
     * catalogados se envían al final, conservando el orden relativo entre
     * ellos según la fecha (criterio secundario del sortBy).
     */
    private function ordenProceso(?string $proceso): int
    {
        $clave = $this->normalizarProceso($proceso);
        $claves = array_keys(self::FLUJO_PROCESOS);
        $indice = array_search($clave, $claves, true);

        return $indice === false ? count($claves) : $indice;
    }

    /**
     * Calcula el % de avance de la OT según el nombre del proceso.
     *
     * Si el proceso coincide con el catálogo FLUJO_PROCESOS, se usa ese
     * valor exacto (por eso "PRODUCTO TERMINADO" y "REVISION" siempre dan
     * 100%). Si el proceso no está catalogado, se hace una estimación
     * conservadora por posición que nunca llega a 100% — ese valor queda
     * reservado exclusivamente para los procesos reconocidos como cierre
     * de la OT.
     */
    private function calcularAvance(?string $proceso, int $posicion): int
    {
        if (!$proceso) {
            return 0;
        }

        $clave = $this->normalizarProceso($proceso);

        if (array_key_exists($clave, self::FLUJO_PROCESOS)) {
            return self::FLUJO_PROCESOS[$clave];
        }

        return min($posicion * 10, 90);
    }

    public function destroyTrazabilidad($id)
    {
        try {

            $trazabilidad = DB::table('ot_trazabilidad')
                ->where('id_trazabilidad', $id)
                ->first();

            if (!$trazabilidad) {

                alert()->error(
                    'Error',
                    'El proceso de trazabilidad no existe.'
                );

                return back();
            }

            DB::table('ot_trazabilidad')
                ->where('id_trazabilidad', $id)
                ->delete();

            alert()->success(
                'Éxito',
                'Proceso de trazabilidad eliminado correctamente.'
            );

            return back();
        } catch (\Exception $e) {

            alert()->error(
                'Error',
                'No se pudo eliminar el proceso: ' . $e->getMessage()
            );

            return back();
        }
    }

    public function otAtrasadas(Request $request)
    {
        $diasAlerta = self::DIAS_PARA_ALERTA;

        /*
     * ============================================================
     * OBTENER OT CON TRAZABILIDAD
     * ============================================================
     */

        $ots = Ot::with('trazabilidades')
            ->whereHas('trazabilidades')
            ->get();

        $otsAtrasadas = collect();

        /*
     * ============================================================
     * RECORRER TODAS LAS OT
     * ============================================================
     */

        foreach ($ots as $ot) {

            /*
         * ========================================================
         * ÚLTIMO MOVIMIENTO REAL
         * ========================================================
         *
         * Se busca la trazabilidad con la fecha más reciente.
         *
         * En caso de que existan varios movimientos con la misma
         * fecha, se utiliza el orden del proceso como desempate.
         */

            $ultimoMovimiento = $ot->trazabilidades
                ->sortByDesc(function ($traza) {

                    $fecha = Carbon::parse(
                        $traza->fecha_proceso
                    )->timestamp;

                    $orden = $this->ordenProceso(
                        $traza->proceso
                    );

                    return ($fecha * 1000) + $orden;
                })
                ->first();


            /*
         * ========================================================
         * SEGURIDAD
         * ========================================================
         */

            if (!$ultimoMovimiento) {
                continue;
            }


            /*
         * ========================================================
         * NORMALIZAR ÚLTIMO PROCESO
         * ========================================================
         *
         * Ejemplos:
         *
         * TERMINACION - PRODUCTO TERMINADO
         * PRODUCTO TERMINADO
         *
         * se convierten en:
         *
         * PRODUCTO TERMINADO
         *
         * También:
         *
         * LOGISTICA - LOGISTICA Y DISTRIBUCION
         *
         * se convierte en:
         *
         * LOGISTICA Y DISTRIBUCION
         */

            $ultimoProcesoNormalizado = $this->normalizarProceso(
                $ultimoMovimiento->proceso
            );


            /*
         * ========================================================
         * EXCLUIR OT FINALIZADAS
         * ========================================================
         *
         * Estas OT ya no deben aparecer como atrasadas.
         */

            if (in_array($ultimoProcesoNormalizado, [
                'PRODUCTO TERMINADO',
                'LOGISTICA Y DISTRIBUCION',
            ], true)) {
                continue;
            }


            /*
         * ========================================================
         * FECHA DEL ÚLTIMO MOVIMIENTO
         * ========================================================
         */

            $fechaUltimoMovimiento = Carbon::parse(
                $ultimoMovimiento->fecha_proceso
            );


            /*
         * ========================================================
         * DÍAS SIN MOVIMIENTO
         * ========================================================
         */

            $diasSinMovimiento = round(
                $fechaUltimoMovimiento
                    ->diffInMinutes(Carbon::now()) / 60 / 24,
                2
            );


            /*
         * ========================================================
         * VERIFICAR SI ESTÁ ATRASADA
         * ========================================================
         */

            if ($diasSinMovimiento < $diasAlerta) {
                continue;
            }


            /*
         * ========================================================
         * CONSTRUIR TODOS LOS PROCESOS
         * ========================================================
         *
         * Mantiene el orden definido por FLUJO_PROCESOS.
         */

            [$procesos, $resumen] = $this->construirReporte(
                $ot->trazabilidades
            );


            /*
         * ========================================================
         * CALCULAR AVANCE REAL
         * ========================================================
         */

            $ultimoProcesoFlujo = $procesos->last();

            $avance = 0;

            if ($ultimoProcesoFlujo) {

                /*
             * Si el último proceso del flujo es PEDIDO SUSPENDIDO,
             * buscamos el último proceso real anterior.
             */

                if ($ultimoProcesoFlujo['es_suspendido']) {

                    $ultimoProcesoReal = $procesos
                        ->reverse()
                        ->first(
                            fn($p) => !$p['es_suspendido']
                        );

                    $avance = $ultimoProcesoReal['avance_acumulado'] ?? 0;
                } else {

                    $avance = $ultimoProcesoFlujo['avance_acumulado'] ?? 0;
                }
            }


            /*
         * ========================================================
         * AGREGAR OT A LAS ATRASADAS
         * ========================================================
         */

            $otsAtrasadas->push([

                'ot' => $ot,

                'procesos' => $procesos,

                'resumen' => $resumen,

                'ultimo_movimiento' => $ultimoMovimiento,

                /*
             * Guardamos también el proceso normalizado.
             * Esto nos permitirá agrupar correctamente.
             */

                'ultimo_proceso_normalizado' =>
                $ultimoProcesoNormalizado,

                'fecha_ultimo_movimiento' =>
                $fechaUltimoMovimiento,

                'dias_sin_movimiento' =>
                $diasSinMovimiento,

                'avance' =>
                $avance,

                'cantidad_procesos' =>
                $procesos->count(),
            ]);
        }


        /*
     * ================================================================
     * ORDENAR LAS OT MÁS ATRASADAS PRIMERO
     * ================================================================
     */

        $otsAtrasadas = $otsAtrasadas
            ->sortByDesc('dias_sin_movimiento')
            ->values();


        /*
     * ================================================================
     * KPI - TOTAL OT ATRASADAS
     * ================================================================
     */

        $totalAtrasadas = $otsAtrasadas->count();


        /*
     * ================================================================
     * KPI - PROMEDIO DÍAS DE ATRASO
     * ================================================================
     */

        $promedioDiasAtraso = $totalAtrasadas > 0
            ? round(
                $otsAtrasadas->avg('dias_sin_movimiento'),
                2
            )
            : 0;


        /*
     * ================================================================
     * KPI - MAYOR ATRASO
     * ================================================================
     */

        $mayorAtraso = $totalAtrasadas > 0
            ? $otsAtrasadas->max('dias_sin_movimiento')
            : 0;


        /*
     * ================================================================
     * OT ATRASADAS POR PROCESO
     * ================================================================
     *
     * IMPORTANTE:
     *
     * Se utiliza el ÚLTIMO PROCESO de cada OT.
     *
     * Por ejemplo:
     *
     * OT 100 -> CORTE
     * OT 101 -> CORTE
     * OT 102 -> COSTURA
     * OT 103 -> CORTE
     *
     * Resultado:
     *
     * CORTE      = 3
     * COSTURA    = 1
     */

        $otsPorProceso = $otsAtrasadas
            ->groupBy(function ($item) {

                return $item['ultimo_proceso_normalizado']
                    ?: 'SIN PROCESO';
            })
            ->map(function ($items) {

                return $items->count();
            })
            ->sortDesc();


        /*
     * ================================================================
     * PORCENTAJE POR PROCESO
     * ================================================================
     *
     * Generamos una colección con:
     *
     * proceso
     * cantidad
     * porcentaje
     */

        $detalleProcesos = $otsPorProceso
            ->map(function ($cantidad, $proceso) use ($totalAtrasadas) {

                $porcentaje = $totalAtrasadas > 0
                    ? round(
                        ($cantidad / $totalAtrasadas) * 100,
                        2
                    )
                    : 0;

                return [
                    'proceso' => $proceso,
                    'cantidad' => $cantidad,
                    'porcentaje' => $porcentaje,
                ];
            })
            ->values();


        /*
     * ================================================================
     * RETORNAR VISTA
     * ================================================================
     */

        return view('dashboard.ot-atrasadas', compact(
            'otsAtrasadas',
            'totalAtrasadas',
            'promedioDiasAtraso',
            'mayorAtraso',
            'diasAlerta',
            'otsPorProceso',
            'detalleProcesos'
        ));
    }
}
