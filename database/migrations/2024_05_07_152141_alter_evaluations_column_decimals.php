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
        Schema::table('evaluations', static function (Blueprint $table) {
            $table->decimal('global_grade', 4, 2)->nullable()->change();
            $table->decimal('service_quality_grade', 4, 2)->nullable()->change();
            $table->decimal('reliability_grade', 4, 2)->nullable()->change();
            $table->decimal('communication_grade', 4, 2)->nullable()->change();
            $table->decimal('hourly_rate_grade', 4, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('evaluations', static function (Blueprint $table) {
            $table->decimal('global_grade', 2)->nullable()->change();
            $table->decimal('service_quality_grade', 2)->nullable()->change();
            $table->decimal('reliability_grade', 2)->nullable()->change();
            $table->decimal('communication_grade', 2)->nullable()->change();
            $table->decimal('hourly_rate_grade', 2)->nullable();        })->change();
    }
};
