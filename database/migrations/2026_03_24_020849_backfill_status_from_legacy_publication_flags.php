<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['pages', 'posts', 'projects', 'forms'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'status')) {
                continue;
            }

            if (Schema::hasColumn($table, 'is_published')) {
                DB::table($table)->where('is_published', true)->update(['status' => 'published']);

                DB::table($table)
                    ->where('is_published', false)
                    ->where(function ($query) {
                        $query->whereNull('status')->orWhere('status', '');
                    })
                    ->update(['status' => 'draft']);
            }

            if (Schema::hasColumn($table, 'published_at')) {
                DB::table($table)
                    ->whereNotNull('published_at')
                    ->where('status', 'draft')
                    ->update(['status' => 'published']);
            }

            if (Schema::hasColumn($table, 'archived_at')) {
                DB::table($table)
                    ->whereNotNull('archived_at')
                    ->update(['status' => 'archived']);
            }
        }
    }

    public function down(): void
    {
        // No-op: data backfill is intentionally irreversible.
    }
};
