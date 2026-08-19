<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RedistribucionLote;
use App\Models\RedistribucionProcesoDetalle;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // ==========================================================
        // LOTES
        // ==========================================================

        $totalLotes = RedistribucionLote::count();

        $lotesGenerados = RedistribucionLote::where(
            'estado',
            'GENERADO'
        )->count();

        $lotesEnProceso = RedistribucionLote::where(
            'estado',
            'EN PROCESO'
        )->count();

        $lotesFinalizados = RedistribucionLote::where(
            'estado',
            'FINALIZADO'
        )->count();


        // ==========================================================
        // TRANSFERENCIAS
        // ==========================================================

        $totalTransferencias = RedistribucionProcesoDetalle::count();

        $transferenciasPendientes = RedistribucionProcesoDetalle::where(
            'estado',
            'PENDIENTE'
        )->count();

        $transferenciasEnProceso = RedistribucionProcesoDetalle::where(
            'estado',
            'EN PROCESO'
        )->count();

        $transferenciasFinalizadas = RedistribucionProcesoDetalle::where(
            'estado',
            'FINALIZADO'
        )->count();


        // ==========================================================
        // UNIDADES
        // ==========================================================

        $totalUnidades = RedistribucionLote::sum('total_unidades');


        // ==========================================================
        // PORCENTAJE DE FINALIZACIÓN
        // ==========================================================

        $porcentajeFinalizacion = $totalLotes > 0
            ? round(($lotesFinalizados / $totalLotes) * 100)
            : 0;


        // ==========================================================
        // ÚLTIMOS LOTES
        // ==========================================================

        $ultimosLotes = RedistribucionLote::with('detalles')
            ->orderBy('fecha_generacion', 'desc')
            ->limit(5)
            ->get();


        // ==========================================================
        // ÚLTIMO LOTE
        // ==========================================================

        $ultimoLote = RedistribucionLote::orderBy(
            'fecha_generacion',
            'desc'
        )->first();


        // ==========================================================
        // LOTES PENDIENTES DE ATENCIÓN
        // ==========================================================

        $lotesAtencion = RedistribucionLote::whereIn(
            'estado',
            [
                'GENERADO',
                'EN PROCESO'
            ]
        )
            ->orderBy('fecha_generacion', 'asc')
            ->limit(5)
            ->get();


        return view('home', compact(

            'totalLotes',

            'lotesGenerados',
            'lotesEnProceso',
            'lotesFinalizados',

            'totalTransferencias',
            'transferenciasPendientes',
            'transferenciasEnProceso',
            'transferenciasFinalizadas',

            'totalUnidades',

            'porcentajeFinalizacion',

            'ultimosLotes',
            'ultimoLote',
            'lotesAtencion'

        ));
    }
}
