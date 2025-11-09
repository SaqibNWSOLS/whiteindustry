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
            // Ensure invoice_id exists and is unsignedBigInteger
            if (!Schema::hasColumn('payments', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id');
            } else {
                try {
                    $table->unsignedBigInteger('invoice_id')->change();
                } catch (Exception $e) {
                    // ignore if change() requires doctrine/dbal
                }
            }

            // Add FK constraint to invoices
            // Drop any existing FK on invoice_id first (safe attempt)
            try {
                $table->dropForeign(['invoice_id']);
            } catch (Exception $e) {
                // no-op
            }

            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            try {
                $table->dropForeign(['invoice_id']);
            } catch (Exception $e) {
                // ignore
            }
        });
    }
};
