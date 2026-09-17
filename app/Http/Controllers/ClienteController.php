<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends AppBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:clientes index')->only('index');
        $this->middleware('permission:clientes create')->only('create', 'store');
        $this->middleware('permission:clientes edit')->only('edit', 'update');
        $this->middleware('permission:clientes destroy')->only('destroy');
    }
    public function index()
    {
        $clientes = DB::table('clientes')
            ->select(
                'clientes.*',
                'departamento.dep_descripcion',
                'ciudad.ciu_descripcion'
            )
            ->leftJoin('departamento', 'clientes.id_departamento', '=', 'departamento.id_departamento')
            ->leftJoin('ciudad', 'clientes.id_ciudad', '=', 'ciudad.id_ciudad')
            ->paginate(10);

        return view('clientes.index')->with('clientes', $clientes);
    }

    public function create(Request $request)
    {
        // Recuperar los departamentos para nuestro select en la vista create
        $departamento = DB::table('departamento')->pluck('dep_descripcion', 'id_departamento');

        // Recuperar todas las ciudades para nuestro select
        $ciudad = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');

        // Detectar si se accede desde el formulario de ventas
        $from = $request->get('from', null);

        // Retornar la vista con los datos necesarios
        return view('clientes.create', compact('ciudad', 'departamento', 'from'));
    }

    public function getCiudades($id_departamento)
    {
        return DB::table('ciudad')
            ->where('id_departamento', $id_departamento)
            ->get();
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $camposObligatorios = [
            'cli_ci' => 'Nro de CI',
            'cli_nombre' => 'Nombres',
            'cli_direccion' => 'Dirección',
            'cli_telefono' => 'Teléfono',
            'id_departamento' => 'Departamento',
            'id_ciudad' => 'Ciudad'
        ];

        foreach ($camposObligatorios as $campo => $nombre) {
            if (empty($input[$campo])) {
                alert()->info('Atención', "Debe completar el campo: $nombre");
                return redirect()->back()->withInput();
            }
        }

        // Validar que no exista el mismo CI
        $validarCi = DB::table('clientes')->where('cli_ci', $input['cli_ci'])->first();
        if (!empty($validarCi)) {
            alert()->info('Atención', 'La Cédula Del Cliente Ya Existe!!!');
            return redirect()->back()->withInput();
        }

        // Guardar en la base de datos
        DB::insert(
            "INSERT INTO clientes(id_ciudad, id_departamento, cli_ci,
        cli_nombre, cli_apellido,
        cli_direccion, cli_telefono)
        VALUES(?, ?, ?, ?, ?, ?, ?)",
            [
                $input['id_ciudad'],
                $input['id_departamento'],
                $input['cli_ci'],
                strtoupper($input['cli_nombre']),
                strtoupper($input['cli_apellido']),
                strtoupper($input['cli_direccion']),
                $input['cli_telefono']
            ]
        );

        alert()->success('Éxito', 'Registro Guardado Correctamente!');
        return redirect(route('clientes.index'));
    }

    public function edit($cliente_id)
    {
        ##verificar si existe el cliente
        $clientes = DB::table('clientes')->where('id_cliente', $cliente_id)->first();

        ##validad
        if (empty($clientes)) {
            alert()->error('Atención', 'El dato consultado no Existe!');
            return redirect(route('clientes.index'));
        }

        ##recuperar los departamentos para nuestro select en la vista create
        $departamento = DB::table('departamento')->pluck('dep_descripcion', 'id_departamento');

        ##recuperar todoas las ciudades para nuestro select
        $ciudad = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');

        return view('clientes.edit')
            ->with('cliente', $clientes)
            ->with('ciudad', $ciudad)
            ->with('departamento', $departamento);
    }

    public function update($cliente_id, Request $request)
    {
        $cliente = \App\Models\Cliente::find($cliente_id);

        if (!$cliente) {
            alert()->info('Atención', 'El Cliente no Existe!!!');
            return redirect()->route('clientes.index');
        }

        $input = $request->all();

        // Validar que la CI no exista en otro registro
        $validarCi = DB::table('clientes')
            ->where('cli_ci', $input['cli_ci'])
            ->where('id_cliente', '<>', $cliente_id)
            ->first();

        if (!empty($validarCi)) {
            alert()->info('Atención', 'La Cédula / R.U.C. Del Cliente Ya Existe!!!');
            return redirect()->back()->withInput();
        }

        // Actualizar los datos
        $cliente->update([
            'id_ciudad' => $input['id_ciudad'],
            'id_departamento' => $input['id_departamento'],
            'cli_ci' => $input['cli_ci'],
            'cli_nombre' => strtoupper($input['cli_nombre']),
            'cli_apellido' => strtoupper($input['cli_apellido']),
            'cli_direccion' => strtoupper($input['cli_direccion']),
            'cli_telefono' => $input['cli_telefono']
        ]);

        alert()->success('Éxito', 'Registro Actualizado correctamente.!');

        return redirect()->route('clientes.index');
    }

    public function destroy($cliente_id)
    {
        $clientes = DB::table('clientes')->where('id_cliente', $cliente_id)->first();

        if (empty($clientes)) {
            alert()->error('Atención', 'El registro No existe!!!');

            return redirect(route('clientes.index'));
        }

        DB::delete('DELETE FROM clientes WHERE id_cliente = ?', [$cliente_id]);

        alert()->success('Exito', 'Cliente Borrado Con Exito!!!');

        return redirect(route('clientes.index'));
    }
}
