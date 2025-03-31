<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoOrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'producto_ordenes_compras';

    protected $fillable = [
        'orden_compra_id',
        'codigo_producto',
        'cantidad_cajas',
        'valor_unitario',
    ];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function productosIngresados()
    {
        return $this->hasMany(ProductoIngresado::class, 'producto_orden_compra_id');
    }
}
