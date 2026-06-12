<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
	{
		Schema::table('setting_translations', static function (Blueprint $table) {
			$table->string('customer_evaluation_title')->nullable();
			$table->text('customer_evaluation_text')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::table('setting_translations', static function (Blueprint $table) {
			$table->dropColumn('customer_evaluation_title');
			$table->dropColumn('customer_evaluation_text');
		});
	}
};
