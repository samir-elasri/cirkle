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
        Schema::table('setting_translations', static function (Blueprint $table) {
            $table->string('search_notification_title')->nullable();
            $table->text('search_notification_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('setting_translations', static function (Blueprint $table) {
            $table->dropColumn('search_notification_title');
            $table->dropColumn('search_notification_text');
        });
    }
};
