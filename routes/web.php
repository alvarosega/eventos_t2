<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistroExternoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\InventarioController;

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

// Solo superadmin puede crear eventos (TIPO 1 - Original)
Route::middleware(['auth:externo,empleado,web'])->group(function () {
    Route::get('eventos/{id}/edit', [EventoController::class, 'edit'])->name('eventos.edit');
    Route::put('eventos/{id}', [EventoController::class, 'update'])->name('eventos.update');
    Route::delete('eventos/{id}', [EventoController::class, 'destroy'])->name('eventos.destroy');
    Route::get('eventos-tipo2/{id}/edit', [EventoController::class, 'editTipo2'])->name('eventos.edit-tipo2');
    Route::put('eventos-tipo2/{id}', [EventoController::class, 'updateTipo2'])->name('eventos.update-tipo2');
    Route::delete('eventos-tipo2/{id}', [EventoController::class, 'destroyTipo2'])->name('eventos.destroy-tipo2');
    
    // Rutas para crear y almacenar eventos (Tipo 1 y Tipo 2)
    Route::get('/eventos/create', [EventoController::class, 'create'])->name('eventos.create');
    Route::post('/eventos/store', [EventoController::class, 'store'])->name('eventos.store');
    
    Route::get('/eventos/select-type', [EventoController::class, 'selectType'])->name('eventos.select-type');
    Route::get('/eventos/create-tipo2', [EventoController::class, 'createTipo2'])->name('eventos.create-tipo2');
    Route::post('/eventos/store-tipo2', [EventoController::class, 'storeTipo2'])->name('eventos.store-tipo2');
    Route::get('/eventos/admin', [EventoController::class, 'admin'])->name('eventos.admin');

    // Ruta para administrar eventos (vía admin o eventos)
    Route::middleware(['auth:empleado,externo,web'])->group(function () {
        Route::get('/eventos/admin', [EventoController::class, 'admin'])->name('eventos.admin');
    });
    Route::get('/admin-eventos', [EventoController::class, 'adminEvents'])
        ->name('admin.events')
        ->middleware(['auth:externo,empleado,web']);
});


// Empleados (admin y superadmin) pueden editar/actualizar/eliminar eventos
Route::middleware(['auth:empleado'])->group(function () {
    Route::get('/eventos/{id}/edit', [EventoController::class, 'edit'])->name('eventos.edit');
    Route::put('/eventos/{id}', [EventoController::class, 'update'])->name('eventos.update');
    Route::delete('/eventos/{id}', [EventoController::class, 'destroy'])->name('eventos.destroy');
    
    // Rutas para editar/eliminar EventoTipo2
    Route::get('/eventos-tipo2/{id}/edit', [EventoController::class, 'editTipo2'])->name('eventos.edit-tipo2');
    Route::put('/eventos-tipo2/{id}', [EventoController::class, 'updateTipo2'])->name('eventos.update-tipo2');
    Route::delete('/eventos-tipo2/{id}', [EventoController::class, 'destroyTipo2'])->name('eventos.destroy-tipo2');
});
/* =========================
   RUTAS PARA CATÁLOGOS
   ========================= */


Route::middleware(['auth:externo,empleado,web'])->group(function () {

    // Acceso para superadmin, admin y master: ver lista y catálogo
    Route::get('/catalogos', [CatalogoController::class, 'index'])->name('catalogos.index');
    Route::get('/catalogos/{evento}', [CatalogoController::class, 'show'])->name('catalogos.show');

    // Acceso exclusivo para superadmin y master: crear, editar y eliminar productos
    Route::get('/catalogos/{evento}/create', [CatalogoController::class, 'create'])->name('catalogos.create');
    Route::post('/catalogos/{evento}', [CatalogoController::class, 'store'])->name('catalogos.store');
    
    Route::get('/catalogos/producto/{id}/edit', [CatalogoController::class, 'edit'])->name('catalogos.edit');
    Route::put('/catalogos/producto/{id}', [CatalogoController::class, 'update'])->name('catalogos.update');
    
    Route::delete('/catalogos/producto/{id}', [CatalogoController::class, 'destroy'])->name('catalogos.destroy');
});
/* =========================
   RUTAS PARA INSCRIPCIONES
   EXCLUSIVO PARA EXTERNOS
   ========================= */
Route::middleware(['auth:externo'])->group(function() {
    // Listar eventos disponibles
    Route::get('/inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');

    // Inscribirse en un evento
    Route::post('/inscripciones/{eventoId}', [InscripcionController::class, 'store'])->name('inscripciones.store');

    // Formulario para cancelar inscripción
    Route::get('/inscripciones/cancelar/{id}', [InscripcionController::class, 'cancelarForm'])->name('inscripciones.cancelForm');
    Route::post('/inscripciones/cancelar/{id}', [InscripcionController::class, 'cancelar'])->name('inscripciones.cancel');

    // Ver mis inscripciones activas
    Route::get('/mis-inscripciones', [InscripcionController::class, 'misInscripciones'])->name('misInscripciones');

    // Mostrar mapa para seleccionar ubicación al inscribirse
    Route::get('/inscripciones/mapa/{eventoId}', [InscripcionController::class, 'showMapa'])->name('inscripciones.showMapa');

    // Guardar la ubicación y asignar el evento al usuario
    Route::post('/inscripciones/mapa/{eventoId}', [InscripcionController::class, 'storeUbicacion'])->name('inscripciones.storeUbicacion');
});

/* =========================
   RUTAS PARA PEDIDOS
   ========================= */

// Todos los usuarios logueados (externo, admin, superadmin) pueden acceder a la index de pedidos
Route::middleware(['auth:externo,empleado,web'])->group(function() {
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
    Route::post('/pedidos/{pedido}/evidence', [PedidoController::class, 'updateEvidence'])->name('pedidos.updateEvidence');
});  
// Usar los mismos middlewares que en HomeController
Route::middleware(['auth:externo,empleado'])->group(function () {
    Route::prefix('inventario')->group(function () {
        Route::get('/', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/descargar-plantilla', [InventarioController::class, 'downloadTemplate'])
             ->name('inventario.download-template');
        Route::post('/subir', [InventarioController::class, 'upload'])
             ->name('inventario.upload');
    });
});