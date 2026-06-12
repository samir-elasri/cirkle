<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSentRemindersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sent_reminders', function (Blueprint $table) {
			$table->id();

			$table->foreignId('purchased_sub_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();
			$table->foreignId('reminder_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();
			$table->date('date')->nullable();

			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sent_reminders');
	}
}
