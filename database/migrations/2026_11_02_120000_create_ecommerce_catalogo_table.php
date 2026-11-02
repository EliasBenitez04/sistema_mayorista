<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ecommerce_catalogo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo_base', 32)->unique();
            $table->integer('linea_cod')->nullable()->index();
            $table->string('imagen', 255)->nullable();
            $table->string('nombre_archivo', 255)->nullable();
            $table->string('valor_v', 40)->nullable();
            $table->string('valor_c', 40)->nullable();
            $table->string('ot', 30)->nullable()->index();
            $table->string('descripcion_archivo', 255)->nullable();
            $table->date('fecha_archivo')->nullable();
            $table->timestamps();
            // Sin FK física: la tabla linea es heredada; la asignación se valida en Laravel.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_catalogo');
    }
};
