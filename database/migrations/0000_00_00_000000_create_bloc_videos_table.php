<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocVideosTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_videos', function (Blueprint $table) {
			$table->id();

			$table->string('video_type')->nullable();
			$table->boolean('use_fr')->default(0);

			$table->timestamps();
		});

		Schema::create('bloc_video_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_video_id');

			$table->string('title')->nullable();
			$table->string('image')->nullable();
			$table->string('video_url')->nullable();
			$table->string('video_filename')->nullable();
			$table->text('description')->nullable();
			$table->text('legend')->nullable();

			$table->string('locale', 2)->index('bv_tr_l_i');
			$table->unique(['bloc_video_id', DB::raw('`locale`(2)')], 'bv_unique');
			$table->foreign('bloc_video_id', 'bv_foreign')->references('id')->on('bloc_videos')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_video_translations');
		Schema::dropIfExists('bloc_videos');
	}
}
