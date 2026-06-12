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
        Schema::create('subscriber_services', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->foreignId('service_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

			$table->foreignId('subscriber_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

			$table->boolean('active')->default(true);
        });

		Schema::create('subscriber_service_translations', static function (Blueprint $table) {
			$table->id();

			$table->foreignId('subscriber_service_id')
				->constrained()
				->cascadeOnDelete();

			$table->text('description')->nullable();

			$table->string('locale', 2)->index();

			$table->unique([
				'subscriber_service_id',
				DB::raw('`locale`(2)')
			], 'subscriber_service_locale');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_service_translations');
        Schema::dropIfExists('subscriber_services');
    }
};
