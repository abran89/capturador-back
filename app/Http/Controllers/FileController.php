<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;
use App\Models\OrdenCompra;
use App\Models\ProductoOrdenCompra;
use Illuminate\Database\QueryException;

class FileController extends Controller
{
    public function readFile(Request $request)
    {
        $request->validate([
            'codigo_orden' => 'required|string'
        ]);

        $existeOrden = OrdenCompra::where('numero_orden', $request->input('codigo_orden'))->first();

        if($existeOrden){
            if ($existeOrden->estado === 'Completa' || $existeOrden->estado == "Enviada completa") {
                return response()->json(['message' => 'La orden ya fue completada'], 400);
            } elseif ($existeOrden->estado == 'Pendiente' || $existeOrden->estado == "Enviada incompleta") {
                return response()->json(['message' => 'Archivo ya ingresado en sistema']);
            }
        }

        $filePath = "C:/Users/Lenovo I7/Downloads/Noc-" . $request->input('codigo_orden') . ".DAT";

        if (!File::exists($filePath)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }

        $lines = File::lines($filePath);

        DB::beginTransaction();
        try {
            $primeraLinea = true;
            foreach ($lines as $line) {
                $data = explode(';', trim($line));
                if (count($data) < 5) continue;

                $numeroOrden = trim(str_replace('-', '', $data[0]));
                $rutProveedor = trim($data[1]);
                $producto = trim($data[2]);
                $cantidad = (int) trim($data[3]);
                $valorUnitario = (int) trim($data[4]);

                if($primeraLinea){

                    $proveedor = Proveedor::firstOrCreate(['rut' => $rutProveedor]);

                    $ordenCompra = OrdenCompra::create([
                        'numero_orden' => $numeroOrden,
                        'proveedor_id' => $proveedor->id,
                        'user_id' => auth()->id()
                    ]);

                    $primeraLinea = false;
                }

                ProductoOrdenCompra::create([
                    'orden_compra_id' => $ordenCompra->id,
                    'codigo_producto' => $producto,
                    'cantidad_cajas' => $cantidad,
                    'valor_unitario' => $valorUnitario
                ]);
            }

            DB::commit();
            return response()->json(['mensaje' => 'Archivo procesado correctamente']);
        } catch (QueryException $e)  {

            if ($e->errorInfo[1] == 1062) { // Código de error para clave única duplicada
                return response()->json([
                    'success' => false,
                    'error' => 'Hay códigos de productos duplicados en el archivo, debe corregir el archivo para continuar',
                ], 400);
            }
            else {
                DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo', 'detalle' => $e->getMessage()], 500);
            }

        }
    }
}
