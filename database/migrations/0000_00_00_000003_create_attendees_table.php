<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendeesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendees', function (Blueprint $table) {
			$table->id();

			$table->date('register_date')->nullable();
			$table->foreignId('order_id')->nullable();
			$table->boolean('active')->default(true);

			$table->foreignId('basic_event_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();
			$table->foreignId('subscriber_id')
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
		Schema::dropIfExists('attendees');
	}
}
