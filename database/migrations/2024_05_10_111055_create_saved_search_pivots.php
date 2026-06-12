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
        Schema::create('saved_search_service', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('service_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('saved_search_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
        Schema::create('saved_search_service_category', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('service_category_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('saved_search_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_search_service');
        Schema::dropIfExists('saved_search_service_category');
    }
};
