<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRemindersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reminders', function (Blueprint $table) {
			$table->id();

			$table->foreignId('subscription_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();
			$table->string('identifier')->nullable();
			$table->integer('days')->nullable();
			$table->boolean('active')->default(true);

			$table->timestamps();
		});

		Schema::create('reminder_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('reminder_id');

			$table->string('email_title')->nullable();
			$table->text('email_content')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['reminder_id', DB::raw('`locale`(2)')], 'reminder_unique');
			$table->foreign('reminder_id')
				->references('id')
				->on('reminders')
				->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('reminder_translations');
		Schema::dropIfExists('reminders');
	}
}
