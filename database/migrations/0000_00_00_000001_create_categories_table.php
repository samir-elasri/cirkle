<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function (Blueprint $table) {
			$table->id();

			$table->foreignId('category_group_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->string('identifier')->nullable();
			$table->integer('position')->nullable();

			$table->boolean('active')->default(true);
			$table->timestamps();
		});

		Schema::create('category_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('category_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['category_id', DB::raw('`locale`(2)')], 'category_unique');
			$table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// ??
		Schema::dropIfExists('associate_pages_categories');
		Schema::dropIfExists('associate_calendar_events_categories');
		Schema::dropIfExists('associate_basic_events_categories');
		Schema::dropIfExists('associate_documents_categories');
		Schema::dropIfExists('associate_news_categories');

		Schema::drop('category_translations');
		Schema::drop('categories');
	}
}
