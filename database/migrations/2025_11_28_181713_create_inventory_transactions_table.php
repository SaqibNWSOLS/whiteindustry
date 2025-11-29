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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('production_item_id')->nullable()->constrained('production_items')->onDelete('cascade');
            $table->enum('transaction_type', [
                'production',      // Goods produced
                'sale',           // Goods sold
                'adjustment',     // Manual inventory adjustment
                'damage',         // Defective/damaged items
                'return',         // Customer returns
                'transfer'        // Transfer between locations
            ])->default('production');
            $table->integer('quantity_change');
            $table->string('reference_type')->nullable();  // 'production_item', 'order', 'invoice', etc.
            $table->bigInteger('reference_id')->nullable();
            $table->string('status')->nullable();  // pending, in_progress, completed, quality_check
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('product_id');
            $table->index('production_item_id');
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index('created_by');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
