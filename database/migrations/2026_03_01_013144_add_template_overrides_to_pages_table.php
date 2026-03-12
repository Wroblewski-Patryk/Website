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
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('header_override_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->foreignId('footer_override_id')->nullable()->constrained('templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'header_override_id')) {
                $table->dropForeign(['header_override_id']);
                $table->dropColumn('header_override_id');
            }
            if (Schema::hasColumn('pages', 'footer_override_id')) {
                $table->dropForeign(['footer_override_id']);
                $table->dropColumn('footer_override_id');
            }
        });
    }
};
