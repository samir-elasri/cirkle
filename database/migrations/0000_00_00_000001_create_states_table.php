<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('states', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->float('gst', 8, 3)->nullable();
			$table->float('pst', 8, 3)->nullable();
			$table->boolean('active')->default(true);

			$table->foreignId('country_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();

			$table->timestamps();
		});

		Schema::create('state_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('state_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['state_id', DB::raw('`locale`(2)')], 'state_locale');
			$table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('state_translations');
		Schema::dropIfExists('states');
	}
}
