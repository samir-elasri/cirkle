<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharingsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sharings', function (Blueprint $table) {
			$table->id();

			$table->foreignId('shareable_id')->nullable();
			$table->string('shareable_type');

			$table->timestamps();
		});

		Schema::create('sharing_translations', function (Blueprint $table) {
			$table->id();

			$table->foreignId('sharing_id');

			$table->string('fb_title')->nullable();
			$table->text('fb_description')->nullable();
			$table->string('fb_image')->nullable();
			$table->string('tw_title')->nullable();
			$table->text('tw_description')->nullable();
			$table->string('tw_image')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['sharing_id', DB::raw('`locale`(2)')], 'sharing_unique');
			$table->foreign('sharing_id')->references('id')->on('sharings')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('sharing_translations');
		Schema::dropIfExists('sharings');
	}
}
