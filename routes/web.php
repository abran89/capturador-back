<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrdenController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'loginWeb'])->name('login');

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

Route::middleware(['admin'])->group(function () {

    Route::get('/admin-dashboard', function() {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::post('/usuarios/{id}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');

    Route::get('/ordenes', [OrdenController::class, 'index'])->name('admin.ordenes.index');
    Route::get('/ordenes/{id}/productos', [OrdenController::class, 'getProductos']);

});
