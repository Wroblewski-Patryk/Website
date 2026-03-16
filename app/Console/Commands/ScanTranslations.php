<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Translation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ScanTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:scan
                            {--scope=all : all|admin|public}
                            {--overwrite-defaults : Overwrite existing fallback locale text with scanned default}
                            {--report-missing : Show database keys that are missing in scanned files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans source files for translation keys and syncs missing entries to the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scope = strtolower((string) $this->option('scope'));
        if (!in_array($scope, ['all', 'admin', 'public'], true)) {
            $this->error('Invalid --scope. Allowed: all|admin|public');
            return self::FAILURE;
        }

        $this->info("Scanning translation keys (scope: {$scope})...");

        $fallbackLocale = config('app.fallback_locale', config('app.locale', 'en'));
        $overwriteDefaults = (bool) $this->option('overwrite-defaults');
        $reportMissing = (bool) $this->option('report-missing');

        $files = $this->getCandidateFiles();
        $found = [];
        $dynamicHints = [];

        foreach ($files as $file) {
            $path = $file->getPathname();
            $content = file_get_contents($path);

            if ($content === false) {
                continue;
            }

            $extracted = $this->extractKeysFromContent($content, $path);
            foreach ($extracted['static'] as $entry) {
                $fullKey = $entry['key'];
                $default = $entry['default'];

                if (!$this->shouldIncludeKeyForScope($fullKey, $scope)) {
                    continue;
                }

                [$group, $key] = $this->splitGroupAndKey($fullKey, $scope);

                if (!isset($found[$group])) {
                    $found[$group] = [];
                }

                if (!isset($found[$group][$key]) || ($default !== '' && $found[$group][$key] === '')) {
                    $found[$group][$key] = $default;
                }
            }

            foreach ($extracted['dynamic'] as $dynamicKey) {
                if ($scope === 'admin' && !Str::startsWith($dynamicKey, 'admin.')) {
                    continue;
                }
                if ($scope === 'public' && Str::startsWith($dynamicKey, 'admin.')) {
                    continue;
                }
                $dynamicHints[$dynamicKey] = true;
            }
        }

        $created = 0;
        $updated = 0;

        foreach ($found as $group => $keys) {
            foreach ($keys as $key => $default) {
                /** @var Translation|null $translation */
                $translation = Translation::where('group', $group)->where('key', $key)->first();

                if (!$translation) {
                    $translation = new Translation();
                    $translation->group = $group;
                    $translation->key = $key;
                    $translation->setTranslation('text', $fallbackLocale, $default !== '' ? $default : $key);
                    $translation->save();
                    $created++;
                    continue;
                }

                if ($default === '') {
                    continue;
                }

                $currentFallback = (string) ($translation->getTranslation('text', $fallbackLocale, false) ?? '');
                $shouldUpdate = $overwriteDefaults
                    ? $currentFallback !== $default
                    : ($currentFallback === '' || $currentFallback === $key);

                if ($shouldUpdate) {
                    $translation->setTranslation('text', $fallbackLocale, $default);
                    $translation->save();
                    $updated++;
                }
            }
        }

        $staticCount = collect($found)->sum(fn ($keys) => count($keys));
        $this->info("Scan complete. Found {$staticCount} static keys.");
        $this->info("Created: {$created}, updated fallback defaults ({$fallbackLocale}): {$updated}.");

        if (!empty($dynamicHints)) {
            $this->warn('Detected dynamic keys (not auto-imported):');
            foreach (array_slice(array_keys($dynamicHints), 0, 20) as $dynamic) {
                $this->line(" - {$dynamic}");
            }
            if (count($dynamicHints) > 20) {
                $this->line(' - ...');
            }
        }

        if ($reportMissing) {
            $this->reportMissingKeys($found, $scope);
        }

        return self::SUCCESS;
    }

    private function getCandidateFiles(): array
    {
        $roots = [
            resource_path('js'),
            resource_path('views'),
        ];

        $allowed = ['vue', 'js', 'ts', 'php', 'blade.php'];
        $files = [];

        foreach ($roots as $root) {
            if (!File::exists($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                $filename = $file->getFilename();
                $ext = $file->getExtension();
                $isBlade = Str::endsWith($filename, '.blade.php');

                if (in_array($ext, $allowed, true) || $isBlade) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    private function extractKeysFromContent(string $content, string $path): array
    {
        $static = [];
        $dynamic = [];

        // t('group.key', 'Default') + t("...") + t(`...`) without interpolation
        $patternT = '/\bt\(\s*([\'"`])([^\'"`]+?)\1(?:\s*,\s*([\'"`])((?:(?!\3).)*)\3)?\s*(?:,|\))/s';
        if (preg_match_all($patternT, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $key = trim($match[2]);
                $default = isset($match[4]) ? trim($match[4]) : '';

                if (Str::contains($key, '${')) {
                    $dynamic[] = $key;
                    continue;
                }

                if (!$this->isValidStaticKey($key)) {
                    continue;
                }

                if (Str::contains($default, '${')) {
                    $default = '';
                }

                $static[] = ['key' => $key, 'default' => $default];
            }
        }

        // Detect obvious dynamic concatenations: t('admin.key_' + var, ...)
        $patternConcat = '/\bt\(\s*[\'"`]([^\'"`]+)[\'"`]\s*\+/';
        if (preg_match_all($patternConcat, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $dynamic[] = trim($match[1]);
            }
        }

        // __('group.key') / trans('group.key') from php/blade
        $patternPhp = '/\b(?:__|trans)\(\s*[\'"]([a-zA-Z0-9_\-.]+)[\'"]\s*(?:,\s*[^\)]*)?\)/';
        if (preg_match_all($patternPhp, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $key = trim($match[1]);
                if ($this->isValidStaticKey($key)) {
                    $static[] = ['key' => $key, 'default' => ''];
                }
            }
        }

        return [
            'static' => $static,
            'dynamic' => array_values(array_unique($dynamic)),
        ];
    }

    private function isValidStaticKey(string $key): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_\-.]+$/', $key);
    }

    private function shouldIncludeKeyForScope(string $fullKey, string $scope): bool
    {
        if ($scope === 'all') {
            return true;
        }

        if ($scope === 'admin') {
            return Str::startsWith($fullKey, 'admin.');
        }

        return !Str::startsWith($fullKey, 'admin.');
    }

    private function splitGroupAndKey(string $fullKey, string $scope): array
    {
        $parts = explode('.', $fullKey, 2);

        if (count($parts) < 2) {
            $group = $scope === 'admin' ? 'admin' : 'frontend';
            return [$group, $fullKey];
        }

        $group = $parts[0];
        $key = $parts[1];

        // If it starts with admin. prefix, we treat it as group 'admin'
        // and strip the first prefix to avoid duplicates like admin.admin...
        if ($group === 'admin') {
            if (Str::startsWith($key, 'admin.')) {
                $key = substr($key, 6);
            }
        }

        return [$group, $key];
    }

    private function reportMissingKeys(array $found, string $scope): void
    {
        $query = Translation::query();
        if ($scope === 'admin') {
            $query->where('group', 'admin');
        }
        if ($scope === 'public') {
            $query->where('group', '!=', 'admin');
        }

        $dbKeys = $query->get(['group', 'key'])->map(function ($row) {
            return "{$row->group}.{$row->key}";
        })->all();

        $foundFlat = [];
        foreach ($found as $group => $keys) {
            foreach (array_keys($keys) as $key) {
                $foundFlat[] = "{$group}.{$key}";
            }
        }

        $missing = array_values(array_diff($dbKeys, $foundFlat));
        if (empty($missing)) {
            $this->info('No missing database keys detected for this scope.');
            return;
        }

        $this->warn('Keys present in DB but missing in scanned files:');
        foreach (array_slice($missing, 0, 50) as $key) {
            $this->line(" - {$key}");
        }
        if (count($missing) > 50) {
            $this->line(' - ...');
        }
    }
}
