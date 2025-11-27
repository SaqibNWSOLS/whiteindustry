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
       Schema::create('rnd_quotes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade');
    $table->timestamp('sent_at')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->text('rnd_notes')->nullable();
    $table->enum('status', ['pending', 'in_review', 'approved', 'rejected'])->default('pending');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_quotes');
    }
};
