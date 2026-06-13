<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Évaluations façon Google (feature #10) : réponse du fournisseur à un avis.
     * La réponse n'apparaît publiquement qu'une fois approuvée par un admin (feature #14).
     * Note globale = global_grade (déjà la moyenne affichée). Les sous-notes restent
     * en base mais ne sont plus requises.
     */
    public function up(): void
    {
        Schema::table('evaluations', static function (Blueprint $table) {
            $table->text('reply')->nullable()->after('comment');
            $table->boolean('reply_approved')->default(false)->after('reply');
            $table->dateTime('reply_created_at')->nullable()->after('reply_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', static function (Blueprint $table) {
            $table->dropColumn(['reply', 'reply_approved', 'reply_created_at']);
        });
    }
};
