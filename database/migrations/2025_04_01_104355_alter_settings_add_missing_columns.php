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
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->string('legal_form')->nullable();
            $table->string('federal_tax_number')->nullable();
            $table->string('toll_free_phone')->nullable();
            $table->string('fax')->nullable();
            $table->date('start_date')->nullable();
            $table->string('insurance_coverage')->nullable();
            $table->string('business_hours')->nullable();
            $table->boolean('registration_completed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->dropColumn('legal_form');
            $table->dropColumn('federal_tax_number');
            $table->dropColumn('toll_free_phone');
            $table->dropColumn('fax');
            $table->dropColumn('start_date');
            $table->dropColumn('insurance_coverage');
            $table->dropColumn('business_hours');
            $table->dropColumn('registration_completed');
        });
    }
};
