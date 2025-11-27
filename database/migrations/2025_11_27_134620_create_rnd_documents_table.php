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
        Schema::create('rnd_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('rnd_quotes_id')->constrained('rnd_quotes')->onDelete('cascade');
    $table->string('document_name');
    $table->string('file_path');
    $table->string('file_type'); // pdf, doc, etc
    $table->unsignedBigInteger('file_size');
    $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_documents');
    }
};
