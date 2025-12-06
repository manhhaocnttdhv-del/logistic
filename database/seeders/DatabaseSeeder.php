<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ImportOrder;
use App\Models\ImportOrderItem;
use App\Models\ExportOrder;
use App\Models\ExportOrderItem;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\Task;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    private $faker;
    private $admin;
    private $staff = [];
    private $suppliers = [];
    private $products = [];

    public function run(): void
    {
        $this->faker = Faker::create('vi_VN');
        
        $this->createUsers();
        $this->createSuppliers();
        $this->createProducts();
        $this->createImportOrders();
        $this->createExportOrders();
        $this->createMaterialRequests();
        $this->createTasks();
    }

    private function createUsers()
    {
        // Create Admin
        $this->admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Quản trị viên',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '0901234567',
            ]
        );

        // Create Staff Users (10 nhân viên)
        $staffNames = [
            'Nguyễn Văn An', 'Trần Thị Bình', 'Lê Văn Cường', 'Phạm Thị Dung',
            'Hoàng Văn Em', 'Vũ Thị Phương', 'Đặng Văn Giang', 'Bùi Thị Hoa',
            'Ngô Văn Hùng', 'Đỗ Thị Lan'
        ];

        $positions = [
            'import_staff',       // Nhân viên nhập kho
            'export_staff',       // Nhân viên xuất kho
            'inventory_staff',    // Nhân viên kiểm kê
            'warehouse_staff',    // Nhân viên kho (nhiệm vụ khác)
            'general_staff'       // Nhân viên tổng hợp (nhiệm vụ khác)
        ];
        
        foreach ($staffNames as $index => $name) {
            // Phân bổ position: ưu tiên đa dạng
            $position = $positions[$index % count($positions)] ?? $this->faker->randomElement($positions);
            
            $staff = User::updateOrCreate(
                ['email' => 'staff' . ($index + 1) . '@example.com'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'position' => $position,
                    'is_active' => $this->faker->boolean(90), // 90% active
                    'phone' => '0' . $this->faker->numerify('#########'),
                ]
            );
            $this->staff[] = $staff;
        }
    }

    private function createSuppliers()
    {
        $supplierNames = [
            'Công ty TNHH Vật liệu Xây dựng ABC',
            'Công ty Cổ phần Thiết bị Công nghiệp XYZ',
            'Công ty TNHH Vật tư Điện tử DEF',
            'Công ty Cổ phần Nguyên liệu Sản xuất GHI',
            'Công ty TNHH Vật liệu Bao bì JKL',
            'Công ty Cổ phần Thiết bị Văn phòng MNO',
            'Công ty TNHH Vật tư Y tế PQR',
            'Công ty Cổ phần Nguyên liệu Thực phẩm STU',
        ];

        foreach ($supplierNames as $index => $name) {
            $supplier = Supplier::updateOrCreate(
                ['code' => 'NCC' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'name' => $name,
                    'contact_person' => $this->faker->name(),
                    'phone' => '0' . $this->faker->numerify('#########'),
                    'email' => $this->faker->companyEmail(),
                    'address' => $this->faker->address(),
                    'is_active' => $this->faker->boolean(85),
                ]
            );
            $this->suppliers[] = $supplier;
        }
    }

    private function createProducts()
    {
        $productCategories = [
            ['name' => 'Vật liệu xây dựng', 'units' => ['bao', 'tấn', 'm³']],
            ['name' => 'Thiết bị điện', 'units' => ['cái', 'bộ', 'thùng']],
            ['name' => 'Vật tư văn phòng', 'units' => ['thùng', 'hộp', 'cái']],
            ['name' => 'Nguyên liệu sản xuất', 'units' => ['kg', 'tấn', 'bao']],
            ['name' => 'Thiết bị công nghiệp', 'units' => ['bộ', 'cái', 'thùng']],
        ];

        $productIndex = 1;
        foreach ($productCategories as $category) {
            for ($i = 0; $i < 8; $i++) {
                $unit = $this->faker->randomElement($category['units']);
                $price = $this->faker->numberBetween(50000, 5000000);
                $minStock = $this->faker->numberBetween(5, 50);
                
                $product = Product::updateOrCreate(
                    ['code' => 'SP' . str_pad($productIndex, 4, '0', STR_PAD_LEFT)],
                    [
                        'name' => $category['name'] . ' ' . $this->faker->words(2, true),
                        'unit' => $unit,
                        'supplier_id' => $this->faker->randomElement($this->suppliers)->id,
                        'description' => $this->faker->sentence(10),
                        'price' => $price,
                        'min_stock' => $minStock,
                        'is_active' => $this->faker->boolean(95),
                    ]
                );

                // Create inventory
                $quantity = $this->faker->numberBetween(0, 200);
                Inventory::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'quantity' => $quantity,
                        'last_updated_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
                    ]
                );

                $this->products[] = $product;
                $productIndex++;
            }
        }
    }

    private function createImportOrders()
    {
        $sources = ['supplier', 'transfer', 'return', 'other'];
        
        // Get current count to avoid duplicate codes
        $currentCount = ImportOrder::count();
        
        for ($i = 0; $i < 30; $i++) {
            $source = $this->faker->randomElement($sources);
            $importDate = $this->faker->dateTimeBetween('-60 days', 'now');
            $status = $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']);
            $assignedStaff = $status !== 'pending' ? $this->faker->randomElement($this->staff) : null;
            
            // Always assign a supplier, but use different sources
            $supplier = $this->faker->randomElement($this->suppliers);
            
            // Generate unique code
            $codeNumber = $currentCount + $i + 1;
            $code = 'PN' . str_pad($codeNumber, 6, '0', STR_PAD_LEFT);
            
            $order = ImportOrder::updateOrCreate(
                ['code' => $code],
                [
                    'supplier_id' => $supplier->id,
                    'source' => $source,
                    'from_warehouse' => $source === 'transfer' ? $this->faker->company() . ' - Kho ' . $this->faker->city() : null,
                    'created_by' => $this->admin->id,
                    'assigned_to' => $assignedStaff?->id,
                    'import_date' => $importDate,
                    'status' => $status,
                    'notes' => $this->faker->optional(0.3)->sentence(),
                    'staff_notes' => $status === 'completed' ? $this->faker->optional(0.5)->sentence() : null,
                    'confirmed_by' => $status === 'completed' ? ($assignedStaff?->id ?? $this->admin->id) : null,
                    'confirmed_at' => $status === 'completed' ? $this->faker->dateTimeBetween($importDate, 'now') : null,
                ]
            );

            // Create items only if order is new or has no items
            if ($order->wasRecentlyCreated || !$order->items()->exists()) {
                // Delete existing items if any
                $order->items()->delete();
                
                $itemCount = $this->faker->numberBetween(2, 5);
                $selectedProducts = $this->faker->randomElements($this->products, $itemCount);
                
                foreach ($selectedProducts as $product) {
                    $quantity = $this->faker->numberBetween(10, 100);
                    $unitPrice = $product->price * (0.8 + $this->faker->randomFloat(2, 0, 0.4)); // 80-120% of product price
                    $totalPrice = $quantity * $unitPrice;
                    
                    ImportOrderItem::create([
                        'import_order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => round($unitPrice, 2),
                        'total_price' => round($totalPrice, 2),
                        'notes' => $this->faker->optional(0.2)->sentence(),
                    ]);

                    // Update inventory if order is completed and new
                    if ($status === 'completed' && $order->wasRecentlyCreated) {
                        $inventory = Inventory::where('product_id', $product->id)->first();
                        if ($inventory) {
                            $inventory->quantity += $quantity;
                            $inventory->last_updated_date = $order->confirmed_at ?? now();
                            $inventory->save();
                        }
                    }
                }
            }
        }
    }

    private function createExportOrders()
    {
        $reasons = ['sale', 'internal', 'transfer', 'return_supplier', 'return', 'other'];
        
        // Get current count to avoid duplicate codes
        $currentCount = ExportOrder::count();
        
        for ($i = 0; $i < 25; $i++) {
            $exportDate = $this->faker->dateTimeBetween('-45 days', 'now');
            $status = $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']);
            $assignedStaff = $status !== 'pending' ? $this->faker->randomElement($this->staff) : null;
            $reason = $this->faker->randomElement($reasons);
            
            // Generate unique code
            $codeNumber = $currentCount + $i + 1;
            $code = 'PX' . str_pad($codeNumber, 6, '0', STR_PAD_LEFT);
            
            $order = ExportOrder::updateOrCreate(
                ['code' => $code],
                [
                    'created_by' => $this->admin->id,
                    'assigned_to' => $assignedStaff?->id,
                    'export_date' => $exportDate,
                    'recipient' => $reason === 'internal' ? $this->faker->name() : ($this->faker->optional(0.7)->company()),
                    'reason' => $reason,
                    'reason_detail' => $this->faker->sentence(),
                    'status' => $status,
                    'notes' => $this->faker->optional(0.3)->sentence(),
                    'department' => $reason === 'internal' ? $this->faker->randomElement(['Phòng Sản xuất', 'Phòng Kỹ thuật', 'Phòng Bảo trì', 'Phòng Vận hành']) : null,
                    'project' => $reason === 'internal' ? $this->faker->optional(0.5)->words(3, true) : null,
                    'confirmed_by' => $status === 'completed' ? ($assignedStaff?->id ?? $this->admin->id) : null,
                    'confirmed_at' => $status === 'completed' ? $this->faker->dateTimeBetween($exportDate, 'now') : null,
                ]
            );

            // Create items only if order is new or has no items
            if ($order->wasRecentlyCreated || !$order->items()->exists()) {
                // Delete existing items if any
                $order->items()->delete();
                
                $itemCount = $this->faker->numberBetween(1, 4);
                $selectedProducts = $this->faker->randomElements($this->products, $itemCount);
                
                foreach ($selectedProducts as $product) {
                    $inventory = Inventory::where('product_id', $product->id)->first();
                    $maxQuantity = $inventory ? min($inventory->quantity, 50) : 20;
                    $quantity = $this->faker->numberBetween(1, $maxQuantity);
                    
                    ExportOrderItem::create([
                        'export_order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'notes' => $this->faker->optional(0.2)->sentence(),
                    ]);

                    // Update inventory if order is completed and new
                    if ($status === 'completed' && $order->wasRecentlyCreated && $inventory) {
                        $inventory->quantity = max(0, $inventory->quantity - $quantity);
                        $inventory->last_updated_date = $order->confirmed_at ?? now();
                        $inventory->save();
                    }
                }
            }
        }
    }

    private function createMaterialRequests()
    {
        $departments = ['Phòng Sản xuất', 'Phòng Kỹ thuật', 'Phòng Bảo trì', 'Phòng Vận hành', 'Phòng Chất lượng'];
        
        // Get current count to avoid duplicate codes
        $currentCount = MaterialRequest::count();
        
        for ($i = 0; $i < 15; $i++) {
            $requestDate = $this->faker->dateTimeBetween('-30 days', 'now');
            $status = $this->faker->randomElement(['pending', 'approved', 'converted', 'rejected']);
            $requestedBy = $this->faker->randomElement($this->staff);
            
            // Generate unique code
            $codeNumber = $currentCount + $i + 1;
            $code = 'YC' . str_pad($codeNumber, 6, '0', STR_PAD_LEFT);
            
            $request = MaterialRequest::updateOrCreate(
                ['code' => $code],
                [
                    'department' => $this->faker->randomElement($departments),
                    'project' => $this->faker->optional(0.7)->words(3, true),
                    'requested_by' => $requestedBy->id,
                    'approved_by' => in_array($status, ['approved', 'converted']) ? $this->admin->id : null,
                    'status' => $status,
                    'notes' => $this->faker->optional(0.4)->sentence(),
                    'rejection_reason' => $status === 'rejected' ? $this->faker->sentence() : null,
                    'approved_at' => in_array($status, ['approved', 'converted']) ? $this->faker->dateTimeBetween($requestDate, 'now') : null,
                    'created_at' => $requestDate,
                    'updated_at' => $requestDate,
                ]
            );

            // Create items (2-5 items per request) - only if request is new or has no items
            if ($request->wasRecentlyCreated || !$request->items()->exists()) {
                // Delete existing items if any (in case of update)
                $request->items()->delete();
                
                $itemCount = $this->faker->numberBetween(2, 5);
                $selectedProducts = $this->faker->randomElements($this->products, $itemCount);
                
                foreach ($selectedProducts as $product) {
                    MaterialRequestItem::create([
                        'material_request_id' => $request->id,
                        'product_id' => $product->id,
                        'quantity' => $this->faker->numberBetween(5, 50),
                        'notes' => $this->faker->optional(0.2)->sentence(),
                    ]);
                }
            }
            
            // Reload request to ensure items are loaded
            $request->load('items');

            // Create export order if converted - only if not already exists
            if ($status === 'converted' && !$request->export_order_id) {
                // Reload request to get items
                $request->load('items');
                
                // Generate unique export order code
                $lastExportOrder = ExportOrder::latest()->first();
                $exportNumber = $lastExportOrder ? (int)substr($lastExportOrder->code, -6) + 1 : ExportOrder::count() + 1;
                $exportCode = 'PX' . str_pad($exportNumber, 6, '0', STR_PAD_LEFT);
                
                $exportOrder = ExportOrder::updateOrCreate(
                    ['code' => $exportCode],
                    [
                        'created_by' => $this->admin->id,
                        'assigned_to' => $this->faker->randomElement($this->staff)->id,
                        'export_date' => $request->approved_at ?? now(),
                        'recipient' => $requestedBy->name,
                        'reason' => 'internal',
                        'reason_detail' => "Xuất từ yêu cầu vật tư {$request->code}",
                        'status' => 'completed',
                        'department' => $request->department,
                        'project' => $request->project,
                        'material_request_id' => $request->id,
                        'confirmed_by' => $this->admin->id,
                        'confirmed_at' => $request->approved_at ?? now(),
                    ]
                );
                
                // Only create items if export order is new
                if ($exportOrder->wasRecentlyCreated || !$exportOrder->items()->exists()) {
                    // Delete existing items if any
                    $exportOrder->items()->delete();
                    
                    foreach ($request->items as $item) {
                        ExportOrderItem::create([
                            'export_order_id' => $exportOrder->id,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'notes' => $item->notes,
                        ]);

                        // Update inventory only if order is new
                        if ($exportOrder->wasRecentlyCreated) {
                            $inventory = Inventory::where('product_id', $item->product_id)->first();
                            if ($inventory) {
                                $inventory->quantity = max(0, $inventory->quantity - $item->quantity);
                                $inventory->last_updated_date = now();
                                $inventory->save();
                            }
                        }
                    }
                }

                // Update material request with export order
                if (!$request->export_order_id) {
                    $request->export_order_id = $exportOrder->id;
                    $request->save();
                }
            }
        }
    }

    private function createTasks()
    {
        $types = ['import', 'export', 'inventory_check', 'picking', 'stock_report'];
        $priorities = ['low', 'normal', 'high', 'urgent'];
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        
        // Get import/export orders for related tasks
        $importOrders = ImportOrder::whereNotNull('assigned_to')->get();
        $exportOrders = ExportOrder::whereNotNull('assigned_to')->get();
        
        for ($i = 0; $i < 50; $i++) {
            $type = $this->faker->randomElement($types);
            $status = $this->faker->randomElement($statuses);
            $assignedStaff = $this->faker->randomElement($this->staff);
            $dueDate = $this->faker->dateTimeBetween('now', '+30 days');
            
            // Determine related order
            $relatedOrderId = null;
            $relatedOrderType = null;
            if (in_array($type, ['import', 'export']) && $this->faker->boolean(60)) {
                if ($type === 'import' && $importOrders->isNotEmpty()) {
                    $relatedOrder = $this->faker->randomElement($importOrders);
                    $relatedOrderId = $relatedOrder->id;
                    $relatedOrderType = 'import_order';
                    $assignedStaff = User::find($relatedOrder->assigned_to) ?? $assignedStaff;
                } elseif ($type === 'export' && $exportOrders->isNotEmpty()) {
                    $relatedOrder = $this->faker->randomElement($exportOrders);
                    $relatedOrderId = $relatedOrder->id;
                    $relatedOrderType = 'export_order';
                    $assignedStaff = User::find($relatedOrder->assigned_to) ?? $assignedStaff;
                }
            }
            
            $task = Task::create([
                'assigned_to' => $assignedStaff->id,
                'assigned_by' => $this->admin->id,
                'type' => $type,
                'priority' => $this->faker->randomElement($priorities),
                'title' => $this->getTaskTitle($type, $relatedOrderId),
                'description' => $this->getTaskDescription($type),
                'related_order_id' => $relatedOrderId,
                'related_order_type' => $relatedOrderType,
                'status' => $status,
                'due_date' => $this->faker->boolean(80) ? $dueDate : null,
                'started_at' => in_array($status, ['in_progress', 'completed']) ? $this->faker->dateTimeBetween('-10 days', 'now') : null,
                'completed_at' => $status === 'completed' ? $this->faker->dateTimeBetween('-5 days', 'now') : null,
                'completion_notes' => $status === 'completed' ? $this->faker->optional(0.6)->sentence() : null,
            ]);
        }
    }

    private function getTaskTitle($type, $relatedOrderId = null)
    {
        $titles = [
            'import' => 'Xử lý phiếu nhập',
            'export' => 'Xử lý phiếu xuất',
            'inventory_check' => 'Kiểm kê kho',
            'picking' => 'Soạn hàng',
            'stock_report' => 'Báo cáo tồn kho',
        ];
        
        $title = $titles[$type] ?? 'Công việc';
        if ($relatedOrderId) {
            $title .= ' #' . $relatedOrderId;
        }
        
        return $title;
    }

    private function getTaskDescription($type)
    {
        $descriptions = [
            'import' => 'Xử lý phiếu nhập hàng: Kiểm tra số lượng, chất lượng hàng hóa, nhập vào kho và cập nhật tồn kho.',
            'export' => 'Xử lý phiếu xuất hàng: Soạn hàng theo phiếu, kiểm tra số lượng, xuất kho và cập nhật tồn kho.',
            'inventory_check' => 'Kiểm kê kho: Đối chiếu số lượng thực tế với sổ sách, phát hiện chênh lệch và báo cáo.',
            'picking' => 'Soạn hàng: Lấy hàng theo đơn hàng, đóng gói và chuẩn bị xuất kho.',
            'stock_report' => 'Báo cáo tồn kho: Tổng hợp số liệu tồn kho, báo cáo hàng tồn kho thấp và đề xuất nhập hàng.',
        ];
        
        return $descriptions[$type] ?? 'Thực hiện công việc được phân công.';
    }
}
