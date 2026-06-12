<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlocAudiosTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_audio', function (Blueprint $table) {
			$table->id();

			$table->timestamps();
		});

		Schema::create('bloc_audio_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_audio_id');

			$table->string('title')->nullable();
			$table->string('image')->nullable();
			$table->string('audio_filename')->nullable();

			$table->string('locale', 2)->index('ba_tr_l_i');
			$table->unique(['bloc_audio_id', DB::raw('`locale`(2)')], 'ba_unique');
			$table->foreign('bloc_audio_id', 'ba_foreign')->references('id')->on('bloc_audio')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_audio_translations');
		Schema::dropIfExists('bloc_audio');
	}
}
