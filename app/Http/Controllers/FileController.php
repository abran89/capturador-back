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

class FileController extends Controller
{
    public function readFile(Request $request)
    {
        $request->validate([
            'codigo_orden' => 'required|string'
        ]);

        $filePath = "C:/Users/Lenovo I7/Downloads/Noc-" . $request->input('codigo_orden') . ".DAT";

        if (!File::exists($filePath)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }

        $lines = File::lines($filePath);

        DB::beginTransaction();
        try {
            foreach ($lines as $line) {
                $data = explode(';', trim($line));
                if (count($data) < 5) continue;

                $numeroOrden = str_replace('-', '', $data[0]);;
                $rutProveedor = $data[1];
                $producto = $data[2];
                $cantidad = (int) $data[3];
                $valorUnitario = (int) $data[4];

                $proveedor = Proveedor::firstOrCreate(['rut' => $rutProveedor]);

                $ordenCompra = OrdenCompra::firstOrCreate([
                    'numero_orden' => $numeroOrden,
                    'proveedor_id' => $proveedor->id,
                    'user_id' => auth()->id()
                ]);

                ProductoOrdenCompra::firstOrCreate([
                    'orden_compra_id' => $ordenCompra->id,
                    'codigo_producto' => $producto,
                    'cantidad_cajas' => $cantidad,
                    'valor_unitario' => $valorUnitario
                ]);
            }

            DB::commit();
            return response()->json(['mensaje' => 'Archivo procesado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo', 'detalle' => $e->getMessage()], 500);
        }
    }
}
