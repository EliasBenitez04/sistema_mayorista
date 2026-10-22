<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargaFotos extends Model
{
    protected $table = 'carga_fotos';
    protected $primaryKey = 'fot_cod';
    public $timestamps = false;


    /**
     * La instalación expone las imágenes mediante public/storage/fotos.
     * Funciona tanto si public/storage es un enlace como si es una carpeta física.
     */
    public function getFotoUrlAttribute()
    {
        if (!$this->fot_img || !$this->foto_disponible) {
            return null;
        }

        return asset('storage/fotos/' . rawurlencode(basename($this->fot_img)));
    }

    public function getFotoDisponibleAttribute()
    {
        return $this->fot_img
            && is_file(public_path('storage/fotos/' . basename($this->fot_img)));
    }

    public function linea()
    {
        return $this->belongsTo(Linea::class, 'linea_cod', 'linea_cod');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
