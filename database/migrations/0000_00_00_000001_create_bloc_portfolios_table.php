<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocPortfoliosTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_portfolios', function (Blueprint $table) {
			$table->id();

			$table->foreignId('gallery_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->timestamp('created_at')->nullable();
			$table->timestamp('updated_at')->nullable();
		});

		Schema::create('bloc_portfolio_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_portfolio_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index('bp_tr_l_i');
			$table->unique(['bloc_portfolio_id', DB::raw('`locale`(2)')], 'bp_unique');
			$table->foreign('bloc_portfolio_id',
				'bp_id_foreign')->references('id')->on('bloc_portfolios')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_portfolio_translations');
		Schema::dropIfExists('bloc_portfolios');
	}
}
