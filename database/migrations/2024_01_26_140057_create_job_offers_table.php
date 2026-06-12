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
        Schema::create('job_offers', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->boolean('currently_recruiting')->nullable()->default(false);
			$table->foreignId('subscriber_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

			$table->boolean('active')->default(true);
        });

		Schema::create('job_offer_translations', static function (Blueprint $table) {
			$table->id();

			$table->foreignId('job_offer_id')
				->constrained()
				->cascadeOnDelete();

			$table->string('title')->nullable();
			$table->text('description')->nullable();

			$table->string('locale', 2)->index();

			$table->unique([
				'job_offer_id',
				DB::raw('`locale`(2)')
			], 'job_offer_locale');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offer_translations');
        Schema::dropIfExists('job_offers');
    }
};
