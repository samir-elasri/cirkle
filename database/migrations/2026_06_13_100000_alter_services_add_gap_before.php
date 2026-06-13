<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * gap_before : une ligne vide précédait cette ligne « O » dans le fichier MASTER —
     * l'espacement vertical (blocs de services) est rendu littéralement sur la fiche.
     */
    public function up(): void
    {
        Schema::table('services', static function (Blueprint $table) {
            $table->boolean('gap_before')->default(false)->after('source_row');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', static function (Blueprint $table) {
            $table->dropColumn('gap_before');
        });
    }
};
