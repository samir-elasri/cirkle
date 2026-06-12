<?php

namespace Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Arr;
use Carbon\Carbon;
use App\Models\Core\Category;
use App\Models\Core\CategoryGroup;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
	public function run()
	{
		$cats = [
			[
				'identifier' => 'news',
				'title' => 'Nouvelles et communiqués',
				'elements' => [
					[
						'fr' => [
							'title' => 'Type 1',
						],
						'en' => [
							'title' => 'Type 1'
						],
					],
					[
						'fr' => [
							'title' => 'Type 2'
						],
						'en' => [
							'title' => 'Type 2'
						]
					]
				]
			],
			[
				'identifier' => 'languages',
				'title' => 'Langues',
				'elements' =>
					[
						[
							'fr' => [
								'title' => 'Francais',
							],
							'en' => [
								'title' => 'French'
							],
						],
						[
							'fr' => [
								'title' => 'Anglais'
							],
							'en' => [
								'title' => 'English'
							]
						]
					]
			],
		];

		foreach ($cats as $index => $cat) {

			CategoryGroup::create([
				'identifier' => Arr::get($cat, 'identifier', ''),
				'title' => Arr::get($cat, 'title'),
				'created_at' => Carbon::now()
			]);

			$count = 0;
			foreach ($cat['elements'] as $val) {
				$data = array_merge(
					[
						'category_group_id' => $index + 1,
						'identifier' => '',
						'position' => $count,
						'active' => true,
						'created_at' => Carbon::now()
					],
					gatherTranslatables($val)
				);

				$count++;
				Category::create($data);
			}
		}
	}
}
