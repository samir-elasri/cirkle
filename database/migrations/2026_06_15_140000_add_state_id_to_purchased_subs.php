<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Zone d'un abonnement acheté : state_id NULL = forfait par code postal (le
     * fournisseur cible 1 à 10 codes postaux); state_id renseigné = forfait pour
     * cette province. Permet de savoir, par achat, quelle zone a été payée.
     */
    public function up(): void
    {
        Schema::table('purchased_subs', static function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->after('subscription_id')
                ->constrained('states')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchased_subs', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('state_id');
        });
    }
};
