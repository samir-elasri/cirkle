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
        Schema::create('subscriber_images', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->string('legend')->nullable();
			$table->string('image')->nullable();
			$table->integer('position')->nullable();
			$table->foreignId('subscriber_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

			$table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_images');
    }
};
