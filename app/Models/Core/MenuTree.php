<?php

namespace App\Models\Core;

use App\Models\Translations\MenuTreeTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Arr;
use Cache;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Request;
use RoutingUtility;
use View;

/**
 * App\Models\Core\MenuTree
 *
 * @property int $id
 * @property string|null $group
 * @property int $locked
 * @property int|null $position
 * @property string|null $identifier
 * @property int $active
 * @property int|null $parent_id
 * @property int|null $page_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, MenuTree> $children
 * @property-read int|null $children_count
 * @property-read mixed $collection_name
 * @property-read bool $has_children
 * @property-read bool $has_parent
 * @property-read bool $is_short_cut
 * @property-read SearchResult $search_result
 * @property-read mixed $url_site
 * @property-read Page|null $page
 * @property-read MenuTree|null $parent
 * @property-read MenuTreeTranslation|null $translation
 * @property-read Collection<int, MenuTreeTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $url
 * @property int|null $target_blank
 * @method static Builder|Model active()
 * @method static Builder|MenuTree listsTranslations(string $translationField)
 * @method static Builder|MenuTree newModelQuery()
 * @method static Builder|MenuTree newQuery()
 * @method static Builder|MenuTree notTranslatedIn(?string $locale = null)
 * @method static Builder|MenuTree orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|MenuTree orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|MenuTree orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|MenuTree query()
 * @method static Builder|MenuTree translated()
 * @method static Builder|MenuTree translatedIn(?string $locale = null)
 * @method static Builder|MenuTree whereActive($value)
 * @method static Builder|MenuTree whereCreatedAt($value)
 * @method static Builder|MenuTree whereGroup($value)
 * @method static Builder|MenuTree whereId($value)
 * @method static Builder|MenuTree whereIdentifier($value)
 * @method static Builder|MenuTree whereLocked($value)
 * @method static Builder|MenuTree wherePageId($value)
 * @method static Builder|MenuTree whereParentId($value)
 * @method static Builder|MenuTree wherePosition($value)
 * @method static Builder|MenuTree whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|MenuTree whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|MenuTree whereUpdatedAt($value)
 * @method static Builder|MenuTree withTranslation()
 * @mixin Eloquent
 */
class MenuTree extends Model implements TranslatableContract
{

	use Translatable;

	public string $order_default = 'position';

	public string $order_direction = 'ASC';

	public bool $isAjaxEnabled = true;

	protected $fillable = [
		'group',
		'lang',
		'title',
		'url',
		'page_id',
		'parent_id',
		'target_blank',
		'locked',
		'identifier',
		'active',
		'data'
	];

	public $translatedAttributes = ['title', 'url', 'target_blank'];

	protected $appends = ['hasChildren', 'hasParent', 'urlSite'];

	protected $hidden = ['created_at', 'updated_at', 'translations'];

	/**
	 * MAIN
	 */
	protected array $rules = ['group' => 'required'];

	protected array $grid = [];

	protected array $niceNames = [];

	protected array $enum = [
		'group' => [
			'corpo'  => ['label' => 'corpo', 'lang' => 'fr'],
			'main'   => ['label' => 'principal', 'lang' => 'fr'],
			'footer' => ['label' => 'bas de page', 'lang' => 'fr']
		]
	];

	/**
	 * RELATIONS
	 */

	protected $resetCacheOnChange = [];

	/**
	 * @return bool
	 */
	protected function getHasChildrenAttribute()
	{
		return ($this->children->count() > 0);
	}

	//important -> sert pour le widget du menu ... ajouter langue si utilisé : par défaut 'fr'

	/**
	 * @return bool
	 */
	protected function getHasParentAttribute(): bool
	{
		return (bool) $this->parent_id;
	}

	/**
	 * @return bool
	 */
	protected function getIsShortCutAttribute()
	{
		return (!empty($this->url) && $this->url == $this->urlSite);
	}

	protected function getUrlSiteAttribute()
	{
		return $this->getUrl();
	}

	public function getUrl($locale = null)
	{

		if (is_null($locale)) {
			$locale = app()->getLocale();
		}

		if ($this->page) {
			return $this->page->getUrl($locale);
		}

		$url = $this->translate($locale)->url ?? '';

		if (!empty($url)) {
			return (str_starts_with($url, '/')) ? urlPath($url, $locale) : $url;
		}

		return '#undefined';
	}

	public static function boot()
	{

		parent::boot();

		static::deleted(function ($menu) { // force la suppression en cascade des enfants...

			$menu->children()->delete();
		});
	}

	public static function setupMenus()
	{
		self::setupMenu('corpo');
		self::setupMenu('main');
		self::setupMenu('footer');
	}

