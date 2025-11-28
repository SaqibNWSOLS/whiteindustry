<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('total_raw_material_cost', 15, 2)->default(0);
            $table->decimal('total_packaging_cost', 15, 2)->default(0);
            $table->decimal('manufacturing_cost', 15, 2)->default(0);
            $table->decimal('risk_cost', 15, 2)->default(0);
            $table->decimal('total_profit', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'sent_to_rnd', 'rnd_approved', 'qa_approved', 'completed'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotes');
    }
};