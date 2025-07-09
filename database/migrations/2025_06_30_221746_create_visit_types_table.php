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
        Schema::create('visit_types', function (Blueprint $table) {
            $table->id();
            $table->string('service_type');
            $table->decimal('price', 8, 2);
            $table->enum('doctor_fee_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('doctor_fee_value', 8, 2);
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_types');
    }
};
