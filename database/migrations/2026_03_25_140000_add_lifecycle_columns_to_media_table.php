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
        Schema::table('media', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('duplicate_of_id');
            $table->timestamp('retention_until')->nullable()->after('archived_at');
            $table->timestamp('purge_after')->nullable()->after('retention_until');

            $table->index('archived_at');
            $table->index('retention_until');
            $table->index('purge_after');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['archived_at']);
            $table->dropIndex(['retention_until']);
            $table->dropIndex(['purge_after']);

            $table->dropColumn(['archived_at', 'retention_until', 'purge_after']);
        });
    }
};
