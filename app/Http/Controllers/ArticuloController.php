<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache as FacadesCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Attribute\Cache;

class ArticuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:articulos index')->only('index');
        $this->middleware('permission:articulos create')->only('create', 'store');
        $this->middleware('permission:articulos edit')->only('edit', 'update');
        $this->middleware('permission:articulos destroy')->only('destroy');
    }

    public function showImportForm()
    {
        return view('articulos.importar');
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $file = $request->file('archivo');

        // 🔄 reiniciar progreso
        \Illuminate\Support\Facades\Cache::put('import_progress', 0, 600);
        \Illuminate\Support\Facades\Cache::put('import_progress_count', 0, 600);

        try {

            // 📊 contar filas
            $collection = \Maatwebsite\Excel\Facades\Excel::toCollection(
                new \App\Imports\ArticulosImport,
                $file
            );

            $totalRows = $collection->sum(fn($sheet) => $sheet->count());

            \Illuminate\Support\Facades\Cache::put('import_total', $totalRows, 600);

            // 🚀 importar
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\ArticulosImport,
                $file
            );

            // ✅ marcar final 100%
            \Illuminate\Support\Facades\Cache::put('import_progress', 100, 600);

            return response()->json([
                'success' => true,
                'message' => 'Artículos importados correctamente!'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $failures = $e->failures();
            $msg = '';

            foreach ($failures as $failure) {
                $msg .= 'Fila ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
            }

            // 🔥 ALERTA ERROR
            alert()->error('Error de Validación', strip_tags($msg));

            return response()->json([
                'success' => false,
                'message' => $msg
            ], 422);
        } catch (\Exception $e) {

            // 🔥 ALERTA ERROR GENERAL
            alert()->error('Error', $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        // Consulta base
        $query = DB::table('articulos')
            ->select('articulos.*');

        // BUSCADOR PRODUCTO
        if ($request->has('buscar') && !empty($request->buscar)) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->where('articulos.art_codigo', 'like', "%{$buscar}%")
                    ->orWhere('articulos.art_descripcion', 'like', "%{$buscar}%");
            });
        }

        // ORDENAR PRECIO
        if ($request->has('ordenar') && $request->ordenar == 'asc') {
            $query->orderBy('articulos.prec_vent', 'asc');
        } elseif ($request->has('ordenar') && $request->ordenar == 'desc') {
            $query->orderBy('articulos.prec_vent', 'desc');
        } else {
            $query->orderBy('articulos.id_articulo');
        }

        // PAGINAR
        $articulos = $query->paginate(15)->appends($request->all());

        return view('articulos.index')
            ->with('articulos', $articulos);
    }

    public function create()
    {
        ## Armar array de ivas
        $iva = ['0' => 'Exenta', '5' => 'Gravada 5%', '10' => 'Gravada 10%'];

        return view('articulos.create')
            ->with('iva', $iva);
    }

    public function store(Request $request)
    {
        // Los precios del formulario se muestran con separador de miles (ej. 150.000).
        // Normalizar antes de validar y guardar para conservar el valor real.
        foreach (['art_precio', 'prec_vent'] as $campoPrecio) {
            if ($request->filled($campoPrecio)) {
                $request->merge([
                    $campoPrecio => str_replace('.', '', trim((string) $request->input($campoPrecio)))
                ]);
            }
        }
        $request->validate([
            'art_codigo' => 'required|string|max:255|regex:/\\S/u',
            'art_descripcion' => 'required|string|max:255|regex:/\\S/u',
            'art_precio' => 'required|numeric|min:0',
            'prec_vent' => 'required|numeric|min:0',
            'art_iva' => 'required|numeric|in:0,5,10',
        ]);
        $input = $request->all();

        // VALIDACIÓN
        $validator = Validator::make(
            $input,
            [
                'art_codigo' => 'required',
                'art_descripcion' => 'required',
                'art_precio' => 'required',
                'art_iva' => 'required|numeric',
                'prec_vent' => 'required|numeric'
            ]
        );

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            alert()->error('Error', 'Datos inválidos');
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {

            $codBase = strtoupper($input['art_codigo']);
            $descripcion = strtoupper($input['art_descripcion']);

            $talles = $request->talles;

            // =========================
            // 🔥 CON TALLES
            // =========================
            if (!empty($talles)) {

                foreach ($talles as $talle) {

                    $codigo = $codBase . $talle;

                    $existe = DB::table('articulos')
                        ->where('art_codigo', $codigo)
                        ->exists();

                    if (!$existe) {

                        $idArticulo = DB::table('articulos')->insertGetId([
                            'art_codigo' => $codigo,
                            'art_descripcion' => $descripcion,
                            'art_precio' => $input['art_precio'],
                            'art_iva' => $input['art_iva'],
                            'prec_vent' => $input['prec_vent']
                        ], 'id_articulo');

                        // STOCK AUTOMÁTICO
                        DB::table('stock')->insert([
                            'id_articulo' => $idArticulo,
                            'cod_suc' => 1,
                            'cantidad' => 1
                        ]);
                    }
                }
            } else {

                // =========================
                // 🔥 SIN TALLES
                // =========================

                $existe = DB::table('articulos')
                    ->where('art_codigo', $codBase)
                    ->exists();

                if (!$existe) {

                    $idArticulo = DB::table('articulos')->insertGetId([
                        'art_codigo' => $codBase,
                        'art_descripcion' => $descripcion,
                        'art_precio' => $input['art_precio'],
                        'art_iva' => $input['art_iva'],
                        'prec_vent' => $input['prec_vent']
                    ], 'id_articulo');

                    // STOCK AUTOMÁTICO
                    DB::table('stock')->insert([
                        'id_articulo' => $idArticulo,
                        'cod_suc' => 1,
                        'cantidad' => 1
                    ]);
                }
            }

            DB::commit();

            alert()->success('Éxito', 'Artículo guardado correctamente');

            return redirect()->route('articulos.index');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e->getMessage());

            alert()->error('Error', 'Error al guardar artículo');

            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $articulos = DB::table('articulos')->where('id_articulo', $id)->first();

        ##validad
        if (empty($articulos)) {

            alert()->error('Error', 'Registro No Encontrado!!!');

            return redirect(route('Articulos.index'));
        }

        $iva = ['0' => 'Exenta', '5' => 'Gravada 5%', '10' => 'Gravada 10%'];

        return view('articulos.edit')
            ->with('articulo', $articulos)
            ->with('iva', $iva);
    }

    public function update($id, Request $request)
    {
        $articulo = DB::table('articulos')->where('id_articulo', $id)->first();
        if (!$articulo) {
            alert()->error('Error', 'Artículo no encontrado.');
            return redirect()->route('articulos.index');
        }

        // Formato utilizado por los artículos con variantes: 060611707 + GR + 02.
        $codigoOriginal = strtoupper(trim((string) $articulo->art_codigo));
        $codigoNuevo = strtoupper(trim((string) $request->input('art_codigo', '')));
        $patron = '/^([0-9]{9})([A-Z]{2})([A-Z0-9]{2})$/';
        $esFamilia = preg_match($patron, $codigoOriginal, $origen);
        $esNuevoValido = preg_match($patron, $codigoNuevo, $destino);
        $aplicarFamilia = $request->boolean('actualizar_familia');

        foreach (['art_precio', 'prec_vent'] as $campo) {
            if ($request->filled($campo)) {
                // Precios en guaraníes: el punto es separador de miles.
                $request->merge([$campo => str_replace('.', '', trim((string) $request->input($campo)))]);
            }
        }
        $request->validate([
            'art_codigo' => ['required', 'string', 'max:255', 'regex:/\\S/u'],
            'art_descripcion' => ['required', 'string', 'max:255', 'regex:/\\S/u'],
            'art_precio' => 'required|numeric|min:0',
            'prec_vent' => 'required|numeric|min:0',
            'art_iva' => 'required|in:0,5,10',
            'actualizar_familia' => 'nullable|boolean',
        ]);

        if ($aplicarFamilia && (!$esFamilia || !$esNuevoValido || $origen[1] !== $destino[1] || $origen[3] !== $destino[3])) {
            return back()->withErrors(['art_codigo' => 'Para actualizar la familia, conservá el código base y el talle. Solo podés cambiar el color.'])->withInput();
        }

        // Evita cambiar accidentalmente el código base o talle de una sola variante.
        $cambios = [
            'art_descripcion' => strtoupper(trim($request->input('art_descripcion'))),
            'art_precio' => $request->input('art_precio'),
            'prec_vent' => $request->input('prec_vent'),
            'art_iva' => $request->input('art_iva'),
        ];

        try {
            $cantidad = DB::transaction(function () use ($articulo, $codigoOriginal, $codigoNuevo, $patron, $esFamilia, $aplicarFamilia, $origen, $destino, $cambios) {
                if (!$aplicarFamilia) {
                    if (DB::table('articulos')->where('art_codigo', $codigoNuevo)
                        ->where('id_articulo', '<>', $articulo->id_articulo)->exists()) {
                        throw new \DomainException('El código ingresado ya pertenece a otro artículo.');
                    }
                    DB::table('articulos')->where('id_articulo', $articulo->id_articulo)
                        ->update(array_merge($cambios, ['art_codigo' => $codigoNuevo]));
                    return 1;
                }

                // Grupo exacto: misma base, mismo color original, distinto talle.
                $prefijo = $origen[1] . $origen[2];
                $candidatos = DB::table('articulos')
                    ->where('art_codigo', 'like', $prefijo . '%')
                    ->lockForUpdate()->get(['id_articulo', 'art_codigo']);
                $familia = $candidatos->filter(function ($item) use ($patron, $origen) {
                    return preg_match($patron, strtoupper($item->art_codigo), $partes)
                        && $partes[1] === $origen[1] && $partes[2] === $origen[2];
                });
                if ($familia->isEmpty()) {
                    throw new \DomainException('No se encontraron artículos de la familia indicada.');
                }

                $identificadores = $familia->pluck('id_articulo')->all();
                $codigos = [];
                foreach ($familia as $item) {
                    preg_match($patron, strtoupper($item->art_codigo), $partes);
                    $codigos[] = $destino[1] . $destino[2] . $partes[3];
                }
                if (count($codigos) !== count(array_unique($codigos))) {
                    throw new \DomainException('Existen talles duplicados en la familia. No se realizaron cambios.');
                }
                if (DB::table('articulos')->whereIn('art_codigo', $codigos)
                    ->whereNotIn('id_articulo', $identificadores)->exists()) {
                    throw new \DomainException('El color de destino ya contiene artículos con esos talles. No se realizaron cambios.');
                }

                foreach ($familia as $item) {
                    preg_match($patron, strtoupper($item->art_codigo), $partes);
                    DB::table('articulos')->where('id_articulo', $item->id_articulo)
                        ->update(array_merge($cambios, [
                            'art_codigo' => $destino[1] . $destino[2] . $partes[3],
                        ]));
                }
                return $familia->count();
            });
            alert()->success('Actualización correcta', "Se actualizaron {$cantidad} artículo(s).");
            return redirect()->route('articulos.index');
        } catch (\DomainException $e) {
            alert()->info('No se pudo actualizar', $e->getMessage());
            return back()->withInput();
        } catch (\Throwable $e) {
            Log::error('Fallo actualizando familia de artículos', ['id' => $id, 'error' => $e->getMessage()]);
            alert()->error('Error', 'No fue posible actualizar los artículos. No se realizaron cambios.');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $articulo = DB::table('articulos')->where('id_articulo', $id)->first();

        // validar existencia
        if (empty($articulo)) {
            alert()->error('Error', 'Registro No Encontrado!');
            return back();
        }

        // 🔥 VALIDAR SI ESTÁ EN USO
        $enUso = DB::table('detalle_pedido')
            ->where('id_articulo', $id)
            ->exists();

        if ($enUso) {
            alert()->error(
                'No se puede eliminar',
                'El artículo está siendo utilizado en pedidos...'
            );

            return back();
        }

        // eliminar stock primero
        DB::table('stock')->where('id_articulo', $id)->delete();

        // eliminar artículo
        DB::delete('DELETE FROM articulos WHERE id_articulo = ?', [$id]);

        alert()->success('Éxito', 'Artículo borrado correctamente.');

        return redirect(route('articulos.index'));
    }
    // Método para mostrar productos según la sucursal del usuario
    public function mostrarProductos()
    {
        // Obtén la sucursal del usuario autenticado
        $usuarioSucursal = Auth::user()->suc_cod;

        // Filtra los productos por la sucursal del usuario
        $productos = DB::table('articulos')
            ->where('sucursal_id', $usuarioSucursal)
            ->get();

        // Retorna la vista con los productos filtrados
        return view('productos.index', compact('productos'));
    }
}
