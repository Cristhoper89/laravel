<?php

use App\Http\Controllers\PanelEstadisticoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\FacturaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminVentaController;
use App\Http\Controllers\MovimientoCajaController;
use App\Http\Controllers\Admin\ConfiguracionController;

Route::get('/', function () {
    return view('welcome');
});

// 1. Redirección por Rol (Dashboard central)
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'cliente' => redirect()->route('cliente.dashboard'),
        default => abort(403),
    };
})->middleware(['auth', 'verified'])->name('dashboard');


// =========================================================================
// 🔒 2. RUTAS EXCLUSIVAS PARA EL ADMINISTRADOR (Panel de Gestión Interna)
// =========================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Pantalla de Bienvenida / Principal del Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // CRUD de Usuarios
    Route::prefix('admin/usuarios')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'usuarios'])->name('index');
        Route::get('/crear', [AdminController::class, 'create'])->name('create');
        Route::post('/store', [AdminController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [AdminController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [AdminController::class, 'destroy'])->name('destroy');
    });

    // CRUD de Productos
    Route::prefix('admin/productos')->name('productos.')->group(function () {
        Route::get('/crear', [AdminController::class, 'createProducto'])->name('create');
        Route::post('/store', [AdminController::class, 'storeProducto'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'editProducto'])->name('edit');
        Route::put('/{id}/update', [AdminController::class, 'updateProducto'])->name('update');
        Route::delete('/{id}/destroy', [AdminController::class, 'destroyProducto'])->name('destroy');
    });

    // CRUD de Proveedores
    Route::prefix('admin/proveedores')->name('proveedores.')->group(function () {
        Route::get('/', [ProveedorController::class, 'index'])->name('index');
        Route::get('/crear', [ProveedorController::class, 'create'])->name('create');
        Route::post('/store', [ProveedorController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProveedorController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [ProveedorController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [ProveedorController::class, 'destroy'])->name('destroy');
    });

    // CRUD de Categorías
    Route::prefix('admin/categorias')->name('categorias.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/crear', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // 🧾 Historial de Facturas (Solo el Admin puede auditar todas las ventas)
    Route::prefix('admin/facturas')->name('facturas.')->group(function () {
        Route::get('/', [FacturaController::class, 'index'])->name('index'); // facturas.index
        Route::get('/{id}', [FacturaController::class, 'show'])->name('show');   // facturas.show
    });

    // 1. Modificar el método de pago desde la vista detalle
    Route::put('/admin/facturas/{id}/update-pago', [FacturaController::class, 'updatePago']);

    // 2. Activar/Desactivar el Reporte de Caja asociado (Usa lógica invertida / toggle)
    Route::patch('/admin/reportes/{id}/toggle', [FacturaController::class, 'toggleReporte']);

    Route::get('/admin/facturas/{id}/imprimir', [App\Http\Controllers\PanelEstadisticoController::class, 'imprimirFactura'])->name('facturas.imprimir');


    Route::get('/admin/estadisticas', [PanelEstadisticoController::class, 'index'])
        ->middleware(['auth']) // Puedes añadir tu middleware de admin aquí si lo tienes
        ->name('admin.estadisticas');

    /// 🛒 Panel de Ventas en Caja Directa (Sincronizado con el prefijo 'admin.' de tus vistas)
    Route::prefix('admin/ventas')->name('admin.ventas.')->group(function () {
        Route::get('/crear', [AdminVentaController::class, 'create'])->name('create'); // admin.ventas.create
        Route::post('/store', [AdminVentaController::class, 'store'])->name('store');   // admin.ventas.store
    });

    Route::post('/admin/movimientos', [MovimientoCajaController::class, 'store'])->name('admin.movimientos.store');
    // Rutas de Configuración del Sistema
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('admin.configuracion.index');
    Route::post('/configuracion/empresa', [ConfiguracionController::class, 'updateEmpresa'])->name('admin.configuracion.empresa');
    Route::post('/configuracion/estilo', [ConfiguracionController::class, 'updateEstilo'])->name('admin.configuracion.estilo');
});


// =========================================================================
// 🍔 3. RUTAS EXCLUSIVAS PARA EL CLIENTE (Catálogo de comida)
// =========================================================================
Route::middleware(['auth', 'role:cliente'])->group(function () {
    // Pantalla principal donde ve la comida
    Route::get('/cliente/dashboard', [ClienteController::class, 'index'])->name('cliente.dashboard');
    Route::get('/cliente/compras', [FacturaController::class, 'historialCliente'])->name('cliente.compras');
});


// =========================================================================
// 🌐 4. PERFIL, CARRITO Y PAGOS (Cualquier usuario logueado: Admin o Cliente)
// =========================================================================
Route::middleware('auth')->group(function () {

    // Rutas de Perfil estándar
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🛒 Gestión global del Carrito de Compras
    Route::prefix('carrito')->name('carrito.')->group(function () {
        Route::get('/', [CarritoController::class, 'index'])->name('index');             // carrito.index
        Route::get('/add/{id}', [CarritoController::class, 'add'])->name('add');         // carrito.add
        Route::patch('/update', [CarritoController::class, 'update'])->name('update');   // carrito.update
        Route::delete('/remove', [CarritoController::class, 'remove'])->name('remove');  // carrito.remove
    });

    // 🧾 Procesamiento de la Venta final (Disponible para el cliente al confirmar su carrito)
    Route::post('/facturas/store', [FacturaController::class, 'store'])->name('facturas.store');
});

require __DIR__ . '/auth.php';
