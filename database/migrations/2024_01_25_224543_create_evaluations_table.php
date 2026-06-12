<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluations', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->foreignId('provider_id')->nullable()->constrained('subscribers')->nullOnDelete();
			$table->foreignId('client_id')->nullable()->constrained('subscribers')->nullOnDelete();
			$table->decimal('global_grade', 2)->nullable();
			$table->decimal('service_quality_grade', 2)->nullable();
			$table->decimal('reliability_grade', 2)->nullable();
			$table->decimal('communication_grade', 2)->nullable();
			$table->decimal('hourly_rate_grade', 2)->nullable();
			$table->text('comment')->nullable();

			$table->boolean('insulting')->nullable()->default(false);
			$table->boolean('validated')->nullable()->default(false);
			$table->boolean('treated')->nullable()->default(false);

			$table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
