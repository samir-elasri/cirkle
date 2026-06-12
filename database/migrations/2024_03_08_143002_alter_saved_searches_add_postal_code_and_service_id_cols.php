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
        Schema::table('saved_searches', static function (Blueprint $table) {
            $table->string('postal_code')->nullable();
			$table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('saved_searches', static function (Blueprint $table) {
            $table->dropColumn('postal_code');
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
