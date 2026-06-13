<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drapeau d'activation de l'option « Diplômes » sur le fournisseur (mêmes que
     * profile_license_active / _activation_datetime).
     */
    public function up(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->boolean('profile_diploma_active')->default(false)->after('profile_license_activation_datetime');
            $table->dateTime('profile_diploma_activation_datetime')->nullable()->after('profile_diploma_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->dropColumn(['profile_diploma_active', 'profile_diploma_activation_datetime']);
        });
    }
};
