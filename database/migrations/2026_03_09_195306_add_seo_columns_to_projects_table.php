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
            $table->json('meta_title')->nullable()->after('description');
            $table->json('meta_description')->nullable()->after('meta_title');
            $table->json('og_image')->nullable()->after('meta_description');
            $table->string('canonical_url')->nullable()->after('og_image');
            $table->boolean('seo_index')->default(true)->after('canonical_url');
            $table->boolean('seo_follow')->default(true)->after('seo_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'og_image', 'canonical_url', 'seo_index', 'seo_follow']);
        });
    }
};
