<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_prices', static function (Blueprint $table) {
            $table->id();
			$table->timestamps();

	        $table->foreignId('subscription_id')
		        ->constrained()
		        ->cascadeOnDelete();

	        $table->integer('position')->nullable();
			$table->string('cost')->nullable();
			$table->integer('month_duration')->nullable();
			$table->integer('year_duration')->nullable();


			$table->boolean('active')->default(true);
        });

		Schema::create('subscription_price_translations', static function (Blueprint $table) {
			$table->id();

			$table->foreignId('subscription_price_id')
				->constrained()
				->cascadeOnDelete();

			$table->string('text')->nullable();

			$table->string('locale', 2)->index();

			$table->unique([
				'subscription_price_id',
				DB::raw('`locale`(2)')
			], 'subscription_price_locale');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_price_translations');
        Schema::dropIfExists('subscription_prices');
    }
};
