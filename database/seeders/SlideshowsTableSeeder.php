<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\Slide;
use App\Models\Core\Slideshow;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SlideshowsTableSeeder extends Seeder
{

	public function run()
	{
		$faker = Faker::create('fr_CA');

		foreach (range(1, 2) as $index) {
			Slideshow::create([
				'label' => 'Diaporama ' . $index,
				'slideshow_height' => 200,
				'created_at' => $faker->dateTimeThisYear()
			]);

			foreach (range(1, 12) as $index2) {

				$filename = '';
				$type = $faker->randomElement(array('image', 'video'));

				if ($type == 'video') {
					$filename = $faker->randomElement(array('/tests/mp4/1.mp4', '/tests/mp4/2.mp4'));
				}

				$data = [
					'slideshow_id' 		=> $index,
					'position' 			=> $index2,
					'active' 			=> $faker->boolean(90),
					'created_at' 		=> $faker->dateTimeThisYear(),
					'image' 			=> getRandomTestImage(),
					'filename_video'	=> $filename,

					'fr' => [
						'title' 		=> 'Titre ' . $index . '_' . $index2,
						'content' 		=> $faker->realText(600),
						'action_label'  => $faker->sentence(3),
						'action_url'    => $faker->url()
					],
					'en' => [
						'title' 		=> 'Title ' . $index . '_' . $index2,
						'content' 		=> $faker->realText(600),
						'action_label'  => $faker->sentence(3),
						'action_url'    => $faker->url()
					]
				];

				Slide::create(gatherTranslatables($data));
			}
		}
	}
}
