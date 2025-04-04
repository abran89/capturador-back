<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrdenCompra;


class OrdenController extends Controller
{
    public function index()
    {
        $ordenes = OrdenCompra::with(['proveedor', 'usuario'])->latest()->paginate(10);
        return view('admin.ordenes.index', compact('ordenes'));
    }

    public function getProductos($id)
    {
        $orden = OrdenCompra::with('productos.productosIngresados.usuario')->find($id);

        if (!$orden) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }

        return response()->json($orden->productos);
    }
}
