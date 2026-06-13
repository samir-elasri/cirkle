<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Option payante « Diplômes académiques » (WEB 11F, code PDIPOMECK, 50 $) — feature #9.
     * Chaque ligne : nom du cours/formation (title, traduit), nom officiel de l'école
     * (school), date d'obtention AN/MOIS (graduated_at). Calque sur le modèle License.
     */
    public function up(): void
    {
        Schema::create('diplomas', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('position')->nullable();
            $table->foreignId('subscriber_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('school')->nullable();        // nom officiel de l'école
            $table->string('graduated_at')->nullable();  // AN/MOIS (texte, fidèle au formulaire)

            $table->boolean('active')->default(true);
        });

        Schema::create('diploma_translations', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('diploma_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title')->nullable();        // nom du cours / de la formation
            $table->string('description')->nullable();

            $table->string('locale', 2)->index();

            $table->unique([
                'diploma_id',
                DB::raw('`locale`(2)')
            ], 'diploma_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diploma_translations');
        Schema::dropIfExists('diplomas');
    }
};
