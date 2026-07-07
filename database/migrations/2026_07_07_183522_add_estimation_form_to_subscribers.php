<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Formulaire ESTIMATION de Denis (WEB 10A, 07.07.26) : toutes les reponses du
 * formulaire (methode de production, cout, paiements, rendez-vous, frais de
 * cancellation, autres) en JSON + la photo de la feuille d'estimation du
 * fournisseur. Remplace les anciens champs estimation_cost / accepts_* pour
 * les nouvelles inscriptions (les anciens restent lus en repli).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->text('estimation_json')->nullable()->after('estimation_cost');
            $table->string('estimation_sheet_image')->nullable()->after('estimation_json');
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->dropColumn(['estimation_json', 'estimation_sheet_image']);
        });
    }
};
