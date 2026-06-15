<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Forfaits par PROVINCE (cahier de charges) : un prix d'abonnement peut être
     * spécifique à une province. state_id NULL = forfait par code postal (défaut);
     * state_id renseigné = forfait pour cette province.
     */
    public function up(): void
    {
        Schema::table('subscription_prices', static function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->after('service_category_id')
                ->constrained('states')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_prices', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('state_id');
        });
    }
};
