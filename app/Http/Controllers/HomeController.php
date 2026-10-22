<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Conteos reales del catálogo y pedidos, sin modificar estos módulos.
        // Se consultan sólo los indicadores que el usuario puede visualizar.
        $resumen = [
            'clientes' => auth()->user()->can('clientes index') ? DB::table('clientes')->count() : null,
            'pedidos' => auth()->user()->can('pedido_compras index') ? DB::table('pedido_compras')->count() : null,
            'ciudades' => auth()->user()->can('ciudades index') ? DB::table('ciudad')->count() : null,
            'departamentos' => auth()->user()->can('departamentos index') ? DB::table('departamento')->count() : null,
        ];

        return view('home', [
            'resumen' => $resumen,
            'clientes' => $resumen['clientes'], // compatibilidad con el indicador anterior
        ]);
    }
}
