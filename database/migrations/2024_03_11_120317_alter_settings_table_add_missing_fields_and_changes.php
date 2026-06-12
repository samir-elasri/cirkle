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
			$table->decimal('license_price',  12,3)->nullable();
			$table->decimal('promotion_price',  12,3)->nullable();
			$table->decimal('image_price',  12,3)->nullable();
			$table->decimal('estimation_price',  12,3)->nullable();
			$table->decimal('job_offer_price',  12,3)->nullable();
			$table->decimal('url_price',  12,3)->nullable();

			$table->integer('license_order')->nullable();
			$table->integer('promotion_order')->nullable();
			$table->integer('image_order')->nullable();
			$table->integer('estimation_order')->nullable();
			$table->integer('job_offer_order')->nullable();
			$table->integer('url_order')->nullable();
			$table->integer('url_month_duration')->nullable();
        });
		Schema::table('setting_translations', static function (Blueprint $table) {
			$table->dropColumn('license_price');
			$table->dropColumn('promotion_price');
			$table->dropColumn('image_price');
			$table->dropColumn('estimation_price');
			$table->dropColumn('job_offer_price');

			$table->dropColumn('license_order');
			$table->dropColumn('promotion_order');
			$table->dropColumn('image_order');
			$table->dropColumn('estimation_order');
			$table->dropColumn('job_offer_order');

			$table->text('url_description')->nullable();
			$table->string('url_title')->nullable();
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
			$table->dropColumn('license_price');
			$table->dropColumn('promotion_price');
			$table->dropColumn('image_price');
			$table->dropColumn('estimation_price');
			$table->dropColumn('job_offer_price');
			$table->dropColumn('url_price');

			$table->dropColumn('license_order');
			$table->dropColumn('promotion_order');
			$table->dropColumn('image_order');
			$table->dropColumn('estimation_order');
			$table->dropColumn('job_offer_order');
			$table->dropColumn('url_order');
			$table->dropColumn('url_month_duration');
        });
		Schema::table('setting_translations', static function (Blueprint $table) {
			$table->string('license_order')->nullable();
			$table->string('promotion_order')->nullable();
			$table->string('image_order')->nullable();
			$table->string('estimation_order')->nullable();
			$table->string('job_offer_order')->nullable();

			$table->string('license_price')->nullable();
			$table->string('promotion_price')->nullable();
			$table->string('image_price')->nullable();
			$table->string('estimation_price')->nullable();
			$table->string('job_offer_price')->nullable();

			$table->dropColumn('url_description');
			$table->dropColumn('url_title');
		});
    }
};
