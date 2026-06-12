<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChoicesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('choices', function (Blueprint $table) {
			$table->id();
			$table->timestamps();

			$table->string('label')->nullable();
			$table->string('code_value')->nullable();
			$table->boolean('other')->nullable();
			$table->integer('position')->nullable();
			$table->boolean('active')->default(true);

			$table->foreignId('choice_group_id')
				->nullable()
				->constrained()
				->nullOnDelete();

		});

		Schema::create('choice_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('choice_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index('c_tr_l_i');
			$table->unique(['choice_id', DB::raw('`locale`(2)')], 'c_unique');
			$table->foreign('choice_id', 'c_foreign')->references('id')->on('choices')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('choice_translations');
		Schema::dropIfExists('choices');
	}
}
