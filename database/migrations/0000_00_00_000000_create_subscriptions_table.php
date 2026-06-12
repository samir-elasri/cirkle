<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscriptions', function (Blueprint $table) {
			$table->id();

			$table->string('identifier')->nullable();
			$table->integer('duration')->nullable();
			$table->string('date_type')->nullable();
			$table->date('date')->nullable();
			$table->boolean('prorate_costs')->default(false);
			$table->boolean('applicable_tvq')->default(false);
			$table->boolean('applicable_tps')->default(false);
			$table->integer('cost')->nullable();
			$table->boolean('buyable_online')->default(false);
			$table->integer('position')->nullable();
			$table->boolean('corpo')->default(false);

			$table->boolean('active')->default(true);
			$table->timestamps();
		});

		Schema::create('subscription_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('subscription_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['subscription_id', DB::raw('`locale`(2)')], 'sub_unique');
			$table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('subscription_translations');
		Schema::dropIfExists('subscriptions');
	}
}
