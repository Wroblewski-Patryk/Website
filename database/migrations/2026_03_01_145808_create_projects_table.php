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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Translatable
            $table->string('slug')->unique();
            $table->json('description')->nullable(); // Translatable
            $table->string('desktop_image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('url')->nullable();
            $table->string('category')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
