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
        // Make sure the table exists before making changes
        if (!Schema::hasTable('inventory_transactions')) {
            return;
        }

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // If a foreign key with an unexpected name exists, try to drop it safely.
            // We can't reliably know the constraint name across environments, so
            // attempt to drop any foreign key on inventory_id if present.
            try {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $doctrineTable = $sm->listTableDetails('inventory_transactions');

                if ($doctrineTable->hasForeignKey('inventory_transactions_inventory_id_foreign')) {
                    $table->dropForeign('inventory_transactions_inventory_id_foreign');
                }
            } catch (Exception $e) {
                // ignore — Doctrine may not be available or permission issues; continue
            }

            // Ensure the column is unsignedBigInteger to match $table->id()
            if (!Schema::hasColumn('inventory_transactions', 'inventory_id')) {
                $table->unsignedBigInteger('inventory_id');
            } else {
                // attempt to alter column type if needed (works on some DB drivers)
                // Note: altering column types requires doctrine/dbal for Laravel
                // When doctrine/dbal is not installed, this step will be skipped.
                try {
                    $table->unsignedBigInteger('inventory_id')->change();
                } catch (Exception $e) {
                    // ignore — developer can run composer require doctrine/dbal if needed
                }
            }

            // Add index if missing
            if (!Schema::hasColumn('inventory_transactions', 'inventory_id') || !Schema::hasColumn('inventory', 'id')) {
                return;
            }

            // Add foreign key constraint to the singular inventory table
            $table->foreign('inventory_id')->references('id')->on('inventory')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('inventory_transactions')) {
            return;
        }

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // drop the FK if it exists
            try {
                $table->dropForeign(['inventory_id']);
            } catch (Exception $e) {
                // ignore
            }
        });
    }
};
