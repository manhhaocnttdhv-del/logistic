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
        Schema::table('import_orders', function (Blueprint $table) {
            $table->enum('source', ['supplier', 'transfer', 'return', 'other'])->default('supplier')->after('supplier_id')->comment('Nguồn nhập: NCC, điều chuyển, trả hàng');
            $table->string('from_warehouse')->nullable()->after('source')->comment('Kho điều chuyển từ (nếu source = transfer)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_orders', function (Blueprint $table) {
            $table->dropColumn(['source', 'from_warehouse']);
        });
    }
};
