<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiniCardsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mini_cards', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->string('align')->nullable();
			$table->boolean('call_to_action_present')->default(false);
			$table->integer('position')->nullable();
			$table->boolean('active')->default(true);

			$table->foreignId('mini_card_group_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();

			$table->timestamps();
		});

		Schema::create('mini_card_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('mini_card_id');

			$table->string('image')->nullable();
			$table->string('title')->nullable();
			$table->string('sub_title')->nullable();
			$table->text('text')->nullable();
			$table->string('call_to_action_label')->nullable();
			$table->string('call_to_action_url')->nullable();

			$table->string('locale', 2)->index('mc_tr_l_i');
			$table->unique(['mini_card_id', DB::raw('`locale`(2)')], 'mc_unique');
			$table->foreign('mini_card_id', 'mc_foreign')->references('id')->on('mini_cards')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('mini_card_translations');
		Schema::dropIfExists('mini_cards');
	}
}
