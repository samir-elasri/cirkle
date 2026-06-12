<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function (Blueprint $table) {
			$table->id();

			$table->datetime('order_datetime')->nullable();
			$table->decimal('sub_total_price')->nullable();
			$table->decimal('discount_amount')->nullable();
			$table->decimal('shipping_price')->nullable();
			$table->decimal('tvq_price')->nullable();
			$table->decimal('tps_price')->nullable();
			$table->decimal('total_price')->nullable();
			$table->string('token')->nullable();
			$table->boolean('is_cart')->default(false);

			$table->foreignId('subscriber_id')
				->nullable()
				->constrained()
				->nullOnDelete()
				->comment('Eventually we want to be able to keep all informations of an order in a static manner.');

			$table->foreignId('price_cut_id')
				->nullable()
				->constrained()
				->nullOnDelete();

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
		Schema::drop('orders');
	}
}
