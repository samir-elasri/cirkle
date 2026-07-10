<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Option RECRUTEMENT (Denis 09.07) : « ajouter un bloc PHOTO et demander au
 * fourn d'insérer son propre formulaire » — le fournisseur téléverse l'image
 * de son propre formulaire de recrutement, affichée sur sa fiche publique.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_offers', static function (Blueprint $table) {
            $table->string('image')->nullable()->after('currently_recruiting');
        });
    }

    public function down(): void
    {
        Schema::table('job_offers', static function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
