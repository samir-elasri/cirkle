<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSlidesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('slides', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->string('image')->nullable();
			$table->integer('position')->nullable();
			$table->string('filename_video')->nullable();
			$table->boolean('call_to_action_present')->default(false);
			$table->boolean('active')->default(true);


			$table->foreignId('slideshow_id')
				->nullable()
			->constrained()
			->cascadeOnDelete();

			$table->timestamps();
		});

		Schema::create('slide_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('slide_id');

			$table->string('title')->nullable();
			$table->string('mobile_title')->nullable();
			$table->text('content')->nullable();
			$table->string('call_to_action_label')->nullable();
			$table->string('call_to_action_label_mobile')->nullable();
			$table->string('call_to_action_url')->nullable();
			$table->string('action_label')->nullable();
			$table->string('action_url')->nullable();
			$table->text('sub_text')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['slide_id', DB::raw('`locale`(2)')], 'slide_unique');
			$table->foreign('slide_id')->references('id')->on('slides')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('slide_translations');
		Schema::dropIfExists('slides');
	}
}
