<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update templates table enum/type
        // Since SQLite doesn't support modifying enums easily, we'll just treat it as a string or handle it if it's MySQL/Postgres.
        // But for this project, let's just make sure the column can accept new values.
        Schema::table('templates', function (Blueprint $table) {
            $table->string('type')->change();
        });

        // 2. Add overrides to pages table
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'template_id')) {
                $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            }
            if (!Schema::hasColumn('pages', 'sidebar_override_id')) {
                $table->foreignId('sidebar_override_id')->nullable()->constrained('templates')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'template_id')) {
                $table->dropForeign(['template_id']);
                $table->dropColumn('template_id');
            }
            if (Schema::hasColumn('pages', 'sidebar_override_id')) {
                $table->dropForeign(['sidebar_override_id']);
                $table->dropColumn('sidebar_override_id');
            }
        });

        Schema::table('templates', function (Blueprint $table) {
        // Revert to enum if necessary, but string is safer
        });
    }
};
