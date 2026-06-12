<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGalleriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('galleries', function (Blueprint $table) {
			$table->id();

			$table->string('label', 255)->nullable();
			$table->dateTime('publication_date')->nullable();
			$table->boolean('active')->default(true);

			$table->timestamps();
		});

		Schema::create('gallery_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('gallery_id');

			$table->string('title')->nullable();
			$table->text('description')->nullable();

			$table->string('locale', 2)->index('g_tr_l_i');
			$table->unique(['gallery_id', DB::raw('`locale`(2)')], 'g_unique');
			$table->foreign('gallery_id', 'g_foreign')->references('id')->on('galleries')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('gallery_translations');
		Schema::dropIfExists('galleries');
	}
}
