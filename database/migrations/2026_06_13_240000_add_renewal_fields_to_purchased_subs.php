<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cycle de vie des abonnements (feature #12) :
     * - renewal_reminder_sent_at : courriel de rappel « 7 jours avant expiration » déjà envoyé.
     * - cancel_at_period_end : annulation effective à la fin du terme (sans remboursement).
     */
    public function up(): void
    {
        Schema::table('purchased_subs', static function (Blueprint $table) {
            $table->dateTime('renewal_reminder_sent_at')->nullable()->after('end_date');
            $table->boolean('cancel_at_period_end')->default(false)->after('renewal_reminder_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchased_subs', static function (Blueprint $table) {
            $table->dropColumn(['renewal_reminder_sent_at', 'cancel_at_period_end']);
        });
    }
};
