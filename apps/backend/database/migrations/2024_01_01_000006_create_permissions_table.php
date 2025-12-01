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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Create Invoice", "View Reports"
            $table->string('slug'); // e.g., "pos.invoice.create", "reports.view"
            $table->string('module'); // e.g., "pos", "inventory", "workshop", "garment"
            $table->string('action'); // e.g., "create", "read", "update", "delete"
            $table->text('description')->nullable();
            $table->timestamps();

            // Unique constraint: slug must be globally unique
            $table->unique('slug');

            // Indexes
            $table->index('module');
            $table->index('action');
            $table->index(['module', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
