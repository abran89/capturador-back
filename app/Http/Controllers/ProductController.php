<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrdenCompra;
use App\Models\ProductoOrdenCompra;
use App\Models\ProductoIngresado;

class ProductController extends Controller
{
    public function ingresarProducto(Request $request)
    {
        $validated = $request->validate([
            'codigo_orden' => 'required|string',
            'codigo_producto' => 'required|string'
        ]);

        $orden = OrdenCompra::where('numero_orden', $validated['codigo_orden'])->first();

        if (!$orden) {
            return response()->json([
                'success' => false,
                'error' => 'Orden de compra no encontrada',
            ], 404);
        }

        $producto = ProductoOrdenCompra::where([
            ['codigo_producto', $validated['codigo_producto']],
            ['orden_compra_id', $orden->id]
        ])->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'error' => 'Producto no encontrado',
            ], 404);
        }

        $productoIngresadoExistente = ProductoIngresado::where('producto_orden_compra_id', $producto->id)
        ->first();

        if ($productoIngresadoExistente) {
            return response()->json([
                'success' => false,
                'error' => 'El producto ya ha sido ingresado para esta orden de compra',
            ], 400);
        }

        $productoIngresado = new ProductoIngresado();
        $productoIngresado->producto_orden_compra_id = $producto->id;
        $productoIngresado->cantidad_cajas = $producto->cantidad_cajas;
        $productoIngresado->user_id = auth()->id();
        $productoIngresado->save();

        $producto->estado = 'Ingresado';
        $producto->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $productoIngresado->id,
                'cantidad_cajas' => $productoIngresado->cantidad_cajas,
                'valor_unitario' => $productoIngresado->productoOrdenCompra->valor_unitario ?? null,
                'producto_orden_compra_id' =>  $productoIngresado->producto_orden_compra_id
            ]
        ]);
    }

    public function obtenerProductosIngresados(Request $request)
    {
        $validated = $request->validate([
            'codigo_orden' => 'required|string',
        ]);

        // Buscar la orden de compra por el número de orden
        $orden = OrdenCompra::where('numero_orden', $validated['codigo_orden'])->first();

        if (!$orden) {
            return response()->json([
                'success' => false,
                'error' => 'Orden de compra no encontrada',
            ], 404);
        }

        // Obtener los productos ingresados para la orden de compra
        $productosIngresados = ProductoIngresado::whereHas('productoOrdenCompra', function ($query) use ($orden) {
            $query->where('orden_compra_id', $orden->id);
        })->get();

        // Verificar si hay productos ingresados
        if ($productosIngresados->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No se han ingresado productos para esta orden de compra',
            ], 404);
        }

        // Devolver los productos ingresados
        return response()->json([
            'success' => true,
            'data' => $productosIngresados->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'codigo_producto' => $producto->productoOrdenCompra->codigo_producto ?? null,
                    'cantidad_cajas' => $producto->cantidad_cajas,
                    'valor_unitario' => $producto->productoOrdenCompra->valor_unitario ?? null,
                    'producto_orden_compra_id' => $producto->producto_orden_compra_id,
                ];
            }),
        ]);
    }

    public function actualizarCantidad($id, Request $request)
    {
         // Validación de los datos recibidos
         $validated = $request->validate([
            'cantidad_cajas' => 'required|integer|min:1', // Validar que sea un número entero mayor a 0
        ]);

        // Buscar el producto por su ID
        $producto = ProductoIngresado::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Actualizar la cantidad de cajas con el valor validado
        $producto->cantidad_cajas = $validated['cantidad_cajas'];
        $producto->save();

        $productoOrden = ProductoOrdenCompra::find($producto->producto_orden_compra_id);

        if ($productoOrden) {

            if($productoOrden->cantidad_cajas != $producto->cantidad_cajas){
                $productoOrden->estado = 'Modificado';
                $productoOrden->save();
            }

        }

        // Devolver la respuesta de éxito
        return response()->json([
            'success' => true,
            'message' => 'Cantidad de cajas actualizada con éxito',
            'data' => $producto
        ]);
    }

}
