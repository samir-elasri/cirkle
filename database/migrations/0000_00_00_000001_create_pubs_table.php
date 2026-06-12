<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePubsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pubs', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->integer('position')->nullable();
			$table->boolean('active')->default(true);

			$table->foreignId('pub_group_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();

			$table->timestamps();
		});

		Schema::create('pub_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('pub_id');

			$table->string('title')->nullable();
			$table->string('pub_image')->nullable();
			$table->text('content')->nullable();
			$table->string('url')->nullable();
			$table->boolean('isTargetBlank')->default(false);

			$table->string('locale', 2)->index();
			$table->unique(['pub_id', DB::raw('`locale`(2)')], 'pub_unique');
			$table->foreign('pub_id')->references('id')->on('pubs')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('pub_translations');
		Schema::dropIfExists('pubs');
	}
}
