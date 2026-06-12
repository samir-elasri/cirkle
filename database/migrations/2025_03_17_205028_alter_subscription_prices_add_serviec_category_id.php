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
        Schema::table('subscription_prices', static function (Blueprint $table) {
            $table->foreignId('service_category_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('subscription_prices', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_category_id');
        });
    }
};
