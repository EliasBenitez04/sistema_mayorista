<?php

use App\Http\Controllers\auditoriaController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PedidoComprasController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::post('/login', [App\Http\Controllers\Auth\Logincontroller::class, 'login']);

Route::resource('ciudades', App\Http\Controllers\Ciudadcontroller::class);

Route::resource('Departamentos', App\Http\Controllers\DepartamentoController::class);

Route::resource('clientes', App\Http\Controllers\ClienteController::class);

Route::resource('articulos', App\Http\Controllers\ArticuloController::class);

Route::resource('sucursal', App\Http\Controllers\sucursalController::class);

Route::resource('lineas', App\Http\Controllers\LineaController::class);

Route::resource('carga_fotos', App\Http\Controllers\CargaFotosController::class);

Route::resource('usuarios', App\Http\Controllers\UsuarioController::class);

Route::resource('auditoria', App\Http\Controllers\auditoriaController::class);

Route::get('users/detail/perfil', [App\Http\Controllers\UsuarioController::class, 'perfil']);

Route::resource('permissions', App\Http\Controllers\PermissionController::class);

Route::resource('roles', App\Http\Controllers\RoleController::class);

Route::resource('pedido_compras', App\Http\Controllers\PedidoComprasController::class);

Route::get('/auditoria', [auditoriaController::class, 'index'])->name('auditoria.index');

Route::get('/buscar-productos', [App\Http\Controllers\ArticuloController::class, 'buscarProductos'])
    ->name('buscar.productos');

Route::get('buscar-productos-ped', [App\Http\Controllers\PedidoComprasController::class, 'buscarProductoPed'])->name('buscar-productos-ped');

Route::patch('/pedido_compras/confirm/{id}', [PedidoComprasController::class, 'confirm'])->name('pedido_compras.confirm');

Route::get('/articulos/importar', [App\Http\Controllers\ArticuloController::class, 'showImportForm'])->name('articulos.importar.form');
Route::post('/articulos/importar', [App\Http\Controllers\ArticuloController::class, 'import'])->name('articulos.importar');

Route::get('pedido_compras/{id}/edit', [PedidoComprasController::class, 'edit'])->name('pedido_compras.edit');
Route::put('pedido_compras/{id}', [PedidoComprasController::class, 'update'])->name('pedido_compras.update');

Route::get('/import-progress', function () {

    return response()->json([
        'progress' => Cache::get('import_progress', 0)
    ]);
})->name('import.progress');

Route::get('pedido_compras/{id}/imprimir', [App\Http\Controllers\PedidoComprasController::class, 'imprimir'])
    ->name('pedido_compras.imprimir')
    ->middleware('auth');

Route::post('users/perfil/cambiar-password', [App\Http\Controllers\UsuarioController::class, 'cambiarPassword']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');
