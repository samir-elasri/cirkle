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
            $table->dropColumn('legal_form');
            $table->foreignId('legal_form_id')
                ->nullable()
                ->constrained('categories')
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
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->string('legal_form')->nullable();
            $table->dropConstrainedForeignId('service_category_id');
        });
    }
};
