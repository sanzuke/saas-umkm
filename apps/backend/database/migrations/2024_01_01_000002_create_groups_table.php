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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('groups')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable(); // Optional: for business unit codes
            $table->enum('type', ['corporate', 'company', 'business_unit'])->default('business_unit');
            $table->tinyInteger('level')->default(0); // 0=corporate, 1=company, 2=business_unit
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'parent_id']);
            $table->index('level');
            $table->index('status');

            // Ensure level constraint based on type
            // Level 0 = Corporate (no parent)
            // Level 1 = Company (parent is Corporate)
            // Level 2 = Business Unit (parent is Company)
        });

        // Add check constraint for max 3 levels (0, 1, 2)
        DB::statement('ALTER TABLE groups ADD CONSTRAINT check_level CHECK (level >= 0 AND level <= 2)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
