<?php

namespace App\Http\Controllers;

use App\Imports\StockImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class StockController extends Controller
{
    public function index()
    {
        /*
         * Esta pantalla solamente muestra el formulario de importación.
         * Antes se cargaba toda la tabla stock_sucursales con get() aunque
         * la vista no utilizaba esos datos.
         */
        return view('stocks.index');
    }

    public function importStock(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls'
        ]);

        $inicio = microtime(true);

        try {
            DB::beginTransaction();

            /*
             * La lógica anterior dejaba en 0 todo stock que ya no venía
             * en el Excel, pero lo hacía registro por registro después
             * de comparar todos los códigos en PHP.
             *
             * Se mantiene exactamente el mismo resultado final:
             * primero se coloca el stock existente en 0 con UNA consulta
             * y luego StockImport repone/actualiza en bloques lo que sí
             * viene en el archivo.
             */
            DB::table('stock_sucursales')->update([
                'cantidad' => 0,
                'updated_at' => now(),
            ]);

            Excel::import(new StockImport(), $request->file('archivo'));

            DB::commit();

            $duracion = round(microtime(true) - $inicio, 2);

            Log::info('Importación de stock completada', [
                'duracion_segundos' => $duracion,
                'archivo' => $request->file('archivo')->getClientOriginalName(),
            ]);

            Alert::success('Éxito', 'Stock importado correctamente!');
            return redirect()->route('stocks.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error import stock: ' . $e->getMessage(), [
                'exception' => get_class($e),
            ]);

            Alert::error('Error', 'Error al importar el stock');
            return back();
        }
    }
}
