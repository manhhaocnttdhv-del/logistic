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
        Schema::create('inventory_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_audit_id')->constrained('inventory_audits')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('system_quantity')->default(0)->comment('Số lượng trong hệ thống');
            $table->integer('actual_quantity')->default(0)->comment('Số lượng thực tế');
            $table->integer('difference')->default(0)->comment('Chênh lệch (thực tế - hệ thống)');
            $table->text('notes')->nullable()->comment('Ghi chú lý do chênh lệch');
            $table->boolean('is_adjusted')->default(false)->comment('Đã điều chỉnh tồn kho');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_audit_items');
    }
};
