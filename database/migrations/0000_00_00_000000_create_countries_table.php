<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('countries', function (Blueprint $table) {
			$table->id();

			$table->boolean('shipping')->default(false);
			$table->boolean('active')->default(true);

			$table->timestamps();
		});

		Schema::create('country_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('country_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['country_id', DB::raw('`locale`(2)')], 'country_locale');
			$table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('country_translations');
		Schema::dropIfExists('countries');
	}
}
