<?php

namespace App\Imports;

use App\Models\Articulo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ArticulosImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $totalRows;

    public function __construct()
    {
        $this->totalRows = Cache::get('import_total', 1);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // aumentar contador
            $processed = Cache::increment('import_progress_count');

            // Saltar filas sin código
            if (empty($row['art_codigo'])) {
                $this->updatePercent($processed);
                continue;
            }

            $art_descripcion = substr($row['art_descripcion'], 0, 45);

            // Evitar duplicados
            $exists = DB::table('articulos')
                ->where('art_codigo', $row['art_codigo'])
                ->exists();

            if ($exists) {
                $this->updatePercent($processed);
                continue;
            }

            DB::table('articulos')->insert([
                'art_codigo' => $row['art_codigo'],
                'art_descripcion' => $art_descripcion,
                'art_precio' => $row['art_precio'],
                'art_iva' => $row['art_iva'],
                'prec_vent' => $row['prec_vent'],
            ]);

            $this->updatePercent($processed);
        }
    }

    private function updatePercent($processed)
    {
        $percent = intval(($processed / $this->totalRows) * 100);

        Cache::put('import_progress', min($percent, 100), 600);
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
