<?php

use Illuminate\Support\Facades\Route;

// Importación de Controladores
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminVentaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\MovimientoCajaController;
use App\Http\Controllers\PanelEstadisticoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\Admin\ConfiguracionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 🌐 1. RUTAS PÚBLICAS Y REDIRECCIÓN INICIAL
// =========================================================================

Route::get('/', function () {
    return view('welcome');
});

// Redirección Dinámica por Rol (Dashboard Central)
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match ($role) {
        'admin'             => redirect()->route('admin.dashboard'),
        'cajero', 'cajero2' => redirect()->route('caja.index'),
        'cliente'           => redirect()->route('cliente.dashboard'),
        default             => abort(403, 'Rol no autorizado.'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');


// =========================================================================
// 🔒 2. PANEL DE ADMINISTRACIÓN (Exclusivo 'admin')
// =========================================================================

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // CRUD: Usuarios
    Route::prefix('usuarios')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'usuarios'])->name('index');
        Route::get('/crear', [AdminController::class, 'create'])->name('create');
        Route::post('/store', [AdminController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [AdminController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [AdminController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle', [AdminController::class, 'toggleEstado'])->name('toggle');
    });

    // CRUD: Productos
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/crear', [AdminController::class, 'createProducto'])->name('create');
        Route::post('/store', [AdminController::class, 'storeProducto'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'editProducto'])->name('edit');
        Route::put('/{id}/update', [AdminController::class, 'updateProducto'])->name('update');
        Route::delete('/{id}/destroy', [AdminController::class, 'destroyProducto'])->name('destroy');
        Route::patch('/{id}/reactivar', [AdminController::class, 'reactivarProducto'])->name('reactivar');
    });

    // CRUD: Proveedores
    Route::prefix('proveedores')->name('proveedores.')->group(function () {
        Route::get('/', [ProveedorController::class, 'index'])->name('index');
        Route::get('/crear', [ProveedorController::class, 'create'])->name('create');
        Route::post('/store', [ProveedorController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProveedorController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [ProveedorController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle', [ProveedorController::class, 'toggleEstado'])->name('toggle');
        Route::delete('/{id}/destroy', [ProveedorController::class, 'destroy'])->name('destroy');
    });

    // CRUD: Categorías
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/crear', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [CategoryController::class, 'destroyProducto'])->name('destroy');
        Route::patch('/{id}/toggle', [CategoryController::class, 'toggleEstado'])->name('toggle');
    });

    // Reportes & Estadísticas
    Route::get('/estadisticas', [PanelEstadisticoController::class, 'index'])->name('admin.estadisticas');
    Route::get('/facturas/{id}/imprimir', [PanelEstadisticoController::class, 'imprimirFactura'])->name('facturas.imprimir');
    Route::put('/facturas/{id}/update-pago', [FacturaController::class, 'updatePago']);
    Route::patch('/reportes/{id}/toggle', [FacturaController::class, 'toggleReporte']);
    Route::patch('/movimientos/{id}/toggle', [FacturaController::class, 'toggleMovimiento'])->name('movimientos.toggle');

    // Configuración del Sistema
    Route::prefix('configuracion')->name('admin.configuracion.')->group(function () {
        Route::get('/', [ConfiguracionController::class, 'index'])->name('index');
        Route::post('/empresa', [ConfiguracionController::class, 'updateEmpresa'])->name('empresa');
        Route::post('/estilo', [ConfiguracionController::class, 'updateEstilo'])->name('estilo');
    });
});


// =========================================================================
// 💵 3. MÓDULO DE CAJA Y VENTAS
// =========================================================================

// A) Consultas y Lectura de Caja (Admin, Cajero y Cajero2)
Route::middleware(['auth', 'role:admin,cajero,cajero2'])->group(function () {
    Route::prefix('caja')->name('caja.')->group(function () {
        Route::get('/', [CajaController::class, 'index'])->name('index');
        Route::get('/historial', [CajaController::class, 'historial'])->name('historial');
        Route::get('/{caja}/movimientos', [CajaController::class, 'obtenerMovimientos'])->name('movimientos');
    });

    // Auditoría de Facturas
    Route::prefix('admin/facturas')->name('facturas.')->group(function () {
        Route::get('/', [FacturaController::class, 'index'])->name('index');
        Route::get('/{id}', [FacturaController::class, 'show'])->name('show');
    });
});

// B) Operaciones y Escritura de Caja (Admin y Cajero)
Route::middleware(['auth', 'role:admin,cajero'])->group(function () {
    // Ventas Pos / Registro
    Route::prefix('admin/ventas')->name('admin.ventas.')->group(function () {
        Route::get('/crear', [AdminVentaController::class, 'create'])->name('create');
        Route::post('/store', [AdminVentaController::class, 'store'])->name('store');
    });

    // Movimientos de Caja
    Route::post('/admin/movimientos', [MovimientoCajaController::class, 'store'])->name('admin.movimientos.store');

    // Procesamiento de Apertura/Cierre
    Route::prefix('caja')->name('caja.')->group(function () {
        Route::post('/procesar', [CajaController::class, 'procesarCaja'])->name('procesar');
        Route::post('/validar-contrasena', [CajaController::class, 'validarContrasena']);
    });
});


// =========================================================================
// 🍔 4. ÁREA EXCLUSIVA DE CLIENTES (Exclusivo 'cliente')
// =========================================================================

Route::middleware(['auth', 'role:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/dashboard', [ClienteController::class, 'index'])->name('dashboard');
    Route::get('/compras', [FacturaController::class, 'historialCliente'])->name('compras');
});


// =========================================================================
// 👤 5. PERFIL Y CARRITO (Cualquier usuario autenticado)
// =========================================================================

Route::middleware('auth')->group(function () {

    // Gestión de Perfil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Carrito de Compras
    Route::prefix('carrito')->name('carrito.')->group(function () {
        Route::get('/', [CarritoController::class, 'index'])->name('index');
        Route::get('/add/{id}', [CarritoController::class, 'add'])->name('add');
        Route::patch('/update', [CarritoController::class, 'update'])->name('update');
        Route::delete('/remove', [CarritoController::class, 'remove'])->name('remove');
    });

    // Creación Final de Factura
    Route::post('/facturas/store', [FacturaController::class, 'store'])->name('facturas.store');
});

// Autenticación de Breeze/Fortify
require __DIR__ . '/auth.php';