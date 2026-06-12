<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceAddressesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoice_addresses', function (Blueprint $table) {
			$table->id();

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

			$table->string('number')->nullable();
			$table->string('street')->nullable();
			$table->string('app')->nullable();
			$table->string('city')->nullable();
			$table->string('zip_code')->nullable();

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
		Schema::drop('invoice_addresses');
	}
}
