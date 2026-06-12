<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscribersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscribers', function (Blueprint $table) {
			$table->id();

			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->datetime('login_datetime')->nullable();
			$table->string('email')->nullable();
			$table->string('password')->nullable();
			$table->string('preference_language')->nullable();
			$table->string('remember_token')->nullable();
			$table->string('recover_token')->nullable();
			$table->boolean('accept_condition')->default(false);
			$table->boolean('email_validated')->default(false);
			$table->boolean('active')->default(true);

			$table->string('api_token', 80)
				->unique()
				->nullable()
				->default(null);

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
		Schema::drop('subscribers');
	}
}
