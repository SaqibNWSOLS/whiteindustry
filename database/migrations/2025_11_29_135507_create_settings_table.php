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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, text, boolean, integer, json
            $table->string('group')->default('general'); // general, company, email, system, etc.
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // whether this setting is publicly accessible
            $table->timestamps();
            
            // Index for better performance
            $table->index(['key']);
            $table->index(['group']);
        });

        // Insert default settings
        $this->insertDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

    /**
     * Insert default settings
     */
    private function insertDefaultSettings(): void
    {
        $defaultSettings = [
            // Company Settings
            [
                'key' => 'company_name',
                'value' => 'White Industry',
                'type' => 'string',
                'group' => 'company',
                'description' => 'The name of the company',
                'is_public' => true,
            ],
            [
                'key' => 'tax_id',
                'value' => 'DZ-123456789',
                'type' => 'string',
                'group' => 'company',
                'description' => 'Company tax identification number',
                'is_public' => false,
            ],
            [
                'key' => 'phone',
                'value' => '+213 21 123 456',
                'type' => 'string',
                'group' => 'company',
                'description' => 'Company phone number',
                'is_public' => true,
            ],
            [
                'key' => 'company_address',
                'value' => '',
                'type' => 'text',
                'group' => 'company',
                'description' => 'Company physical address',
                'is_public' => true,
            ],
            [
                'key' => 'email_signature',
                'value' => '',
                'type' => 'text',
                'group' => 'company',
                'description' => 'Default email signature',
                'is_public' => false,
            ],

            // System Settings
            [
                'key' => 'default_currency',
                'value' => 'DZD',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Default currency for the system',
                'is_public' => true,
            ],
            [
                'key' => 'timezone',
                'value' => 'Africa/Algiers',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Default timezone',
                'is_public' => false,
            ],
            [
                'key' => 'date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Default date format',
                'is_public' => false,
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Default time format',
                'is_public' => false,
            ],

            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'White Industry',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Default sender name for emails',
                'is_public' => false,
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@whiteindustry.com',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Default sender email address',
                'is_public' => false,
            ],

            // Application Settings
            [
                'key' => 'items_per_page',
                'value' => '25',
                'type' => 'integer',
                'group' => 'application',
                'description' => 'Number of items per page in listings',
                'is_public' => false,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'application',
                'description' => 'Whether the application is in maintenance mode',
                'is_public' => false,
            ],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
};