<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocMiniCardsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_mini_cards', function (Blueprint $table) {
			$table->id();

			$table->integer('items_per_row')->default(0);
			$table->boolean('call_to_action_present')->default(false);

			$table->foreignId('mini_card_group_id')
				->nullable()
				->constrained()
				->nullOnDelete();

			$table->timestamps();
		});

		Schema::create('bloc_mini_card_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_mini_card_id');

			$table->string('title')->nullable();
			$table->text('content')->nullable();
			$table->string('call_to_action_label')->nullable();
			$table->string('call_to_action_url')->nullable();

			$table->string('locale', 2)->index('bmc_tr_l_i');
			$table->unique(['bloc_mini_card_id', DB::raw('`locale`(2)')], 'bmc_unique');
			$table->foreign('bloc_mini_card_id',
				'bmc_foreign')->references('id')->on('bloc_mini_cards')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_mini_card_translations');
		Schema::dropIfExists('bloc_mini_cards');
	}
}
