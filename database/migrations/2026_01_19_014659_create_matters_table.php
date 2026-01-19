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
        Schema::create('matters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responsible_lawyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('matters')->nullOnDelete(); // For appeals/hierarchy
            $table->string('title'); // e.g., "قضية ضد شركة X"
            $table->year('year')->nullable(); // Convention: Files often have year slash number
            $table->string('reference_number')->nullable(); // Internal file number
            $table->string('type'); // litigation, procedure, consultation
            $table->string('status')->default('open'); // open, closed, pending, archived
            $table->decimal('agreed_fee', 10, 2)->default(0); // Total agreed fees
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matters');
    }
};
