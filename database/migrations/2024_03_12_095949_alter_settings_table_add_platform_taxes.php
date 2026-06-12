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
        Schema::table('settings', static function (Blueprint $table) {
            $table->decimal('platform_tps')->nullable();
            $table->decimal('platform_tvq')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('settings', static function (Blueprint $table) {
			$table->dropColumn('platform_tps');
			$table->dropColumn('platform_tvq');
        });
    }
};
