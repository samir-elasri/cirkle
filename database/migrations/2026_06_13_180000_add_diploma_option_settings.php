<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Réglages de l'option payante « Diplômes » (PDIPOMECK) — mêmes colonnes que les
     * autres options de profil (license/image/…). Prix + ordre sur settings; titre +
     * description traduits sur setting_translations.
     */
    public function up(): void
    {
        Schema::table('settings', static function (Blueprint $table) {
            $table->decimal('diploma_price', 12, 3)->nullable()->after('registration_fee');
            $table->integer('diploma_order')->nullable()->after('diploma_price');
        });

        Schema::table('setting_translations', static function (Blueprint $table) {
            $table->string('diploma_title')->nullable();
            $table->string('diploma_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', static function (Blueprint $table) {
            $table->dropColumn(['diploma_price', 'diploma_order']);
        });

        Schema::table('setting_translations', static function (Blueprint $table) {
            $table->dropColumn(['diploma_title', 'diploma_description']);
        });
    }
};
