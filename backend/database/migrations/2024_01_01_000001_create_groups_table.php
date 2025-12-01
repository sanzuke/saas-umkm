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
            $table->string('name');
            $table->string('code')->unique(); // Unique identifier for the group
            $table->enum('type', ['corporate', 'company', 'business_unit'])->default('business_unit');
            $table->integer('level')->default(2); // 0=corporate, 1=company, 2=business_unit
            $table->foreignId('parent_id')->nullable()->constrained('groups')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('groups')->onDelete('cascade'); // Root corporate as tenant
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Additional settings per group
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['parent_id', 'level']);
            $table->index('tenant_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
