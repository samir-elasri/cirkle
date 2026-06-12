<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->string('banner_image')->nullable();
			$table->integer('banner_height')->nullable();
			$table->integer('page_top_spacing')->nullable();
			$table->integer('footer_top_spacing')->nullable();
			$table->boolean('has_right_column')->default(false);
			$table->boolean('is_form_before_pubs')->default(false);
			$table->string('custom_code')->nullable();
			$table->dateTime('publication_date')->nullable();
			$table->boolean('restricted')->default(false);
			$table->boolean('integrated')->nullable();
			$table->boolean('active')->default(true);

			$table->foreignId('slideshow_id')
				->nullable()
				->constrained()
				->nullOnDelete();
			$table->foreignId('form_generator_id')
				->nullable()
				->constrained()
				->nullOnDelete();
			$table->foreignId('pub_group_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->timestamps();
		});

		Schema::create('page_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('page_id');

			$table->string('title')->nullable();
			$table->string('meta_title')->nullable();
			$table->text('meta_description')->nullable();
			$table->string('custom_url')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['page_id', DB::raw('`locale`(2)')], 'page_unique');
			$table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('page_translations');
		Schema::dropIfExists('pages');
	}
}
