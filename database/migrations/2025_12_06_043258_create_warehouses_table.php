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
        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique()->comment('Mã kho');
                $table->string('name')->comment('Tên kho');
                $table->string('address')->nullable()->comment('Địa chỉ kho');
                $table->string('manager_name')->nullable()->comment('Tên người quản lý');
                $table->string('phone')->nullable()->comment('Số điện thoại');
                $table->text('description')->nullable()->comment('Mô tả');
                $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
                $table->timestamps();
            });
        } else {
            // Bảng đã tồn tại, chỉ thêm các cột còn thiếu
            Schema::table('warehouses', function (Blueprint $table) {
                if (!Schema::hasColumn('warehouses', 'manager_name')) {
                    $table->string('manager_name')->nullable()->comment('Tên người quản lý')->after('address');
                }
                if (!Schema::hasColumn('warehouses', 'phone')) {
                    $table->string('phone')->nullable()->comment('Số điện thoại')->after('manager_name');
                }
                if (!Schema::hasColumn('warehouses', 'description')) {
                    $table->text('description')->nullable()->comment('Mô tả')->after('phone');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        if (Schema::hasTable('inventory_audits')) {
            Schema::table('inventory_audits', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
            });
        }
        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
            });
        }
        Schema::dropIfExists('warehouses');
    }
};
