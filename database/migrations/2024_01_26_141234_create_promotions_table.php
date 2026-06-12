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
        Schema::create('promotions', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->string('image')->nullable();
			$table->string('legend')->nullable();
			$table->boolean('in_progress')->nullable()->default(false);
			$table->foreignId('subscriber_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

			$table->boolean('active')->default(true);
        });

		Schema::create('promotion_translations', static function (Blueprint $table) {
			$table->id();

			$table->foreignId('promotion_id')
				->constrained()
				->cascadeOnDelete();

			$table->string('title')->nullable();
			$table->string('description')->nullable();

			$table->string('locale', 2)->index();

			$table->unique([
				'promotion_id',
				DB::raw('`locale`(2)')
			], 'promotion_locale');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_translations');
        Schema::dropIfExists('promotions');
    }
};
