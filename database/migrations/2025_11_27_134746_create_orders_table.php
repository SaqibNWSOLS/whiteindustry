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
        Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('customer_id')->nullable();
    $table->string('order_number')->unique();
    $table->foreignId('quote_id')->nullable()->constrained('quotes')->onDelete('cascade');
    $table->foreignId('rnd_quotes_id')->nullable()->constrained('rnd_quotes')->onDelete('cascade');
    $table->date('order_date')->nullable();
    $table->date('delivery_date')->nullable();
    $table->decimal('total_amount', 15, 2)->nullable();
    $table->text('order_notes')->nullable();
    $table->enum('status', ['pending', 'confirmed', 'production', 'completed', 'cancelled'])->default('pending');
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
