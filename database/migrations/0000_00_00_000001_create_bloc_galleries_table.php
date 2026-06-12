<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocGalleriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_galleries', function (Blueprint $table) {
			$table->id();

			$table->foreignId('gallery_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->timestamps();
		});

		Schema::create('bloc_gallery_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_gallery_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index('bg_tr_l_i');
			$table->unique(['bloc_gallery_id', DB::raw('`locale`(2)')], 'bg_unique');
			$table->foreign('bloc_gallery_id',
				'bg_id_foreign')->references('id')->on('bloc_galleries')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_gallery_translations');
		Schema::dropIfExists('bloc_galleries');
	}
}
