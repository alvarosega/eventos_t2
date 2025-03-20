<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistroExternoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\PedidoController;

/*
|--------------------------------------------------------------------------
| Rutas de la Aplicación
|--------------------------------------------------------------------------
|
| Aquí definimos todas las rutas necesarias para autenticación, 
| gestión de eventos, catálogos, inscripciones y pedidos.
|
*/

// Ruta principal (home) para usuarios autenticados
Route::get('/', [HomeController::class, 'home'])
    ->name('home')
    ->middleware('auth:externo,empleado,web');

// Rutas de autenticación (login, logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas para registro de usuarios externos
Route::get('/registro-externo', [RegistroExternoController::class, 'showRegistrationForm'])
    ->name('registro.externo');
Route::post('/registro-externo', [RegistroExternoController::class, 'register']);

/* =========================
   RUTAS PARA EVENTOS
   ========================= */

// Solo superadmin puede crear eventos
Route::middleware(['auth:externo,empleado,web'])->group(function () {
    Route::get('/eventos/create', [EventoController::class, 'create'])->name('eventos.create');
    Route::post('/eventos/store', [EventoController::class, 'store'])->name('eventos.store');
});

// Empleados (admin y superadmin) pueden editar/actualizar/eliminar eventos
Route::middleware(['auth:empleado'])->group(function () {
    Route::get('/eventos/{id}/edit', [EventoController::class, 'edit'])->name('eventos.edit');
    Route::put('/eventos/{id}', [EventoController::class, 'update'])->name('eventos.update');
    Route::delete('/eventos/{id}', [EventoController::class, 'destroy'])->name('eventos.destroy');
});

/* =========================
   RUTAS PARA CATÁLOGOS
   ========================= */
Route::middleware(['auth:externo,empleado,web'])->group(function () {
    // Lista de eventos para administrar catálogos
    Route::get('/catalogos', [CatalogoController::class, 'index'])->name('catalogos.index');

    // Mostrar catálogo para un evento específico
    Route::get('/catalogos/{evento}', [CatalogoController::class, 'show'])->name('catalogos.show');

    // Crear producto en el catálogo (solo superadmin)
    Route::get('/catalogos/{evento}/create', [CatalogoController::class, 'create'])->name('catalogos.create');
    Route::post('/catalogos/{evento}', [CatalogoController::class, 'store'])->name('catalogos.store');

    // Editar producto del catálogo (solo superadmin)
    Route::get('/catalogos/producto/{id}/edit', [CatalogoController::class, 'edit'])->name('catalogos.edit');
    Route::put('/catalogos/producto/{id}', [CatalogoController::class, 'update'])->name('catalogos.update');

    // Eliminar producto del catálogo (solo superadmin)
    Route::delete('/catalogos/producto/{id}', [CatalogoController::class, 'destroy'])->name('catalogos.destroy');
});

/* =========================
   RUTAS PARA INSCRIPCIONES
   EXCLUSIVO PARA EXTERNOS
   ========================= */
Route::middleware(['auth:externo'])->group(function() {
    // Listar eventos disponibles
    Route::get('/inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');

    // Inscribirse
    Route::post('/inscripciones/{eventoId}', [InscripcionController::class, 'store'])->name('inscripciones.store');

    // Form y acción de cancelar (encuesta)
    Route::get('/inscripciones/cancelar/{id}', [InscripcionController::class, 'cancelarForm'])->name('inscripciones.cancelForm');
    Route::post('/inscripciones/cancelar/{id}', [InscripcionController::class, 'cancelar'])->name('inscripciones.cancel');

    // Ver mis inscripciones
    Route::get('/mis-inscripciones', [InscripcionController::class, 'misInscripciones'])->name('misInscripciones');

    // Mapa para seleccionar ubicación
    Route::get('/inscripciones/mapa/{eventoId}', [InscripcionController::class, 'showMapa'])->name('inscripciones.showMapa');

    // Guardar ubicación y asignar el evento al usuario
    Route::post('/inscripciones/mapa/{eventoId}', [InscripcionController::class, 'storeUbicacion'])->name('inscripciones.storeUbicacion');
});

/* =========================
   RUTAS PARA PEDIDOS
   ========================= */

// Todos los usuarios logueados (externo, admin, superadmin) pueden acceder a la index de pedidos
Route::middleware(['auth:externo,empleado,web'])->group(function() {
    // Muestra pedidos según el rol:
    // - admin/superadmin: todos
    // - externo: solo sus pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
});

// El usuario externo crea y ve (y confirma entrega) de sus pedidos
Route::middleware(['auth:externo'])->group(function() {
    // Crear pedido
    Route::get('/pedidos/create/{eventoId}', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::post('/pedidos/store/{eventoId}', [PedidoController::class, 'store'])->name('pedidos.store');

    // Ver un pedido
    Route::get('/pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');

    // Confirmar entrega (el externo marca el pedido como 'entregado')
    Route::post('/pedidos/{id}/confirmar-entrega', [PedidoController::class, 'confirmarEntrega'])->name('pedidos.confirmarEntrega');
});

// Admin/Superadmin pueden cambiar el estado del pedido
Route::middleware(['auth:empleado'])->group(function() {
    Route::post('/pedidos/{id}/cambiar-estado', [PedidoController::class, 'changeStatus'])->name('pedidos.changeStatus');
});

// Solo admin/superadmin pueden ver pedidos de un evento específico
Route::middleware(['auth:empleado'])->group(function () {
    Route::get('/eventos/{evento}/pedidos', [PedidoController::class, 'porEvento'])->name('pedidos.evento');
});

// Rutas para actualizar estado y subir evidencia (AJAX)
Route::middleware(['auth:empleado'])->group(function () {
    Route::put('/pedidos/{pedido}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.updateStatus');
    Route::put('/pedidos/{pedido}/evidence', [PedidoController::class, 'updateEvidence'])->name('pedidos.updateEvidence');
});