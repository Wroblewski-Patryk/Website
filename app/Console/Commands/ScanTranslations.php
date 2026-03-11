<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Translation;
use Illuminate\Support\Facades\File;

class ScanTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans Vue and Blade files for t() function calls and adds missing keys to the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for translation keys...');

        $directories = [
            resource_path('js'),
            resource_path('views'),
        ];

        $pattern = '/\bt\([\'"]([a-zA-Z0-9_\-\.]+)[\'"](?:,\s*[\'"](.*?)[\'"])?\)/';
        $foundTranslations = [];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                continue;
            }

            $files = File::allFiles($directory);

            foreach ($files as $file) {
                if (!in_array($file->getExtension(), ['vue', 'js', 'php'])) {
                    continue;
                }

                $content = file_get_contents($file->getPathname());
                
                if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $fullKey = $match[1];
                        $defaultValue = $match[2] ?? '';

                        $parts = explode('.', $fullKey, 2);
                        if (count($parts) < 2) {
                            $group = 'frontend';
                            $key = $fullKey;
                        } else {
                            $group = $parts[0];
                            $key = $parts[1];
                        }

                        if (!isset($foundTranslations[$group])) {
                            $foundTranslations[$group] = [];
                        }

                        // Store the default value if it represents the first time we see this key or if we have a better default
                        if (!isset($foundTranslations[$group][$key]) || ($defaultValue !== '' && $foundTranslations[$group][$key] === '')) {
                            $foundTranslations[$group][$key] = $defaultValue;
                        }
                    }
                }
            }
        }

        $addedCount = 0;

        foreach ($foundTranslations as $group => $keys) {
            foreach ($keys as $key => $defaultValue) {
                $exists = Translation::where('group', $group)->where('key', $key)->exists();

                if (!$exists) {
                    $translation = new Translation();
                    $translation->group = $group;
                    $translation->key = $key;
                    
                    // Set default text for English as fallback base
                    $translation->setTranslation('text', 'en', $defaultValue ?: $key);
                    
                    $translation->save();
                    
                    $this->line("Added missing translation: <comment>{$group}.{$key}</comment> -> '{$defaultValue}'");
                    $addedCount++;
                }
            }
        }

        $this->info("Scan complete. Added {$addedCount} new translation keys.");
    }
}
