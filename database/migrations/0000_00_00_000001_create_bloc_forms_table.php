<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocFormsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_forms', function (Blueprint $table) {
			$table->id();

			$table->foreignId('form_generator_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->boolean('use_activation')->default(false);
			$table->datetime('start_datetime')->nullable();
			$table->datetime('end_datetime')->nullable();

			$table->boolean('email_confirm_send')->default(false);
			$table->string('email_confirm_from')->nullable();
			$table->string('email_confirm_bcc')->nullable();

			$table->boolean('email_alert_send')->default(false);
			$table->string('email_alert_from')->nullable();
			$table->string('email_alert_to')->nullable();

			$table->timestamps();
		});

		Schema::create('bloc_form_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_form_id');

			$table->string('title')->nullable();
			$table->text('content')->nullable();
			$table->string('call_to_action_label')->nullable();
			$table->text('note')->nullable();
			$table->string('message')->nullable();

			$table->string('email_confirm_title')->nullable();
			$table->text('email_confirm_content')->nullable();
			$table->string('email_confirm_name')->nullable();

			$table->string('email_alert_title')->nullable();
			$table->text('email_alert_content')->nullable();
			$table->string('email_alert_name')->nullable();

			$table->string('locale', 2)->index('bf_tr_l_i');
			$table->unique(['bloc_form_id', DB::raw('`locale`(2)')], 'bf_unique');
			$table->foreign('bloc_form_id', 'bf_id_foreign')->references('id')->on('bloc_forms')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_form_translations');
		Schema::dropIfExists('bloc_forms');
	}
}
