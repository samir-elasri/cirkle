<?php

namespace App\Models\Core;

use App\Models\Translations\PubTranslation;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;

/**
 * App\Models\Core\Pub
 *
 * @property int $id
 * @property string|null $label
 * @property int|null $position
 * @property int $active
 * @property int|null $pub_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $image_src
 * @property-read SearchResult $search_result
 * @property-read PubGroup|null $pubGroup
 * @property-read PubGroup|null $pub_group
 * @property-read PubTranslation|null $translation
 * @property-read Collection<int, PubTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $pub_image
 * @property string|null $content
 * @property string|null $url
 * @property int|null $isTargetBlank
 * @method static Builder|Model active()
 * @method static Builder|Pub listsTranslations(string $translationField)
 * @method static Builder|Pub newModelQuery()
 * @method static Builder|Pub newQuery()
 * @method static Builder|Pub notTranslatedIn(?string $locale = null)
 * @method static Builder|Pub orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Pub orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Pub orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Pub query()
 * @method static Builder|Pub translated()
 * @method static Builder|Pub translatedIn(?string $locale = null)
 * @method static Builder|Pub whereActive($value)
 * @method static Builder|Pub whereCreatedAt($value)
 * @method static Builder|Pub whereId($value)
 * @method static Builder|Pub whereLabel($value)
 * @method static Builder|Pub wherePosition($value)
 * @method static Builder|Pub wherePubGroupId($value)
 * @method static Builder|Pub whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Pub whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Pub whereUpdatedAt($value)
 * @method static Builder|Pub withTranslation()
 * @mixin Eloquent
 */
class Pub extends Model implements TranslatableContract
{

	use Translatable;

	public string $order_default = 'position';
	public string $order_direction = 'ASC';
	public string $singular = 'une publicité';
	public $relatedGrid = 'publicités pour cette campagne';

	public $visible = [
		'id',
		'label',
		'title',
		'imageSrc',
		'url',
		'isTargetBlank',
		'active'
	];

	protected $fillable = [
		'pub_group_id',
		'label',
		'title',
		'pub_image',
		'content',
		'url',
		'isTargetBlank',
		'active'
	];

	public $translatedAttributes = [
		'title',
		'pub_image',
		'content',
		'url',
		'isTargetBlank'
	];

	protected $appends = ['imageSrc'];

	protected array $rules = [
		'label'     => 'required',
		'pub_image' => 'image|mimes:jpeg,jpg,png,gif',
	];
	protected array $niceNames = [
		'pub_image' => 'Image',
		'isTargetBlank' => 'Ouverture dans une autre fenêtre'
	];
	protected array $customFields = [
		'content' => [
			'widget' => 'wysiwyg',
			'options' => ['height' => '150']
		]
	];

	protected $resetCacheOnChange = [
		PubGroup::class
	];

	public array $positionParentFields = ['pub_group_id'];

	protected array $grid = [
		'label',
		'active'
	];

	protected function getImageSrcAttribute()
	{
		return empty($this->pub_image) ? '' : $this->pub_image;
	}

	/**
	 * @return BelongsTo|PubGroup
	 */
	public function pub_group()
	{
		return $this->belongsTo(PubGroup::class);
	}

	/**
	 * @return BelongsTo|PubGroup
	 */
	public function pubGroup()
	{
		return $this->belongsTo(PubGroup::class);
	}

	protected static function boot()
	{
		parent::boot();

		static::saved(function ($model) {
			$pages = Page::where('pub_group_id', $model->pub_group_id)->orWhere('has_right_column', true)->whereNull('pub_group_id')->get();
			foreach ($pages as $page) {
				if (Cache::has($page->getCacheKey())) {
					Cache::pull($page->getCacheKey());
				}
			}
		});
	}
}
