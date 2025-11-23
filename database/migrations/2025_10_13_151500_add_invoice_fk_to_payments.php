<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            // Ensure invoice_id exists as unsignedBigInteger
            if (!Schema::hasColumn('payments', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable();
            } else {
                try {
                    $table->unsignedBigInteger('invoice_id')->nullable()->change();
                } catch (\Exception $e) {
                    // ignore if dbal not installed
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            // Optional: remove column on rollback
            try {
                if (Schema::hasColumn('payments', 'invoice_id')) {
                    $table->dropColumn('invoice_id');
                }
            } catch (\Exception $e) {
                // ignore
            }
        });
    }
};
