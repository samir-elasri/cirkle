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
        Schema::create('profile_options', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->string('label')->nullable();
			$table->integer('duration')->nullable();
			$table->decimal('price', 8, 2)->nullable();
			$table->string('type')->nullable();

			$table->boolean('active')->default(true);
        });

		Schema::create('profile_option_translations', static function (Blueprint $table) {
			$table->id();

			$table->foreignId('profile_option_id')
				->constrained()
				->cascadeOnDelete();

			$table->string('title')->nullable();
			$table->string('description')->nullable();

			$table->string('locale', 2)->index();

			$table->unique([
				'profile_option_id',
				DB::raw('`locale`(2)')
			], 'profile_option_locale');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_option_translations');
        Schema::dropIfExists('profile_options');
    }
};
