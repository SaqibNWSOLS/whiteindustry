<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter enum to include 'blend'. Use raw statement to avoid Doctrine dependency.
        // This assumes MySQL. If you're using a different DB, adapt accordingly.
        DB::statement("ALTER TABLE `inventory` MODIFY `type` ENUM('raw_material','packaging','final_product','blend') NOT NULL DEFAULT 'raw_material'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // revert enum to previous values (remove 'blend')
        DB::statement("ALTER TABLE `inventory` MODIFY `type` ENUM('raw_material','packaging','final_product') NOT NULL DEFAULT 'raw_material'");
    }
};
