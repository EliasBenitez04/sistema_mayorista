<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardGerencialController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:pedido_compras index']);
    }

    public function index(Request $request)
    {
        $datos = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ]);
        $mes = $datos['mes'] ?? now()->format('Y-m');
        $inicio = Carbon::createFromFormat('!Y-m', $mes)->startOfMonth();
        $fin = $inicio->copy()->addMonth();
        $inicioAnterior = $inicio->copy()->subMonth();

        // Los anulados no cuentan en indicadores comerciales.
        // Las cifras provienen de pedidos registrados, no de facturas ni cobros.
        $base = function () {
            return DB::table('pedido_compras')
                ->where(function ($q) {
                    $q->whereNull('ped_estado')->orWhereRaw('UPPER(TRIM(ped_estado)) <> ?', ['ANULADO']);
                });
        };

        $actual = $base()->where('ped_fecha', '>=', $inicio->toDateString())
            ->where('ped_fecha', '<', $fin->toDateString());
        $anterior = $base()->where('ped_fecha', '>=', $inicioAnterior->toDateString())
            ->where('ped_fecha', '<', $inicio->toDateString());
        $pedidosMes = (clone $actual)->count();
        $importeMes = (float) (clone $actual)->sum('ped_total');
        $clientesActivos = (clone $actual)->whereNotNull('id_cliente')->distinct()->count('id_cliente');
        $pedidosAnterior = (clone $anterior)->count();
        $importeAnterior = (float) (clone $anterior)->sum('ped_total');
        $variacion = $importeAnterior > 0
            ? (($importeMes - $importeAnterior) / $importeAnterior) * 100
            : null;

        $resumenDetalle = DB::table('detalle_pedido as d')
            ->join('pedido_compras as p', 'p.id_pedido', '=', 'd.id_pedido_compras')
            ->where('p.ped_fecha', '>=', $inicio->toDateString())
            ->where('p.ped_fecha', '<', $fin->toDateString())
            ->where(function ($q) {
                $q->whereNull('p.ped_estado')->orWhereRaw('UPPER(TRIM(p.ped_estado)) <> ?', ['ANULADO']);
            });
        $unidadesMes = (float) (clone $resumenDetalle)->sum('d.det_cantidad');

        $articulos = (clone $resumenDetalle)
            ->leftJoin('articulos as a', 'a.id_articulo', '=', 'd.id_articulo')
            ->select('d.id_articulo', 'a.art_codigo', 'a.art_descripcion')
            ->selectRaw('SUM(d.det_cantidad) as unidades')
            ->groupBy('d.id_articulo', 'a.art_codigo', 'a.art_descripcion')
            ->orderByDesc('unidades')->limit(10)->get();

        $meses = [];
        $desde = $inicio->copy()->subMonths(11);
        $serie = $base()->where('ped_fecha', '>=', $desde->toDateString())
            ->where('ped_fecha', '<', $fin->toDateString())
            ->selectRaw("TO_CHAR(ped_fecha, 'YYYY-MM') as periodo")
            ->selectRaw('COUNT(*) as pedidos')
            ->selectRaw('COALESCE(SUM(ped_total), 0) as importe')
            ->groupByRaw("TO_CHAR(ped_fecha, 'YYYY-MM')")
            ->get()->keyBy('periodo');
        for ($i = 0; $i < 12; $i++) {
            $fecha = $desde->copy()->addMonths($i);
            $periodo = $fecha->format('Y-m');
            $dato = $serie->get($periodo);
            $meses[] = [
                'periodo' => $periodo,
                'etiqueta' => $fecha->locale('es')->translatedFormat('M y'),
                'importe' => (float) ($dato->importe ?? 0),
                'pedidos' => (int) ($dato->pedidos ?? 0),
            ];
        }
        $maxImporte = max(1, max(array_column($meses, 'importe')));

        $ultimos = (clone $actual)
            ->leftJoin('clientes as c', 'c.id_cliente', '=', 'pedido_compras.id_cliente')
            ->select('pedido_compras.id_pedido', 'pedido_compras.nro_pedido', 'pedido_compras.ped_fecha',
                'pedido_compras.ped_estado', 'pedido_compras.ped_total',
                DB::raw("TRIM(CONCAT(COALESCE(c.cli_nombre,''), ' ', COALESCE(c.cli_apellido,''))) as cliente"))
            ->orderByDesc('pedido_compras.ped_fecha')
            ->orderByDesc('pedido_compras.id_pedido')->limit(8)->get();

        return view('dashboard_gerencial.index', compact(
            'mes', 'inicio', 'pedidosMes', 'importeMes', 'clientesActivos',
            'pedidosAnterior', 'importeAnterior', 'variacion', 'unidadesMes',
            'articulos', 'meses', 'maxImporte', 'ultimos'
        ));
    }
}
