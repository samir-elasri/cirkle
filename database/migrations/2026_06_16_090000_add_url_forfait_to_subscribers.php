<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Forfait « site web du fournisseur » (MASTER 2350) : le fournisseur choisit UN
     * couple palier × durée (100$/150$ × 1/6/12 mois).
     *   - url_forfait      : le choix, sous la forme « palier-durée » (ex. « 150-6 »)
     *   - url_forfait_end  : l'expiration PROPRE du forfait site web (n'écrase plus la
     *                        date de fin d'abonnement, contrairement à l'ancien code).
     */
    public function up(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->string('url_forfait', 16)->nullable()->after('profile_url_activation_datetime');
            $table->timestamp('url_forfait_end')->nullable()->after('url_forfait');
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->dropColumn(['url_forfait', 'url_forfait_end']);
        });
    }
};
