<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->index(['module', 'type', 'order', 'id'], 'taxonomies_module_type_order_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropIndex('taxonomies_module_type_order_id_idx');
        });
    }
};
