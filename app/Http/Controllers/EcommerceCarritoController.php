<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EcommerceCarritoController extends Controller
{
    private const CLAVE = 'ecommerce_carrito';
    private const MAX_REFERENCIAS = 100;
    private const MAX_CANTIDAD = 9999;

    public function __construct()
    {
        $this->middleware(['auth', 'permission:pedido_compras create']);
    }

    private function carrito(Request $request): array
    {
        $guardado = $request->session()->get(self::CLAVE, []);
        if (!is_array($guardado)) return [];
        $carrito = [];
        foreach ($guardado as $codigo => $cantidad) {
            if (is_string($codigo) && preg_match('/^[A-Z0-9]{1,32}$/', $codigo)
                && is_numeric($cantidad) && (int) $cantidad >= 1) {
                $carrito[$codigo] = min(self::MAX_CANTIDAD, (int) $cantidad);
            }
            if (count($carrito) >= self::MAX_REFERENCIAS) break;
        }
        return $carrito;
    }

    private function resumen(Request $request)
    {
        $carrito = $this->carrito($request);
        $articulos = empty($carrito) ? collect()
            : DB::table('articulos')
                ->whereIn('art_codigo', array_keys($carrito))
                ->get(['art_codigo', 'art_descripcion', 'prec_vent'])
                ->keyBy('art_codigo');

        $items = [];
        $cantidadTotal = 0;
        $total = 0;
        $validos = [];

        foreach ($carrito as $codigo => $cantidad) {
            $producto = $articulos->get($codigo);
            if (!$producto) continue;
            $precio = max(0, (float) $producto->prec_vent);
            $validos[$codigo] = $cantidad;
            $cantidadTotal += $cantidad;
            $subtotal = $precio * $cantidad;
            $total += $subtotal;
            $items[] = [
                'codigo' => $codigo,
                'descripcion' => $producto->art_descripcion,
                'cantidad' => $cantidad,
                'precio' => $precio,
                'subtotal' => $subtotal,
            ];
        }
        // Un artículo retirado de la BD se descarta del carrito.
        if ($validos !== $carrito) $request->session()->put(self::CLAVE, $validos);

        return response()->json([
            'items' => $items,
            'referencias' => count($items),
            'unidades' => $cantidadTotal,
            'total' => $total,
        ]);
    }

    public function index(Request $request)
    {
        return $this->resumen($request);
    }

    public function agregar(Request $request)
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:32', 'regex:/^[A-Za-z0-9]+$/'],
            'cantidad' => 'required|integer|min:1|max:9999',
        ]);
        $codigo = strtoupper($datos['codigo']);
        $cantidad = (int) $datos['cantidad'];

        if (!DB::table('articulos')->where('art_codigo', $codigo)->exists()) {
            throw ValidationException::withMessages(['codigo' => 'El artículo seleccionado no existe.']);
        }

        $carrito = $this->carrito($request);
        if (!array_key_exists($codigo, $carrito) && count($carrito) >= self::MAX_REFERENCIAS) {
            throw ValidationException::withMessages(['codigo' => 'Alcanzaste el límite de 100 variantes por pedido.']);
        }
        $total = ($carrito[$codigo] ?? 0) + $cantidad;
        if ($total > self::MAX_CANTIDAD) {
            throw ValidationException::withMessages(['cantidad' => 'No podés agregar más de 9.999 unidades de esta variante.']);
        }
        $carrito[$codigo] = $total;
        $request->session()->put(self::CLAVE, $carrito);
        return $this->resumen($request);
    }

    public function actualizar(Request $request, string $codigo)
    {
        $datos = $request->validate(['cantidad' => 'required|integer|min:1|max:9999']);
        $codigo = strtoupper($codigo);
        $carrito = $this->carrito($request);
        if (!array_key_exists($codigo, $carrito)) abort(404);
        $carrito[$codigo] = (int) $datos['cantidad'];
        $request->session()->put(self::CLAVE, $carrito);
        return $this->resumen($request);
    }

    public function eliminar(Request $request, string $codigo)
    {
        $carrito = $this->carrito($request);
        unset($carrito[strtoupper($codigo)]);
        $request->session()->put(self::CLAVE, $carrito);
        return $this->resumen($request);
    }

    public function vaciar(Request $request)
    {
        $request->session()->forget(self::CLAVE);
        return $this->resumen($request);
    }

    public function continuar(Request $request)
    {
        $carrito = $this->carrito($request);
        if (!$carrito) {
            return redirect()->route('ecommerce.index')->with(
                'ecommerce_mensaje', 'El carrito está vacío. Agregá artículos para continuar.'
            );
        }
        $existentes = DB::table('articulos')->whereIn('art_codigo', array_keys($carrito))
            ->pluck('art_codigo')->all();
        $validos = array_intersect_key($carrito, array_flip($existentes));
        if (!$validos) {
            return redirect()->route('ecommerce.index')->with(
                'ecommerce_mensaje', 'No quedan artículos válidos en el carrito.'
            );
        }

        // Traspaso de un solo uso: evita sumar dos veces al recargar /create.
        $request->session()->put('ecommerce_transfer', $validos);
        $request->session()->put('ecommerce_pedido_pendiente', true);
        return redirect()->route('pedido_compras.create');
    }
}
