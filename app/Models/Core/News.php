<?php

namespace App\Models\Core;

use App\Models\Translations\NewsTranslation;
use Arr;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Carbon\Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Support\Collection;
use Route;
use StringUtility;

/**
 * App\Models\Core\News
 *
 * @property int $id
 * @property string|null $label
 * @property string $news_type
 * @property string|null $official_date
 * @property string|null $publication_date
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Bloc> $blocs
 * @property-read int|null $blocs_count
 * @property-read mixed $category_names
 * @property-read mixed $collection_name
 * @property-read mixed $image_grid
 * @property-read mixed $meta_description
 * @property-read mixed $meta_image
 * @property-read mixed $news_type_grid
 * @property-read mixed $news_where_display_grid
 * @property-read SearchResult $search_result
 * @property-read mixed $type_grid
 * @property-read mixed $url
 * @property-read Sharing|null $sharing
 * @property-read NewsTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, NewsTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $image
 * @property string|null $legend
 * @property string|null $description
 * @method static Builder|Model active()
 * @method static Builder|News listsTranslations(string $translationField)
 * @method static Builder|News newModelQuery()
 * @method static Builder|News newQuery()
 * @method static Builder|News notTranslatedIn(?string $locale = null)
 * @method static Builder|News orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|News orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|News orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|News query()
 * @method static Builder|News translated()
 * @method static Builder|News translatedIn(?string $locale = null)
 * @method static Builder|News whereActive($value)
 * @method static Builder|News whereCreatedAt($value)
 * @method static Builder|News whereId($value)
 * @method static Builder|News whereLabel($value)
 * @method static Builder|News whereNewsType($value)
 * @method static Builder|News whereOfficialDate($value)
 * @method static Builder|News wherePublicationDate($value)
 * @method static Builder|News whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|News whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|News whereUpdatedAt($value)
 * @method static Builder|News withTranslation()
 * @mixin Eloquent
 */
class News extends Model implements TranslatableContract
{
	use Translatable;

	public string $order_default = 'publication_date';

	public string $order_direction = 'DESC';

	public string $singular = 'une nouvelle';

	public $searchFields = ['title', 'description', 'legend', 'blocs'];

	protected $table = 'news';

	protected bool $bigData = true;

	protected array $rules = [
		'image' => 'image|mimes:jpeg,jpg,png,gif'
	];

	protected $fillable = [
		'label',
		'title',
		'news_type',
		'news_categories',
		'description',
		'image',
		'legend',
		'official_date',
		'publication_date',
		'active',
	];

	public $translatedAttributes = [
		'title',
		'description',
		'image',
		'legend'
	];

	protected array $grid = [
		'publication_date',
		'imageGrid',
		'label',
		'title',
		'news_type',
		'active'
	];

	protected array $gridFields = [
		'imageGrid' => 'image',
		//		'typeGrid'  => 'news_type'
	];

	// Cet attribut est pour remapper la $grid pour le data-table (tri + recherche CMS big data)
	protected array $niceNames = [
		'label'            => 'Titre interne',
		'title'            => 'Titre',
		'image'            => 'Image',
		'imageGrid'        => 'Image',
		'description'      => 'Résumé',
		'legend'           => 'Légende',
		'news_type'        => 'Type de nouvelles',
		'typeGrid'         => 'Type de nouvelles',
		'official_date'    => 'Date officielle associée',
		'publication_date' => 'Date de publication',
		'news_categories'  => 'Catégories de nouvelles/communiqués',
	];

	protected array $enum = [
		'news_type' => [
			'news'    => 'Nouvelle',
			'reports' => 'Communiqué'
		]
	];

	protected array $customFields = [
		'description'     => ['widget' => 'wysiwyg'],
		'news_categories' => [
			'widget'  => 'associate_categories',
			'options' => [
				'identifier' => 'news',
				'table'      => 'associate_news_categories',
			]
		]
	];

	public function __construct(array $attributes = [])
	{
		$this->attributes['publication_date'] = Carbon::now();
		$this->attributes['official_date'] = Carbon::now();
		parent::__construct($attributes);
	}

