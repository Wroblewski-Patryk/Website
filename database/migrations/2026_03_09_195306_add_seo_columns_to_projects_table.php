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
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'meta_title')) {
                $table->json('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('projects', 'meta_description')) {
                $table->json('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('projects', 'og_image')) {
                $table->json('og_image')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('projects', 'canonical_url')) {
                $table->string('canonical_url')->nullable()->after('og_image');
            }
            if (!Schema::hasColumn('projects', 'seo_index')) {
                $table->boolean('seo_index')->default(true)->after('canonical_url');
            }
            if (!Schema::hasColumn('projects', 'seo_follow')) {
                $table->boolean('seo_follow')->default(true)->after('seo_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $columns = ['meta_title', 'meta_description', 'og_image', 'canonical_url', 'seo_index', 'seo_follow'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
