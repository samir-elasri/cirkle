<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListEmailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('list_emails', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->string('send_name')->nullable();
			$table->string('send_email')->nullable();

			$table->boolean('active')->default(true);
			$table->timestamps();
		});

		Schema::create('list_email_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('list_email_id');

			$table->string('title')->nullable();
			$table->string('object')->nullable();
			$table->text('content')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['list_email_id', DB::raw('`locale`(2)')], 'sub_unique');
			$table->foreign('list_email_id')->references('id')->on('list_emails')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('list_email_translations');
		Schema::dropIfExists('list_emails');
	}
}
