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
        Schema::create('quote_products', function (Blueprint $table) {
            $table->id();
            $table->string('quote_id');
            $table->string('product_name');
            $table->enum('product_type', ['cosmetic', 'food_supplement']);
            $table->decimal('total_raw_material_cost', 15, 2)->default(0);
            $table->decimal('total_packaging_cost', 15, 2)->default(0);
            $table->decimal('manufacturing_cost', 15, 2)->default(0);
            $table->decimal('risk_cost', 15, 2)->default(0);
            $table->decimal('profit_margin', 5, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(19.00);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('final_product_volume', 10, 3)->default(0);
            $table->string('volume_unit')->default('ml');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_products');
    }
};
