<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocTextsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_texts', function (Blueprint $table) {
			$table->id();

			$table->string('align')->nullable();
			$table->string('media_type')->nullable();
			$table->integer('google_map_zoom')->nullable();
			$table->string('relation')->nullable();
			$table->string('bg_color')->nullable();
			$table->integer('top_spacing')->nullable();
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->boolean('half_width_mode')->default(false);
			$table->boolean('accordion')->default(false);
			$table->boolean('call_to_action_present')->default(false);

			$table->foreignId('google_map_group_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->timestamps();
		});

		Schema::create('bloc_text_translations', function (Blueprint $table) {
			$table->id();

			$table->foreignId('bloc_text_id');
			$table->string('label')->nullable();
			$table->string('title')->nullable();
			$table->text('content')->nullable();
			$table->text('summary')->nullable();
			$table->string('image')->nullable();
			$table->string('back_image')->nullable();
			$table->string('video_url')->nullable();
			$table->string('video_filename')->nullable();
			$table->string('alt')->nullable();
			$table->string('legend')->nullable();
			$table->string('call_to_action_label')->nullable();
			$table->string('call_to_action_url')->nullable();

			$table->string('locale', 2)->index('bt_tr_l_i');
			$table->unique(['bloc_text_id', DB::raw('`locale`(2)')], 'bt_unique');
			$table->foreign('bloc_text_id', 'bt_foreign')->references('id')->on('bloc_texts')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_text_translations');
		Schema::dropIfExists('bloc_texts');
	}
}
