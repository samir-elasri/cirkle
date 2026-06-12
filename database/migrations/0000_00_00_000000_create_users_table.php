<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();

			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('gender')->nullable();
			$table->dateTime('birth_date')->nullable();
			$table->string('avatar')->nullable();
			$table->boolean('receive_notification_in_advance')->default(false);
			$table->boolean('receive_reminder')->default(false);
			$table->string('email')->unique();
			$table->string('password');
			$table->dateTime('previous_login')->nullable();
			$table->boolean('admin')->default(false);
			$table->timestamp('email_verified_at')->nullable();
			$table->rememberToken();

			$table->boolean('active')->default(true);
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
		Schema::dropIfExists('users');
	}
}
