<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\Api\ReportingController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ProductController;

// Show login on first load
Route::get('/', [AuthWebController::class, 'showLogin']);
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::post('/logout', [AuthWebController::class, 'logout']);


Route::resource('products',ProductController::class);
Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
Route::put('/products/{product}/status', [ProductController::class, 'updateStatus'])->name('products.status');

// Protected pages
Route::middleware('auth')->group(function () {




// Quotation Routes
Route::prefix('quotes')->group(function () {
    // Multi-step quotation creation
    Route::get('/create/{step?}', [QuotationController::class, 'create'])->name('quotes.create');
    Route::post('/store-basic', [QuotationController::class, 'storeBasic'])->name('quotes.store-basic');
        Route::put('/update-basic/{id}', [QuotationController::class, 'updateBasic'])->name('quotes.update-basic');

    Route::put('/{quote}/add-products', [QuotationController::class, 'addProducts'])->name('quotes.update-products');
    Route::put('/{quote}/add-raw-materials', [QuotationController::class, 'addRawMaterials'])->name('quotes.update-raw-materials');
    Route::put('/{quote}/update-blend', [QuotationController::class, 'addBlend'])->name('quotes.update-blend');
    Route::put('/{quote}/update-packaging', [QuotationController::class, 'addPackaging'])->name('quotes.update-packaging');
    Route::post('/{quote}/calculate', [QuotationController::class, 'calculate'])->name('quotes.calculate');
    
    // Standard CRUD routes
    Route::get('/', [QuotationController::class, 'index'])->name('quotes.index');
    Route::get('/{quote}', [QuotationController::class, 'show'])->name('quotes.show');
    Route::get('/{quote}/edit', [QuotationController::class, 'editModal'])->name('quotes.edit');
    Route::put('/{quote}', [QuotationController::class, 'update'])->name('quotes.update');
    Route::delete('/{quote}', [QuotationController::class, 'destroy'])->name('quotes.destroy');
    
    // Item management
    Route::delete('/items/{itemId}', [QuotationController::class, 'removeItem'])->name('quotes.remove-item');
    
    // AJAX routes
    Route::get('/product/{id}/details', [QuotationController::class, 'getProductDetails'])->name('quotes.product-details');
});

    Route::view('/dashboard', 'dashboard');
    Route::view('/users', 'users.index')->name('users.index');
    Route::view('/crm', 'modules.crm');
    Route::view('/production', 'modules.production');
    Route::view('/inventory', 'modules.inventory');
    Route::view('/orders', 'modules.orders');
    Route::view('/invoicing', 'modules.invoicing');
    // Render reports view and inject initial sales stats so the page loads server-side data
    Route::get('/reports', [ReportingController::class, 'index']);
    // Export reports as PDF
    Route::get('/reports/export', [ReportingController::class, 'exportPdf'])->name('reports.export');
    Route::view('/workflow', 'modules.workflow_functional');
    // Task endpoints
    Route::post('/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [\App\Http\Controllers\TaskController::class, 'edit'])->name('tasks.edit');
    Route::match(['put','patch'], '/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::view('/documents', 'modules.documents');
    Route::view('/notifications', 'modules.notifications');
    Route::view('/admin', 'modules.admin');
    // Admin update endpoints (AJAX-friendly) - accept POST and PUT so forms with _method work and AJAX can POST
    Route::match(['post', 'put'], '/admin/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::match(['post', 'put'], '/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});