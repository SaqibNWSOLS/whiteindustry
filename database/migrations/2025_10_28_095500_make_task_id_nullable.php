<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make tasks.task_id nullable so the model can populate it after insert.
     *
     * @return void
     */
    public function up()
    {
        // Using raw SQL to avoid requiring doctrine/dbal for column modification
        DB::statement("ALTER TABLE `tasks` MODIFY `task_id` VARCHAR(255) NULL;");
    }

    /**
     * Reverse the migrations.
     * Make task_id NOT NULL (no default) — only do this if you know all rows have values.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `tasks` MODIFY `task_id` VARCHAR(255) NOT NULL;");
    }
};
