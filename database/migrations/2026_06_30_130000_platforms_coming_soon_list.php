<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Remplace le booléen « french_platforms_coming_soon » par une LISTE des plateformes
 * « À VENIR » (chacune des 4 plateformes peut être bloquée individuellement — Denis 30.06).
 * Format : JSON de clés « locale-type », ex. ["fr-residential","fr-business"].
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'platforms_coming_soon')) {
                $table->text('platforms_coming_soon')->nullable();
            }
        });

        // Conserve le comportement actuel : les 2 plateformes françaises restent « À VENIR ».
        DB::table('settings')->whereNull('platforms_coming_soon')
            ->update(['platforms_coming_soon' => json_encode(['fr-residential', 'fr-business'])]);

        if (Schema::hasColumn('settings', 'french_platforms_coming_soon')) {
            Schema::table('settings', fn (Blueprint $t) => $t->dropColumn('french_platforms_coming_soon'));
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'platforms_coming_soon')) {
                $table->dropColumn('platforms_coming_soon');
            }
            if (!Schema::hasColumn('settings', 'french_platforms_coming_soon')) {
                $table->boolean('french_platforms_coming_soon')->default(true);
            }
        });
    }
};
