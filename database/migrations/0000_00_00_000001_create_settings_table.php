<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function (Blueprint $table) {
			$table->id();

			$table->string('news_default_image')->nullable();
			$table->string('events_default_image')->nullable();
			$table->boolean('header_show_menu_corpo')->default(true);
			$table->string('sender_email_name')->nullable();
			$table->string('sender_email')->nullable();
			$table->string('reception_email')->nullable();
			$table->integer('optimal_content_width')->default(1600);
			$table->integer('default_bloc_inner_spacing')->default(50);
			$table->integer('default_bloc_spacing')->default(50);
			$table->integer('default_page_top_spacing')->default(50);
			$table->integer('default_footer_top_spacing')->default(50);
			$table->integer('default_single_image_height')->default(200);
			$table->integer('footer_zone_width_1')->default(40);
			$table->integer('footer_zone_width_2')->default(20);
			$table->integer('footer_zone_width_3')->default(20);
			$table->integer('footer_zone_width_4')->default(20);
			$table->boolean('maintenance')->default(false);

			$table->string('tps_number')->nullable();
			$table->float('default_tps', 8, 3)->default(5.000);
			$table->string('tvq_number')->nullable();
			$table->float('default_tvq', 8, 3)->default(9.975);

			$table->foreignId('pub_group_id')
				->nullable()
				->constrained()
				->nullOnDelete();
			$table->foreignId('socials_mini_card_group_id')
				->nullable()
				->constrained('mini_card_groups')
				->nullOnDelete();
			$table->foreignId('partner_mini_card_group_id')
				->nullable()
				->constrained('mini_card_groups')
				->nullOnDelete();

			$table->timestamps();
		});

		Schema::create('setting_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('setting_id');

			$table->string('corpo_statement')->nullable();
			$table->string('main_logo_image')->nullable();
			$table->string('mobile_logo_image')->nullable();
			$table->string('copyright_notice')->nullable();
			$table->text('footer_about')->nullable();
			$table->text('footer_contact_details')->nullable();
			$table->string('footer_partners_title')->nullable();
			$table->text('maintenance_text')->nullable();
			$table->string('confirm_button_text')->nullable();
			$table->string('positive_retroaction')->nullable();
			$table->string('negative_retroaction')->nullable();

			$table->string('validation_email_title')->nullable();
			$table->text('validation_email_content')->nullable();
			$table->string('reset_email_title')->nullable();
			$table->text('reset_email_content')->nullable();
			$table->string('purchase_confirmation_email_title')->nullable();
			$table->text('purchase_confirmation_email_content')->nullable();
			$table->text('email_footer_text')->nullable();
			$table->string('email_header_image')->nullable();


			$table->string('company_name')->nullable();
			$table->text('company_address')->nullable();
			$table->text('android_install_text')->nullable();
			$table->text('apple_install_text')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['setting_id', DB::raw('`locale`(2)')], 'setting_unique');
			$table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('setting_translations');
		Schema::dropIfExists('settings');
	}
}
