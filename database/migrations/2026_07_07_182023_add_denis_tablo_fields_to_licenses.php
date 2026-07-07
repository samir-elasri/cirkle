<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * « Tablo » de Denis pour l'option PERMIS (07.07.26) : TYPE | NOM OFFICIEL DE
 * L'ÉMETTEUR | NO DE PERMIS/LICENCE/MEMBRE/INSCRIPTION | DÉBUT (AAAA/MM) |
 * FIN (AAAA/MM) — sans les colonnes STATUS et COMMENTS de la version ChatGPT.
 * Le TYPE reste dans le titre (traduit); les 4 autres colonnes ici.
 * Dates en texte AAAA/MM (comme graduated_at des diplômes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licenses', static function (Blueprint $table) {
            $table->string('issuer')->nullable()->after('subscriber_id');
            $table->string('registration_number')->nullable()->after('issuer');
            $table->string('start_date', 20)->nullable()->after('registration_number');
            $table->string('expiry_date', 20)->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('licenses', static function (Blueprint $table) {
            $table->dropColumn(['issuer', 'registration_number', 'start_date', 'expiry_date']);
        });
    }
};
