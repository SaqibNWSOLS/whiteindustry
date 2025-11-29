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
        Schema::create('qa_quotes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('orders_id')->constrained('orders')->onDelete('cascade');
    $table->foreignId('rnd_quotes_id')->nullable()->constrained('rnd_quotes')->onDelete('cascade');
    $table->timestamp('sent_at')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->text('qa_notes')->nullable();
    $table->enum('status', ['pending', 'in_review', 'approved', 'rejected'])->default('pending');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qa_quotes');
    }
};
