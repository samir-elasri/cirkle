<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicEventsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('basic_events', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->datetime('start_datetime')->nullable();
			$table->datetime('end_datetime')->nullable();
			$table->string('categories')->nullable();
			$table->boolean('online_register')->default(false);
			$table->decimal('non_sub_price')->nullable()->default(0);
			$table->decimal('sub_price')->nullable()->default(0);
			$table->boolean('applicable_tvq')->default(false);
			$table->boolean('applicable_tps')->default(false);
			$table->integer('available_places')->nullable()->default(0);
			$table->boolean('reserved_member')->default(false);
			$table->string('number')->nullable();
			$table->string('street')->nullable();
			$table->string('app')->nullable();
			$table->string('city')->nullable();
			$table->string('zip_code')->nullable();
			$table->text('email_text')->nullable();
			$table->boolean('active')->default(true);

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

		Schema::create('basic_event_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('basic_event_id');

			$table->string('title')->nullable();
			$table->string('image')->nullable();
			$table->string('legend')->nullable();
			$table->text('description')->nullable();
			$table->string('email_title')->nullable();
			$table->text('email_text')->nullable();

			$table->string('locale', 2)->index('e_tr_l_i');
			$table->unique(['basic_event_id', DB::raw('`locale`(2)')], 'e_unique');
			$table->foreign('basic_event_id', 'cd_foreign')->references('id')->on('basic_events')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('basic_event_translations');
		Schema::dropIfExists('basic_events');
	}
}
