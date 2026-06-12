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
        Schema::create('service_categories', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->string('label')->nullable();
			$table->string('image')->nullable();
			$table->foreignId('service_category_id')
				->nullable()
				->constrained('service_categories', 'id')
				->cascadeOnDelete();
			$table->boolean('active')->default(true);
        });

		Schema::create('service_category_translations', static function (Blueprint $table) {
            $table->id();
			$table->foreignId('service_category_id');

			$table->string('title')->nullable();
			$table->text('description')->nullable();
			$table->string('legend')->nullable();
			$table->text('provider_description')->nullable();
			$table->text('client_description')->nullable();

			$table->string('locale', 2)->index();
			$table->unique(['service_category_id', DB::raw('`locale`(2)')], 'service_category_unique');
			$table->foreign('service_category_id')->references('id')->on('service_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_categories');
        Schema::dropIfExists('service_category_translations');
    }
};
