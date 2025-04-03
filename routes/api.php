<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas
// Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas con middleware de autenticación
Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'profile']);
    Route::post('/leer-archivo', [FileController::class, 'readFile']);
    Route::post('/ingresar_producto', [ProductController::class, 'ingresarProducto']);
    Route::get('/productos-ingresados', [ProductController::class, 'obtenerProductosIngresados']);
    Route::put('/modificar_producto/{id}', [ProductController::class, 'actualizarCantidad']);
    Route::get('/verificar-productos', [ProductController::class, 'verificarProductosOrden']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
});
