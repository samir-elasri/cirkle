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
        Schema::create('purchased_sub_records', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->boolean('on_pause')->nullable();
			$table->foreignId('subscription_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

			$table->foreignId('order_id')
            	->nullable()
            	->constrained()
            	->nullOnDelete();

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
        Schema::dropIfExists('purchased_sub_records');
    }
};
