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
        Schema::create('productions', function (Blueprint $table) {
    $table->id();
    $table->string('production_number')->unique();
    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->text('production_notes')->nullable();
    $table->enum('status', ['pending', 'in_progress', 'completed', 'quality_check'])->default('pending');
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
