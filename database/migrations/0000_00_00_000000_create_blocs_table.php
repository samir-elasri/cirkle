<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlocsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blocs', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();

			$table->foreignId('pageable_id')->nullable();
			$table->string('pageable_type');

			$table->foreignId('blocable_id')->nullable();
			$table->string('blocable_type');

			$table->foreignId('top_spacing')->nullable();
			$table->string('title_color')->nullable();
			$table->string('bg_color')->nullable();
			$table->boolean('half_width_mode')->default(false);
			$table->boolean('bg_bleed')->default(false);

			$table->integer('position')->nullable();
			$table->boolean('active')->default(true);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('blocs');
	}
}
