<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSlideshowsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('slideshows', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->integer('slideshow_height')->nullable();
			$table->integer('auto_play_speed')->nullable();

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
		Schema::drop('slideshows');
	}
}
