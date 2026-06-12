<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTargetsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('targets', function (Blueprint $table) {
			$table->id();

			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('email')->nullable();
			$table->boolean('sent')->default(false);
			$table->date('sent_date')->nullable();
			$table->boolean('active');

			$table->foreignId('list_email_id')
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
		Schema::dropIfExists('targets');
	}
}
