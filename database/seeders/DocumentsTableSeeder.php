<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\Document;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DocumentsTableSeeder extends Seeder
{
	public function run()
	{
		$faker = Faker::create('fr_CA');

		$data = [
			'label' => 'Page standard',
			'date' => $faker->dateTimeThisYear(),
			'active' => true,
			'fr' => [
				'title' => 'Titre',
				'filename' => '/dist/tests/logos/1.png',
				'description' => $faker->sentence(10)
			],
			'en' => [
				'title' => 'Titre',
				'filename' => '/dist/tests/logos/1.png',
				'description' => $faker->sentence(10)
			],
		];

		$data = gatherTranslatables($data);

		Document::create($data);
	}
}
