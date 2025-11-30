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
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NotificationController;

// Show login on first load
Route::get('/', [AuthWebController::class, 'showLogin']);
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::post('/logout', [AuthWebController::class, 'logout']);


Route::middleware('auth')->group(function () {


Route::resource('products',ProductController::class);
Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
Route::put('/products/{product}/status', [ProductController::class, 'updateStatus'])->name('products.status');
    // Web routes for CRM
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::get('customers/export/{type}', [App\Http\Controllers\CustomerController::class, 'export'])->name('customers.export');
});
// Protected pages
Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class,'index']);

Route::resource('roles', RoleController::class);
Route::post('roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
Route::delete('roles/{role}/revoke-permission/{permission}', [RoleController::class, 'revokePermission'])->name('roles.revoke-permission');

Route::get('/production/{id}/inventory-history', [ProductionController::class, 'inventoryHistory'])->name('production.inventory-history');
Route::get('/production/{productionId}/item/{itemId}/inventory-history', [ProductionController::class, 'itemInventoryHistory'])->name('production.item-inventory-history');


// Quotation Routes
Route::prefix('quotes')->group(function () {
    Route::get('convert-to-order/{id}',[QuotationController::class,'convertToOrder'])->name('quotes.convert-to-order');
    Route::get('/quotes/{id}/download-pdf', [QuotationController::class, 'downloadPDF'])->name('quotes.download-pdf');
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
    Route::get('qa-production/{id}',[QaQuoteController::class,'production'])->name('qa-production.show');
    Route::get('/', [QaQuoteController::class, 'index'])->name('qa.index');
    Route::get('{id}', [QaQuoteController::class, 'show'])->name('qa.show');
    Route::post('{id}/upload', [QaQuoteController::class, 'uploadDocuments'])->name('qa.upload');
    Route::post('{id}/approve', [QaQuoteController::class, 'approve'])->name('qa.approve');
    Route::post('{id}/reject', [QaQuoteController::class, 'reject'])->name('qa.reject');

     Route::patch('approve-inventory/{transaction}', [QaQuoteController::class, 'approveInventory'])->name('inventory-transactions.approve');
    Route::patch('reject-inventory/{transaction}', [QaQuoteController::class, 'rejectInventory'])->name('inventory-transactions.reject');
    Route::patch('/production/{production}/approve-all', [QaQuoteController::class, 'approveAll'])->name('inventory-transactions.approve-all');
    Route::patch('/production/{production}/reject-all', [QaQuoteController::class, 'rejectAll'])->name('inventory-transactions.reject-all');
});

Route::resource('orders',OrderController::class);
Route::get('/orders/{id}/download-pdf', [OrderController::class, 'downloadPDF'])->name('orders.download-pdf');
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
Route::put('/production/item/{id}/add-ready', [ProductionController::class, 'addReadyQuantity'])
    ->name('production.item.addReady');



// Production Routes
Route::prefix('production')->group(function () {
    Route::get('/', [ProductionController::class, 'index'])->name('production.index');
    Route::get('create', [ProductionController::class, 'create'])->name('production.create');
    Route::post('/', [ProductionController::class, 'store'])->name('production.store');
    Route::get('{id}', [ProductionController::class, 'show'])->name('production.show');
    Route::get('{id}/start', [ProductionController::class, 'startProduction'])->name('production.start');
    Route::get('{id}/complete', [ProductionController::class, 'completeProduction'])->name('production.complete');
});

    Route::post('invoices/{id}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
    Route::post('invoices/{id}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');

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

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.delete-all');
    Route::post('/notifications/{id}/toggle-read', [NotificationController::class, 'toggleRead'])->name('notifications.toggle-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
});

    Route::resource('/users', UserController::class);
    Route::get('/inventory', [InventoryController::class,'index']);
   
       Route::view('/workflow', 'modules.workflow_functional');
    // Task endpoints
    Route::post('/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [\App\Http\Controllers\TaskController::class, 'edit'])->name('tasks.edit');
    Route::match(['put','patch'], '/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::view('/documents', 'modules.documents');
    Route::get('/admin', [AdminController::class,'index']);
    // Admin update endpoints (AJAX-friendly) - accept POST and PUT so forms with _method work and AJAX can POST
     Route::put('/profile', [App\Http\Controllers\AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/password', [App\Http\Controllers\AdminController::class, 'updatePassword'])->name('admin.password.update');
    Route::put('/settings', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('admin.settings.update');
});