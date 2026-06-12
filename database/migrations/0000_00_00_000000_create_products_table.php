<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->integer('category')->nullable();
			$table->boolean('manage_inventory')->default(false);
			$table->integer('quantity_left')->nullable();
			$table->decimal('base_price')->nullable();
			$table->integer('current_discount')->nullable();
			$table->date('discount_end_date')->nullable();
			$table->boolean('promotion')->default(false);
			$table->boolean('active')->default(true);

			$table->timestamps();
		});

		Schema::create('product_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('product_id');

			$table->string('title')->nullable();
			$table->text('description')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['product_id', DB::raw('`locale`(2)')], 'product_unique');
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_translations');
		Schema::drop('products');
	}
}
