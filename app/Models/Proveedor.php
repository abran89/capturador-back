<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut',
        'nombre',
    ];

    public function ordenesCompras()
    {
        return $this->hasMany(OrdenCompra::class);
    }
}
