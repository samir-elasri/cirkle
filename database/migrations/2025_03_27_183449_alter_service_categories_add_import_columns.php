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
        Schema::table('service_category_translations', static function (Blueprint $table) {
            $table->text('customers_text')->nullable();
            $table->text('capabilities_text')->nullable();
            $table->text('keywords_json')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('service_category_translations', static function (Blueprint $table) {
            $table->dropColumn('customers_text');
            $table->dropColumn('capabilities_text');
            $table->dropColumn('keywords_json');
        });
    }
};
