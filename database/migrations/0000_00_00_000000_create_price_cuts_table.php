<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceCutsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('price_cuts', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->string('code')->nullable();
			$table->string('discount_type')->nullable();
			$table->float('value', 8, 2)->nullable();
			$table->boolean('use_once')->default(false);
			$table->date('end_date')->nullable();
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
		Schema::drop('price_cuts');
	}
}
