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
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->string('idempotency_key', 64)->nullable()->after('form_id');
            $table->unique(['form_id', 'idempotency_key'], 'form_submissions_form_id_idempotency_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropUnique('form_submissions_form_id_idempotency_key_unique');
            $table->dropColumn('idempotency_key');
        });
    }
};
