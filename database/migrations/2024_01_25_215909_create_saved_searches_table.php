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
        Schema::create('saved_searches', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->boolean('obsolete')->default(false);

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
        Schema::dropIfExists('saved_searches');
    }
};
