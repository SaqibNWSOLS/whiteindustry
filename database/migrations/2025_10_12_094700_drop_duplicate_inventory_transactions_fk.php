<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Defensive: only run if the information_schema is accessible and the database
        // has any foreign key with the problematic name.
        try {
            $rows = DB::select("SELECT TABLE_NAME, CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND CONSTRAINT_NAME = 'inventory_transactions_inventory_id_foreign'");

            foreach ($rows as $row) {
                $table = $row->TABLE_NAME;
                $constraint = $row->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE `" . $table . "` DROP FOREIGN KEY `" . $constraint . "`");
                } catch (\Throwable $e) {
                    // ignore and continue
                }
            }
        } catch (\Throwable $e) {
            // In some environments information_schema access or permissions might be restricted.
            // We intentionally swallow the error because this migration is defensive.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do on rollback — original constraints will be handled by later migrations.
    }
};
