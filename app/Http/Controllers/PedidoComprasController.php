<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PedidoComprasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pedido_compras index')->only('index');
        $this->middleware('permission:pedido_compras create')->only('create', 'store');
        $this->middleware('permission:pedido_compras destroy')->only('destroy');
        $this->middleware('permission:pedido_compras confirm')->only('confirm');
    }

    public function index()
    {
        // Consulta de datos para pedido_compras
        $pedidoQuery = DB::table('pedido_compras')
            ->select(
                'pedido_compras.*',
                'sucursal.suc_descri as suc_descri',
                'users.name as usuario',
                'u2.name as confirmado_por',
                DB::raw("COALESCE(SUM(detalle_pedido.det_cantidad), 0) as total_cantidad"),
                DB::raw("CONCAT(clientes.cli_nombre, ' ', clientes.cli_apellido) as cliente") // <-- cliente concatenado
            )
            ->join('users', 'users.id', '=', 'pedido_compras.user_id')
            ->join('sucursal', 'sucursal.cod_suc', '=', 'pedido_compras.cod_suc')
            ->leftJoin('detalle_pedido', 'detalle_pedido.id_pedido_compras', '=', 'pedido_compras.id_pedido')
            ->leftJoin('users as u2', 'u2.id', '=', 'pedido_compras.confirmado_por')
            ->leftJoin('clientes', 'clientes.id_cliente', '=', 'pedido_compras.id_cliente') // <-- join con clientes
            ->groupBy(
                'pedido_compras.id_pedido',
                'pedido_compras.ped_fecha',
                'pedido_compras.ped_estado',
                'sucursal.suc_descri',
                'users.name',
                'u2.name',
                'clientes.cli_nombre',
                'clientes.cli_apellido'
            )
            ->orderByDesc('pedido_compras.id_pedido');

        $pedido = $pedidoQuery->paginate(10);

        return view('pedido_compras.index')->with('pedido_compras', $pedido);
    }

    public function create()
    {
        $condicion = ["CONTADO" => "CONTADO", "CREDITO" => "CREDITO"];

        $sucursal = DB::table('sucursal')
            ->select(DB::raw("suc_descri, cod_suc"))
            ->pluck('suc_descri', 'cod_suc');

        $clientes = DB::table('clientes')
            ->select(
                'id_cliente',
                DB::raw("cli_ci || ' - ' || cli_nombre || ' ' || cli_apellido AS nombre")
            )
            ->orderBy('cli_ci')
            ->pluck('nombre', 'id_cliente');

        $productos = DB::table('articulos')
            ->join('stock', 'articulos.id_articulo', '=', 'stock.id_articulo')
            ->select(
                'articulos.id_articulo',
                'articulos.art_codigo',
                'articulos.art_descripcion',
                'stock.cantidad'
            )
            ->orderBy('articulos.art_codigo', 'asc')
            ->take(20)
            ->get();

        $detalles = [];

        $usados = DB::table('pedido_compras')
            ->where('ped_estado', '!=', 'ANULADO')
            ->selectRaw("CAST(split_part(nro_pedido, '-', 2) AS INTEGER) as num")
            ->get()
            ->pluck('num')
            ->toArray();

        $numero = 1;

        while (in_array($numero, $usados)) {
            $numero++;
        }

        $nroPedidoPreview = "PED-" . $numero;

        return view('pedido_compras.create')
            ->with('condicion', $condicion)
            ->with('sucursal', $sucursal)
            ->with('productos', $productos)
            ->with('clientes', $clientes)
            ->with('detalles', $detalles)
            ->with('nroPedidoPreview', $nroPedidoPreview);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $fecha  = Carbon::parse($input['ped_fecha'])->format('Y-m-d');
        $actual = Carbon::now()->format('Y-m-d');

        // Validar fecha
        if ($fecha > $actual) {
            alert()->info('Error', 'La fecha del pedido no puede ser mayor a la fecha actual.');
            return redirect(route('pedido_compras.create'))->withInput();
        }

        // Validar que haya artículos
        if (!$request->has('codigo') || count($request->codigo) === 0) {
            alert()->warning('Atención', 'Debe agregar al menos un artículo al pedido.');
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();

        try {
            // Buscar número de pedido libre
            $usados = DB::table('pedido_compras')
                ->where('ped_estado', '!=', 'ANULADO')
                ->selectRaw("CAST(split_part(nro_pedido, '-', 2) AS INTEGER) as num")
                ->get()
                ->pluck('num')
                ->toArray();

            $numero = 1;
            while (in_array($numero, $usados)) {
                $numero++;
            }
            $nroPedido = "PED-" . $numero;

            // Insertar pedido cabecera
            $insertCompra = DB::table('pedido_compras')->insertGetId([
                'nro_pedido'  => $nroPedido,
                'user_id'     => auth()->user()->id,
                'id_cliente'  => $input['id_cliente'],
                'condicion'   => $input['condicion'],
                'intervalo'   => $input['intervalo'] ?? null,
                'cant_cuotas' => $input['cant_cuotas'] ?? null,
                'ped_fecha'   => $input['ped_fecha'],
                'ped_estado'  => "PENDIENTE",
                'cod_suc'     => $input['cod_suc'],
            ], 'id_pedido');

            // Verificar si se aplica descuento general
            $aplicaDescuento = $input['aplica_descuento'] ?? 'NO';
            $descuentoGeneral = ($aplicaDescuento === 'SI' && isset($input['descuento']))
                ? floatval($input['descuento'])
                : 0;

            if ($descuentoGeneral > 0 && $descuentoGeneral < 1) {
                $descuentoGeneral = $descuentoGeneral * 100;
            }

            $totalSinDescuento = 0;

            // Insertar detalles
            foreach ($input['codigo'] as $key => $value) {
                $articulo = DB::table('articulos')
                    ->where('art_codigo', $value)
                    ->select('id_articulo', 'prec_vent')
                    ->first();

                if (!$articulo) {
                    throw new \Exception("El artículo con código {$value} no existe.");
                }

                $cantidad = $input['cantidad'][$key];
                $precio   = $articulo->prec_vent;

                $subtotal = $cantidad * $precio;
                $subtotalConDescuento = $subtotal * (1 - $descuentoGeneral / 100);

                $totalSinDescuento += $subtotal;

                DB::insert(
                    "INSERT INTO detalle_pedido(id_articulo, id_pedido_compras, det_cantidad, det_subtotal, det_descuento)
                 VALUES(?, ?, ?, ?, ?)",
                    [
                        $articulo->id_articulo,
                        $insertCompra,
                        $cantidad,
                        $subtotalConDescuento,
                        $descuentoGeneral
                    ]
                );
            }

            // Actualizar cabecera con total y descuento
            DB::table('pedido_compras')
                ->where('id_pedido', $insertCompra)
                ->update([
                    'ped_total' => $totalSinDescuento * (1 - $descuentoGeneral / 100),
                    'descuento' => $descuentoGeneral
                ]);

            DB::commit();

            Log::info("Pedido {$insertCompra} creado correctamente por usuario: " . auth()->user()->id);
            alert()->success("Éxito", "Pedido generado correctamente!!!");
            return redirect(route('pedido_compras.index'));
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error("ERROR DE CREACION DE PEDIDOS:::::::::" . $ex->getMessage());
            alert()->error("Error", "Error en la creación de Pedido.");
            return redirect()->back()->withInput($input);
        }
    }

    public function buscarProductoPed(Request $request)
    {
        $query = $request->get('query');
        $cod_suc = $request->get('cod_suc');

        $productosQuery = DB::table('articulos')
            ->select('articulos.art_codigo', 'articulos.art_descripcion', 'stock.cantidad', 'stock.cod_suc')
            ->join('stock', 'articulos.id_articulo', '=', 'stock.id_articulo')
            ->when($cod_suc, function ($q) use ($cod_suc) {
                return $q->where('stock.cod_suc', $cod_suc);
            })
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($q2) use ($query) {
                    $q2->where('articulos.art_codigo', 'ILIKE', "%{$query}%")
                        ->orWhere('articulos.art_descripcion', 'ILIKE', "%{$query}%");
                });
            })
            ->orderBy('articulos.art_codigo', 'asc')
            ->take(20)
            ->get();

        return view('pedido_compras.buscar_producto', ['productos' => $productosQuery]);
    }

    public function show($id)
    {
        // Consulta de cabecera del pedido con datos del usuario, sucursal y cliente
        $pedido = DB::table('pedido_compras')
            ->select(
                'pedido_compras.*',
                'users.name as usuario',
                'sucursal.suc_descri as sucursal',
                DB::raw("CONCAT(clientes.cli_nombre, ' ', clientes.cli_apellido) as cliente"),
                'clientes.cli_ci as cli_ci',
                'clientes.cli_telefono as cli_telefono',
                'clientes.cli_direccion as cli_direccion'
            )
            ->join('users', 'users.id', 'pedido_compras.user_id')
            ->join('sucursal', 'sucursal.cod_suc', 'pedido_compras.cod_suc')
            ->join('clientes', 'clientes.id_cliente', 'pedido_compras.id_cliente')
            ->where('pedido_compras.id_pedido', $id)
            ->first();

        if (empty($pedido)) {
            alert()->error('Error', 'El Pedido no existe.');
            return redirect(route('pedido_compras.index'));
        }

        // Consulta de detalle del pedido
        $detalle = DB::table('detalle_pedido')
            ->select(
                'detalle_pedido.id_articulo',
                'detalle_pedido.det_cantidad',
                'detalle_pedido.det_subtotal',
                DB::raw('COALESCE(detalle_pedido.det_descuento, 0) as det_descuento'),
                'articulos.art_codigo',
                'articulos.art_descripcion'
            )
            ->join('articulos', 'articulos.id_articulo', '=', 'detalle_pedido.id_articulo')
            ->where('detalle_pedido.id_pedido_compras', $id)
            ->get();

        // Calcular totales generales
        $totalCantidad = $detalle->sum('det_cantidad');
        $totalSinDescuento = $detalle->sum(function ($d) {
            return $d->det_subtotal / (1 - $d->det_descuento / 100);
        });
        $totalDescuento = $detalle->sum(function ($d) {
            return $d->det_subtotal / (1 - $d->det_descuento / 100) - $d->det_subtotal;
        });
        $totalConDescuento = $detalle->sum('det_subtotal');

        return view('pedido_compras.show', compact(
            'pedido',
            'detalle',
            'totalCantidad',
            'totalSinDescuento',
            'totalDescuento',
            'totalConDescuento'
        ));
    }

    public function destroy($id)
    {
        $pedido = DB::table('pedido_compras')->where('id_pedido', $id)->first();

        if (empty($pedido)) {
            alert()->error('Error', 'El Pedido no existe.');
            return redirect(route('pedido_compras.index'));
        }

        DB::table('pedido_compras')->where('id_pedido', $id)->update([
            'ped_estado' => "ANULADO"
        ]);

        alert()->success('Éxito', 'El Pedido se anuló correctamente.');
        return redirect(route('pedido_compras.index'));
    }

    public function getDetallePedido($id)
    {
        $detalles = DB::table('detalle_pedido')
            ->join('articulos', 'detalle_pedido.id_articulo', '=', 'articulos.id_articulo')
            ->where('id_pedido_compras', $id)
            ->select('articulos.art_descripcion', 'detalle_pedido.det_cantidad')
            ->get();

        return response()->json($detalles);
    }

    public function confirm(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pedido = DB::table('pedido_compras')->where('id_pedido', $id)->first();

            if (!$pedido || $pedido->ped_estado !== 'PENDIENTE') {
                DB::rollBack();
                alert()->error('Error', 'Pedido no válido o ya confirmado!!!');
                return redirect()->back();
            }

            DB::table('pedido_compras')
                ->where('id_pedido', $id)
                ->update([
                    'ped_estado'     => 'CONFIRMADO',
                    'confirmado_por' => auth()->user()->id,
                ]);

            DB::commit();

            alert()->success('Éxito', 'Pedido confirmado correctamente!!!');
            return redirect()->route('pedido_compras.index');
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error("ERROR DE CONFIRMACION DE PEDIDO: " . $ex->getMessage());

            alert()->error('Error', 'Error al confirmar el pedido!!!');
            return redirect()->back();
        }
    }

    public function imprimir($id)
    {
        $pedido = DB::table('pedido_compras as p')
            ->join('clientes as c', 'c.id_cliente', '=', 'p.id_cliente')
            ->join('sucursal as s', 's.cod_suc', '=', 'p.cod_suc')
            ->select(
                'p.*',
                's.suc_descri',
                'c.cli_nombre',
                'c.cli_apellido',
                'c.cli_ci',
                'c.cli_telefono',
                'c.cli_direccion',
                DB::raw("CONCAT(c.cli_nombre, ' ', c.cli_apellido) as cliente")
            )
            ->where('p.id_pedido', $id)
            ->first();

        if (!$pedido) {
            alert()->error('Error', 'Pedido no encontrado.');
            return redirect()->route('pedido_compras.index');
        }

        $detalle = DB::table('detalle_pedido as d')
            ->join('articulos as a', 'a.id_articulo', '=', 'd.id_articulo')
            ->where('d.id_pedido_compras', $id)
            ->select('d.*', 'a.art_codigo', 'a.art_descripcion')
            ->get();

        return view('pedido_compras.imprimir', compact('pedido', 'detalle'));
    }
}
