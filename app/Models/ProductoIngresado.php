<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoIngresado extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_orden_compra_id',
        'cantidad_cajas',
    ];

    public function productoOrdenCompra()
    {
        return $this->belongsTo(ProductoOrdenCompra::class, 'producto_orden_compra_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
