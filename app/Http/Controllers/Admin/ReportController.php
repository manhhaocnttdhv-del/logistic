<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ImportOrder;
use App\Models\ExportOrder;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        // Quick statistics
        $totalProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::where('is_active', true)->get()->filter(function($p) {
            return $p->isLowStock();
        })->count();
        
        $todayImports = ImportOrder::whereDate('import_date', today())->count();
        $todayExports = ExportOrder::whereDate('export_date', today())->count();
        
        $monthImports = ImportOrder::whereMonth('import_date', now()->month)
            ->whereYear('import_date', now()->year)
            ->where('status', 'completed')
            ->with('items')
            ->get()
            ->sum(function($order) {
                return $order->items->sum('total_price');
            });
        
        $monthExports = ExportOrder::whereMonth('export_date', now()->month)
            ->whereYear('export_date', now()->year)
            ->where('status', 'completed')
            ->get()
            ->sum(function($order) {
                return $order->items->sum(function($item) {
                    return $item->quantity * ($item->product->price ?? 0);
                });
            });
        
        $totalInventoryValue = Inventory::with('product')->get()->sum(function($inv) {
            return $inv->quantity * ($inv->product->price ?? 0);
        });
        
        $pendingTasks = Task::whereIn('status', ['pending', 'in_progress'])->count();
        $overdueTasks = Task::whereIn('status', ['pending', 'in_progress'])
            ->where('due_date', '<', now()->toDateString())
            ->count();
        
        $activeStaff = User::where('role', 'staff')->where('is_active', true)->count();
        
        return view('admin.reports.index', compact(
            'totalProducts', 'lowStockProducts', 'todayImports', 'todayExports',
            'monthImports', 'monthExports', 'totalInventoryValue',
            'pendingTasks', 'overdueTasks', 'activeStaff'
        ));
    }

    public function inventory(Request $request)
    {
        $query = Inventory::with('product.supplier');
        
        $period = $request->get('period', 'all');
        $startDate = null;
        $endDate = now();
        
        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                break;
            case 'quarter':
                $startDate = now()->startOfQuarter();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                break;
        }
        
        if ($startDate) {
            $query->where('last_updated_date', '>=', $startDate);
        }
        
        $inventories = $query->orderBy('last_updated_date', 'desc')->get();
        
        // Statistics
        $totalProducts = $inventories->count();
        $totalValue = $inventories->sum(function($inv) {
            return $inv->quantity * ($inv->product->price ?? 0);
        });
        $lowStockCount = $inventories->filter(function($inv) {
            return $inv->product->isLowStock();
        })->count();
        $outOfStockCount = $inventories->filter(function($inv) {
            return $inv->quantity <= 0;
        })->count();
        
        return view('admin.reports.inventory', compact('inventories', 'period', 'totalProducts', 'totalValue', 'lowStockCount', 'outOfStockCount'));
    }

    public function importExport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $imports = ImportOrder::whereBetween('import_date', [$startDate, $endDate])
            ->with('supplier', 'items.product', 'creator', 'assignedStaff')
            ->orderBy('import_date', 'desc')
            ->get();
        
        $exports = ExportOrder::whereBetween('export_date', [$startDate, $endDate])
            ->with('items.product', 'creator', 'assignedStaff')
            ->orderBy('export_date', 'desc')
            ->get();
        
        $importTotal = $imports->sum(function($order) {
            return $order->items->sum('total_price');
        });
        $importQuantity = $imports->sum(function($order) {
            return $order->items->sum('quantity');
        });
        
        $exportTotal = $exports->sum(function($order) {
            return $order->items->sum(function($item) {
                return $item->quantity * ($item->product->price ?? 0);
            });
        });
        $exportQuantity = $exports->sum(function($order) {
            return $order->items->sum('quantity');
        });
        
        // Product summary
        $productSummary = [];
        foreach ($imports as $import) {
            foreach ($import->items as $item) {
                $productId = $item->product_id;
                if (!isset($productSummary[$productId])) {
                    $productSummary[$productId] = [
                        'product' => $item->product,
                        'import_quantity' => 0,
                        'export_quantity' => 0,
                        'import_value' => 0,
                        'export_value' => 0,
                    ];
                }
                $productSummary[$productId]['import_quantity'] += $item->quantity;
                $productSummary[$productId]['import_value'] += $item->total_price ?? ($item->quantity * $item->unit_price);
            }
        }
        
        foreach ($exports as $export) {
            foreach ($export->items as $item) {
                $productId = $item->product_id;
                if (!isset($productSummary[$productId])) {
                    $productSummary[$productId] = [
                        'product' => $item->product,
                        'import_quantity' => 0,
                        'export_quantity' => 0,
                        'import_value' => 0,
                        'export_value' => 0,
                    ];
                }
                $productSummary[$productId]['export_quantity'] += $item->quantity;
                $productSummary[$productId]['export_value'] += $item->quantity * ($item->product->price ?? 0);
            }
        }
        
        return view('admin.reports.import-export', compact(
            'imports', 'exports', 'startDate', 'endDate', 
            'importTotal', 'exportTotal', 'importQuantity', 'exportQuantity',
            'productSummary'
        ));
    }

    public function employeePerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        
        $performance = [];
        foreach ($staff as $user) {
            $importCount = ImportOrder::where('assigned_to', $user->id)
                ->whereBetween('import_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count();
            
            $exportCount = ExportOrder::where('assigned_to', $user->id)
                ->whereBetween('export_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count();
            
            $taskCount = Task::where('assigned_to', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count();
            
            $pendingTasks = Task::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->count();
            
            $overdueTasks = Task::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('due_date', '<', now()->toDateString())
                ->count();
            
            $performance[] = [
                'user' => $user,
                'import_count' => $importCount,
                'export_count' => $exportCount,
                'task_count' => $taskCount,
                'pending_tasks' => $pendingTasks,
                'overdue_tasks' => $overdueTasks,
                'total' => $importCount + $exportCount + $taskCount,
            ];
        }
        
        usort($performance, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        return view('admin.reports.employee-performance', compact('performance', 'startDate', 'endDate'));
    }

    public function lowStock()
    {
        $products = Product::with('inventory', 'supplier')
            ->where('is_active', true)
            ->get()
            ->filter(function($product) {
                return $product->isLowStock();
            });
        
        return view('admin.reports.low-stock', compact('products'));
    }

    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'inventory');
        
        // Implementation for Excel export
        // You can use Maatwebsite\Excel here
        return back()->with('info', 'Chức năng xuất Excel đang được phát triển.');
    }

    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'inventory');
        
        // Implementation for PDF export
        return back()->with('info', 'Chức năng xuất PDF đang được phát triển.');
    }
}
