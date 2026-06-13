<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Feature #11 :
     * - liked_professions : favoris « profession » d'un client (cœur sur une profession),
     *   en complément des favoris fournisseurs (liked_subscribers, déjà présents).
     * - consultation_histories : historique des fiches consultées par un client
     *   (remplace l'auto-suivi « fournisseur contacté » abandonné).
     */
    public function up(): void
    {
        Schema::create('liked_professions', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('subscriber_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('active')->default(true);
        });

        Schema::create('consultation_histories', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('subscriber_id')->nullable()->constrained()->cascadeOnDelete();          // le client
            $table->foreignId('viewed_subscriber_id')->nullable()->constrained('subscribers')->cascadeOnDelete(); // le fournisseur consulté
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_histories');
        Schema::dropIfExists('liked_professions');
    }
};
