<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\Gallery;
use App\Models\Core\GalleryElement;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class GalleriesTableSeeder extends Seeder
{

	public function run()
	{
		$faker = Faker::create('fr_CA');

		$gallery = Gallery::create([
			'label'            => 'Galerie',
			'title'            => 'Titre',
			'description'      => 'Description ' . $faker->sentence(10),
			'publication_date' => $faker->dateTimeThisYear(),
			'active'           => true,
			'created_at'       => $faker->dateTimeThisYear()
		]);


		foreach (range(1, 8) as $index2) {
			$filename = '';
			$typeElement = $faker->randomElement(array('img', 'local', 'youtube'));

			$filename_thumb = null;

			if ($typeElement === 'local') {
				$fileID = $faker->randomElement(range(1, 2));
				$filename = '/tests/mp4/' . $fileID . '.mp4';
				$filename_thumb = '/tests/mp4/' . $fileID . '.jpg';

			} elseif ($typeElement === 'img') {
				$filename = getRandomTestImage();

			} elseif ($typeElement === 'youtube') {
				$filename = $faker->randomElement(['P9-FCC6I7u0', 'xk-9btct8FU']);
			}

			GalleryElement::create([
				'gallery_id'       => $gallery->id,
				'type_element'     => $typeElement,
				'publication_date' => $faker->dateTimeThisYear(),
				'filename'         => $filename,
				'filename_thumb'   => $filename_thumb,
				'description'      => 'description: ' . $faker->sentence(10) . '(' . $typeElement . ')',
				'active'           => true,
				'position'         => $index2,
				'created_at'       => $faker->dateTimeThisYear()
			]);
		}
	}
}
