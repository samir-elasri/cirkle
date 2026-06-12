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
        Schema::create('licenses', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->integer('position')->nullable();
			$table->foreignId('subscriber_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

			$table->boolean('active')->default(true);
        });

		Schema::create('license_translations', static function (Blueprint $table) {
			$table->id();

			$table->foreignId('license_id')
				->constrained()
				->cascadeOnDelete();

			$table->string('title')->nullable();
			$table->string('description')->nullable();

			$table->string('locale', 2)->index();

			$table->unique([
				'license_id',
				DB::raw('`locale`(2)')
			], 'license_locale');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_translations');
        Schema::dropIfExists('licenses');
    }
};
