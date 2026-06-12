<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocGoogleMapsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_google_maps', function (Blueprint $table) {
			$table->id();
			$table->float('zoom')->nullable();
			$table->integer('height')->nullable();

			$table->foreignId('google_map_group_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->timestamps();
		});

		Schema::create('bloc_google_map_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_google_map_id');

			$table->string('title')->nullable();
			$table->text('content')->nullable();

			$table->string('locale', 2)->index('bgm_tr_l_i');
			$table->unique(['bloc_google_map_id', DB::raw('`locale`(2)')], 'bgm_unique');
			$table->foreign('bloc_google_map_id',
				'bgm_foreign')->references('id')->on('bloc_google_maps')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_google_map_translations');
		Schema::dropIfExists('bloc_google_maps');
	}
}
