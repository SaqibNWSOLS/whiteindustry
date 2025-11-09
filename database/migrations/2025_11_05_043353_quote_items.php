<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->string('quote_product_id');
            $table->string('item_type'); // 'raw_material' or 'packaging'
            $table->foreignId('item_id'); // polymorphic relation
            $table->string('item_name');
            $table->decimal('quantity', 10, 3)->default(0);
            $table->string('unit');
            $table->decimal('percentage', 5, 2)->default(0); // for raw materials
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_items');
    }
};