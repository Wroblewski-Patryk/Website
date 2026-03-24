<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('languages') || !Schema::hasColumn('languages', 'is_default')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS languages_single_default_insert_chk');
            DB::statement('DROP TRIGGER IF EXISTS languages_single_default_update_chk');

            DB::statement("
                CREATE TRIGGER languages_single_default_insert_chk
                BEFORE INSERT ON languages
                FOR EACH ROW
                WHEN NEW.is_default = 1
                    AND EXISTS(SELECT 1 FROM languages WHERE is_default = 1)
                BEGIN
                    SELECT RAISE(ABORT, 'Only one default language allowed');
                END
            ");

            DB::statement("
                CREATE TRIGGER languages_single_default_update_chk
                BEFORE UPDATE OF is_default ON languages
                FOR EACH ROW
                WHEN NEW.is_default = 1
                    AND EXISTS(SELECT 1 FROM languages WHERE is_default = 1 AND id != OLD.id)
                BEGIN
                    SELECT RAISE(ABORT, 'Only one default language allowed');
                END
            ");

            return;
        }

        if ($driver === 'pgsql') {
            DB::unprepared(<<<'SQL'
                CREATE OR REPLACE FUNCTION enforce_single_default_language()
                RETURNS TRIGGER AS $$
                BEGIN
                    IF NEW.is_default = true AND EXISTS (
                        SELECT 1
                        FROM languages
                        WHERE is_default = true
                          AND id <> COALESCE(NEW.id, 0)
                    ) THEN
                        RAISE EXCEPTION 'Only one default language allowed';
                    END IF;
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            SQL);

            DB::statement('DROP TRIGGER IF EXISTS languages_single_default_insert_chk ON languages');
            DB::statement('DROP TRIGGER IF EXISTS languages_single_default_update_chk ON languages');

            DB::statement('
                CREATE TRIGGER languages_single_default_insert_chk
                BEFORE INSERT ON languages
                FOR EACH ROW
                EXECUTE FUNCTION enforce_single_default_language()
            ');

            DB::statement('
                CREATE TRIGGER languages_single_default_update_chk
                BEFORE UPDATE OF is_default ON languages
                FOR EACH ROW
                EXECUTE FUNCTION enforce_single_default_language()
            ');

            return;
        }

        // mysql / mariadb
        DB::unprepared('DROP TRIGGER IF EXISTS languages_single_default_insert_chk');
        DB::unprepared('DROP TRIGGER IF EXISTS languages_single_default_update_chk');

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER languages_single_default_insert_chk
            BEFORE INSERT ON languages
            FOR EACH ROW
            BEGIN
                IF NEW.is_default = 1 AND EXISTS (SELECT 1 FROM languages WHERE is_default = 1) THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only one default language allowed';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER languages_single_default_update_chk
            BEFORE UPDATE ON languages
            FOR EACH ROW
            BEGIN
                IF NEW.is_default = 1
                    AND EXISTS (SELECT 1 FROM languages WHERE is_default = 1 AND id <> OLD.id) THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only one default language allowed';
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        if (!Schema::hasTable('languages') || !Schema::hasColumn('languages', 'is_default')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS languages_single_default_insert_chk');
            DB::statement('DROP TRIGGER IF EXISTS languages_single_default_update_chk');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('DROP TRIGGER IF EXISTS languages_single_default_insert_chk ON languages');
            DB::statement('DROP TRIGGER IF EXISTS languages_single_default_update_chk ON languages');
            DB::statement('DROP FUNCTION IF EXISTS enforce_single_default_language()');
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS languages_single_default_insert_chk');
        DB::unprepared('DROP TRIGGER IF EXISTS languages_single_default_update_chk');
    }
};
