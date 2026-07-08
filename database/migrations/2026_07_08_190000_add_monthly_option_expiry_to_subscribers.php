<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Options MENSUELLES (Denis 08.07) : Recrutement 100 $/mois et Promotion
 * 100 $/mois (les autres options restent en frais unique). Chaque achat couvre
 * UN mois : date d'échéance propre à l'option + marqueur du rappel de
 * renouvellement (même cycle rappel/grâce que l'abonnement — DailyCron).
 * À l'échéance non renouvelée, l'option est désactivée et son logo (PROMO / E)
 * disparaît de la fiche — règle de Denis (18.06).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->dateTime('profile_promotion_expires_at')->nullable()->after('profile_promotion_activation_datetime');
            $table->dateTime('profile_promotion_renewal_reminder_sent_at')->nullable()->after('profile_promotion_expires_at');
            $table->dateTime('profile_job_offer_expires_at')->nullable()->after('profile_job_offer_activation_datetime');
            $table->dateTime('profile_job_offer_renewal_reminder_sent_at')->nullable()->after('profile_job_offer_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->dropColumn([
                'profile_promotion_expires_at',
                'profile_promotion_renewal_reminder_sent_at',
                'profile_job_offer_expires_at',
                'profile_job_offer_renewal_reminder_sent_at',
            ]);
        });
    }
};
