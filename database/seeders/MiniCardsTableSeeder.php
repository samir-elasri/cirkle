<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\MiniCardGroup;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class MiniCardsTableSeeder extends Seeder
{

	public function run()
	{
		$faker = Faker::create('fr_CA');
		$parent = MiniCardGroup::create([
			'label'        => 'Page standard',
			'width'        => 250,
			'image_height' => 200,
			'bg_color'     => 'lightgrey',
			'image_mode'   => 'cover',
			'active'       => true,
		]);

		$data = [
			'label'                  => 'Mini fiche',
			'align'                  => 'top',
			'call_to_action_present' => true,
			'active'                 => true,
		];

		$data_fr = [
			'fr' => [
				'title'                => 'Mini fiche',
				'sub_title'            => 'Sous titre',
				'text'                 => "<p>{$faker->sentence(10)}</p>",
				'image'                => '/dist/img/bloc_image.png',
				'call_to_action_label' => 'Voir plus',
				'call_to_action_url'   => '#',
			]
		];

		$data_en = [
			'en' => [
				'title'                => 'Mini card',
				'sub_title'            => 'Sub title',
				'text'                 => "<p>{$faker->sentence(10)}</p>",
				'image'                => '/dist/img/bloc_image.png',
				'call_to_action_label' => 'See more',
				'call_to_action_url'   => '#',
			]
		];

		foreach (getLocales() as $locale) {
			$var = 'data_' . $locale;

			$data = array_merge($data, $$var);
		}

		foreach (range(1, 4) as $i) {
			$parent->miniCards()->create($data);
		}
	}
}