	public static function setupMenu($name)
	{

		$menus = Cache::get('menus', []);

		$menu = Arr::get($menus, $name);

		// Le menu n'est pas en cache
		if (!$menu) {

			$items = self::orderBy('position')
				->where('group', $name)
				->whereNull('parent_id')
				->get();

			$menu = self::createItems($items);

			$menus[$name] = $menu;

			// Conserve le menu en cache, afin d'éviter d'avoir à le recréer à chaque fois
			Cache::forever('menus', $menus);
		}

		// Configure les items du menu et le fil d'Ariane
		if (!RoutingUtility::isAdmin()) {
			self::setupItems($menu);
		}
		View::share($name . 'Menu', $menu);
	}

	private static function createItems($items, $id = 0)
	{

		$children = [];
		$locales = getLocales();

		foreach ($items as $item) {

			if (!$item->active) {
				continue;
			}

			$child = (object) [];

			foreach ($locales as $locale) {
				if ($locale === app()->getLocale()) {
					$child->title = $item->translate($locale)->title ?? null;
					$child->target_blank = $item->translate($locale)->target_blank ?? null;
					$child->url = $item->getUrl($locale);
				}

				$child->$locale = (object) [
					'title'        => $item->translate($locale)->title ?? '',
					'target_blank' => $item->translate($locale)->target_blank ?? '',
					'url'          => $item->getUrl($locale)
				];
			}

			$child->children = self::createItems($item->children()->where('active', true)->orderBy('position')->get(),
				$item->id);
			$child->hasChildren = count($child->children) > 0;
			$child->identifier = $item->identifier;

			$class = [];
			if (!empty($child->identifier)) {
				$class[] = $item->identifier;
			}
			if ($item->isShortcut) {
				$class[] = 'shortchut';
			}
			if ($child->hasChildren) {
				$class[] = 'parent';
			}
			$child->class = implode(' ', $class);
			$children[] = $child;
		}

		return $children;
	}

	private static function setupItems($items, $breadcrumb = [])
	{

		$locale = app()->getLocale();
		$path = trim(Request::path(), '/');

		foreach ($items as $item) {

			$breadcrumb[] = $item;

			// Configure la langue courante
			foreach ($item->$locale as $key => $value) {
				$item->$key = $value;
			}

			// Indique que c'est l'item de la page courante
			if (trim($item->url, '/') === $path) {

				foreach ($breadcrumb as $crumb) {
					$crumb->class .= ' active';
					$crumb->isActive = true;
				}

				View::share('breadcrumb', $breadcrumb);
			} else {

				$item->isActive = false;
			}

			// Configure ces items
			self::setupItems($item->children, $breadcrumb);

			array_pop($breadcrumb);
		}
	}

	public function saveElement($data = null, $isUnguard = false)
	{
		if (Arr::get($data, 'parent_id') === 0) {
			$data['parent_id'] = null;
		}

		if (Arr::get($data, 'page_id') === 0) {
			$data['page_id'] = null;
		}

		return parent::saveElement($data, $isUnguard);
	}

	/**
	 * @return BelongsTo|MenuTree
	 */
	public function parent()
	{
		return $this->belongsTo(__CLASS__, 'id', 'parent_id');
	}

	/**
	 * @return HasMany|MenuTree[]|MenuTree
	 */
	public function children()
	{
		return $this->hasMany(__CLASS__, 'parent_id', 'id');
	}

	/**
	 * @return BelongsTo|Page
	 */
	public function page()
	{
		return $this->belongsTo(Page::class);
	}

	public function changeOrder($data)
	{
		foreach ($data as $parent_id => $parent) {
			$parent_id = $parent_id !== 0 ? $parent_id : null;

			if (count($parent['items'])) {
				DB::table('menu_trees')
					->whereIn('id', $parent['items'])
					->update([
						'group'     => $parent['group'],
						'parent_id' => $parent_id,
					]);
			}
		}

		foreach ($data as $parent_id => $parent) {
			$parent_id = $parent_id !== 0 ? $parent_id : null;

			$items = $parent['items'];
			$count = count($items);

			if ($count) {
				$i = 0;

				while ($i < $count) {
					DB::table('menu_trees')
						->where('id', '=', $items[$i])
						->where('parent_id', '=', $parent_id)
						->update([
							'position' => $i++
						]);
				}
			}
		}

		Cache::forget($this->getCacheKey());

		$this->deleteCache();
	}

	// Crée les items d'un menu

	public function deleteCache()
	{
		parent::deleteCache();
		self::clearCache();
	}

	/**
	 * MENUS
	 */

	public static function clearCache()
	{
		Cache::forget('menus');
	}
}
