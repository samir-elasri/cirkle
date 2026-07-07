<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bloc PROMOTION de Denis (07.07.26) : une promotion = titre, description,
 * duree (debut/fin AAAA/MM/JJ), et l'option photos A (3 photos 50 $) ou
 * B (6 photos 80 $) — chemins des photos en JSON.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotions', static function (Blueprint $table) {
            $table->string('start_date', 12)->nullable()->after('legend');
            $table->string('end_date', 12)->nullable()->after('start_date');
            $table->string('photos_tier', 1)->nullable()->after('end_date'); // A|B
            $table->text('photos_json')->nullable()->after('photos_tier');
        });
    }

    public function down(): void
    {
        Schema::table('promotions', static function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'photos_tier', 'photos_json']);
        });
    }
};
