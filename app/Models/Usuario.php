<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    public $table = 'usuarios';

    public $fillable = [
        'usu_nick',
        'usu_clave',
        'role_id',
        'usu_nombres',
        'usu_estado'
    ];

    protected $casts = [
        'usu_nick' => 'string',
        'usu_clave' => 'string',
        'usu_nombres' => 'string',
        'usu_estado' => 'string'
    ];

    public static array $rules = [
        'usu_nick' => 'required|string|max:60',
        'usu_clave' => 'required|string|max:120',
        'role_id' => 'required',
        'usu_nombres' => 'required|string|max:150',
        'usu_estado' => 'nullable|string|max:20'
    ];

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }
}
