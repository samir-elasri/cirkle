<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenuTreesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menu_trees', function (Blueprint $table) {
			$table->id();

			$table->string('group', 150)->nullable();
			$table->boolean('locked')->default(false);
			$table->integer('position')->nullable();
			$table->string('identifier')->nullable();
			$table->boolean('active')->default(true);


			$table->foreignId('parent_id')
				->nullable()
				->constrained('menu_trees')
				->cascadeOnDelete();
			$table->foreignId('page_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->timestamps();
		});

		Schema::create('menu_tree_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('menu_tree_id');

			$table->string('title')->nullable();
			$table->string('url')->nullable();
			$table->boolean('target_blank')->default(false)->nullable();

			$table->string('locale', 2)->index('mt_tr_l_i');
			$table->unique(['menu_tree_id', DB::raw('`locale`(2)')], 'mt_unique');
			$table->foreign('menu_tree_id', 'mt_foreign')->references('id')->on('menu_trees')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('menu_tree_translations');
		Schema::dropIfExists('menu_trees');
	}
}
