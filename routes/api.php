<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportingController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ProductController;
// Public auth routes: register / login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes require web session auth (user must be logged in via session)
Route::middleware(['web', 'auth'])->group(function () {
    // Dashboard endpoints
    Route::prefix('dashboard')->group(function () {
        Route::get('/summary', [DashboardController::class, 'index']);
        Route::get('/recent-orders', [DashboardController::class, 'recentOrders']);
        Route::get('/recent-invoices', [DashboardController::class, 'recentInvoices']);
        Route::get('/sales-report', [DashboardController::class, 'salesReport']);
        Route::get('/recent-products', [DashboardController::class, 'recentProducts']);
        Route::get('/recent-inventory', [DashboardController::class, 'recentInventory']);
    });

    // Lightweight reporting endpoints (used by reports view)
    Route::get('/reporting', [ReportingController::class, 'reporting']);

    // User management (admins and managers only - controller enforces role checks)
    Route::apiResource('users', App\Http\Controllers\Api\UserController::class);
    // Tasks - expose lightweight events endpoint for calendars
    Route::get('tasks/events', [App\Http\Controllers\Api\TaskController::class, 'events']);
    // Roles
    Route::apiResource('roles', App\Http\Controllers\Api\RoleController::class)->only(['index', 'show']);

    // Customer management
    Route::apiResource('customers', App\Http\Controllers\Api\CustomerController::class);

    // Leads and Quotes (CRM)
    Route::apiResource('leads', App\Http\Controllers\Api\LeadController::class);
    Route::apiResource('quotes', App\Http\Controllers\Api\QuoteController::class);


    Route::post('quotes/{quotation}/calculate', [QuoteController::class, 'calculate']);
Route::post('quotes/{quotation}/add-raw-material', [QuoteController::class, 'addRawMaterial']);
Route::post('quotes/{quotation}/add-packaging', [QuoteController::class, 'addPackaging']);



    Route::get('raw-materials', [QuoteController::class, 'rawMaterials']);
Route::get('packaging',  [QuoteController::class, 'packaging']);
Route::apiResource('quotations', QuoteController::class);

    // Product management
    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
    Route::get('products/statistics', [App\Http\Controllers\Api\ProductController::class, 'statistics']);

    // Production Orders
    Route::apiResource('production-orders', App\Http\Controllers\Api\ProductionOrderController::class);
    Route::get('production-orders-statistics', [App\Http\Controllers\Api\ProductionOrderController::class, 'statistics']);

    // Orders
    // register specific endpoints first so they are not captured by the resource {order} parameter
    Route::get('orders/statistics', [App\Http\Controllers\Api\OrderController::class, 'statistics']);
    Route::get('orders/suggested-number', [App\Http\Controllers\Api\OrderController::class, 'suggestedNumber']);
    Route::apiResource('orders', App\Http\Controllers\Api\OrderController::class);

    // Invoicing
    Route::get('invoices/statistics', [App\Http\Controllers\Api\InvoiceController::class, 'statistics']);
    Route::apiResource('invoices', App\Http\Controllers\Api\InvoiceController::class);
    // Inventory endpoints
    // Calculate cost for manufactured product (preview before creating)
    Route::post('inventory/calc-cost', [App\Http\Controllers\Api\InventoryController::class, 'calculateCost']);
    Route::get('inventory/statistics', [App\Http\Controllers\Api\InventoryController::class, 'statistics']);
    Route::get('inventory/low', [App\Http\Controllers\Api\InventoryController::class, 'lowStock']);
    Route::post('inventory/{inventory}/adjust-stock', [App\Http\Controllers\Api\InventoryController::class, 'adjustStock']);
    Route::apiResource('inventory', App\Http\Controllers\Api\InventoryController::class);
    // Documents - listing, upload, show, download and delete
    Route::get('documents', [App\Http\Controllers\Api\DocumentController::class, 'index']);
    Route::post('documents', [App\Http\Controllers\Api\DocumentController::class, 'upload']);
    Route::get('documents/{document}', [App\Http\Controllers\Api\DocumentController::class, 'show']);
    Route::get('documents/{document}/download', [App\Http\Controllers\Api\DocumentController::class, 'download']);
    Route::delete('documents/{document}', [App\Http\Controllers\Api\DocumentController::class, 'destroy']);
    // Payments suggested number
    Route::get('payments/suggested-number', [App\Http\Controllers\Api\PaymentController::class, 'suggestedNumber']);
    Route::apiResource('payments', App\Http\Controllers\Api\PaymentController::class);

    // Workflow endpoints (minimal)
    Route::prefix('workflow')->group(function () {
        Route::post('/leads/{lead}/create-quote', [WorkflowController::class, 'createQuoteFromLead']);
        Route::post('/quotes/{quote}/accept', [WorkflowController::class, 'acceptQuote']);
        Route::post('/orders/{order}/record-payment', [WorkflowController::class, 'recordPaymentForOrder']);
        Route::get('/orders/{order}/status', [WorkflowController::class, 'getOrderWorkflowStatus']);
    });
    // Notifications
    Route::get('notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('notifications/{notification}/mark-as-read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{notification}', [App\Http\Controllers\Api\NotificationController::class, 'destroy']);
});

// Logout should be protected (revokes token)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);