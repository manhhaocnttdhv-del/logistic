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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('department')->nullable()->comment('Phòng ban yêu cầu');
            $table->string('project')->nullable()->comment('Dự án/Chương trình');
            $table->enum('status', ['pending', 'approved', 'rejected', 'converted'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('export_order_id')->nullable()->constrained('export_orders')->onDelete('set null')->comment('Phiếu xuất được tạo từ yêu cầu này');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
