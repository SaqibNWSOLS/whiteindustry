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
        Schema::table('inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory', 'composition')) {
                $table->json('composition')->nullable()->after('unit_cost');
            }
            if (!Schema::hasColumn('inventory', 'commission_percent')) {
                $table->decimal('commission_percent', 5, 2)->default(0)->after('composition');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            if (Schema::hasColumn('inventory', 'commission_percent')) {
                $table->dropColumn('commission_percent');
            }
            if (Schema::hasColumn('inventory', 'composition')) {
                $table->dropColumn('composition');
            }
        });
    }
};
