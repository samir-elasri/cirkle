<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleryElementsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gallery_elements', function (Blueprint $table) {
			$table->id();

			$table->string('type_element', 50)->nullable();
			$table->boolean('use_fr')->default(0);
			$table->dateTime('publication_date')->nullable();
			$table->boolean('is_headline')->default(0);
			$table->integer('position')->nullable();
			$table->boolean('active')->default(true);


			$table->foreignId('gallery_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();

			$table->timestamps();
		});

		Schema::create('gallery_element_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('gallery_element_id');

			$table->string('title')->nullable();
			$table->string('filename')->nullable();
			$table->string('filename_thumb')->nullable();
			$table->text('description')->nullable();
			$table->text('legend')->nullable();

			$table->string('locale', 2)->index('ge_tr_l_i');
			$table->unique(['gallery_element_id', DB::raw('`locale`(2)')], 'ge_unique');
			$table->foreign('gallery_element_id',
				'ge_foreign')->references('id')->on('gallery_elements')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('gallery_element_translations');
		Schema::dropIfExists('gallery_elements');
	}
}
