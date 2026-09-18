<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class StockImport implements ToCollection
{
    /**
     * Cantidad de filas enviadas a PostgreSQL por sentencia.
     *
     * Con 2.000 registros reducimos drásticamente los viajes a la base de datos
     * sin generar sentencias excesivamente grandes.
     */
    private const BATCH_SIZE = 2000;

    public function collection(Collection $rows)
    {
        $ahora = now();
        $registros = [];

        foreach ($rows->skip(1) as $row) {
            $sucursal = trim((string) ($row[0] ?? ''));
            $cantidad = (float) ($row[1] ?? 0);
            $descripcion = trim((string) ($row[2] ?? ''));
            $codigo = trim((string) ($row[3] ?? ''));

            if ($sucursal === '' || $codigo === '') {
                continue;
            }

            /*
             * La clave evita duplicados dentro del mismo Excel.
             * Si la misma sucursal/código aparece más de una vez,
             * se conserva la última fila, igual que en la importación anterior.
             */
            $clave = $sucursal . "\x1F" . $codigo;

            $registros[$clave] = [
                'sucursal' => $sucursal,
                'codigo' => $codigo,
                'descripcion' => $descripcion,
                'cantidad' => $cantidad,
                'updated_at' => $ahora,
            ];
        }

        if (empty($registros)) {
            return;
        }

        /*
         * Antes se ejecutaba un INSERT ... ON CONFLICT por cada fila.
         * Ahora se hace UPSERT masivo por bloques.
         */
        foreach (array_chunk(array_values($registros), self::BATCH_SIZE) as $lote) {
            DB::table('stock_sucursales')->upsert(
                $lote,
                ['sucursal', 'codigo'],
                ['descripcion', 'cantidad', 'updated_at']
            );
        }
    }
}
