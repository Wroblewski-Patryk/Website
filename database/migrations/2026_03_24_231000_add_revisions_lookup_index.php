<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->index(
                ['revisionable_type', 'revisionable_id', 'created_at'],
                'revisions_revisionable_created_at_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->dropIndex('revisions_revisionable_created_at_idx');
        });
    }
};
