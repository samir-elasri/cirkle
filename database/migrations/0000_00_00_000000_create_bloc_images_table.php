<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocImagesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_images', function (Blueprint $table) {
			$table->id();

			$table->timestamps();
		});

		Schema::create('bloc_image_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_image_id');

			$table->string('title')->nullable();
			$table->string('image')->nullable();
			$table->string('alt')->nullable();
			$table->string('legend')->nullable();

			$table->string('locale', 2)->index('bi_tr_l_i');
			$table->unique(['bloc_image_id', DB::raw('`locale`(2)')], 'bi_unique');
			$table->foreign('bloc_image_id', 'bi_foreign')->references('id')->on('bloc_images')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_image_translations');
		Schema::dropIfExists('bloc_images');
	}
}
