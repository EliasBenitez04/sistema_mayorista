<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PedidoClienteRapidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pedido_compras create');
    }

    /**
     * Catálogos independientes para registrar un cliente desde el pedido.
     */
    public function catalogos()
    {
        $departamentos = DB::table('departamento')
            ->select('id_departamento', 'dep_descripcion')
            ->orderBy('dep_descripcion')
            ->get();

        $ciudades = DB::table('ciudad')
            ->select('id_ciudad', 'ciu_descripcion')
            ->orderBy('ciu_descripcion')
            ->get();

        return response()->json([
            'departamentos' => $departamentos,
            'ciudades' => $ciudades,
        ]);
    }

    /**
     * Registra un cliente sin abandonar la pantalla del pedido.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cli_ci' => [
                'required',
                'string',
                'max:12',
                'regex:/^[0-9]+(-[0-9]+)?$/',
                'unique:clientes,cli_ci',
            ],
            'cli_nombre' => ['required', 'string', 'max:45'],
            'cli_apellido' => ['nullable', 'string', 'max:45'],
            'cli_direccion' => ['required', 'string', 'max:100'],
            'cli_telefono' => ['required', 'string', 'max:12'],
            'id_departamento' => ['required', 'exists:departamento,id_departamento'],
            'id_ciudad' => ['required', 'exists:ciudad,id_ciudad'],
        ], [
            'cli_ci.required' => 'Ingrese el Nro. de CI / R.U.C.',
            'cli_ci.max' => 'El Nro. de CI / R.U.C. no puede superar 12 caracteres.',
            'cli_ci.regex' => 'El Nro. de CI / R.U.C. solo puede contener números y un guion.',
            'cli_ci.unique' => 'La cédula / R.U.C. ya está registrada.',
            'cli_nombre.required' => 'Ingrese los nombres o la razón social.',
            'cli_nombre.max' => 'Los nombres o razón social no pueden superar 45 caracteres.',
            'cli_apellido.max' => 'Los apellidos no pueden superar 45 caracteres.',
            'cli_direccion.required' => 'Ingrese la dirección.',
            'cli_direccion.max' => 'La dirección no puede superar 100 caracteres.',
            'cli_telefono.required' => 'Ingrese el teléfono.',
            'cli_telefono.max' => 'El teléfono no puede superar 12 caracteres.',
            'id_departamento.required' => 'Seleccione un departamento.',
            'id_departamento.exists' => 'El departamento seleccionado no es válido.',
            'id_ciudad.required' => 'Seleccione una ciudad.',
            'id_ciudad.exists' => 'La ciudad seleccionada no es válida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Revise los datos del cliente.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $nombre = strtoupper(trim((string) $request->input('cli_nombre')));
        $apellido = strtoupper(trim((string) $request->input('cli_apellido', '')));
        $direccion = strtoupper(trim((string) $request->input('cli_direccion')));
        $ci = trim((string) $request->input('cli_ci'));
        $telefono = trim((string) $request->input('cli_telefono'));

        try {
            $idCliente = DB::table('clientes')->insertGetId([
                'id_ciudad' => $request->input('id_ciudad'),
                'id_departamento' => $request->input('id_departamento'),
                'cli_ci' => $ci,
                'cli_nombre' => $nombre,
                'cli_apellido' => $apellido,
                'cli_direccion' => $direccion,
                'cli_telefono' => $telefono,
            ], 'id_cliente');

            $nombreCompleto = trim($nombre . ' ' . $apellido);

            return response()->json([
                'message' => 'Cliente registrado correctamente.',
                'cliente' => [
                    'id' => $idCliente,
                    'texto' => $ci . ' - ' . $nombreCompleto,
                    'cli_ci' => $ci,
                    'cli_nombre' => $nombre,
                    'cli_apellido' => $apellido,
                    'cli_direccion' => $direccion,
                    'cli_telefono' => $telefono,
                    'id_departamento' => (int) $request->input('id_departamento'),
                    'id_ciudad' => (int) $request->input('id_ciudad'),
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error al registrar cliente desde pedido', [
                'message' => $e->getMessage(),
                'cli_ci' => $ci,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'No se pudo registrar el cliente. Intente nuevamente.',
            ], 500);
        }
    }
}
