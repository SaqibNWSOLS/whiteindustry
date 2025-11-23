<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure table exists
        if (!Schema::hasTable('inventory_transactions')) {
            return;
        }

        Schema::table('inventory_transactions', function (Blueprint $table) {

            // Ensure inventory_id exists
            if (!Schema::hasColumn('inventory_transactions', 'inventory_id')) {
                $table->unsignedBigInteger('inventory_id')->nullable();
            } else {
                // Try to adjust the column type if possible
                try {
                    $table->unsignedBigInteger('inventory_id')->nullable()->change();
                } catch (\Exception $e) {
                    // ignore — no doctrine/dbal
                }
            }

            // No foreign keys added
            // No foreign keys removed
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('inventory_transactions')) {
            return;
        }

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Optionally remove column on rollback
            if (Schema::hasColumn('inventory_transactions', 'inventory_id')) {
                try {
                    $table->dropColumn('inventory_id');
                } catch (\Exception $e) {
                    // ignore
                }
            }
        });
    }
};
