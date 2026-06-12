<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\GoogleMapGroup;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class GoogleMapsTableSeeder extends Seeder
{

	public function run()
	{
		$faker = Faker::create('fr_CA');
		$parent = GoogleMapGroup::create([
			'label' => 'Page standard',
			'active' => true,
		]);

		$data = [
			'label' => 'Point pour page standard',
			'image' => '/dist/img/logo-mbiance-blue.png',
			'latitude' => '45.528714',
			'longitude' => '-73.6150547',
			'active' => true,
			'fr' => [
				'title' => 'Mbiance',
				'text'	=> 'Conception de sites web et applications mobiles',
				'url_label' => 'Site',
				'url' => 'https://mbiance.com'
			],
			'en' => [
				'title' => 'Mbiance',
				'text'	=> 'Web sites and mobile applications',
				'url_label' => 'Website',
				'url' => 'https://mbiance.com',
			]
		];

		$data = gatherTranslatables($data);

		$child = $parent->googleMaps()->create($data);
	}
}
