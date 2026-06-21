<?php

namespace App\Http\Controllers;

use App\Models\Ot;
use App\Imports\OtImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
    }

    public function index()
    {
        $ots = Ot::paginate(20);

        return view('ots.index', compact('ots'));
    }

    public function create()
    {
        return view('ots.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nro_ot' => 'required|unique:ot,nro_ot',
            'codigo' => 'required',
            'descripcion' => 'required',
            'cantidad_orden' => 'required|numeric'
        ]);

        Ot::create($request->all());

        alert()->success('Éxito', 'OT creada correctamente');

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

    public function buscar(Request $request)
    {
        $ot = Ot::with('trazabilidades')
            ->where('nro_ot', $request->nro_ot)
            ->first();

        return view('ots.resultado', compact('ot'));
    }

    /**
     * Catálogo de procesos del flujo de producción y el % de avance que
     * representa cada uno cuando es el ÚLTIMO proceso registrado en la OT.
     *
     * ⚠️ AJUSTA ESTA LISTA: reemplaza estos nombres por los procesos reales
     * de tu negocio (en MAYÚSCULAS, tal como se guardan en la BD), en el
     * orden real en que ocurren. El proceso que marca el cierre/entrega de
     * la OT (en tu caso "PRODUCTO TERMINADO") debe quedar siempre en 100.
     */
    private const FLUJO_PROCESOS = [
        'RECEPCION DE MATERIALES' => 10,
        'CORTE'                   => 25,
        'ARMADO'                  => 40,
        'COSTURA'                 => 55,
        'ACABADO'                 => 70,
        'CONTROL DE CALIDAD'      => 85,
        'EMBALAJE'                => 95,
        'TERMINACION - PRODUCTO TERMINADO'      => 100,
    ];

    /**
     * Días sin movimiento a partir de los cuales la OT se marca como
     * "Demorada" en el reporte (alerta visual para gerencia).
     */
    private const DIAS_PARA_ALERTA = 3;

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

            $ot = Ot::where('nro_ot', $nroOt)
                ->with(['trazabilidades' => function ($q) {
                    $q->orderBy('fecha_proceso', 'asc');
                }])
                ->first();

            if (!$ot) {
                $mensaje = "No se encontró ninguna OT con el número \"{$nroOt}\".";
            } elseif ($ot->trazabilidades->isEmpty()) {
                $mensaje = "La OT N° {$ot->nro_ot} no tiene procesos registrados todavía.";
            } else {
                [$procesos, $resumen] = $this->construirReporte($ot->trazabilidades);

                $labels = $procesos->pluck('proceso')->all();
                $duraciones = $procesos->pluck('duracion_horas')->all();
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
     * trazabilidades de una OT, ya ordenadas por fecha ascendente.
     *
     * @param  Collection  $trazas
     * @return array{0: Collection, 1: array}
     */
    private function construirReporte(Collection $trazas): array
    {
        $procesos = collect();
        $prevFecha = null;
        $horasAcumuladas = 0.0;
        $posicion = 0;

        foreach ($trazas as $traza) {
            $posicion++;

            $fecha = Carbon::parse($traza->fecha_proceso);

            // Duración real con precisión de minutos (no se trunca a horas enteras).
            $duracionHoras = $prevFecha
                ? round($prevFecha->diffInMinutes($fecha) / 60, 2)
                : 0.0;

            $horasAcumuladas += $duracionHoras;

            $procesos->push([
                'proceso'          => $traza->proceso,
                'resultado'        => $traza->resultado,
                'fecha'            => $fecha,
                'duracion_horas'   => $duracionHoras,
                'duracion_dias'    => round($duracionHoras / 24, 2),
                'horas_acumuladas' => round($horasAcumuladas, 2),
                'avance_acumulado' => $this->calcularAvance($traza->proceso, $posicion),
            ]);

            $prevFecha = $fecha;
        }

        $primerProceso = $procesos->first();
        $ultimoProceso = $procesos->last();

        $fechaInicio = $primerProceso['fecha'];
        $fechaUltimo = $ultimoProceso['fecha'];
        $avance = $ultimoProceso['avance_acumulado'];
        $finalizada = $avance >= 100;

        // Días totales que tomó la OT desde el primer registro hasta el último (con precisión decimal).
        $diasTotales = round($fechaInicio->diffInMinutes($fechaUltimo) / 60 / 24, 2);

        // Días transcurridos desde el último movimiento hasta HOY (clave para detectar OTs estancadas).
        $diasDesdeUltimoProceso = round($fechaUltimo->diffInMinutes(Carbon::now()) / 60 / 24, 2);

        // Para promedios/cuellos de botella se descarta el primer registro (su duración siempre es 0).
        $duracionesValidas = $procesos->skip(1)->pluck('duracion_horas');
        $duracionPromedio = $duracionesValidas->isNotEmpty() ? round($duracionesValidas->avg(), 2) : 0.0;

        $procesoMasLento = $procesos->skip(1)->sortByDesc('duracion_horas')->first();
        $procesoMasRapido = $procesos->skip(1)->sortBy('duracion_horas')->first();

        $estaDemorada = !$finalizada && $diasDesdeUltimoProceso >= self::DIAS_PARA_ALERTA;

        $estadoTexto = $finalizada ? 'Finalizado' : ($estaDemorada ? 'Demorado' : 'En Proceso');
        $estadoColor = $finalizada ? 'success' : ($estaDemorada ? 'danger' : 'info');
        $avanceColor = $avance >= 100 ? 'success' : ($avance >= 50 ? 'info' : 'warning');

        $resumen = [
            'cantidad_procesos'         => $procesos->count(),
            'fecha_inicio'              => $fechaInicio,
            'fecha_ultimo_proceso'      => $fechaUltimo,
            'ultimo_proceso'            => $ultimoProceso['proceso'],
            'avance'                    => $avance,
            'avance_color'              => $avanceColor,
            'finalizada'                => $finalizada,
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

        return [$procesos, $resumen];
    }

    /**
     * Calcula el % de avance de la OT según el nombre del proceso.
     *
     * Si el proceso coincide con el catálogo FLUJO_PROCESOS, se usa ese
     * valor exacto (por eso "PRODUCTO TERMINADO" siempre da 100%). Si el
     * proceso no está catalogado, se hace una estimación conservadora por
     * posición que nunca llega a 100% — ese valor queda reservado
     * exclusivamente para los procesos reconocidos como cierre de la OT.
     */
    private function calcularAvance(?string $proceso, int $posicion): int
    {
        if (!$proceso) {
            return 0;
        }

        $clave = mb_strtoupper(trim($proceso));

        if (array_key_exists($clave, self::FLUJO_PROCESOS)) {
            return self::FLUJO_PROCESOS[$clave];
        }

        return min($posicion * 10, 90);
    }
}
