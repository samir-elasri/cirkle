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
        Schema::create('liked_subscribers', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->foreignId('subscriber_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

			$table->foreignId('liked_subscriber_id')
            	->nullable()
            	->constrained('subscribers')
            	->nullOnDelete();

			$table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liked_subscribers');
    }
};
