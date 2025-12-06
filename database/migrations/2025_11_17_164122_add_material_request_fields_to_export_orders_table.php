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
        Schema::table('export_orders', function (Blueprint $table) {
            $table->foreignId('material_request_id')->nullable()->after('confirmed_at')->constrained('material_requests')->onDelete('set null');
            $table->string('department')->nullable()->after('material_request_id')->comment('Phòng ban/Bộ phận');
            $table->string('project')->nullable()->after('department')->comment('Dự án/Chương trình');
        });
        
        // Update reason enum to include more options
        Schema::table('export_orders', function (Blueprint $table) {
            $table->enum('reason', ['sale', 'internal', 'transfer', 'return_supplier', 'return', 'other'])->default('internal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('export_orders', function (Blueprint $table) {
            $table->dropForeign(['material_request_id']);
            $table->dropColumn(['material_request_id', 'department', 'project']);
            $table->enum('reason', ['sale', 'transfer', 'return', 'other'])->default('sale')->change();
        });
    }
};
