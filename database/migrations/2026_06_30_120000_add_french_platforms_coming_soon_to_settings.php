<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'french_platforms_coming_soon')) {
                // true = plateformes françaises affichées « À VENIR » (par défaut, Denis 30.06)
                $table->boolean('french_platforms_coming_soon')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'french_platforms_coming_soon')) {
                $table->dropColumn('french_platforms_coming_soon');
            }
        });
    }
};
