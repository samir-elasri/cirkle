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
        Schema::create('services', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->string('label')->nullable();
			$table->string('image')->nullable();
			$table->string('legend')->nullable();
			$table->foreignId('service_category_id')
            	->nullable()
            	->constrained('service_categories')
            	->nullOnDelete();

			$table->boolean('active')->default(true);
        });
		Schema::create('service_translations', static function (Blueprint $table) {
            $table->id();
			$table->foreignId('service_id');

			$table->string('title')->nullable();
			$table->string('description')->nullable();


			$table->string('locale', 2)->index();
			$table->unique(['service_id', DB::raw('`locale`(2)')], 'service_unique');
			$table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_translations');
    }
};
