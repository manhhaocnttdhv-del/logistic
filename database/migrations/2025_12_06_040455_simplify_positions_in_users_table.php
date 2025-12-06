<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật các giá trị cũ thành giá trị mới
        DB::table('users')
            ->whereIn('position', ['import_staff', 'export_staff', 'picking_staff'])
            ->update(['position' => 'warehouse_staff']);

        // Thay đổi enum để chỉ còn 3 giá trị
        DB::statement("ALTER TABLE users MODIFY COLUMN position ENUM('warehouse_staff', 'inventory_staff', 'general_staff') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục enum về 6 giá trị
        DB::statement("ALTER TABLE users MODIFY COLUMN position ENUM('warehouse_staff', 'import_staff', 'export_staff', 'inventory_staff', 'picking_staff', 'general_staff') NULL");
    }
};
