<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePurchasedSubsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchased_subs', function (Blueprint $table) {
			$table->id();

			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->boolean('active')->default(true);
			$table->boolean('validated')->default(false);
			$table->date('record_date')->nullable();
			$table->string('domain')->nullable();

			$table->foreignId('subscriber_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();
			$table->foreignId('subscription_id')
				->nullable()
				->constrained()
				->nullOnDelete();
			$table->foreignId('order_id')
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
		Schema::drop('purchased_subs');
	}
}
