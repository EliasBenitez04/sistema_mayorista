<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        /*
         * Índices orientados al buscador de productos de pedidos.
         * Compatibles con PostgreSQL 9.5.
         */
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_stock_cod_suc_id_articulo
            ON stock (cod_suc, id_articulo)
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_articulos_codigo_lower_prefix
            ON articulos (LOWER(art_codigo) text_pattern_ops)
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_articulos_descripcion_lower_prefix
            ON articulos (LOWER(art_descripcion) text_pattern_ops)
        ");
    }

    public function down()
    {
        DB::statement('DROP INDEX IF EXISTS idx_stock_cod_suc_id_articulo');
        DB::statement('DROP INDEX IF EXISTS idx_articulos_codigo_lower_prefix');
        DB::statement('DROP INDEX IF EXISTS idx_articulos_descripcion_lower_prefix');
    }
};
