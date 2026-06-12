<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->integer('max_postal_codes')->nullable()->default(0);
			$table->string('type')->nullable();
			$table->boolean('is_recommended')->nullable()->default(false);
        });

        Schema::table('subscription_translations', static function (Blueprint $table) {
			$table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('subscriptions', static function (Blueprint $table) {
	        $table->dropColumn('max_postal_codes');
	        $table->dropColumn('type');
	        $table->dropColumn('is_recommended');
        });

	    Schema::table('subscription_translations', static function (Blueprint $table) {
		    $table->dropColumn('description');
	    });
    }
};
