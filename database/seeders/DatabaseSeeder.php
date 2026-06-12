<?php

namespace Database\Seeders;

use Arr;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(UsersTableSeeder::class);

		$this->call(GoogleMapsTableSeeder::class);

		$this->call(FormsTableSeeder::class);

		$this->call(MiniCardsTableSeeder::class);

		$this->call(DocumentsTableSeeder::class);

		$this->call(CategoriesTableSeeder::class);

		$this->call(PubsTableSeeder::class);

		$this->call(SlideshowsTableSeeder::class);

		$this->call(GalleriesTableSeeder::class);

		$this->call(SettingsTableSeeder::class);

		$this->call(PageAndMenuTreeSeeder::class);
	}
}
