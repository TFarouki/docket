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
        Schema::table('matters', function (Blueprint $table) {
            $table->index('reference_number');
        });

        Schema::table('parties', function (Blueprint $table) {
            $table->index('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matters', function (Blueprint $table) {
            $table->dropIndex(['reference_number']);
        });

        Schema::table('parties', function (Blueprint $table) {
            $table->dropIndex(['full_name']);
        });
    }
};
