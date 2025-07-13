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
        Schema::create('appointment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->boolean('is_printed')->default(false);
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('accountant_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_submissions');
    }
};