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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['header_override_id']);
            $table->dropForeign(['footer_override_id']);
            $table->dropColumn(['header_override_id', 'footer_override_id']);
        });
    }
};
