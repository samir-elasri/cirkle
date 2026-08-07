<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lien Facebook affiche dans le bas de page (demande de Steve, 05.08).
 * Modifiable dans l'admin (Reglages) — le lien n'apparait que s'il est rempli.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', static function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->after('socials_mini_card_group_id');
        });
    }

    public function down(): void
    {
        Schema::table('settings', static function (Blueprint $table) {
            $table->dropColumn('facebook_url');
        });
    }
};
