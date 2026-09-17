<?php

use App\Http\Controllers\auditoriaController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PedidoComprasController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| HOME / AUTH
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::post('/login', [App\Http\Controllers\Auth\Logincontroller::class, 'login']);

/*
|--------------------------------------------------------------------------
| CRUD PRINCIPALES
|--------------------------------------------------------------------------
*/

Route::resource('ciudades', App\Http\Controllers\Ciudadcontroller::class);
Route::resource('Departamentos', App\Http\Controllers\DepartamentoController::class);
Route::resource('clientes', App\Http\Controllers\ClienteController::class);
Route::resource('articulos', App\Http\Controllers\ArticuloController::class);
Route::resource('sucursal', App\Http\Controllers\sucursalController::class);
Route::resource('lineas', App\Http\Controllers\LineaController::class);
Route::resource('carga_fotos', App\Http\Controllers\CargaFotosController::class);
Route::resource('usuarios', App\Http\Controllers\UsuarioController::class);
Route::resource('auditoria', App\Http\Controllers\auditoriaController::class);
Route::resource('permissions', App\Http\Controllers\PermissionController::class);
Route::resource('roles', App\Http\Controllers\RoleController::class);
Route::resource('pedido_compras', App\Http\Controllers\PedidoComprasController::class);
Route::resource('stocks', App\Http\Controllers\StockController::class);

/*
|--------------------------------------------------------------------------
| USUARIO / PERFIL
|--------------------------------------------------------------------------
*/

Route::get('users/detail/perfil', [App\Http\Controllers\UsuarioController::class, 'perfil']);

Route::post('users/perfil/cambiar-password', [App\Http\Controllers\UsuarioController::class, 'cambiarPassword']);

/*
|--------------------------------------------------------------------------
| PEDIDOS COMPRA (ACCIONES ESPECIALES)
|--------------------------------------------------------------------------
*/

Route::get('/pedido_compras/clientes/catalogos', [App\Http\Controllers\PedidoClienteRapidoController::class, 'catalogos'])
    ->name('pedido_compras.clientes.catalogos');

Route::post('/pedido_compras/clientes/rapido', [App\Http\Controllers\PedidoClienteRapidoController::class, 'store'])
    ->name('pedido_compras.clientes.store');

Route::patch('/pedido_compras/confirm/{id}', [PedidoComprasController::class, 'confirm'])->name('pedido_compras.confirm');

Route::get('pedido_compras/{id}/edit', [PedidoComprasController::class, 'edit'])->name('pedido_compras.edit');

Route::put('pedido_compras/{id}', [PedidoComprasController::class, 'update'])->name('pedido_compras.update');

Route::get('pedido_compras/{id}/imprimir', [App\Http\Controllers\PedidoComprasController::class, 'imprimir'])
    ->name('pedido_compras.imprimir')
    ->middleware('auth');

Route::get('/pedido/export/{id}', [PedidoComprasController::class, 'export'])->name('pedido.export');

route::get('/pedido_compras/{id}/detalle', [PedidoComprasController::class, 'detalle'])->name('pedido_compras.detalle');

/*
|--------------------------------------------------------------------------
| ARTICULOS
|--------------------------------------------------------------------------
*/

Route::get('/buscar-productos', [App\Http\Controllers\ArticuloController::class, 'buscarProductos'])
    ->name('buscar.productos');

Route::get('/articulos/importar', [App\Http\Controllers\ArticuloController::class, 'showImportForm'])
    ->name('articulos.importar.form');

Route::post('/articulos/importar', [App\Http\Controllers\ArticuloController::class, 'import'])
    ->name('articulos.importar');

/*
|--------------------------------------------------------------------------
| PEDIDO COMPRA - BUSQUEDA
|--------------------------------------------------------------------------
*/

Route::get('buscar-productos-ped', [App\Http\Controllers\PedidoComprasController::class, 'buscarProductoPed'])
    ->name('buscar-productos-ped');

/*
|--------------------------------------------------------------------------
| STOCK
|--------------------------------------------------------------------------
*/

Route::post('/import-stock', [StockController::class, 'importStock'])
    ->name('import.stock');

/*
|--------------------------------------------------------------------------
| AUDITORIA
|--------------------------------------------------------------------------
*/

Route::get('/auditoria', [auditoriaController::class, 'index'])->name('auditoria.index');

/*
|--------------------------------------------------------------------------
| IMPORT PROGRESS (CACHE)
|--------------------------------------------------------------------------
*/

Route::get('/import-progress', function () {
    return response()->json([
        'progress' => Cache::get('import_progress', 0)
    ]);
})->name('import.progress');

/*
|--------------------------------------------------------------------------
| PASSWORD RESET
|--------------------------------------------------------------------------
*/

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');
