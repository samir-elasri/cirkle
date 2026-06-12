<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiniCardGroupsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mini_card_groups', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->integer('width')->nullable();
			$table->string('bg_color')->nullable();
			$table->boolean('active')->default(true);
			$table->integer('image_height')->nullable();
			$table->string('image_mode')->nullable();

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mini_card_groups');
	}
}
