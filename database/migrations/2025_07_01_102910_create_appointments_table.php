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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number')->unique();
            $table->enum('status', ['pending', 'finished', 'cancelled','missed'])->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('submited')->default(false);
            $table->foreignId('rescptionist_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('visit_type_id')->constrained('visit_types')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};