<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SYSTEME DE TIRAGE (Denis/Steve 05.08) : 1) membres CLIENTS, 2) FOURNISSEURS,
 * 3) CLIENTS + FOURNISSEURS. Chaque tirage garde ses gagnants (numero de membre
 * fige au moment du tirage) pour l'historique.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('pool', 20);           // clients | providers | both
            $table->unsignedSmallInteger('winners_count')->default(1);
            $table->text('note')->nullable();
            $table->timestamp('drawn_at')->nullable();
            $table->unsignedInteger('eligible_count')->default(0);
            $table->timestamps();
        });

        Schema::create('draw_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained('draws')->cascadeOnDelete();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->string('member_number')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->unsignedSmallInteger('position')->default(1);
            $table->timestamps();
            $table->index('draw_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draw_winners');
        Schema::dropIfExists('draws');
    }
};
