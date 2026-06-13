<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fidélité d'import du formulaire MASTER 2350 (1 WEB MASTER 2350 COLONNE ABCD) :
     * - formatted_title : le texte de la colonne C avec sa mise en forme littérale
     *   (couleurs par fragment, gras, espaces préservés; le rouge disparaît à l'import)
     * - has_input : la colonne D porte un « X » = le fournisseur saisit un texte personnalisé
     * - source_row : numéro de ligne Excel d'origine — préserve l'espacement vertical
     *   du fichier pour le rendu littéral de la fiche
     * - provider_type : « Clientèle cible » déclarée en tête de fiche (residential|business)
     */
    public function up(): void
    {
        Schema::table('services', static function (Blueprint $table) {
            $table->boolean('has_input')->default(false)->after('type');
            $table->unsignedInteger('source_row')->nullable()->after('has_input');
        });

        Schema::table('service_translations', static function (Blueprint $table) {
            $table->text('formatted_title')->nullable()->after('title');
        });

        Schema::table('service_categories', static function (Blueprint $table) {
            $table->string('provider_type')->nullable()->after('service_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', static function (Blueprint $table) {
            $table->dropColumn('has_input');
            $table->dropColumn('source_row');
        });

        Schema::table('service_translations', static function (Blueprint $table) {
            $table->dropColumn('formatted_title');
        });

        Schema::table('service_categories', static function (Blueprint $table) {
            $table->dropColumn('provider_type');
        });
    }
};
