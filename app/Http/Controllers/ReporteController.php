<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $filtros = $request->validate([
            'tipo' => ['nullable', Rule::in(['pedidos', 'articulos', 'stock', 'clientes'])],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:40'],
            'sucursal' => ['nullable', 'string', 'max:100'],
            'exportar' => ['nullable', Rule::in(['csv'])],
        ]);
        $tipo = $filtros['tipo'] ?? 'pedidos';
        $buscar = trim($filtros['buscar'] ?? '');
        $sucursal = trim($filtros['sucursal'] ?? '');
        $estado = trim($filtros['estado'] ?? '');

        if ($tipo === 'pedidos') {
            $query = DB::table('pedido_compras as p')
                ->leftJoin('clientes as c', 'c.id_cliente', '=', 'p.id_cliente')
                ->leftJoin('sucursal as s', 's.cod_suc', '=', 'p.cod_suc')
                ->select('p.id_pedido', 'p.nro_pedido', 'p.ped_fecha', 'p.ped_estado',
                    'p.ped_total', 's.suc_descri as sucursal',
                    DB::raw("TRIM(CONCAT(COALESCE(c.cli_nombre, ''), ' ', COALESCE(c.cli_apellido, ''))) as cliente"),
                    DB::raw('(SELECT COALESCE(SUM(d.det_cantidad),0) FROM detalle_pedido d WHERE d.id_pedido_compras = p.id_pedido) as cantidad'));
            if (!empty($filtros['desde'])) $query->whereDate('p.ped_fecha', '>=', $filtros['desde']);
            if (!empty($filtros['hasta'])) $query->whereDate('p.ped_fecha', '<=', $filtros['hasta']);
            if ($estado !== '') $query->where('p.ped_estado', $estado);
            if ($sucursal !== '') $query->where('p.cod_suc', $sucursal);
            if ($buscar !== '') $query->where(function ($q) use ($buscar) {
                $q->where('p.nro_pedido', 'ilike', "%{$buscar}%")
                    ->orWhere('c.cli_nombre', 'ilike', "%{$buscar}%")
                    ->orWhere('c.cli_apellido', 'ilike', "%{$buscar}%");
            });
            $columnas = ['Nº pedido' => 'nro_pedido', 'Fecha' => 'ped_fecha',
                'Cliente' => 'cliente', 'Sucursal' => 'sucursal', 'Estado' => 'ped_estado',
                'Unidades' => 'cantidad', 'Total (Gs)' => 'ped_total'];
            $query->orderByDesc('p.ped_fecha')->orderByDesc('p.id_pedido');
        } elseif ($tipo === 'articulos') {
            $query = DB::table('articulos')->select('art_codigo', 'art_descripcion', 'prec_vent');
            if ($buscar !== '') $query->where(function ($q) use ($buscar) {
                $q->where('art_codigo', 'ilike', "%{$buscar}%")
                    ->orWhere('art_descripcion', 'ilike', "%{$buscar}%");
            });
            $columnas = ['Código' => 'art_codigo', 'Descripción' => 'art_descripcion', 'Precio venta (Gs)' => 'prec_vent'];
            $query->orderBy('art_codigo');
        } elseif ($tipo === 'stock') {
            // Coincide con el origen real del importador de stock.
            $query = DB::table('stock_sucursales')->select('sucursal', 'codigo', 'descripcion', 'cantidad');
            if ($sucursal !== '') $query->where('sucursal', $sucursal);
            if ($buscar !== '') $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'ilike', "%{$buscar}%")
                    ->orWhere('descripcion', 'ilike', "%{$buscar}%");
            });
            $columnas = ['Sucursal' => 'sucursal', 'Código' => 'codigo',
                'Descripción' => 'descripcion', 'Existencia' => 'cantidad'];
            $query->orderBy('sucursal')->orderBy('codigo');
        } else {
            $query = DB::table('clientes')
                ->select('cli_ci', 'cli_nombre', 'cli_apellido', 'cli_telefono', 'cli_direccion');
            if ($buscar !== '') $query->where(function ($q) use ($buscar) {
                $q->where('cli_ci', 'ilike', "%{$buscar}%")
                    ->orWhere('cli_nombre', 'ilike', "%{$buscar}%")
                    ->orWhere('cli_apellido', 'ilike', "%{$buscar}%");
            });
            $columnas = ['C.I./RUC' => 'cli_ci', 'Nombre' => 'cli_nombre',
                'Apellido' => 'cli_apellido', 'Teléfono' => 'cli_telefono', 'Dirección' => 'cli_direccion'];
            $query->orderBy('cli_apellido')->orderBy('cli_nombre');
        }

        if (($filtros['exportar'] ?? '') === 'csv') {
            return response()->streamDownload(function () use ($query, $columnas) {
                $salida = fopen('php://output', 'w');
                fwrite($salida, "\xEF\xBB\xBF");
                fputcsv($salida, array_keys($columnas), ';');
                foreach ($query->cursor() as $fila) {
                    $valores = [];
                    foreach ($columnas as $campo) {
                        $valor = (string) ($fila->{$campo} ?? '');
                        // Evitar ejecución de fórmulas en hojas de cálculo.
                        if (preg_match('/^[=+@\\-\\t\\r]/u', $valor) && !is_numeric($valor)) {
                            $valor = "'" . $valor;
                        }
                        $valores[] = $valor;
                    }
                    fputcsv($salida, $valores, ';');
                }
                fclose($salida);
            }, 'reporte_' . $tipo . '_' . now()->format('Ymd_His') . '.csv',
                ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $filas = $query->paginate(25)->withQueryString();
        $sucursales = $tipo === 'stock'
            ? DB::table('stock_sucursales')->select('sucursal')->distinct()->orderBy('sucursal')->pluck('sucursal')
            : DB::table('sucursal')->orderBy('suc_descri')->pluck('suc_descri', 'cod_suc');
        $estados = $tipo === 'pedidos'
            ? DB::table('pedido_compras')->whereNotNull('ped_estado')->distinct()->orderBy('ped_estado')->pluck('ped_estado')
            : collect();

        return view('reportes.index', compact('tipo', 'columnas', 'filas', 'sucursales', 'estados', 'filtros'));
    }
}
