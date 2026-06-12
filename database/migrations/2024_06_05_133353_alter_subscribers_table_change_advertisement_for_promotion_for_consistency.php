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
		Schema::table('subscribers', static function (Blueprint $table) {
			$table->renameColumn('profile_advertisement_active', 'profile_promotion_active');
			$table->renameColumn('profile_advertisement_activation_datetime', 'profile_promotion_activation_datetime');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::table('subscribers', static function (Blueprint $table) {
			$table->renameColumn('profile_promotion_active', 'profile_advertisement_active');
			$table->renameColumn('profile_promotion_activation_datetime', 'profile_advertisement_activation_datetime');

		});
	}
};
