<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class StockImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {

            $sucursal = trim($row[0] ?? '');
            $cantidad = (float) ($row[1] ?? 0);
            $descripcion = trim($row[2] ?? '');
            $codigo = trim($row[3] ?? '');

            if ($sucursal === '' || $codigo === '') {
                continue;
            }

            DB::statement("
                INSERT INTO stock_sucursales
                    (sucursal, codigo, descripcion, cantidad, updated_at)
                VALUES (?, ?, ?, ?, NOW())
                ON CONFLICT (sucursal, codigo)
                DO UPDATE SET
                    cantidad = EXCLUDED.cantidad,
                    descripcion = EXCLUDED.descripcion,
                    updated_at = NOW()
            ", [
                $sucursal,
                $codigo,
                $descripcion,
                $cantidad
            ]);
        }
    }
}
