<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    public $table = 'articulos';

    public $timestamps = false;

    public $fillable = [
        'mar_cod',
        'art_descripcion',
        'art_precio',
        'art_imagen',
        'art_iva'
    ];

    protected $casts = [
        'art_descripcion' => 'string',
        'art_precio' => 'decimal:0',
        'art_imagen' => 'string'
    ];

    public static array $rules = [
        'mar_cod' => 'nullable',
        'art_descripcion' => 'required|string|max:45',
        'art_precio' => 'nullable|numeric',
        'art_imagen' => 'nullable|string|max:200',
        'art_iva' => 'nullable'
    ];


    public function sucursals(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Sucursal::class, 'stock');
    }

    public function ventas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\pedido_compras::class, 'detalle_pedido');
    }
}
