<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleMapsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('google_maps', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->string('image')->nullable();
			$table->float('latitude', 8, 6)->nullable();
			$table->float('longitude', 8, 6)->nullable();
			$table->integer('position')->nullable();
			$table->boolean('active')->default(true);

			$table->foreignId('google_map_group_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->timestamps();
		});

		Schema::create('google_map_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('google_map_id');

			$table->string('title')->nullable();
			$table->text('text')->nullable();
			$table->string('url_label')->nullable();
			$table->string('url')->nullable();

			$table->string('locale', 2)->index('gm_tr_l_i');
			$table->unique(['google_map_id', DB::raw('`locale`(2)')], 'gm_unique');
			$table->foreign('google_map_id', 'gm_foreign')->references('id')->on('google_maps')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('google_map_translations');
		Schema::dropIfExists('google_maps');
	}
}
