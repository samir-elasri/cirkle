<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Frais de la fiche de compétence, variables par profession (spec : défaut 75 $).
     * - fiche_fee      : montant lu dans le TITRE INTERNE (« 0001 RF 75/100/100 » → 75)
     * - fiche_fee_text : le bloc « OBLIGATOIRE … FRAIS POUR LA FICHE … SI VOUS REFUSEZ »
     *   du fichier MASTER, affiché tel quel dans la porte d'acceptation (feature #6).
     * Colonnes simples (non traduites) : chaque fiche importée est mono-langue.
     */
    public function up(): void
    {
        Schema::table('service_categories', static function (Blueprint $table) {
            $table->decimal('fiche_fee', 10, 2)->nullable()->after('provider_type');
            $table->text('fiche_fee_text')->nullable()->after('fiche_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_categories', static function (Blueprint $table) {
            $table->dropColumn(['fiche_fee', 'fiche_fee_text']);
        });
    }
};
