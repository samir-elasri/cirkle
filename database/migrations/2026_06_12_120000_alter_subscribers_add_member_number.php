<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Numéro de membre séquentiel partagé clients/fournisseurs (C02350, F02351, ...).
     * La séquence démarre à 2350. La lettre (C/F) est dérivée de is_provider à l'affichage;
     * seul le numéro est stocké. Voir Subscriber::MEMBER_NUMBER_START.
     */
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->unsignedInteger('member_number')->nullable()->unique()->after('id');
        });

        // Backfill déterministe des membres existants, du plus ancien au plus récent.
        $next = 2350;
        $ids = DB::table('subscribers')
            ->whereNull('member_number')
            ->orderBy('id')
            ->pluck('id');

        foreach ($ids as $id) {
            DB::table('subscribers')
                ->where('id', $id)
                ->update(['member_number' => $next++]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn('member_number');
        });
    }
};
