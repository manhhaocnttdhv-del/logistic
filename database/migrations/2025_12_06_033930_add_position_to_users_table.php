<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('position', [
                'warehouse_staff',    // Nhân viên kho (xử lý cả nhập và xuất)
                'import_staff',       // Nhân viên nhập kho
                'export_staff',       // Nhân viên xuất kho
                'inventory_staff',    // Nhân viên kiểm kê
                'picking_staff',      // Nhân viên soạn hàng
                'general_staff'       // Nhân viên tổng hợp
            ])->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
