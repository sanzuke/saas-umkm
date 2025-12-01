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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->string('name'); // e.g., "Admin", "Manager", "Staff"
            $table->string('slug'); // e.g., "admin", "manager", "staff"
            $table->text('description')->nullable();
            $table->boolean('is_inheritable')->default(false); // Can corporate/company roles be inherited by children?
            $table->boolean('is_system')->default(false); // System roles cannot be deleted
            $table->integer('priority')->default(0); // Higher priority = more permissions
            $table->timestamps();

            // Unique constraint: role name must be unique within a group
            $table->unique(['group_id', 'slug']);

            // Indexes
            $table->index('group_id');
            $table->index('slug');
            $table->index('is_inheritable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
