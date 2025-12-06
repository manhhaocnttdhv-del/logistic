<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Products
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::get('products/export/excel', [\App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');
    Route::get('products/import/template', [\App\Http\Controllers\Admin\ProductController::class, 'downloadTemplate'])->name('products.template');
    Route::post('products/import', [\App\Http\Controllers\Admin\ProductController::class, 'import'])->name('products.import');
    
    // Suppliers
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);
    Route::get('suppliers/export/excel', [\App\Http\Controllers\Admin\SupplierController::class, 'export'])->name('suppliers.export');
    Route::get('suppliers/import/template', [\App\Http\Controllers\Admin\SupplierController::class, 'downloadTemplate'])->name('suppliers.template');
    Route::post('suppliers/import', [\App\Http\Controllers\Admin\SupplierController::class, 'import'])->name('suppliers.import');
    
    // Import Orders
    Route::resource('import-orders', \App\Http\Controllers\Admin\ImportOrderController::class);
    Route::post('import-orders/{importOrder}/confirm', [\App\Http\Controllers\Admin\ImportOrderController::class, 'confirm'])->name('import-orders.confirm');
    Route::get('import-orders/{importOrder}/pdf', [\App\Http\Controllers\Admin\ImportOrderController::class, 'pdf'])->name('import-orders.pdf');
    Route::get('import-orders/export/excel', [\App\Http\Controllers\Admin\ImportOrderController::class, 'export'])->name('import-orders.export');
    
    // Export Orders
    Route::resource('export-orders', \App\Http\Controllers\Admin\ExportOrderController::class);
    Route::post('export-orders/{id}/confirm', [\App\Http\Controllers\Admin\ExportOrderController::class, 'confirm'])->name('export-orders.confirm');
    Route::get('export-orders/export/excel', [\App\Http\Controllers\Admin\ExportOrderController::class, 'export'])->name('export-orders.export');
    
    // Material Requests
    Route::resource('material-requests', \App\Http\Controllers\Admin\MaterialRequestController::class);
    Route::post('material-requests/{id}/approve', [\App\Http\Controllers\Admin\MaterialRequestController::class, 'approve'])->name('material-requests.approve');
    Route::post('material-requests/{id}/reject', [\App\Http\Controllers\Admin\MaterialRequestController::class, 'reject'])->name('material-requests.reject');
    
    // Employees
    Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
    Route::post('employees/{id}/toggle-status', [\App\Http\Controllers\Admin\EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    
    // Tasks
    Route::resource('tasks', \App\Http\Controllers\Admin\TaskController::class);
    
    // Warehouses
    Route::resource('warehouses', \App\Http\Controllers\Admin\WarehouseController::class);
    
    // Inventory Audits
    Route::resource('inventory-audits', \App\Http\Controllers\Admin\InventoryAuditController::class);
    Route::post('inventory-audits/{inventoryAudit}/confirm', [\App\Http\Controllers\Admin\InventoryAuditController::class, 'confirm'])->name('inventory-audits.confirm');
    Route::post('inventory-audits/{inventoryAudit}/adjust', [\App\Http\Controllers\Admin\InventoryAuditController::class, 'adjust'])->name('inventory-audits.adjust');
    
    // Reports
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/import-export', [\App\Http\Controllers\Admin\ReportController::class, 'importExport'])->name('reports.import-export');
    Route::get('reports/employee-performance', [\App\Http\Controllers\Admin\ReportController::class, 'employeePerformance'])->name('reports.employee-performance');
    Route::get('reports/low-stock', [\App\Http\Controllers\Admin\ReportController::class, 'lowStock'])->name('reports.low-stock');
    Route::get('reports/export-excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('reports/export-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.export-pdf');
});

// Staff Routes
Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    // Tasks
    Route::get('tasks', [\App\Http\Controllers\Staff\TaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/{id}', [\App\Http\Controllers\Staff\TaskController::class, 'show'])->name('tasks.show');
    Route::post('tasks/{id}/start', [\App\Http\Controllers\Staff\TaskController::class, 'start'])->name('tasks.start');
    Route::post('tasks/{id}/complete', [\App\Http\Controllers\Staff\TaskController::class, 'complete'])->name('tasks.complete');
    
    // Import Orders
    Route::get('import-orders', [\App\Http\Controllers\Staff\ImportOrderController::class, 'index'])->name('import-orders.index');
    Route::get('import-orders/create', [\App\Http\Controllers\Staff\ImportOrderController::class, 'create'])->name('import-orders.create');
    Route::post('import-orders', [\App\Http\Controllers\Staff\ImportOrderController::class, 'store'])->name('import-orders.store');
    Route::get('import-orders/{id}', [\App\Http\Controllers\Staff\ImportOrderController::class, 'show'])->name('import-orders.show');
    Route::post('import-orders/{id}/process', [\App\Http\Controllers\Staff\ImportOrderController::class, 'process'])->name('import-orders.process');
    
    // Export Orders
    Route::get('export-orders', [\App\Http\Controllers\Staff\ExportOrderController::class, 'index'])->name('export-orders.index');
    Route::get('export-orders/create', [\App\Http\Controllers\Staff\ExportOrderController::class, 'create'])->name('export-orders.create');
    Route::post('export-orders', [\App\Http\Controllers\Staff\ExportOrderController::class, 'store'])->name('export-orders.store');
    Route::get('export-orders/{id}', [\App\Http\Controllers\Staff\ExportOrderController::class, 'show'])->name('export-orders.show');
    Route::post('export-orders/{id}/process', [\App\Http\Controllers\Staff\ExportOrderController::class, 'process'])->name('export-orders.process');
    
    // Material Requests
    Route::get('material-requests', [\App\Http\Controllers\Staff\MaterialRequestController::class, 'index'])->name('material-requests.index');
    Route::get('material-requests/create', [\App\Http\Controllers\Staff\MaterialRequestController::class, 'create'])->name('material-requests.create');
    Route::post('material-requests', [\App\Http\Controllers\Staff\MaterialRequestController::class, 'store'])->name('material-requests.store');
    Route::get('material-requests/{id}', [\App\Http\Controllers\Staff\MaterialRequestController::class, 'show'])->name('material-requests.show');
    
    // Inventory
    Route::get('inventory', [\App\Http\Controllers\Staff\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/check', [\App\Http\Controllers\Staff\InventoryController::class, 'check'])->name('inventory.check');
    
    // Inventory Audits
    Route::get('inventory-audits', [\App\Http\Controllers\Staff\InventoryAuditController::class, 'index'])->name('inventory-audits.index');
    Route::get('inventory-audits/{id}', [\App\Http\Controllers\Staff\InventoryAuditController::class, 'show'])->name('inventory-audits.show');
    Route::post('inventory-audits/{id}/process', [\App\Http\Controllers\Staff\InventoryAuditController::class, 'process'])->name('inventory-audits.process');
    Route::post('inventory-audits/{id}/update-items', [\App\Http\Controllers\Staff\InventoryAuditController::class, 'updateItems'])->name('inventory-audits.update-items');
});

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});
