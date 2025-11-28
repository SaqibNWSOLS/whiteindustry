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
        Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_products_id')->constrained('order_products')->onDelete('cascade');
     $table->string('quote_product_id')->nullable();     
     $table->string('quote_item_id')->nullable();     
     
            $table->string('item_type')->nullable(); // 'raw_material' or 'packaging'
            $table->foreignId('item_id')->nullable(); // polymorphic relation
            $table->string('item_name');
            $table->decimal('quantity', 10, 3)->default(0);
            $table->string('unit');
            $table->decimal('percentage', 5, 2)->default(0); // for raw materials
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
