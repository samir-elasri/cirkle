<?php

namespace App\Models\Core;

use App\Models\Translations\ProductCatTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\ProductCat
 *
 * @property int $id
 * @property string|null $identifier
 * @property int $applicable_tvq
 * @property int $applicable_tps
 * @property int $active
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $name
 * @property-read SearchResult $search_result
 * @property-read ProductCat|null $parent
 * @property-read Reminder|null $reminder
 * @property-read ProductCatTranslation|null $translation
 * @property-read Collection<int, ProductCatTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @method static Builder|Model active()
 * @method static Builder|ProductCat listsTranslations(string $translationField)
 * @method static Builder|ProductCat newModelQuery()
 * @method static Builder|ProductCat newQuery()
 * @method static Builder|ProductCat notTranslatedIn(?string $locale = null)
 * @method static Builder|ProductCat orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|ProductCat orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|ProductCat orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|ProductCat query()
 * @method static Builder|ProductCat translated()
 * @method static Builder|ProductCat translatedIn(?string $locale = null)
 * @method static Builder|ProductCat whereActive($value)
 * @method static Builder|ProductCat whereApplicableTps($value)
 * @method static Builder|ProductCat whereApplicableTvq($value)
 * @method static Builder|ProductCat whereCreatedAt($value)
 * @method static Builder|ProductCat whereId($value)
 * @method static Builder|ProductCat whereIdentifier($value)
 * @method static Builder|ProductCat whereParentId($value)
 * @method static Builder|ProductCat whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|ProductCat whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|ProductCat whereUpdatedAt($value)
 * @method static Builder|ProductCat withTranslation()
 * @mixin Eloquent
 */
class ProductCat extends Model implements TranslatableContract
{

	use Translatable;

	protected $fillable = [
		'parent_id',
		'identifier',
		'title',

		'applicable_tvq',
		'applicable_tps',

		'active'
	];

	public $translatedAttributes = ['title'];

	protected array $grid = ['title', 'active'];

	protected array $niceNames = [
		'identifier'     => 'Titre interne',
		'title'          => 'Titre',
		'applicable_tvq' => 'Taxe TVQ applicable pour ce niveau et les enfants',
		'applicable_tps' => 'Taxe TPS applicable pour ce niveau et les enfants',
		'parent_id'      => 'Id parent',
	];

	protected array $rules = [];

	protected array $enum = [];

	protected array $customFields = [
		'parent_id' => [
			'widget'  => 'associate_entity',
			'options' => ['associate_class' => self::class]
		],
	];

	protected array $toggleFields = [];

	public function getNameAttribute()
	{
		return $this->identifier;
	}

	public static function breadcrumb($cat, $breadcrumb)
	{
		$parent = self::find($cat->parent_id);
		if ($parent && $cat->id != $parent->id) {
			$breadcrumb = $parent->identifier . '/' . $breadcrumb;
			return static::breadcrumb($parent, $breadcrumb);
		}

		return $breadcrumb;
	}

	public static function getBreadcrumbList()
	{
		$cats = static::where('active', true)->get();
		foreach ($cats as $cat) {
			$cat->breadcrumb = static::breadcrumb($cat, $cat->identifier);
		}
		return $cats;
	}

	/**
	 * @return HasOne|Reminder
	 */
	public function reminder()
	{
		return $this->hasOne(Reminder::class);
	}

	/**
	 * @return BelongsTo
	 */
	public function parent()
	{
		return $this->belongsTo(__CLASS__);
	}
}
