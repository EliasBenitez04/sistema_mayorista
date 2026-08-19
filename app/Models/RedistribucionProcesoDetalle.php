<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RedistribucionProcesoDetalle extends Model
{
    use HasFactory;

    protected $table = 'redistribucion_proceso_detalle';

    public $timestamps = false;

    protected $fillable = [
        'proceso_id',
        'codigo',
        'sucursal_origen',
        'sucursal_destino',
        'cantidad',
        'estado',
        'observacion',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'cantidad' => 'integer',
        'sucursal_origen' => 'integer',
        'sucursal_destino' => 'integer',
    ];

    public function proceso()
    {
        return $this->belongsTo(
            RedistribucionProceso::class,
            'proceso_id'
        );
    }

    public function origen()
    {
        return $this->belongsTo(
            Sucursal::class,
            'sucursal_origen',
            'cod_suc'
        );
    }

    public function destino()
    {
        return $this->belongsTo(
            Sucursal::class,
            'sucursal_destino',
            'cod_suc'
        );
    }

    public function lote()
    {
        return $this->belongsTo(
            RedistribucionLote::class,
            'lote_id'
        );
    }
}
