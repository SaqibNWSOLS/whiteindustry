// database/migrations/2024_01_XX_create_customers_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->nullable();
            $table->enum('type', ['lead', 'customer'])->default('lead'); // Distinguish between leads and customers
            $table->string('company_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->unique();
            $table->string('phone', 50);
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('industry_type')->nullable();
            $table->string('tax_id', 50)->nullable();
            
            // Lead-specific fields
            $table->enum('source', ['website', 'referral', 'trade_show', 'cold_call', 'social_media', 'other'])->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'proposal', 'lost', 'active', 'inactive'])->default('new');
            $table->decimal('estimated_value', 12, 2)->nullable(); // For leads
            $table->longText('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->index('type');
            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};