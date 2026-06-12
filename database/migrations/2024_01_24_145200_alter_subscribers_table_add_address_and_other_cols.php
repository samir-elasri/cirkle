<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
	{
		Schema::table('subscribers', static function (Blueprint $table) {
			$table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete();
			$table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
			$table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
			$table->string('provider_type')->nullable();
			$table->string('profile_image')->nullable();
			$table->string('company_name')->nullable();
			$table->text('main_description')->nullable();
			$table->string('number')->nullable();
			$table->string('street')->nullable();
			$table->string('app')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('country')->nullable();
			$table->string('postal_code')->nullable();
			$table->string('phone')->nullable();
			$table->boolean('website_subscriber')->default(false);
			$table->boolean('is_provider')->default(false);
			$table->boolean('profile_license_active')->default(false);
			$table->boolean('profile_advertisement_active')->default(false);
			$table->boolean('profile_images_active')->default(false);
			$table->boolean('profile_estimation_active')->default(false);
			$table->boolean('profile_job_offers_active')->default(false);
			$table->boolean('accepts_cash')->default(false);
			$table->boolean('accepts_check')->default(false);
			$table->boolean('accepts_debit')->default(false);
			$table->boolean('accepts_credit')->default(false);
			$table->boolean('is_public')->default(false);
			$table->dateTime('activation_datetime')->nullable();
			$table->date('end_date')->nullable();
			$table->dateTime('profile_license_activation_datetime')->nullable();
			$table->dateTime('profile_advertisement_activation_datetime')->nullable();
			$table->dateTime('profile_images_activation_datetime')->nullable();
			$table->dateTime('profile_estimation_activation_datetime')->nullable();
			$table->dateTime('profile_job_offers_activation_datetime')->nullable();
			$table->decimal('estimation_cost')->nullable();
		});

		Schema::create('subscriber_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('subscriber_id');

			$table->text('other_service_descriptions')->nullable();
			$table->string('url')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['subscriber_id', DB::raw('`locale`(2)')], 'subscriber_unique');
			$table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
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
			$table->dropConstrainedForeignId('service_category_id');
			$table->dropConstrainedForeignId('state_id');
			$table->dropConstrainedForeignId('country_id');
			$table->dropColumn('number');
			$table->dropColumn('street');
			$table->dropColumn('app');
			$table->dropColumn('city');
			$table->dropColumn('state');
			$table->dropColumn('country');
			$table->dropColumn('postal_code');
			$table->dropColumn('phone');
			$table->dropColumn('website_subscriber');
			$table->dropColumn('is_provider');
			$table->dropColumn('profile_license_active');
			$table->dropColumn('profile_advertisement_active');
			$table->dropColumn('profile_images_active');
			$table->dropColumn('profile_estimation_active');
			$table->dropColumn('profile_job_offers_active');
			$table->dropColumn('accepts_cash');
			$table->dropColumn('accepts_check');
			$table->dropColumn('accepts_debit');
			$table->dropColumn('accepts_credit');
			$table->dropColumn('is_public');
			$table->dropColumn('profile_license_activation_datetime');
			$table->dropColumn('profile_advertisement_activation_datetime');
			$table->dropColumn('profile_images_activation_datetime');
			$table->dropColumn('profile_estimation_activation_datetime');
			$table->dropColumn('profile_job_offers_activation_datetime');
			$table->dropColumn('provider_type');
			$table->dropColumn('estimation_cost');
			$table->dropColumn('activation_datetime');
			$table->dropColumn('end_date');
			$table->dropColumn('provider_type');
			$table->dropColumn('profile_image');
			$table->dropColumn('company_name');
			$table->dropColumn('main_description');
		});

		Schema::dropIfExists('subscriber_translations');

	}
};
