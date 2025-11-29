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
        Schema::create('production_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('products_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('production_id')->constrained('productions')->onDelete('cascade');
            $table->foreignId('order_product_id')->constrained('order_products')->onDelete('cascade');
            $table->integer('quantity_planned')->default(0);
            $table->integer('quantity_produced')->default(0);
            $table->integer('quantity_deliverd')->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'quality_check'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_items');
    }
};
