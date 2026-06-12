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
            $table->string('home_client_advantage_title1')->nullable();
            $table->string('home_client_advantage_title2')->nullable();
            $table->text('home_client_advantage_content')->nullable();
            $table->string('home_client_link1_url')->nullable();
            $table->string('home_client_link1_label')->nullable();
            $table->string('home_client_link2_url')->nullable();
            $table->string('home_client_link2_label')->nullable();
            $table->string('home_provider_advantage_title1')->nullable();
            $table->string('home_provider_advantage_title2')->nullable();
            $table->text('home_provider_advantage_content')->nullable();
            $table->string('home_provider_link1_url')->nullable();
            $table->string('home_provider_link1_label')->nullable();
            $table->string('home_provider_link2_url')->nullable();
            $table->string('home_provider_link2_label')->nullable();
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
            $table->dropColumn('home_client_advantage_title1');
            $table->dropColumn('home_client_advantage_title2');
            $table->dropColumn('home_client_advantage_content');
            $table->dropColumn('home_client_link1_url');
            $table->dropColumn('home_client_link1_label');
            $table->dropColumn('home_client_link2_url');
            $table->dropColumn('home_client_link2_label');
            $table->dropColumn('home_provider_advantage_title1');
            $table->dropColumn('home_provider_advantage_title2');
            $table->dropColumn('home_provider_advantage_content');
            $table->dropColumn('home_provider_link1_url');
            $table->dropColumn('home_provider_link1_label');
            $table->dropColumn('home_provider_link2_url');
            $table->dropColumn('home_provider_link2_label');
        });
    }
};
