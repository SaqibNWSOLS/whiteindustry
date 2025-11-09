<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('inventory_transactions')) {
            return;
        }

        // Find any existing foreign key constraints on inventory_id and drop them.
        $rows = DB::select("SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'inventory_transactions'
              AND COLUMN_NAME = 'inventory_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL");

        foreach ($rows as $row) {
            $name = $row->CONSTRAINT_NAME;
            try {
                DB::statement("ALTER TABLE `inventory_transactions` DROP FOREIGN KEY `" . $name . "`");
            } catch (\Throwable $e) {
                // ignore and continue
            }
        }

        // Add a uniquely named foreign key to avoid name collisions
        $fkName = 'fk_it_inventory_id';

        // Drop if a constraint with the target name already exists (defensive)
        try {
            DB::statement("ALTER TABLE `inventory_transactions` DROP FOREIGN KEY `" . $fkName . "`");
        } catch (\Throwable $e) {
            // ignore
        }

        // Finally add the foreign key pointing to the singular inventory table
        DB::statement("ALTER TABLE `inventory_transactions`
            ADD CONSTRAINT `" . $fkName . "`
            FOREIGN KEY (`inventory_id`) REFERENCES `inventory`(`id`) ON DELETE CASCADE");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('inventory_transactions')) {
            return;
        }

        $fkName = 'fk_it_inventory_id';

        try {
            DB::statement("ALTER TABLE `inventory_transactions` DROP FOREIGN KEY `" . $fkName . "`");
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
