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
       Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number')->unique();
    $table->foreignId('production_id')->constrained('productions')->onDelete('cascade');
    $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
    $table->date('invoice_date');
    $table->date('due_date')->nullable();
    $table->decimal('subtotal', 15, 2);
    $table->decimal('tax_amount', 15, 2);
    $table->decimal('total_amount', 15, 2);
    $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('pending_amount', 10, 2)->default(0);
    $table->enum('status', ['draft', 'sent', 'paid', 'overdue'])->default('draft');
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
