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
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->foreignId('header_override_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->foreignId('footer_override_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->foreignId('sidebar_override_id')->nullable()->constrained('templates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropForeign(['header_override_id']);
            $table->dropForeign(['footer_override_id']);
            $table->dropForeign(['sidebar_override_id']);
            $table->dropColumn(['template_id', 'header_override_id', 'footer_override_id', 'sidebar_override_id']);
        });

        Schema::table('templates', function (Blueprint $table) {
        // Revert to enum if necessary, but string is safer
        });
    }
};
