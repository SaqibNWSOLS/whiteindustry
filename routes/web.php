<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\Api\ReportingController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RndQuoteController;
use App\Http\Controllers\QaQuoteController;
use App\Http\Controllers\OrderController;
use App\Models\QaQuote;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

// Show login on first load
Route::get('/', [AuthWebController::class, 'showLogin']);
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::post('/logout', [AuthWebController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class,'index']);


Route::resource('products',ProductController::class);
Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
Route::put('/products/{product}/status', [ProductController::class, 'updateStatus'])->name('products.status');
Route::middleware('auth')->group(function () {
    // Web routes for CRM
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::get('customers/export/{type}', [App\Http\Controllers\CustomerController::class, 'export'])->name('customers.export');
});
// Protected pages
Route::middleware('auth')->group(function () {




// Quotation Routes
Route::prefix('quotes')->group(function () {
    // Multi-step quotation creation
    Route::get('/create/{step?}', [QuotationController::class, 'create'])->name('quotes.create');
    Route::post('/store-basic', [QuotationController::class, 'storeBasic'])->name('quotes.store-basic');
        Route::put('/update-basic/{id}', [QuotationController::class, 'updateBasic'])->name('quotes.update-basic');

    Route::put('/{quote}/add-products', [QuotationController::class, 'addProducts'])->name('quotes.update-products');
    Route::put('/{quote}/add-raw-materials', [QuotationController::class, 'addRawMaterialsAndBlends'])->name('quotes.update-raw-materials');
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


Route::post('quotes/{quote}/send-to-rnd', [QuotationController::class, 'sendToRnd'])->name('rnd.send');
Route::post('quotes/{quote}/mark-as-accepted', [QuotationController::class, 'markAsAccepted'])->name('quotes.accepted');

// R&D Department Routes
Route::prefix('rnd')->group(function () {
    Route::get('/', [RndQuoteController::class, 'index'])->name('rnd.index');
    Route::get('{id}', [RndQuoteController::class, 'show'])->name('rnd.show');
    Route::post('{id}/upload', [RndQuoteController::class, 'uploadDocuments'])->name('rnd.upload');
    Route::post('{id}/approve', [RndQuoteController::class, 'approve'])->name('rnd.approve');
    Route::post('{id}/reject', [RndQuoteController::class, 'reject'])->name('rnd.reject');
});

// QA Department Routes
Route::prefix('qa')->group(function () {
    Route::get('/', [QaQuoteController::class, 'index'])->name('qa.index');
    Route::get('{id}', [QaQuoteController::class, 'show'])->name('qa.show');
    Route::post('{id}/upload', [QaQuoteController::class, 'uploadDocuments'])->name('qa.upload');
    Route::post('{id}/approve', [QaQuoteController::class, 'approve'])->name('qa.approve');
    Route::post('{id}/reject', [QaQuoteController::class, 'reject'])->name('qa.reject');
});

Route::resource('orders',OrderController::class);
Route::get('/productions/{production}/details', [InvoiceController::class, 'getProductionDetails'])->name('productions.details');
// Orders Routes
Route::prefix('orders')->group(function () {
    Route::post('{id}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');


    Route::post('/store-basic', [OrderController::class, 'storeBasic'])->name('orders.store-basic');
        Route::put('/update-basic/{id}', [OrderController::class, 'updateBasic'])->name('orders.update-basic');

    Route::put('/{order}/add-products', [OrderController::class, 'addProducts'])->name('orders.update-products');
    Route::put('/{order}/add-raw-materials', [OrderController::class, 'addRawMaterialsAndBlends'])->name('orders.update-raw-materials');
    Route::put('/{order}/update-blend', [OrderController::class, 'addBlend'])->name('orders.update-blend');
    Route::put('/{order}/update-packaging', [OrderController::class, 'addPackaging'])->name('orders.update-packaging');
    Route::post('/{order}/calculate', [OrderController::class, 'calculate'])->name('orders.calculate');
});

Route::put('production/item/{item}', [ProductionController::class, 'updateProductionItem'])->name('production.item.update');


// Production Routes
Route::prefix('production')->group(function () {
    Route::get('/', [ProductionController::class, 'index'])->name('production.index');
    Route::get('create', [ProductionController::class, 'create'])->name('production.create');
    Route::post('/', [ProductionController::class, 'store'])->name('production.store');
    Route::get('{id}', [ProductionController::class, 'show'])->name('production.show');
    Route::get('{id}/start', [ProductionController::class, 'startProduction'])->name('production.start');
    Route::get('{id}/complete', [ProductionController::class, 'completeProduction'])->name('production.complete');
});


Route::resource('invoices', InvoiceController::class);
 Route::get('payments/{invoice_id}/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments/{invoice_id}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');

// Invoices Routes
Route::prefix('invoices')->group(function () {
    
    Route::post('{id}/send', [InvoiceController::class, 'sendInvoice'])->name('invoices.send');
    Route::post('{id}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.paid');
    Route::get('{id}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
});

Route::delete('/rnd/documents/{id}', [RndQuoteController::class, 'deleteDocument'])->name('rnd.document.delete');
Route::delete('/qa/documents/{id}', [QaQuoteController::class, 'deleteDocument'])->name('qa.document.delete');

// API Route for loading products
Route::get('api/qa/{id}/products', function($id) {
    $qa = QaQuote::findOrFail($id);
    return response()->json(['products' => $qa->quote->products]);
});


    Route::resource('/users', UserController::class);
    Route::view('/inventory', 'modules.inventory');
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