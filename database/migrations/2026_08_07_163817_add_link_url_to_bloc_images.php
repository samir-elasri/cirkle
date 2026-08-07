<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bloc IMAGE cliquable (demande de Steve, 05.08 : « finaliser la photo du bas de
 * page avec un lien cliquable »). Lien par langue; vide = image non cliquable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bloc_image_translations', static function (Blueprint $table) {
            $table->string('link_url')->nullable()->after('legend');
        });
    }

    public function down(): void
    {
        Schema::table('bloc_image_translations', static function (Blueprint $table) {
            $table->dropColumn('link_url');
        });
    }
};
