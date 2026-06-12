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
        Schema::table('purchased_subs', static function (Blueprint $table) {
            $table->date('pause_start_date')->nullable();
            $table->date('pause_end_date')->nullable();
            $table->boolean('on_pause')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchased_subs', static function (Blueprint $table) {
	        $table->dropColumn('pause_start_date');
	        $table->dropColumn('pause_end_date');
	        $table->dropColumn('on_pause');
        });
    }
};
