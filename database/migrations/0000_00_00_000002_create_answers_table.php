<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('answers', function (Blueprint $table) {
			$table->id();

			$table->string('field_name')->nullable();
			$table->text('field_value')->nullable();
			$table->foreignId('form_answer_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();

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
		Schema::drop('answers');
	}
}
