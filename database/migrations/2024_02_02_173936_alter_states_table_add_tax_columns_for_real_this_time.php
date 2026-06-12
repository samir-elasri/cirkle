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
        Schema::table('states', static function (Blueprint $table) {
			$table->float('tvp', 8, 3)->nullable();
			$table->float('tvh', 8, 3)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('states', static function (Blueprint $table) {
	        $table->dropColumn('tvp');
	        $table->dropColumn('tvh');
        });
    }
};
