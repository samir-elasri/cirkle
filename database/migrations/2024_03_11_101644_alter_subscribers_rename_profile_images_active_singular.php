<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->renameColumn('profile_images_active', 'profile_image_active');
            $table->renameColumn('profile_images_activation_datetime', 'profile_image_activation_datetime');
            $table->renameColumn('profile_job_offers_active', 'profile_job_offer_active');
            $table->renameColumn('profile_job_offers_activation_datetime', 'profile_job_offer_activation_datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
			$table->renameColumn('profile_image_active', 'profile_images_active');
			$table->renameColumn('profile_image_activation_datetime', 'profile_images_activation_datetime');
			$table->renameColumn('profile_job_offer_active', 'profile_job_offers_active');
			$table->renameColumn('profile_job_offer_activation_datetime', 'profile_job_offers_activation_datetime');
        });
    }
};
