<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Forfait SITE WEB par MOIS CHOISIS (Denis 04-07.08 : « le meme principe pour le
 * forfait du site web », « la meme presentation que pour les tarifs »).
 * url_months_json = mois couverts ["2026/08", ...]; NULL = ancien modele (duree
 * continue a partir du paiement).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->text('url_months_json')->nullable()->after('url_forfait_end');
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->dropColumn('url_months_json');
        });
    }
};
