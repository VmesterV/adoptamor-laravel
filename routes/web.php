<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\PublishController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdoptionController; 


//Web Routes

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tienda', [StoreController::class, 'index'])->name('store');
Route::get('/producto/{id}', [StoreController::class, 'show'])->name('product.show');
Route::view('/contacto', 'pages.contact')->name('contact');

// Rutas que requieren Login
Route::middleware(['auth'])->group(function () {
    
    // Publicación
    Route::get('/publicar', [PublishController::class, 'index'])->name('publish');
    Route::post('/publicar/mascota', [PublishController::class, 'storePet'])->name('publish.pet.store');
    Route::post('/publicar/producto', [PublishController::class, 'storeProduct'])->name('publish.product.store');

    // Adopción (ESTA ES LA QUE TE FALTA)
    Route::post('/adoptar/{id}', [AdoptionController::class, 'store'])->name('adopt.store'); // <--- AGREGAR ESTA LÍNEA

    // Carrito de compra
    Route::post('/checkout', [StoreController::class, 'checkout'])->name('checkout');

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/pet/{id}/approve', [DashboardController::class, 'approvePet'])->name('admin.pet.approve');
        Route::delete('/pet/{id}/reject', [DashboardController::class, 'rejectPet'])->name('admin.pet.reject');
        Route::post('/product/{id}/approve', [DashboardController::class, 'approveProduct'])->name('admin.product.approve');
        Route::delete('/product/{id}/reject', [DashboardController::class, 'rejectProduct'])->name('admin.product.reject');
    });

    Route::post('/venta/{id}/entregar', [PublishController::class, 'markSaleAsDelivered'])->name('sale.deliver');
    Route::post('/venta/{id}/cancelar', [PublishController::class, 'cancelSale'])->name('sale.cancel');
    Route::post('/adopcion/{id}/aprobar', [PublishController::class, 'approveAdoption'])->name('adoption.approve');
    Route::post('/adopcion/{id}/rechazar', [PublishController::class, 'rejectAdoption'])->name('adoption.reject');

});

require __DIR__.'/auth.php';