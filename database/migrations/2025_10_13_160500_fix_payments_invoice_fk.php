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
        if (!Schema::hasTable('payments') || !Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            // Drop any existing foreign key on invoice_id (safe attempt)
            try {
                $table->dropForeign(['invoice_id']);
            } catch (\Exception $e) {
                // ignore if not exists
            }

            // Ensure invoice_id is unsignedBigInteger
            try {
                if (!Schema::hasColumn('payments', 'invoice_id')) {
                    $table->unsignedBigInteger('invoice_id')->nullable();
                } else {
                    try { $table->unsignedBigInteger('invoice_id')->change(); } catch (\Exception $e) {}
                }
            } catch (\Exception $e) {
                // ignore
            }

            // Add new FK to invoices
            try {
                $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            } catch (\Exception $e) {
                // ignore FK add failures
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('payments')) return;
        Schema::table('payments', function (Blueprint $table) {
            try { $table->dropForeign(['invoice_id']); } catch (\Exception $e) {}
        });
    }
};
