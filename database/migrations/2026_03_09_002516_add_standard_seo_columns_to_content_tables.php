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
        $tables = ['pages', 'posts', 'templates'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'meta_title')) {
                    $table->json('meta_title')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'meta_description')) {
                    $table->json('meta_description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'og_image')) {
                    $table->json('og_image')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'canonical_url')) {
                    $table->string('canonical_url')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'seo_index')) {
                    $table->boolean('seo_index')->default(true);
                }
                if (!Schema::hasColumn($tableName, 'seo_follow')) {
                    $table->boolean('seo_follow')->default(true);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['pages', 'posts', 'templates'];
        $columns = ['meta_title', 'meta_description', 'og_image', 'canonical_url', 'seo_index', 'seo_follow'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        Schema::table($tableName, function (Blueprint $table) use ($column) {
                            $table->dropColumn($column);
                        });
                    }
                }
            }
        }
    }
};
