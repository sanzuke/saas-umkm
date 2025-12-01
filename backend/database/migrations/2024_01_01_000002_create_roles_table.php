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
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->boolean('is_inheritable')->default(false); // Can be inherited by child groups
            $table->boolean('is_system')->default(false); // System role, cannot be deleted
            $table->timestamps();

            // Indexes
            $table->index('group_id');
            $table->unique(['slug', 'group_id']);
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
