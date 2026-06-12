<?php

namespace Database\Seeders;

use App\Models\Core\Blocs\BlocText;
use App\Models\Core\MenuTree;
use App\Models\Core\Page;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;
use ModelUtility;
use StringUtility;
use Illuminate\Support\Arr;

class PageAndMenuTreeSeeder extends Seeder
{

	private $locale;
	private $locales;
	private $integratedPages;

	public function run()
	{
		$this->locale = app()->getLocale();
		$this->locales = getLocales();

		$this->integratedPages = config('routes.front-end');

		DB::table('pages')->delete();
		DB::table('menu_trees')->delete();

		$this->createTree(database_path('seeders/jsons/tree.json'));
	}

	public function createBlocs($page, $data)
	{
		foreach ($data['blocs'] ?? [] as $blocData) {
			$blocType = $blocData['blocType'];
			unset($blocData['blocType']);
			$blocClass = ModelUtility::getClassByCollectionName($blocType);

			if (!class_exists($blocClass)) {
				continue;
			}

			$bloc = new $blocClass;

			if ($bloc->save()) {
				$bloc->bloc()->create([
					'pageable_id'     => $page->id,
					'pageable_type'   => Page::class,
					'blocable_id'     => $bloc['id'],
					'blocable_type'   => $blocClass,
					'active'          => Arr::pull($blocData, 'active'),
					'label'           => Arr::pull($blocData, 'label'),
					'top_spacing'     => Arr::pull($blocData, 'top_spacing'),
					'half_width_mode' => Arr::pull($blocData, 'half_width_mode', 0),
				]);

				/** @var BlocText $bloc */
				$bloc->saveElement(gatherTranslatables($blocData), true);
			}
		}
	}

	private function createTree($filename)
	{
		$tree = json_decode(file_get_contents($filename), true);
		foreach ($tree as $name => $data) {
			$this->createMenu($name, $data);
		}
	}

	private function createMenu($name, $data, $parent = null)
	{
		$position = 0;
		foreach ($data as $item) {

			$position++;

			if ($this->isPage($item)) {
				$menu = $this->createPage($position, $item, $parent, $name);
			} else {
				$menu = $this->createItem($position, $item, $parent, $name);
			}

			if (isset($item['children'])) {
				$this->createMenu($name, $item['children'], $menu);
			}
		}
	}

	private function createPage($position, $data, $parent, $group)
	{
		foreach ($this->locales as $locale) {
			if (isset($data[$locale]['custom_url']) && $data[$locale]['custom_url'] == '*') {
				$data[$locale]['custom_url'] = '/' . StringUtility::sluggify($data[$locale]['title']);
			}
		}

		$data['label'] = $data['label'] ?? StringUtility::sluggify($data[$this->locale]['title']);
		$data['publication_date'] = Carbon::now();
		$data['created_at'] = Carbon::now();
		$data['active'] = true;
		$data['slideshow_id'] = $data['slideshow_id'] ?? null;

		if (Arr::exists($this->integratedPages, $data['label'])) {
			$data['integrated'] = true;
		}

		$page = null;
		if (empty($data['url'])) {
			$page = Page::create(gatherTranslatables($data));
		}

		if ($page) {
			$this->createBlocs($page, $data);
		}

		return MenuTree::create(array_merge(
			[
				'group'     => $group,
				'parent_id' => $parent ? $parent->id : null,
				'page_id'   => $page->id ?? null,
				'position'  => $position,
				'locked'    => false,
				'active'    => true
			],
			gatherTranslatables([
				'fr' => [
					'title' => $data['fr']['title'],
				],
				'en' => [
					'title' => $data['en']['title'],
				]
			])
		));
	}

	private function createItem($position, $data, $parent, $group)
	{

		return MenuTree::create(array_merge(
			[
				'group'     => $group,
				'parent_id' => $parent ? $parent->id : null,
				'page_id'   => null,
				'position'  => $position,
				'locked'    => false,
				'active'    => true
			],
			gatherTranslatables($data)
		));
	}

	private function isPage(&$data)
	{
		// Sous-niveau
		if (isset($data['sublevel'])) {
			unset($data['sublevel']);
			return false;
		}

		// Lien vers une page web
		foreach ($this->locales as $locale) {
			if (isset($data[$locale]['url'])) {
				return false;
			}
		}

		return true;
	}
}
