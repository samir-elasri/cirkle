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
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('registration_fee', 10, 2)->default(0.00)->after('url_month_duration');
        });

        Schema::table('setting_translations', function (Blueprint $table) {
            $table->string('registration_fee_title')->nullable()->after('url_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('registration_fee');
        });

        Schema::table('setting_translations', function (Blueprint $table) {
            $table->dropColumn('registration_fee_title');
        });
    }
};