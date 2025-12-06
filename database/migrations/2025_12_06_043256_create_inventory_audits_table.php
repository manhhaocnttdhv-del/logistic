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
        Schema::create('inventory_audits', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Mã phiếu kiểm toán');
            $table->unsignedBigInteger('warehouse_id')->nullable()->comment('Kho kiểm toán');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict')->comment('Người tạo');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null')->comment('Người được giao');
            $table->date('audit_date')->comment('Ngày kiểm toán');
            $table->enum('type', ['full', 'partial', 'spot'])->default('full')->comment('Loại kiểm toán: toàn bộ, một phần, đột xuất');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending')->comment('Trạng thái');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->text('staff_notes')->nullable()->comment('Ghi chú từ nhân viên');
            $table->integer('total_items')->default(0)->comment('Tổng số sản phẩm kiểm toán');
            $table->integer('matched_items')->default(0)->comment('Số sản phẩm khớp');
            $table->integer('mismatched_items')->default(0)->comment('Số sản phẩm chênh lệch');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null')->comment('Người xác nhận');
            $table->timestamp('confirmed_at')->nullable()->comment('Thời gian xác nhận');
            $table->timestamps();
        });
        
        // Tạo foreign key sau khi bảng warehouses đã tồn tại
        if (Schema::hasTable('warehouses')) {
            Schema::table('inventory_audits', function (Blueprint $table) {
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_audits', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
        });
        Schema::dropIfExists('inventory_audits');
    }
};
