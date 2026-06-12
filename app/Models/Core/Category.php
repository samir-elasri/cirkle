<?php

namespace App\Models\Core;

use App\Models\Translations\CategoryTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Category
 *
 * @property int $id
 * @property int|null $category_group_id
 * @property string|null $identifier
 * @property int|null $position
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CategoryGroup|null $categoryGroup
 * @property-read CategoryGroup|null $category_group
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read string $title_grid
 * @property-read CategoryTranslation|null $translation
 * @property-read Collection<int, CategoryTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @method static Builder|Model active()
 * @method static Builder|Category listsTranslations(string $translationField)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category notTranslatedIn(?string $locale = null)
 * @method static Builder|Category orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Category orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Category orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Category query()
 * @method static Builder|Category translated()
 * @method static Builder|Category translatedIn(?string $locale = null)
 * @method static Builder|Category whereActive($value)
 * @method static Builder|Category whereCategoryGroupId($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereIdentifier($value)
 * @method static Builder|Category wherePosition($value)
 * @method static Builder|Category whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Category whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category withTranslation()
 * @mixin Eloquent
 */
class Category extends Model implements TranslatableContract
{

	use Translatable;

	public string $order_default = 'position';

	public string $order_direction = 'ASC';

	public string $singular = 'un élément';

	public $relatedGrid = 'éléments pour cette catégorie';

	protected $fillable = [
		'identifier',
		'title',
		'category_group_id',
		'active'
	];

	public $translatedAttributes = [
		'title'
	];

	protected array $grid = [
		'titleGrid',
		'active'
	];

	protected array $niceNames = [
		'identifier' => 'Identifiant',
		'titleGrid'  => 'Titre',
		'title'      => 'Titre',
	];

	protected array $rules = [
		'title' => 'required'
	];

	protected $appends = ['titleGrid'];

	protected $resetCacheOnChange = [
		CategoryGroup::class
	];

	public array $positionParentFields = ['category_group_id'];

	/**
	 * @return string
	 */
	protected function getTitleGridAttribute(): string
	{
		return $this->title . (empty($this->identifier) ? '' : '<br/><small>' . $this->identifier . '</small>');
	}

	/**
	 * @param $tag
	 * @param $identifier
	 * @return string
	 */
	public static function getByTag($tag, $identifier): string
	{

		$obj = static::getListByIdentifier($identifier)->first(function ($index, $item) use ($tag) {
			return ($item->identifier == $tag);
		});
		return ($obj) ? $obj->title : '';
	}

	/**
	 * @param $listIds
	 * @return Collection
	 */
	public static function getAllByIds($listIds): Collection
	{

		$categories = new Collection;
		foreach ($listIds as $id) {
			$category = static::find($id);
			if ($category) {
				$category->setVisible(['id', 'title', 'identifier']);
				$categories->add($category);
			}
		}
		return $categories;
	}

	/**
	 * @param $listTags
	 * @param $identifier
	 * @return array
	 */
	public static function getAllByTags($listTags, $identifier): array
	{

		$categories = array(); //new Collection;

		foreach ($listTags as $tag) {
			$category = static::getByTag($tag, $identifier);
			if ($category) {
				//$category->setVisible(['id', 'title', 'identifier']);
				//$categories->add($category->title);
				$categories[] = $category;
			}
		}

		return $categories;
	}

	/**
	 * @param $identifier
	 * @return Category[]|Collection|mixed
	 */
	public static function getListByIdentifier($identifier)
	{
		$group = CategoryGroup::firstOrCreate(['identifier' => $identifier]);
		return $group->categories ?? new Collection;
	}

	/**
	 * @param $identifier
	 * @return Category
	 */
	public static function finentifier($identifier): Category
	{
		return self::firstOrCreate(['identifier' => $identifier]);
	}

	public static function getTitle($id)
	{
		$category = self::find($id);
		return $category->title ?? '';
	}

	/**
	 * @return BelongsTo|CategoryGroup
	 */
	public function category_group()
	{
		return $this->belongsTo(CategoryGroup::class);
	}

	/**
	 * @return BelongsTo|CategoryGroup
	 */
	public function categoryGroup()
	{
		return $this->belongsTo(CategoryGroup::class);
	}

	/**
	 * @param $identifier
	 * @param $name
	 * @param  null  $placeholder
	 * @return array
	 */
	public static function getSelect($identifier, $name = null, $placeholder = null): array
	{
		$select = [];
		if ($placeholder) {
			$select[''] = $placeholder;
		}

		$group = CategoryGroup::firstOrCreate(['identifier' => $identifier]);

		if ($group) {
			foreach ($group->categories()->active()->orderBy('position')->get() as $item) {
				/** @var Category $item */
				$select[$item->id] = $item->title;
			}
		}
		return $select;
	}
}
