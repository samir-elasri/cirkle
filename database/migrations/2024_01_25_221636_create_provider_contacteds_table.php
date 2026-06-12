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
        Schema::create('contacted_providers', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

	        $table->foreignId('client_id')
		        ->nullable()
		        ->constrained('subscribers')
		        ->nullOnDelete();

	        $table->foreignId('provider_id')
		        ->nullable()
		        ->constrained('subscribers')
		        ->nullOnDelete();

	        $table->boolean('evaluation_mail_sent');
	        $table->boolean('deal_made');


			$table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacted_providers');
    }
};
