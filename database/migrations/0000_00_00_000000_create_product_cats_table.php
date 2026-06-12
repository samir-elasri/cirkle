<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductCatsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_cats', function (Blueprint $table) {
			$table->id();

			$table->string('identifier')->nullable();
			$table->boolean('applicable_tvq')->default(false);
			$table->boolean('applicable_tps')->default(false);
			$table->boolean('active')->default(true);

			$table->foreignId('parent_id')
				->nullable()
				->constrained('product_cats')
				->cascadeOnDelete();

			$table->timestamps();
		});

		Schema::create('product_cat_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('product_cat_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['product_cat_id', DB::raw('`locale`(2)')], 'product_cat_unique');
			$table->foreign('product_cat_id')->references('id')->on('product_cats')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_cat_translations');
		Schema::drop('product_cats');
	}
}