	public function getCategoryNamesAttribute()
	{
		$categories = array();
		if (empty($this->news_type)) {
			return $categories;
		}
		$table = 'associate_' . $this->news_type . '_categories';
		$cats = Category::select('category_translations.title', 'categories.id')
			->join('category_translations', 'category_translations.category_id', '=', 'categories.id')
			->join($table, $table . '.cid', '=', 'categories.id')
			->where($table . '.mid', $this->id)
			->where('categories.active', '=', true)
			->where('locale', app()->getLocale())
			->get();

		foreach ($cats as $cat) {
			$categories[$cat->id] = $cat['attributes']['title'];
		}

		return $categories;
	}

	public function getImageGridAttribute()
	{
		return '<img src="' . imageCache($this->image, ['height' => 50]) . '" alt=""/>';
	}

	protected function getMetaDescriptionAttribute()
	{
		return $this->resume;
	}

	protected function getMetaImageAttribute()
	{
		return $this->image;
	}

	protected function getNewsTypeGridAttribute()
	{
		$types = $this['enum']['news_type'];
		return safe($types[$this->news_type], '');
	}

	protected function getNewsWhereDisplayGridAttribute()
	{
		$types = $this['enum']['display_where'];
		return safe($types[$this->display_where], '');
	}

	public function getSearchResultAttribute(): SearchResult
	{

		$result = new SearchResult();
		$result->label = $this->title;
		$result->url = $this->url;
		return $result;
	}
	protected function getTypeGridAttribute()
	{
		if (!empty($this->enum['news_type'][$this->news_type])) {

			return $this->enum['news_type'][$this->news_type];
		}

		return 'N/D';
	}

	public function getUrlAttribute()
	{
		return urlRouteName('news', ['id' => $this->id, 'slug' => slug($this->title)]);
	}

	public function setNewsTypeAttribute($value)
	{
		if (empty($value)) {
			$this->attributes['news_type'] = 'news';
		} else {
			$this->attributes['news_type'] = $value;
		}
	}

	public static function getAllFromType($identifier)
	{
		$category = Category::whereIdentifier($identifier)->first();
		return $category ? self::where('active', true)->whereType($category->id)->get() : new Collection;
	}

	public static function getListForSearch()
	{
		return static::where('active', true)->get();
	}

	public static function getLatest($number = 3)
	{
		return static::orderBy('publication_date', 'DESC')->limit($number)->get();
	}

	public function getCacheKey(): string
	{
		return trim($this->url, '/');
	}

	public function sharing()
	{
		return $this->morphOne(Sharing::class, 'shareable');
	}

	public function next($type = null)
	{
		if (!$type) {
			$type = $this->news_type ?: '';
		}
		return self::active()
			->where('news_type', $type)
			->where('publication_date', '<=', DB::raw('CURDATE()'))
			->where(function ($query){
			    $query->where('official_date', '>', $this->official_date)
					->orWhere(function ($subquery) {
						$subquery->where('official_date', $this->official_date)
							->where('id', '>', $this->id);
					});
			})
			->orderBy('official_date')
			->orderBy('id')
			->first();
	}

	public function previous($type = null)
	{
		if (!$type) {
			$type = $this->news_type ?: '';
		}
		return self::active()
			->where('news_type', $type)
			->where('official_date', '<=', DB::raw('CURDATE()'))
			->where(function ($query){
				$query->where('official_date', '<', $this->official_date)
					->orWhere(function ($subquery) {
						$subquery->where('official_date', $this->official_date)
							->where('id', '<', $this->id);
					});
			})
			->orderBy('official_date', 'DESC')
			->orderBy('id', 'DESC')
			->first();
	}

	public function blocs()
	{
		return $this->morphMany(Bloc::class, 'pageable');
	}

	/**
	 * ONLY FOR SITEMAP
	 * @param $locale
	 * @return mixed|string
	 */
	public function getLocalizedUrl($locale)
	{
		$params = ['id' => $this->id, 'slug' => StringUtility::sluggify($this->title)];
		return localizedUrl('news', $params, $locale);
	}

	/**
	 * Give the categories of news
	 *
	 * @return HigherOrderBuilderProxy|Category[]|Category
	 */
	public static function categories()
	{
		$categories = [];
		$categoryGroup = CategoryGroup::where('identifier', '=', 'news')->first();
		if ($categoryGroup) {
			$categories = $categoryGroup->categories()->where('active', '=', true)->get();
		}
		return $categories;
	}
}
