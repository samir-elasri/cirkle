<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Palier du forfait « site web du fournisseur » PROPRE À CHAQUE FICHE (Denis 03.07 :
 * « mes fiches WW000? vont déterminer soit le 100 $ ou le 150 $ »). Lu de la section
 * SITE WEB du fichier 2350 à l'import; l'inscription ne propose que ce palier.
 * NULL = palier inconnu (fiche importée avant ce champ) → tous les paliers offerts.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', static function (Blueprint $table) {
            $table->unsignedSmallInteger('website_tier')->nullable()->after('fiche_fee');
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', static function (Blueprint $table) {
            $table->dropColumn('website_tier');
        });
    }
};
