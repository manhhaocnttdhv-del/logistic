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
        // Thêm import_staff và export_staff vào enum
        DB::statement("ALTER TABLE users MODIFY COLUMN position ENUM('warehouse_staff', 'import_staff', 'export_staff', 'inventory_staff', 'general_staff') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục về 3 chức vụ
        DB::statement("ALTER TABLE users MODIFY COLUMN position ENUM('warehouse_staff', 'inventory_staff', 'general_staff') NULL");
    }
};
