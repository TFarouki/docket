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
        Schema::table('documents', function (Blueprint $table) {
            // Drop old column
            $table->dropConstrainedForeignId('matter_id');
            
            // Add polymorphic columns
            $table->morphs('documentable'); // documentable_id and documentable_type
            
            // Add category/type field
            $table->string('category')->nullable()->after('title'); // ID, Contract, Training, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropMorphs('documentable');
            $table->dropColumn('category');
            $table->foreignId('matter_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }
};
