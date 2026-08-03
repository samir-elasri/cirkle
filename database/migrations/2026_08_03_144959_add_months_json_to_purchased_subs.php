<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Forfaits par MOIS CHOISIS (Denis : mois consecutifs OU NON, parmi les 13
 * prochains mois). months_json = liste des mois couverts ["2026/08", ...];
 * NULL = ancien modele (periode continue start_date -> end_date).
 * Regle definie (cas C) : chaque mois choisi = mois de calendrier complet;
 * si le mois EN COURS est choisi, la couverture debute au paiement et le mois
 * compte comme complet (aucun prorata).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchased_subs', static function (Blueprint $table) {
            $table->text('months_json')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('purchased_subs', static function (Blueprint $table) {
            $table->dropColumn('months_json');
        });
    }
};
