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
        Schema::create('court_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matter_id')->constrained()->cascadeOnDelete();
            $table->string('court_name'); // e.g., "المحكمة الابتدائية بالرباط"
            $table->string('case_number'); // e.g., "123/4567/2025"
            $table->string('judge_name')->nullable();
            $table->string('opponent_name')->nullable();
            $table->string('opponent_lawyer')->nullable();
            $table->string('current_stage')->nullable(); // e.g., "Mise en délibéré", "Expertise"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_cases');
    }
};
