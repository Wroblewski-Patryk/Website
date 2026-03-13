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
        $tables = ['pages', 'posts', 'projects', 'templates'];

        foreach ($tables as $table) {
            \Illuminate\Support\Facades\DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    $content = $row->content;
                    
                    if (empty($content)) {
                        continue;
                    }

                    // Try to decode content
                    $decoded = json_decode($content, true);

                    // If it's already an array, check if it's already in the translatable format
                    // Spatie translatable usually looks like {"pl": [...], "en": [...]}
                    // We check if it's an associative array where keys are 2-letter lang codes.
                    $isTranslatable = false;
                    if (is_array($decoded) && !empty($decoded)) {
                        $keys = array_keys($decoded);
                        // Simple check: if keys are all strings of length 2 (like 'pl', 'en')
                        $isTranslatable = true;
                        foreach ($keys as $key) {
                            if (!is_string($key) || strlen($key) !== 2) {
                                $isTranslatable = false;
                                break;
                            }
                        }
                    }

                    if (!$isTranslatable) {
                        // Wrap it in the default locale (pl)
                        $newContent = json_encode(['pl' => $decoded]);
                        \Illuminate\Support\Facades\DB::table($table)
                            ->where('id', $row->id)
                            ->update(['content' => $newContent]);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['pages', 'posts', 'projects', 'templates'];

        foreach ($tables as $table) {
            \Illuminate\Support\Facades\DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    $content = $row->content;
                    
                    if (empty($content)) {
                        continue;
                    }

                    $decoded = json_decode($content, true);

                    // If it's translatable, extract the 'pl' content as fallback
                    if (is_array($decoded) && isset($decoded['pl'])) {
                        $newContent = json_encode($decoded['pl']);
                        \Illuminate\Support\Facades\DB::table($table)
                            ->where('id', $row->id)
                            ->update(['content' => $newContent]);
                    }
                }
            });
        }
    }
};
