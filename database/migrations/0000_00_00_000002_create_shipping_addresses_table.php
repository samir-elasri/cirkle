<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShippingAddressesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shipping_addresses', function (Blueprint $table) {
			$table->id();

			$table->boolean('same_invoice')->default(false);
			$table->string('number')->nullable();
			$table->string('street')->nullable();
			$table->string('app')->nullable();
			$table->string('city')->nullable();
			$table->string('zip_code')->nullable();

			$table->foreignId('subscriber_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();
			$table->foreignId('state_id')
				->nullable()
				->constrained()
				->nullOnDelete();
			$table->foreignId('country_id')
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
		Schema::drop('shipping_addresses');
	}
}
