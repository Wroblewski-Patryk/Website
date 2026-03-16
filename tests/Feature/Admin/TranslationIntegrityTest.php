<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class TranslationIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed languages first
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /**
     * Test if all translations defined in scan exist in database.
     * This ensures developer has seeded the translations.
     */
    public function test_admin_translations_are_complete(): void
    {
        $dbKeys = \App\Models\Translation::where('group', 'admin')->pluck('key')->toArray();
        sort($dbKeys);

        // Run scanner in scope admin with report-missing
        $exitCode = Artisan::call('i18n:scan', [
            '--scope' => 'admin',
            '--report-missing' => true,
        ]);

        $output = Artisan::output();
        
        // If it failed or created something, let's look closer
        if (!str_contains($output, 'Created: 0')) {
             $allAdminKeysAfter = \App\Models\Translation::where('group', 'admin')->pluck('key')->toArray();
             sort($allAdminKeysAfter);
             
             $newKeys = array_diff($allAdminKeysAfter, $dbKeys);
             
             dump("DB KEYS BEFORE SCAN (Sample 5):", array_slice($dbKeys, 0, 5));
             dump("NEW KEYS CREATED BY SCANNER (Total " . count($newKeys) . "):", array_values($newKeys));
        }

        $this->assertEquals(0, $exitCode, "i18n:scan command failed.");
        $this->assertStringContainsString(
            'Created: 0', 
            $output, 
            "Detected new translation keys in code that were NOT in the database. Please update your seeder! Check dump for details."
        );
    }
}
