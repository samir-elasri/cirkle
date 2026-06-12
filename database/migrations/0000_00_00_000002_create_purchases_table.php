<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePurchasesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchases', function (Blueprint $table) {
			$table->id();

			$table->string('purchase_type')->nullable();
			$table->string('item_name')->nullable();
			$table->integer('quantity')->default(0)->nullable();
			$table->decimal('unit_price')->default(0);
			$table->decimal('total_price')->default(0);

			$table->foreignId('order_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();

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
		Schema::drop('purchases');
	}
}
