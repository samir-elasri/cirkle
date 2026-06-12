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
        Schema::table('settings', static function (Blueprint $table) {
			$table->string('cities_serviced_label')->nullable();
			$table->string('state_serviced_label')->nullable();
			$table->string('country_serviced_label')->nullable();
			$table->string('default_profile_image')->nullable();
			$table->string('tps_text')->nullable();
			$table->string('tvq_text')->nullable();
			$table->dropColumn('sender_email_name');
			$table->dropColumn('sender_email');
        });

        Schema::table('setting_translations', static function (Blueprint $table) {
			$table->string('license_title')->nullable();
			$table->text('license_description')->nullable();
			$table->string('license_price')->nullable();
			$table->string('license_order')->nullable();
			$table->string('promotion_title')->nullable();
			$table->text('promotion_description')->nullable();
			$table->string('promotion_price')->nullable();
			$table->string('promotion_order')->nullable();
			$table->string('image_title')->nullable();
			$table->text('image_description')->nullable();
			$table->string('image_price')->nullable();
			$table->string('image_order')->nullable();
			$table->string('estimation_title')->nullable();
			$table->text('estimation_description')->nullable();
			$table->string('estimation_price')->nullable();
			$table->string('estimation_order')->nullable();
			$table->string('job_offer_title')->nullable();
			$table->text('job_offer_description')->nullable();
			$table->string('job_offer_price')->nullable();
			$table->string('job_offer_order')->nullable();
			$table->string('sender_email_name')->nullable();
			$table->string('sender_email')->nullable();
			$table->string('new_service_proposition_title')->nullable();
			$table->text('new_service_proposition_text')->nullable();
			$table->string('new_contact_request_title')->nullable();
			$table->text('new_contact_request_text')->nullable();
			$table->string('low_evaluation_title')->nullable();
			$table->text('low_evaluation_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('settings', static function (Blueprint $table) {
			$table->dropColumn('cities_serviced_label');
			$table->dropColumn('state_serviced_label');
			$table->dropColumn('country_serviced_label');
			$table->dropColumn('default_profile_image');
			$table->dropColumn('tps_text');
			$table->dropColumn('tvq_text');
			$table->string('sender_email_name')->nullable();
			$table->string('sender_email')->nullable();
        });
        Schema::table('setting_translations', static function (Blueprint $table) {
			$table->dropColumn('license_title');
			$table->dropColumn('license_description');
			$table->dropColumn('license_price');
			$table->dropColumn('license_order');
			$table->dropColumn('promotion_title');
			$table->dropColumn('promotion_description');
			$table->dropColumn('promotion_price');
			$table->dropColumn('promotion_order');
			$table->dropColumn('image_title');
			$table->dropColumn('image_description');
			$table->dropColumn('image_price');
			$table->dropColumn('image_order');
			$table->dropColumn('estimation_title');
			$table->dropColumn('estimation_description');
			$table->dropColumn('estimation_price');
			$table->dropColumn('estimation_order');
			$table->dropColumn('job_offer_title');
			$table->dropColumn('job_offer_description');
			$table->dropColumn('job_offer_price');
			$table->dropColumn('job_offer_order');
			$table->dropColumn('sender_email_name');
			$table->dropColumn('sender_email');
			$table->dropColumn('new_service_proposition_title');
			$table->dropColumn('new_service_proposition_text');
			$table->dropColumn('new_contact_request_title');
			$table->dropColumn('new_contact_request_text');
			$table->dropColumn('low_evaluation_title');
			$table->dropColumn('low_evaluation_text');
        });
    }
};
