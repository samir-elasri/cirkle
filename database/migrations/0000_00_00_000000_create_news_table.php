<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->string('news_type')->default('news');
			$table->date('official_date')->nullable();
			$table->date('publication_date')->nullable();

			$table->boolean('active')->default(true);
			$table->timestamps();
		});

		Schema::create('news_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('news_id');

			$table->string('title')->nullable();
			$table->string('image')->nullable();
			$table->string('legend')->nullable();
			$table->text('description')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['news_id', DB::raw('`locale`(2)')], 'news_locale');
			$table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('news_translations');
		Schema::dropIfExists('news');
	}
}
