<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class InstallController extends Controller
{
    public function index(): View|Factory
    {
        $availableLocales = ['en', 'pl'];

        return view('install', [
            'title' => 'Application Installation',
            'headline' => 'Welcome to Featherly Installer',
            'description' => 'The application is not initialized yet. Continue with installation steps to configure database and default language.',
            'database' => session('installer.database', [
                'connection' => 'sqlite',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => '',
                'username' => '',
                'sqlite_path' => database_path('database.sqlite'),
            ]),
            'databaseValidated' => (bool) session('installer.database_validated', false),
            'availableLocales' => $availableLocales,
            'selectedLocale' => session('installer.language', app()->getLocale()),
            'languageSelected' => (bool) session('installer.language_selected', false),
        ]);
    }

    public function validateDatabase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'connection' => 'required|in:sqlite,mysql,pgsql',
            'host' => 'required_if:connection,mysql,pgsql|string|max:255',
            'port' => 'required_if:connection,mysql,pgsql|numeric',
            'database' => 'required_if:connection,mysql,pgsql|string|max:255',
            'username' => 'required_if:connection,mysql,pgsql|string|max:255',
            'password' => 'nullable|string|max:255',
            'sqlite_path' => 'required_if:connection,sqlite|string|max:1024',
        ]);

        if (!$this->canConnect($validated, $errorMessage)) {
            return back()
                ->withInput()
                ->withErrors(['database' => $errorMessage]);
        }

        session([
            'installer.database' => [
                'connection' => $validated['connection'],
                'host' => $validated['host'] ?? null,
                'port' => $validated['port'] ?? null,
                'database' => $validated['database'] ?? null,
                'username' => $validated['username'] ?? null,
                'password' => $validated['password'] ?? null,
                'sqlite_path' => $validated['sqlite_path'] ?? null,
            ],
            'installer.database_validated' => true,
        ]);

        return redirect()
            ->route('install.index', ['locale' => app()->getLocale()])
            ->with('success', 'Database connection validated.');
    }

    public function storeLanguage(Request $request): RedirectResponse
    {
        if (!(bool) session('installer.database_validated', false)) {
            return redirect()
                ->route('install.index', ['locale' => app()->getLocale()])
                ->withErrors(['language' => 'Validate database connection first.']);
        }

        $validated = $request->validate([
            'language' => 'required|in:en,pl',
        ]);

        session([
            'installer.language' => $validated['language'],
            'installer.language_selected' => true,
        ]);

        return redirect()
            ->route('install.index', ['locale' => app()->getLocale()])
            ->with('success', 'Default language saved.');
    }

    public function finalize(Request $request): RedirectResponse
    {
        if (!(bool) session('installer.database_validated', false)) {
            return redirect()
                ->route('install.index', ['locale' => app()->getLocale()])
                ->withErrors(['finalize' => 'Validate database connection first.']);
        }

        if (!(bool) session('installer.language_selected', false)) {
            return redirect()
                ->route('install.index', ['locale' => app()->getLocale()])
                ->withErrors(['finalize' => 'Select default language first.']);
        }

        /** @var array<string, mixed> $dbConfig */
        $dbConfig = session('installer.database', []);
        $language = (string) session('installer.language', 'en');

        if ($dbConfig === []) {
            return redirect()
                ->route('install.index', ['locale' => app()->getLocale()])
                ->withErrors(['finalize' => 'Installer database configuration is missing.']);
        }

        if (!$this->persistEnvironment($dbConfig, $language, $errorMessage)) {
            return redirect()
                ->route('install.index', ['locale' => app()->getLocale()])
                ->withErrors(['finalize' => $errorMessage]);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            $this->applyDefaultLanguage($language);
            $this->writeInstallerLock();
        } catch (Throwable $exception) {
            return redirect()
                ->route('install.index', ['locale' => app()->getLocale()])
                ->withErrors(['finalize' => 'Installation finalize failed: ' . $exception->getMessage()]);
        }

        $request->session()->forget([
            'installer.database',
            'installer.database_validated',
            'installer.language',
            'installer.language_selected',
        ]);

        return redirect()
            ->route('auth.login', ['locale' => app()->getLocale()])
            ->with('success', 'Installation completed successfully.');
    }

    private function canConnect(array $validated, ?string &$errorMessage = null): bool
    {
        $connectionName = 'installer_probe';
        $errorMessage = null;

        $connectionConfig = $this->buildConnectionConfig($validated);
        if ($connectionConfig === []) {
            $errorMessage = 'Unsupported database driver.';
            return false;
        }

        try {
            Config::set("database.connections.{$connectionName}", $connectionConfig);
            DB::purge($connectionName);
            DB::connection($connectionName)->getPdo();
            DB::disconnect($connectionName);
            return true;
        } catch (Throwable $exception) {
            $errorMessage = 'Could not connect to the database. ' . $exception->getMessage();
            return false;
        } finally {
            DB::purge($connectionName);
            Config::set("database.connections.{$connectionName}", null);
        }
    }

    private function buildConnectionConfig(array $validated): array
    {
        return match ($validated['connection']) {
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => $validated['sqlite_path'],
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'mysql' => [
                'driver' => 'mysql',
                'host' => $validated['host'],
                'port' => (string) $validated['port'],
                'database' => $validated['database'],
                'username' => $validated['username'],
                'password' => $validated['password'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
            ],
            'pgsql' => [
                'driver' => 'pgsql',
                'host' => $validated['host'],
                'port' => (string) $validated['port'],
                'database' => $validated['database'],
                'username' => $validated['username'],
                'password' => $validated['password'] ?? '',
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ],
            default => [],
        };
    }

    private function persistEnvironment(array $dbConfig, string $language, ?string &$errorMessage = null): bool
    {
        $errorMessage = null;
        $envPath = (string) config('installer.env_file', base_path('.env'));

        if (!File::exists($envPath) || !File::isWritable($envPath)) {
            $errorMessage = '.env file is missing or not writable.';
            return false;
        }

        $updates = [
            'APP_LOCALE' => $language,
            'DB_CONNECTION' => (string) ($dbConfig['connection'] ?? 'sqlite'),
            'DB_HOST' => (string) ($dbConfig['host'] ?? '127.0.0.1'),
            'DB_PORT' => (string) ($dbConfig['port'] ?? '3306'),
            'DB_DATABASE' => (string) (($dbConfig['connection'] ?? null) === 'sqlite'
                ? ($dbConfig['sqlite_path'] ?? database_path('database.sqlite'))
                : ($dbConfig['database'] ?? '')),
            'DB_USERNAME' => (string) ($dbConfig['username'] ?? ''),
            'DB_PASSWORD' => (string) ($dbConfig['password'] ?? ''),
        ];

        $content = File::get($envPath);
        foreach ($updates as $key => $value) {
            $content = $this->setEnvValue($content, $key, $value);
        }

        File::put($envPath, $content);

        return true;
    }

    private function setEnvValue(string $content, string $key, string $value): string
    {
        $escaped = str_replace('"', '\"', $value);
        $line = $key . '="' . $escaped . '"';
        $pattern = "/^{$key}=.*$/m";

        if (preg_match($pattern, $content) === 1) {
            return (string) preg_replace($pattern, $line, $content);
        }

        return rtrim($content) . PHP_EOL . $line . PHP_EOL;
    }

    private function applyDefaultLanguage(string $language): void
    {
        if (!Schema::hasTable('languages')) {
            return;
        }

        DB::table('languages')->update(['is_default' => false]);
        DB::table('languages')->where('code', $language)->update(['is_default' => true]);
    }

    private function writeInstallerLock(): void
    {
        $lockFile = (string) config('installer.lock_file', storage_path('app/installer.lock'));
        File::ensureDirectoryExists(dirname($lockFile));
        File::put($lockFile, now()->toDateTimeString());
    }
}
