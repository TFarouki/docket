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
        $tables = [
            'users',
            'matters',
            'parties',
            'documents',
            'court_cases',
            'hearings',
            'tasks',
            'appointments',
            'document_categories'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                        $table->softDeletes();
                    }
                    if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                    }
                    if (!Schema::hasColumn($table->getTable(), 'updated_by')) {
                        $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'matters',
            'parties',
            'documents',
            'court_cases',
            'hearings',
            'tasks',
            'appointments',
            'document_categories'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                    $table->dropConstrainedForeignId('created_by');
                    $table->dropConstrainedForeignId('updated_by');
                });
            }
        }
    }
};
