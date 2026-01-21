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
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type')->default('general'); // To distinguish between user docs, matter docs, etc if needed
            $table->timestamps();
        });

        // Add foreign key to documents
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('document_category_id')->nullable()->after('category')->constrained('document_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('document_category_id');
        });
        Schema::dropIfExists('document_categories');
    }
};
