<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\Pub;
use App\Models\Core\PubGroup;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PubsTableSeeder extends Seeder
{

	public function run()
	{
		$faker = Faker::create('fr_CA');

		foreach (range(1, 2) as $index) {

			PubGroup::create(['label' => 'Campagne de pubs ' . $index]);
			$nb = rand(7, 10);

			foreach (range(1, $nb) as $index2) {
				$data = [
					'fr' => [
						'title' => 'Titre - ' . $index . '_' . $index2,
						'pub_image'	=> (rand(0, 100) > 50) ? getRandomTestImage() : '',
						'content' => (rand(0, 100) > 50) ? 'Content - ' . $faker->realtext(rand(20, 100))  : '',
						'url' => (rand(0, 100) > 50) ? $faker->url() : '',
						'isTargetBlank'	=> $faker->boolean(50),
					],
					'label' 		=> 'Pub ' . $index . '_' . $index2,
					'pub_group_id'	=> $index,
					'position' 		=> $index2,
					'active' 		=> $faker->boolean(90),
					'created_at'	=> $faker->dateTimeThisYear(),
				];
				Pub::create(gatherTranslatables($data));
			}
		}
	}
}
